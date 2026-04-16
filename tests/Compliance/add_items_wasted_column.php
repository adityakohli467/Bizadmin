<?php
// Add items_wasted column to all tenant DBs
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/../../system/');
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
require __DIR__ . '/../../application/config/database.php';

foreach ($db as $key => $conf) {
    if ($key === 'default') continue;
    $c = @new mysqli($conf['hostname'], $conf['username'], $conf['password'], $conf['database']);
    if ($c->connect_error) continue;
    $r = $c->query("SHOW TABLES LIKE 'Compliance_wasteManagement_history'");
    if ($r && $r->num_rows > 0) {
        $cols = $c->query("SHOW COLUMNS FROM Compliance_wasteManagement_history LIKE 'items_wasted'");
        if ($cols->num_rows == 0) {
            $ok = $c->query("ALTER TABLE Compliance_wasteManagement_history ADD `items_wasted` VARCHAR(255) DEFAULT NULL AFTER `entered_by`");
            echo $conf['database'] . ': ' . ($ok ? 'Column added' : 'FAILED: ' . $c->error) . "\n";
        } else {
            echo $conf['database'] . ': Column already exists' . "\n";
        }
    }
    $c->close();
}
echo "Done.\n";
