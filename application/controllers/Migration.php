<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Database Migration Runner
 * 
 * Runs SQL migration files across ALL tenant databases.
 * Tracks which migrations have been applied per tenant via `schema_migrations` table.
 * 
 * USAGE:
 *   - Via browser: /migration/run?key=YOUR_SECRET_KEY
 *   - Via CLI: php index.php migration run
 *   - Dry run (preview only): /migration/run?key=YOUR_SECRET_KEY&dry=1
 *   - Run for single tenant: /migration/run?key=YOUR_SECRET_KEY&tenant=cjs
 *   - Check status: /migration/status?key=YOUR_SECRET_KEY
 * 
 * HOW TO ADD A NEW MIGRATION:
 *   1. Create a new .sql file in application/migrations/
 *   2. Name it with incrementing number: 003_description.sql, 004_another_change.sql
 *   3. Write your SQL (ALTER TABLE, CREATE TABLE, etc.)
 *   4. Run /migration/run?key=YOUR_SECRET_KEY
 *   5. Done - it applies to ALL tenant databases automatically
 */
class Migration extends MY_Controller {

    private $migrationsPath;
    private $log = [];
    
    // Change this secret key to something only you know
    private $secretKey = 'BizAdmin@Migrate2026!';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->migrationsPath = APPPATH . 'migrations/';
    }

    /**
     * Run all pending migrations across all tenant databases
     */
    public function run() {
        // Security: Only allow from CLI or authenticated super admin
        if (!$this->is_authorized()) {
            show_error('Unauthorized. Access this from CLI or as super admin.', 403);
            return;
        }

        $dryRun = $this->input->get('dry') == '1';
        $specificTenant = $this->input->get('tenant');

        // Get all migration files sorted
        $migrations = $this->getMigrationFiles();

        if (empty($migrations)) {
            $this->output_log('No migration files found.');
            return;
        }

        // Get all tenant databases
        $tenants = $this->getTenants($specificTenant);

        if (empty($tenants)) {
            $this->output_log('No tenants found.');
            return;
        }

        $this->output_log("Found " . count($migrations) . " migration file(s).");
        $this->output_log("Found " . count($tenants) . " tenant database(s).");
        $this->output_log($dryRun ? "=== DRY RUN MODE (no changes will be made) ===" : "=== RUNNING MIGRATIONS ===");
        $this->output_log(str_repeat('-', 60));

        $totalSuccess = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        foreach ($tenants as $tenant) {
            $dbName = $tenant['db_name'];
            $tenantId = $tenant['tenant_identifier'];

            $this->output_log("\n[Tenant: {$tenantId}] Database: {$dbName}");

            // Connect to tenant DB
            $tenantDb = $this->connectTenantDb($tenant);
            if (!$tenantDb) {
                $this->output_log("  ERROR: Could not connect to {$dbName}. Skipping.");
                $totalFailed++;
                continue;
            }

            // Ensure schema_migrations table exists
            $this->ensureMigrationsTable($tenantDb);

            // Get already-run migrations for this tenant
            $executed = $this->getExecutedMigrations($tenantDb);

            // Determine next batch number
            $nextBatch = $this->getNextBatch($tenantDb);

            $pendingCount = 0;
            foreach ($migrations as $filename) {
                if (in_array($filename, $executed)) {
                    continue; // Already run
                }

                $pendingCount++;
                $filePath = $this->migrationsPath . $filename;
                $sql = file_get_contents($filePath);

                if ($dryRun) {
                    $this->output_log("  [PENDING] {$filename}");
                } else {
                    // Execute the migration
                    $success = $this->executeMigration($tenantDb, $sql, $filename);

                    if ($success) {
                        // Record it
                        $tenantDb->insert('schema_migrations', [
                            'migration' => $filename,
                            'batch' => $nextBatch,
                            'executed_at' => date('Y-m-d H:i:s')
                        ]);
                        $this->output_log("  [OK] {$filename}");
                        $totalSuccess++;
                    } else {
                        $error = $tenantDb->error();
                        $this->output_log("  [FAILED] {$filename} - " . ($error['message'] ?? 'Unknown error'));
                        $totalFailed++;
                    }
                }
            }

            if ($pendingCount === 0) {
                $this->output_log("  Up to date. No pending migrations.");
                $totalSkipped++;
            }

            $tenantDb->close();
        }

        $this->output_log(str_repeat('-', 60));
        $this->output_log("DONE. Success: {$totalSuccess} | Skipped: {$totalSkipped} | Failed: {$totalFailed}");
        $this->output_result();
    }

    /**
     * Show migration status for all tenants
     */
    public function status() {
        if (!$this->is_authorized()) {
            show_error('Unauthorized.', 403);
            return;
        }

        $specificTenant = $this->input->get('tenant');
        $migrations = $this->getMigrationFiles();
        $tenants = $this->getTenants($specificTenant);

        $this->output_log("=== MIGRATION STATUS ===");
        $this->output_log("Total migration files: " . count($migrations));
        $this->output_log(str_repeat('-', 60));

        foreach ($tenants as $tenant) {
            $tenantDb = $this->connectTenantDb($tenant);
            if (!$tenantDb) {
                $this->output_log("[{$tenant['tenant_identifier']}] Could not connect.");
                continue;
            }

            $this->ensureMigrationsTable($tenantDb);
            $executed = $this->getExecutedMigrations($tenantDb);
            $pending = array_diff($migrations, $executed);

            $statusText = empty($pending) ? 'UP TO DATE' : count($pending) . ' PENDING';
            $this->output_log("[{$tenant['tenant_identifier']}] {$statusText}");

            if (!empty($pending)) {
                foreach ($pending as $p) {
                    $this->output_log("  - {$p}");
                }
            }

            $tenantDb->close();
        }

        $this->output_result();
    }

    /**
     * Show list of available migration files
     */
    public function files() {
        if (!$this->is_authorized()) {
            show_error('Unauthorized.', 403);
            return;
        }

        $migrations = $this->getMigrationFiles();
        $this->output_log("=== MIGRATION FILES ===");
        foreach ($migrations as $i => $file) {
            $this->output_log(($i + 1) . ". {$file}");
        }
        $this->output_result();
    }

    // =========================================
    // PRIVATE HELPERS
    // =========================================

    /**
     * Get sorted list of .sql migration files
     */
    private function getMigrationFiles() {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '*.sql');
        $filenames = array_map('basename', $files);
        sort($filenames); // Ensures numeric order: 001_, 002_, 003_...
        return $filenames;
    }

    /**
     * Fetch all active tenants from organization_list
     */
    private function getTenants($specificTenant = null) {
        $this->db->select('tenant_identifier, db_host, db_username, db_pass, db_name');
        $this->db->from('organization_list');
        $this->db->where('organization_list_status', 1); // Only active orgs

        if ($specificTenant) {
            $this->db->where('tenant_identifier', $specificTenant);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Connect to a tenant database dynamically
     */
    private function connectTenantDb($tenant) {
        $config = [
            'dsn'      => '',
            'hostname' => $tenant['db_host'] ?: 'localhost',
            'username' => $tenant['db_username'],
            'password' => $tenant['db_pass'],
            'database' => $tenant['db_name'],
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => FALSE, // Don't throw errors, we handle them
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8mb4',
            'dbcollat' => 'utf8mb4_general_ci',
            'swap_pre' => '',
            'encrypt'  => FALSE,
            'compress' => FALSE,
            'stricton' => FALSE,
            'failover' => [],
            'save_queries' => FALSE
        ];

        $db = $this->load->database($config, TRUE);

        // Verify connection
        if (!$db->conn_id) {
            return false;
        }

        return $db;
    }

    /**
     * Create schema_migrations table if it doesn't exist
     */
    private function ensureMigrationsTable($tenantDb) {
        $sql = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT(11) NOT NULL,
            `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_migration` (`migration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $tenantDb->query($sql);
    }

    /**
     * Get list of already-executed migration filenames
     */
    private function getExecutedMigrations($tenantDb) {
        $result = $tenantDb->select('migration')
            ->get('schema_migrations')
            ->result_array();

        return array_column($result, 'migration');
    }

    /**
     * Get next batch number
     */
    private function getNextBatch($tenantDb) {
        $result = $tenantDb->select_max('batch')
            ->get('schema_migrations')
            ->row_array();

        return ($result['batch'] ?? 0) + 1;
    }

    /**
     * Execute a SQL migration (supports multiple statements)
     */
    private function executeMigration($tenantDb, $sql, $filename) {
        // Remove comments and split by semicolons for multi-statement support
        $statements = $this->splitSqlStatements($sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || $statement === 'SELECT 1') {
                continue;
            }

            $result = $tenantDb->query($statement);
            if ($result === FALSE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Split SQL into individual statements (handles semicolons properly)
     */
    private function splitSqlStatements($sql) {
        // Remove single-line comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        // Remove multi-line comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Split by semicolons
        $statements = explode(';', $sql);

        return array_filter(array_map('trim', $statements), function($s) {
            return !empty($s);
        });
    }

    /**
     * Authorization check - CLI or secret key in URL
     */
    private function is_authorized() {
        // Always allow CLI access
        if ($this->input->is_cli_request()) {
            return true;
        }

        // Check secret key in query string
        $key = $this->input->get('key');
        if ($key && $key === $this->secretKey) {
            return true;
        }

        return false;
    }

    /**
     * Add message to output log
     */
    private function output_log($message) {
        $this->log[] = $message;
    }

    /**
     * Render final output (CLI-friendly or HTML)
     */
    private function output_result() {
        if ($this->input->is_cli_request()) {
            echo implode("\n", $this->log) . "\n";
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo '<pre style="font-family:monospace; background:#1a1a2e; color:#16c79a; padding:20px; border-radius:8px; font-size:14px; line-height:1.6; max-width:900px; margin:40px auto; overflow-x:auto;">';
            echo htmlspecialchars(implode("\n", $this->log), ENT_QUOTES, 'UTF-8');
            echo '</pre>';
        }
    }
}
