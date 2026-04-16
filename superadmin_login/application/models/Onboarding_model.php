<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Onboarding_model
 * 
 * Automates the entire organization onboarding process:
 * 1. Creates MySQL database
 * 2. Imports reference schema (structure only)
 * 3. Seeds required data (roles, admin user, SMTP, order statuses)
 * 4. Appends tenant config to both database.php files
 * 5. Creates uploaded_files folder structure
 * 6. Verifies the entire setup
 */
class Onboarding_model extends CI_Model {

    private $setupLog = [];
    private $errors = [];

    // Path to the reference SQL file (contains full schema of a tenant DB)
    // This SQL is treated as schema-only; all INSERT statements are stripped 
    // except for SUPPLIERS_orderStatusList seed data
    private $referenceSqlPath;

    // Paths to the two database.php config files that need tenant entries
    private $mainDbConfigPath;
    private $externalDbConfigPath;

    // Base path for uploaded_files folder creation
    private $uploadedFilesBasePath;

    function __construct() {
        parent::__construct();

        // Resolve paths relative to FCPATH (the front controller path = superadmin_login/)
        // We go up one level to reach the main Bizadmin root
        $bizadminRoot = realpath(FCPATH . '/../');

        $this->referenceSqlPath      = $bizadminRoot . '/application/third_party/bizadmincom_db.sql';
        $this->mainDbConfigPath      = $bizadminRoot . '/application/config/database.php';
        $this->externalDbConfigPath  = $bizadminRoot . '/External/application/config/database.php';
        $this->uploadedFilesBasePath = $bizadminRoot . '/uploaded_files';
    }

    /**
     * Run the full automated onboarding process
     * 
     * @param array  $postData       Form data from the organization add form
     * @param int    $orzId          The newly created organization_list ID
     * @param string $hashedPassword The hashed password for the admin user
     * @return array ['success' => bool, 'log' => array, 'errors' => array]
     */
    public function runFullSetup($postData, $orzId, $hashedPassword) {
        $this->setupLog = [];
        $this->errors   = [];

        $tenantIdentifier = trim($postData['tenant_identifier']);
        $dbName           = trim($postData['db_name']);
        $dbUsername        = trim($postData['db_username']);
        $dbPass           = trim($postData['db_pass']);

        // Step 1: Create the database (auto on localhost, detects existing on cPanel)
        $this->logStep('create_database', 'Checking/creating database: ' . $dbName);
        $dbCreated = $this->createDatabase($dbName, $dbUsername, $dbPass);

        // Step 2: Import reference schema — always attempt if we can connect to the DB
        $this->logStep('import_schema', 'Importing reference schema into: ' . $dbName);
        $schemaImported = $this->importSchema($dbName, $dbUsername, $dbPass);

        // Step 3: Populate seed data — only if schema was imported (tables must exist)
        if ($schemaImported) {
            $this->logStep('populate_seed_data', 'Populating admin user, roles, SMTP settings');
            $this->populateSeedData($postData, $orzId, $hashedPassword);
        } else {
            $this->logError('populate_seed_data', 'SKIPPED: Cannot seed data because schema import failed');
        }

        // Step 4: Update database.php config files
        $this->logStep('update_config_files', 'Updating database config files');
        $this->updateDatabaseConfigs($tenantIdentifier, $dbName, $dbUsername, $dbPass);

        // Step 5: Create uploaded_files folder structure
        $this->logStep('create_folders', 'Creating uploaded_files folder structure');
        $this->createUploadFolders($tenantIdentifier, $postData);

        // Step 6: Verify everything
        $this->logStep('verify_setup', 'Running verification checks');
        $this->verifySetup($postData, $orzId);

        return [
            'success' => empty($this->errors),
            'log'     => $this->setupLog,
            'errors'  => $this->errors,
        ];
    }

    // =========================================================================
    // STEP 1: Create Database
    // =========================================================================

    /**
     * Create the MySQL database. 
     * Uses the default (superadmin) DB connection's credentials which should 
     * have CREATE DATABASE privileges. On shared hosting (cPanel), the database 
     * must be pre-created manually — this step will detect that and skip.
     */
    private function createDatabase($dbName, $dbUsername, $dbPass) {
        try {
            // First: try to connect directly to the database using tenant credentials.
            // This is the most reliable check — works on both localhost and cPanel.
            $directConn = @new mysqli('localhost', $dbUsername, $dbPass, $dbName);
            if (!$directConn->connect_error) {
                $this->logStep('create_database', 'Database already exists and tenant credentials work: ' . $dbName . ' (skipping creation)');
                $directConn->close();
                return true;
            }

            // Second: try with superadmin credentials to connect to the database
            $hostname = $this->db->hostname;
            $username = $this->db->username;
            $password = $this->db->password;
            $directConn = @new mysqli($hostname, $username, $password, $dbName);
            if (!$directConn->connect_error) {
                $this->logStep('create_database', 'Database already exists (connected via superadmin): ' . $dbName . ' (skipping creation)');
                $directConn->close();
                return true;
            }

            // Database doesn't exist or can't connect — try to create it (localhost only)
            $conn = @new mysqli($hostname, $username, $password);
            if ($conn->connect_error) {
                $this->logError('create_database', 'Cannot connect to MySQL: ' . $conn->connect_error);
                return false;
            }

            $escaped = $conn->real_escape_string($dbName);
            if ($conn->query("CREATE DATABASE IF NOT EXISTS `{$escaped}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
                $this->logStep('create_database', 'Database created successfully: ' . $dbName);
                $conn->close();
                return true;
            } else {
                $this->logError('create_database', 'Failed to create database (on cPanel, create it manually first): ' . $conn->error);
                $conn->close();
                return false;
            }
        } catch (Exception $e) {
            $this->logError('create_database', 'Exception: ' . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // STEP 2: Import Reference Schema
    // =========================================================================

    /**
     * Import the reference SQL schema into the new tenant database.
     * Strips all INSERT statements except SUPPLIERS_orderStatusList.
     */
    private function importSchema($dbName, $dbUsername, $dbPass) {
        if (!file_exists($this->referenceSqlPath)) {
            $this->logError('import_schema', 'Reference SQL file not found at: ' . $this->referenceSqlPath);
            return false;
        }

        try {
            $conn = new mysqli('localhost', $dbUsername, $dbPass, $dbName);
            if ($conn->connect_error) {
                // Fallback: try with root/superadmin credentials
                $this->logStep('import_schema', 'Tenant credentials failed, trying superadmin credentials...');
                $conn = new mysqli($this->db->hostname, $this->db->username, $this->db->password, $dbName);
                if ($conn->connect_error) {
                    $this->logError('import_schema', 'Cannot connect to new database: ' . $conn->connect_error);
                    return false;
                }
            }

            $sql = file_get_contents($this->referenceSqlPath);

            // Clean the SQL: remove all INSERT statements EXCEPT SUPPLIERS_orderStatusList
            $cleanedSql = $this->cleanSqlForImport($sql);

            // Disable foreign key checks during import
            $conn->query("SET FOREIGN_KEY_CHECKS = 0");
            $conn->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

            if ($conn->multi_query($cleanedSql)) {
                // Consume all results to avoid "Commands out of sync" errors
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());

                if ($conn->error) {
                    $this->logError('import_schema', 'SQL import completed with errors: ' . $conn->error);
                }
            } else {
                $this->logError('import_schema', 'Failed to start schema import: ' . $conn->error);
            }

            $conn->query("SET FOREIGN_KEY_CHECKS = 1");

            // Verify tables were actually created by checking table count
            $tableCheck = $conn->query("SHOW TABLES");
            $tableCount = $tableCheck ? $tableCheck->num_rows : 0;
            if ($tableCount > 0) {
                $this->logStep('import_schema', 'Schema imported successfully (' . $tableCount . ' tables created)');
            } else {
                $this->logError('import_schema', 'Schema import ran but NO tables were created! Check the SQL file and MySQL error logs.');
            }

            $conn->close();
            return ($tableCount > 0);

        } catch (Exception $e) {
            $this->logError('import_schema', 'Exception during import: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean SQL dump for import:
     * - Keep all CREATE TABLE, ALTER TABLE, SET statements
     * - Remove all INSERT statements EXCEPT for SUPPLIERS_orderStatusList
     * - Remove database-specific USE/CREATE DATABASE statements
     */
    private function cleanSqlForImport($sql) {
        $lines = explode("\n", $sql);
        $cleanedLines = [];
        $skipInsert = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Skip USE and CREATE DATABASE statements
            if (preg_match('/^(USE |CREATE DATABASE )/i', $trimmed)) {
                continue;
            }

            // Handle INSERT statements - only keep SUPPLIERS_orderStatusList
            if (preg_match('/^INSERT INTO/i', $trimmed)) {
                if (stripos($trimmed, 'SUPPLIERS_orderStatusList') !== false) {
                    $cleanedLines[] = $line;
                    // Multi-line INSERT: keep reading until semicolon
                    if (substr(rtrim($trimmed), -1) !== ';') {
                        $skipInsert = false; // Actually we want to keep this one
                    }
                } else {
                    // Skip this INSERT and any continuation lines
                    if (substr(rtrim($trimmed), -1) !== ';') {
                        $skipInsert = true;
                    }
                    continue;
                }
            } elseif ($skipInsert) {
                // We're in a multi-line INSERT that should be skipped
                if (substr(rtrim($trimmed), -1) === ';') {
                    $skipInsert = false;
                }
                continue;
            } else {
                $cleanedLines[] = $line;
            }
        }

        return implode("\n", $cleanedLines);
    }

    // =========================================================================
    // STEP 3: Populate Seed Data
    // =========================================================================

    /**
     * Insert all required seed data into the new tenant database:
     * - 5 default roles (Admin, Manager, Staff, Employee, Timesheet)
     * - Admin user record
     * - User-to-role mapping
     * - User-to-location mappings
     * - Backup SMTP settings
     */
    private function populateSeedData($postData, $orzId, $hashedPassword) {
        try {
            // Try connecting with tenant credentials first, then fallback to superadmin
            $dbName = $postData['db_name'];
            $connHost = 'localhost';
            $connUser = $postData['db_username'];
            $connPass = $postData['db_pass'];

            $testConn = @new mysqli($connHost, $connUser, $connPass, $dbName);
            if ($testConn->connect_error) {
                // Fallback to superadmin credentials
                $this->logStep('populate_seed_data', 'Tenant credentials failed, trying superadmin credentials...');
                $connHost = $this->db->hostname;
                $connUser = $this->db->username;
                $connPass = $this->db->password;
                $testConn = @new mysqli($connHost, $connUser, $connPass, $dbName);
                if ($testConn->connect_error) {
                    $this->logError('populate_seed_data', 'Cannot connect to tenant database: ' . $testConn->connect_error);
                    return false;
                }
            }
            $testConn->close();

            $config = [
                'hostname' => $connHost,
                'username' => $connUser,
                'password' => $connPass,
                'database' => $dbName,
                'dbdriver' => 'mysqli',
                'db_debug' => FALSE,
            ];
            $newDb = @$this->load->database($config, TRUE);

            if (!$newDb || !$newDb->conn_id) {
                $this->logError('populate_seed_data', 'CI database loader failed despite successful mysqli test');
                return false;
            }

            // --- Verify tables exist before inserting ---
            $tableCheck = $newDb->query("SHOW TABLES LIKE 'Global_roles'");
            if (!$tableCheck || $tableCheck->num_rows() == 0) {
                $this->logError('populate_seed_data', 'CRITICAL: Global_roles table does not exist! Schema import likely failed. Cannot seed data.');
                return false;
            }

            // --- Check if data already exists (re-run safety) ---
            $existingRoles = $newDb->get('Global_roles');
            if ($existingRoles && $existingRoles->num_rows() > 0) {
                $this->logStep('populate_seed_data', 'Seed data already exists (' . $existingRoles->num_rows() . ' roles found). Skipping seed to avoid duplicates.');
                return true;
            }

            // --- Insert 5 Default Roles ---
            $roles = [
                ['id' => 1, 'name' => 'Admin',     'displayName' => 'Admin',     'description' => 'Owner',                                              'status' => 1, 'showSeprateChecklist' => 0, 'location_id' => 0],
                ['id' => 2, 'name' => 'Manager',   'displayName' => 'Manager',   'description' => 'Site Manager - Who does what owner delegates',        'status' => 1, 'showSeprateChecklist' => 0, 'location_id' => 0],
                ['id' => 3, 'name' => 'Staff',     'displayName' => 'Staff',     'description' => 'Staff member with limited access',                    'status' => 1, 'showSeprateChecklist' => 1, 'location_id' => 0],
                ['id' => 4, 'name' => 'Employee',  'displayName' => 'Employee',  'description' => 'This role will be by default, assigned to all employees', 'status' => 1, 'showSeprateChecklist' => 0, 'location_id' => 0],
                ['id' => 5, 'name' => 'Timesheet', 'displayName' => NULL,        'description' => 'This is for timesheet portal, Dont delete it',       'status' => 1, 'showSeprateChecklist' => 0, 'location_id' => 0],
            ];

            foreach ($roles as $role) {
                $newDb->insert('Global_roles', $role);
            }
            
            // Verify roles were actually inserted
            $rolesCheck = $newDb->get('Global_roles');
            if (!$rolesCheck || $rolesCheck->num_rows() == 0) {
                $this->logError('populate_seed_data', 'FAILED: Roles insert ran but Global_roles is still empty!');
                return false;
            }
            $this->logStep('populate_seed_data', $rolesCheck->num_rows() . ' default roles created (Admin, Manager, Staff, Employee, Timesheet)');

            // --- Insert Admin User ---
            $adminData = [
                'id'           => 1,
                'role_id'      => 1,
                'username'     => $postData['tenant_identifier'],
                'first_name'   => $postData['orz_name'],
                'email'        => $postData['orz_email'],
                'phone'        => isset($postData['orz_phone']) ? $postData['orz_phone'] : '',
                'password'     => $hashedPassword,
                'company'      => $orzId,
                'system_ids'   => serialize($postData['system_ids']),
                'location_ids' => serialize($postData['location_ids']),
                'active'       => isset($postData['organization_list_status']) ? $postData['organization_list_status'] : 1,
                'created_on'   => time(),
            ];
            $newDb->insert('Global_users', $adminData);
            $adminUserId = $newDb->insert_id();
            if (!$adminUserId || $adminUserId == 0) {
                $this->logError('populate_seed_data', 'FAILED: Admin user insert failed! insert_id returned: ' . $adminUserId);
                return false;
            }
            $this->logStep('populate_seed_data', 'Admin user created (ID: ' . $adminUserId . ')');

            // --- Assign Admin Role to User ---
            $newDb->insert('Global_userid_to_roles', [
                'user_id'  => $adminUserId,
                'group_id' => 1, // Admin role
            ]);
            $this->logStep('populate_seed_data', 'Admin role assigned to user');

            // --- Assign All Locations to Admin ---
            if (isset($postData['location_ids']) && is_array($postData['location_ids'])) {
                foreach ($postData['location_ids'] as $locationId) {
                    $newDb->insert('Global_users_to_location', [
                        'user_id'     => $adminUserId,
                        'location_id' => (int)$locationId,
                    ]);
                }
                $this->logStep('populate_seed_data', count($postData['location_ids']) . ' location(s) assigned to admin');
            }

            // --- Insert Backup SMTP Settings ---
            $smtpData = [
                'id'                 => 1,
                'location_id'        => 9999,
                'system_id'          => '9999',
                'smtp_host'          => 'smtp.office365.com',
                'smtp_username'      => 'info@bizadmin.com.au',
                'smtp_pass'          => '1800@Footscray123!',
                'smtp_port'          => '25',
                'smtp_encryptionType'=> 'tls',
                'mail_protocol'      => 'smtp',
                'mail_from'          => 'info@bizadmin.com.au',
            ];
            $newDb->insert('Global_SmtpSettings', $smtpData);
            $this->logStep('populate_seed_data', 'Backup SMTP settings inserted (location_id=9999)');

            return true;

        } catch (Exception $e) {
            $this->logError('populate_seed_data', 'Exception: ' . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // STEP 4: Update Database Config Files
    // =========================================================================

    /**
     * Append the new tenant's database config to both:
     * - application/config/database.php  (main app)
     * - External/application/config/database.php
     * 
     * Checks if the tenant entry already exists before appending.
     */
    private function updateDatabaseConfigs($tenantIdentifier, $dbName, $dbUsername, $dbPass) {
        $configBlock = $this->generateDbConfigBlock($tenantIdentifier, $dbName, $dbUsername, $dbPass);

        // Update main database.php
        $this->appendToConfigFile($this->mainDbConfigPath, $tenantIdentifier, $configBlock, 'Main app');

        // Update External database.php
        $this->appendToConfigFile($this->externalDbConfigPath, $tenantIdentifier, $configBlock, 'External app');
    }

    /**
     * Generate a CI database config array block for a tenant
     */
    private function generateDbConfigBlock($tenantIdentifier, $dbName, $dbUsername, $dbPass) {
        // Escape single quotes in password for PHP string
        $escapedPass = str_replace("'", "\\'", $dbPass);
        $escapedUser = str_replace("'", "\\'", $dbUsername);
        $escapedDb   = str_replace("'", "\\'", $dbName);

        return <<<EOT

\$db['{$tenantIdentifier}'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => '{$escapedUser}',
	'password' => '{$escapedPass}',
	'database' => '{$escapedDb}',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

EOT;
    }

    /**
     * Append a config block to a database.php file if the tenant key doesn't already exist
     */
    private function appendToConfigFile($filePath, $tenantIdentifier, $configBlock, $label) {
        if (!file_exists($filePath)) {
            $this->logError('update_config_files', $label . ' config file not found: ' . $filePath);
            return false;
        }

        $content = file_get_contents($filePath);

        // Check if this tenant already has an entry
        if (strpos($content, "\$db['{$tenantIdentifier}']") !== false) {
            $this->logStep('update_config_files', $label . ': Tenant config already exists for "' . $tenantIdentifier . '" (skipping)');
            return true;
        }

        // Append the config block at the end of the file
        // Remove trailing PHP close tag if present, append config, re-add close tag
        $content = rtrim($content);
        if (substr($content, -2) === '?>') {
            $content = rtrim(substr($content, 0, -2));
        }

        $content .= "\n" . $configBlock . "\n";

        if (file_put_contents($filePath, $content) !== false) {
            $this->logStep('update_config_files', $label . ': Tenant config added for "' . $tenantIdentifier . '"');
            return true;
        } else {
            $this->logError('update_config_files', $label . ': Failed to write config file (check permissions): ' . $filePath);
            return false;
        }
    }

    // =========================================================================
    // STEP 5: Create Upload Folders
    // =========================================================================

    /**
     * Create the uploaded_files/{org_name}/ folder with system subfolders.
     * System folder names come from the system_details.slug values in the DB.
     */
    private function createUploadFolders($tenantIdentifier, $postData) {
        $orgFolder = $this->uploadedFilesBasePath . '/' . $tenantIdentifier;

        // Create base uploaded_files folder if missing
        if (!is_dir($this->uploadedFilesBasePath)) {
            if (!mkdir($this->uploadedFilesBasePath, 0755, true)) {
                $this->logError('create_folders', 'Cannot create base uploaded_files folder');
                return false;
            }
        }

        // Create org folder
        if (!is_dir($orgFolder)) {
            if (!mkdir($orgFolder, 0755, true)) {
                $this->logError('create_folders', 'Cannot create org folder: ' . $orgFolder);
                return false;
            }
        }

        // Fetch all system slugs from the superadmin DB to create subfolders
        $systemSlugs = $this->getSystemSlugs($postData);

        // Standard subfolders that every org needs
        $standardFolders = ['Checklist', 'Checklist/checklistAttachments'];

        foreach ($standardFolders as $folder) {
            $path = $orgFolder . '/' . $folder;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Create system-specific folders
        foreach ($systemSlugs as $slug) {
            if (empty($slug)) continue;
            $path = $orgFolder . '/' . $slug;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Also create in External/uploaded_files/
        $externalOrgFolder = realpath(FCPATH . '/../') . '/External/uploaded_files/' . $tenantIdentifier;
        if (!is_dir($externalOrgFolder)) {
            mkdir($externalOrgFolder, 0755, true);
            foreach ($systemSlugs as $slug) {
                if (!empty($slug)) {
                    mkdir($externalOrgFolder . '/' . $slug, 0755, true);
                }
            }
        }

        $this->logStep('create_folders', 'Upload folders created for "' . $tenantIdentifier . '" with ' . count($systemSlugs) . ' system subfolders');
        return true;
    }

    /**
     * Get system slugs from the superadmin database for the assigned system IDs
     */
    private function getSystemSlugs($postData) {
        $slugs = [];
        if (isset($postData['system_ids']) && is_array($postData['system_ids'])) {
            $ids = array_map('intval', $postData['system_ids']);
            $this->db->where_in('system_details_id', $ids);
            $this->db->where('system_details_status', 1);
            $query = $this->db->get('system_details');
            foreach ($query->result() as $row) {
                if (!empty($row->slug)) {
                    $slugs[] = $row->slug;
                }
            }
        }
        return $slugs;
    }

    // =========================================================================
    // STEP 6: Verify Setup
    // =========================================================================

    /**
     * Run verification checks on the newly created tenant database
     */
    private function verifySetup($postData, $orzId) {
        try {
            // Try connecting with tenant credentials first, then fallback to superadmin
            $dbName = $postData['db_name'];
            $connHost = 'localhost';
            $connUser = $postData['db_username'];
            $connPass = $postData['db_pass'];

            $testConn = @new mysqli($connHost, $connUser, $connPass, $dbName);
            if ($testConn->connect_error) {
                $connHost = $this->db->hostname;
                $connUser = $this->db->username;
                $connPass = $this->db->password;
                $testConn = @new mysqli($connHost, $connUser, $connPass, $dbName);
                if ($testConn->connect_error) {
                    $this->logError('verify_setup', 'Cannot connect to tenant DB for verification: ' . $testConn->connect_error);
                    return;
                }
            }
            $testConn->close();

            $config = [
                'hostname' => $connHost,
                'username' => $connUser,
                'password' => $connPass,
                'database' => $dbName,
                'dbdriver' => 'mysqli',
                'db_debug' => FALSE,
            ];
            $verifyDb = @$this->load->database($config, TRUE);

            if (!$verifyDb || !$verifyDb->conn_id) {
                $this->logError('verify_setup', 'CI database loader failed for verification');
                return;
            }

            // Quick connection test - run a simple query first
            $testQuery = $verifyDb->query("SELECT 1");
            if (!$testQuery) {
                $this->logError('verify_setup', 'Database connection exists but queries fail - tables may not have been imported');
                return;
            }

            // Check 1: Global_users has admin record
            $users = $verifyDb->get('Global_users');
            if ($users && $users->num_rows() > 0) {
                $admin = $users->row();
                if ($admin->role_id == 1 && $admin->company == $orzId) {
                    $this->logStep('verify_setup', 'PASS: Global_users has admin record (company=' . $orzId . ')');
                } else {
                    $this->logError('verify_setup', 'WARN: Global_users admin record has unexpected role_id=' . $admin->role_id . ' or company=' . $admin->company . ' (expected company=' . $orzId . ')');
                }
            } else {
                $this->logError('verify_setup', 'FAIL: Global_users is empty or table does not exist!');
            }

            // Check 2: Global_roles has 5 records
            $roles = $verifyDb->get('Global_roles');
            if ($roles && $roles->num_rows() > 0) {
                $roleCount = $roles->num_rows();
                if ($roleCount == 5) {
                    $this->logStep('verify_setup', 'PASS: Global_roles has 5 records');
                } else {
                    $this->logError('verify_setup', 'FAIL: Global_roles has ' . $roleCount . ' records (expected 5)');
                }
            } else {
                $this->logError('verify_setup', 'FAIL: Global_roles is empty or table does not exist!');
            }

            // Check 3: Global_userid_to_roles has admin mapping
            $roleMap = $verifyDb->get('Global_userid_to_roles');
            if ($roleMap && $roleMap->num_rows() > 0) {
                $this->logStep('verify_setup', 'PASS: Global_userid_to_roles has ' . $roleMap->num_rows() . ' record(s)');
            } else {
                $this->logError('verify_setup', 'FAIL: Global_userid_to_roles is empty or table does not exist!');
            }

            // Check 4: Global_users_to_location has location assignments
            $locMap = $verifyDb->get('Global_users_to_location');
            if ($locMap && $locMap->num_rows() > 0) {
                $this->logStep('verify_setup', 'PASS: Global_users_to_location has ' . $locMap->num_rows() . ' location(s)');
            } else {
                $this->logError('verify_setup', 'FAIL: Global_users_to_location is empty or table does not exist!');
            }

            // Check 5: Global_SmtpSettings has backup SMTP
            $verifyDb->where('location_id', 9999);
            $smtp = $verifyDb->get('Global_SmtpSettings');
            if ($smtp && $smtp->num_rows() > 0) {
                $this->logStep('verify_setup', 'PASS: Global_SmtpSettings has backup SMTP (location_id=9999)');
            } else {
                $this->logError('verify_setup', 'FAIL: Global_SmtpSettings missing backup SMTP record or table does not exist!');
            }

            // Check 6: SUPPLIERS_orderStatusList has data
            $orderStatuses = $verifyDb->get('SUPPLIERS_orderStatusList');
            if ($orderStatuses && $orderStatuses->num_rows() > 0) {
                $this->logStep('verify_setup', 'PASS: SUPPLIERS_orderStatusList has ' . $orderStatuses->num_rows() . ' status(es)');
            } else {
                $this->logError('verify_setup', 'WARN: SUPPLIERS_orderStatusList is empty or table does not exist');
            }

            // Check 7: Verify config files have the tenant entry
            $tenantIdentifier = trim($postData['tenant_identifier']);
            
            if (file_exists($this->mainDbConfigPath)) {
                $mainContent = file_get_contents($this->mainDbConfigPath);
                if (strpos($mainContent, "\$db['{$tenantIdentifier}']") !== false) {
                    $this->logStep('verify_setup', 'PASS: Main database.php has tenant config');
                } else {
                    $this->logError('verify_setup', 'FAIL: Main database.php missing tenant config!');
                }
            }

            if (file_exists($this->externalDbConfigPath)) {
                $extContent = file_get_contents($this->externalDbConfigPath);
                if (strpos($extContent, "\$db['{$tenantIdentifier}']") !== false) {
                    $this->logStep('verify_setup', 'PASS: External database.php has tenant config');
                } else {
                    $this->logError('verify_setup', 'FAIL: External database.php missing tenant config!');
                }
            }

        } catch (Exception $e) {
            $this->logError('verify_setup', 'Verification exception: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // Logging Helpers  
    // =========================================================================

    private function logStep($step, $message) {
        $this->setupLog[] = [
            'step'    => $step,
            'status'  => 'ok',
            'message' => $message,
            'time'    => date('H:i:s'),
        ];
        log_message('info', '[Onboarding] ' . $step . ': ' . $message);
    }

    private function logError($step, $message) {
        $this->errors[] = [
            'step'    => $step,
            'message' => $message,
            'time'    => date('H:i:s'),
        ];
        $this->setupLog[] = [
            'step'    => $step,
            'status'  => 'error',
            'message' => $message,
            'time'    => date('H:i:s'),
        ];
        log_message('error', '[Onboarding] ' . $step . ': ' . $message);
    }

    public function getSetupLog() {
        return $this->setupLog;
    }

    public function getErrors() {
        return $this->errors;
    }

    // =========================================================================
    // DELETE Organization - Remove all data for an organization
    // =========================================================================

    /**
     * Permanently delete all data for an organization:
     * 1. Drop the tenant database
     * 2. Remove tenant config from both database.php files
     * 3. Delete uploaded_files folders
     * 4. Soft-delete the organization_list record in superadmin DB
     * 
     * @param object $org   The organization record from organization_list
     * @param int    $orzId The organization_list_id
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteOrganizationData($org, $orzId) {
        $errors = [];
        $tenantIdentifier = $org->tenant_identifier;
        $dbName           = $org->db_name;

        // --- Step 1: Drop the tenant database ---
        try {
            $hostname = $this->db->hostname;
            $username = $this->db->username;
            $password = $this->db->password;

            $conn = new mysqli($hostname, $username, $password);
            if (!$conn->connect_error && !empty($dbName)) {
                $escaped = $conn->real_escape_string($dbName);
                $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$escaped}'");
                if ($result && $result->num_rows > 0) {
                    if ($conn->query("DROP DATABASE `{$escaped}`")) {
                        log_message('info', '[Delete ORZ] Database dropped: ' . $dbName);
                    } else {
                        $errors[] = 'Failed to drop database: ' . $conn->error;
                        log_message('error', '[Delete ORZ] Failed to drop DB: ' . $conn->error);
                    }
                } else {
                    log_message('info', '[Delete ORZ] Database does not exist (already removed): ' . $dbName);
                }
                $conn->close();
            } else {
                if (empty($dbName)) {
                    log_message('info', '[Delete ORZ] No database name set for org ID ' . $orzId);
                } else {
                    $errors[] = 'Cannot connect to MySQL to drop database';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Exception dropping database: ' . $e->getMessage();
        }

        // --- Step 2: Remove tenant config from database.php files ---
        if (!empty($tenantIdentifier)) {
            $this->removeTenantFromConfig($this->mainDbConfigPath, $tenantIdentifier, 'Main app', $errors);
            $this->removeTenantFromConfig($this->externalDbConfigPath, $tenantIdentifier, 'External app', $errors);
        }

        // --- Step 3: Delete uploaded_files folders ---
        if (!empty($tenantIdentifier)) {
            $bizadminRoot = realpath(FCPATH . '/../');

            // Main uploaded_files/{tenant}/
            $mainFolder = $bizadminRoot . '/uploaded_files/' . $tenantIdentifier;
            if (is_dir($mainFolder)) {
                $this->deleteDirectory($mainFolder);
                log_message('info', '[Delete ORZ] Deleted folder: ' . $mainFolder);
            }

            // External/uploaded_files/{tenant}/
            $extFolder = $bizadminRoot . '/External/uploaded_files/' . $tenantIdentifier;
            if (is_dir($extFolder)) {
                $this->deleteDirectory($extFolder);
                log_message('info', '[Delete ORZ] Deleted folder: ' . $extFolder);
            }
        }

        // --- Step 4: Hard-delete the organization_list record ---
        $this->db->where('organization_list_id', $orzId);
        $this->db->delete('organization_list');
        log_message('info', '[Delete ORZ] Deleted org ID ' . $orzId . ' (' . $tenantIdentifier . ') from organization_list');

        if (empty($errors)) {
            return [
                'success' => true,
                'message' => 'Organization "' . htmlspecialchars($org->orz_name) . '" and all its data have been permanently deleted.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Organization deleted with some issues: ' . implode(' | ', $errors)
            ];
        }
    }

    /**
     * Remove a tenant's config block from a database.php file
     */
    private function removeTenantFromConfig($filePath, $tenantIdentifier, $label, &$errors) {
        if (!file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $marker  = "\$db['{$tenantIdentifier}']";

        if (strpos($content, $marker) === false) {
            log_message('info', "[Delete ORZ] {$label}: No config entry for '{$tenantIdentifier}' (nothing to remove)");
            return;
        }

        // Match the entire $db['tenant'] = array( ... ); block
        $escapedId = preg_quote($tenantIdentifier, '/');
        $pattern   = '/\n?\$db\[\'' . $escapedId . '\'\]\s*=\s*array\s*\([^;]+;\s*/s';

        $newContent = preg_replace($pattern, "\n", $content, 1);
        if ($newContent !== null && $newContent !== $content) {
            file_put_contents($filePath, $newContent);
            log_message('info', "[Delete ORZ] {$label}: Removed config for '{$tenantIdentifier}'");
        } else {
            $errors[] = "{$label}: Could not remove config entry (may need manual cleanup)";
            log_message('error', "[Delete ORZ] {$label}: Failed to remove config for '{$tenantIdentifier}'");
        }
    }

    /**
     * Recursively delete a directory and all its contents
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
