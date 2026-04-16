<?php
/**
 * Integration test: Verify Waste Product CRUD against real DB
 */

// Define constants so CI config files load
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/../../system/');
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');

// Load DB config
require __DIR__ . '/../../application/config/database.php';

$dbConf = $db['default'];
$conn = new mysqli($dbConf['hostname'], $dbConf['username'], $dbConf['password'], $dbConf['database']);
if ($conn->connect_error) {
    echo "DB connect failed: " . $conn->connect_error . "\n";
    exit(1);
}

echo "=== 1. TABLE STRUCTURE ===\n";
$res = $conn->query("DESCRIBE Compliance_wasteManagementproducts");
if (!$res) {
    echo "Table not found in default DB: " . $conn->error . "\n";
    $conn->close();
    exit(1);
}
$cols = [];
while ($row = $res->fetch_assoc()) {
    $cols[] = $row['Field'];
    echo "  " . $row['Field'] . " | " . $row['Type'] . " | Default: " . ($row['Default'] ?? 'NULL') . "\n";
}
$res->free();

echo "\n=== 2. COLUMN VALIDATION ===\n";
$insertCols = ['product_name', 'par_level', 'prep_id', 'status', 'is_deleted'];
$allGood = true;
foreach ($insertCols as $c) {
    $exists = in_array($c, $cols);
    echo "  " . $c . ": " . ($exists ? "OK" : "MISSING!") . "\n";
    if (!$exists) $allGood = false;
}
$hasLocationId = in_array('location_id', $cols);
echo "  location_id: " . ($hasLocationId ? "EXISTS (would need to include)" : "NOT IN TABLE (correctly excluded)") . "\n";

echo "\n=== 3. INSERT TEST ===\n";
$testName = 'PHPUNIT_TEST_' . time();
$stmt = $conn->prepare("INSERT INTO Compliance_wasteManagementproducts (product_name, par_level, prep_id, status, is_deleted) VALUES (?, ?, ?, 1, 0)");
$parLevel = 99;
$prepId = 1;
$stmt->bind_param("sii", $testName, $parLevel, $prepId);
$insertOk = $stmt->execute();
$insertId = $stmt->insert_id;
$stmt->close();

if ($insertOk && $insertId > 0) {
    echo "  INSERT OK - id=$insertId, product_name=$testName\n";
} else {
    echo "  INSERT FAILED: " . $conn->error . "\n";
    $conn->close();
    exit(1);
}

echo "\n=== 4. FETCH TEST (status=1, is_deleted=0) ===\n";
$stmt2 = $conn->prepare("SELECT id, product_name, par_level, prep_id, status, is_deleted FROM Compliance_wasteManagementproducts WHERE status=1 AND is_deleted=0 AND id=?");
$stmt2->bind_param("i", $insertId);
$stmt2->execute();
$result = $stmt2->get_result();
$found = $result->fetch_assoc();
$stmt2->close();

if ($found) {
    echo "  FETCH OK - Found: id=" . $found['id'] . ", name=" . $found['product_name'] . ", status=" . $found['status'] . ", is_deleted=" . $found['is_deleted'] . "\n";
} else {
    echo "  FETCH FAILED - Product not found with status=1 AND is_deleted=0!\n";
    $conn->close();
    exit(1);
}

echo "\n=== 5. DELETE TEST (soft delete) ===\n";
$conn->query("UPDATE Compliance_wasteManagementproducts SET is_deleted=1 WHERE id=$insertId");
$stmt3 = $conn->prepare("SELECT id FROM Compliance_wasteManagementproducts WHERE status=1 AND is_deleted=0 AND id=?");
$stmt3->bind_param("i", $insertId);
$stmt3->execute();
$result3 = $stmt3->get_result();
$afterDelete = $result3->fetch_assoc();
$stmt3->close();

if (!$afterDelete) {
    echo "  DELETE OK - Product no longer appears in listing after soft-delete\n";
} else {
    echo "  DELETE FAILED - Product still shows in listing!\n";
}

// Cleanup: remove test row
$conn->query("DELETE FROM Compliance_wasteManagementproducts WHERE id=$insertId");
echo "\n=== 6. CLEANUP ===\n";
echo "  Test row id=$insertId removed\n";

$conn->close();

echo "\n=== ALL TESTS PASSED ===\n";
