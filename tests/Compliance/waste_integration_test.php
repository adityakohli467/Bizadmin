<?php
/**
 * Integration test: Verify Waste Management CRUD against real DB
 * 
 * Tables tested:
 *   Compliance_wasteManagementproducts: id, product_name, par_level, site_id, prep_id, status, is_deleted
 *   Compliance_wasteManagement_history: id, product_id, prep_id, wasteM_value, entered_by, date_entered, location_id
 */

if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/../../system/');
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
require __DIR__ . '/../../application/config/database.php';

// --- Connect to a tenant DB that has the waste tables ---
$conn = null;
$targetDb = null;

// Try default DB first
$dbConf = $db['default'];
$testConn = @new mysqli($dbConf['hostname'], $dbConf['username'], $dbConf['password'], $dbConf['database']);
if (!$testConn->connect_error) {
    $res = $testConn->query("SHOW TABLES LIKE 'Compliance_wasteManagement_history'");
    if ($res && $res->num_rows > 0) {
        $conn = $testConn;
        $targetDb = $dbConf['database'];
    } else {
        $testConn->close();
    }
}

// Search tenant DBs
if (!$conn) {
    foreach ($db as $key => $conf) {
        if ($key === 'default') continue;
        $testConn = @new mysqli($conf['hostname'], $conf['username'], $conf['password'], $conf['database']);
        if ($testConn->connect_error) continue;
        $res = $testConn->query("SHOW TABLES LIKE 'Compliance_wasteManagement_history'");
        if ($res && $res->num_rows > 0) {
            $conn = $testConn;
            $targetDb = $conf['database'];
            break;
        }
        $testConn->close();
    }
}

if (!$conn) {
    echo "ERROR: Could not find Compliance_wasteManagement_history in any DB\n";
    exit(1);
}
echo "Connected to DB: $targetDb\n";

$passed = 0;
$failed = 0;

function test($name, $ok, $detail = '') {
    global $passed, $failed;
    if ($ok) {
        echo "  PASS: $name\n";
        $passed++;
    } else {
        echo "  FAIL: $name" . ($detail ? " -- $detail" : "") . "\n";
        $failed++;
    }
}

// --- Get actual table columns ---
$productCols = [];
$res = $conn->query("DESCRIBE Compliance_wasteManagementproducts");
while ($row = $res->fetch_assoc()) $productCols[] = $row['Field'];
$res->free();

$historyCols = [];
$res = $conn->query("DESCRIBE Compliance_wasteManagement_history");
while ($row = $res->fetch_assoc()) $historyCols[] = $row['Field'];
$res->free();

echo "\nProduct columns: " . implode(', ', $productCols) . "\n";
echo "History columns: " . implode(', ', $historyCols) . "\n";

// ===================== PRODUCT TESTS =====================

echo "\n====== PRODUCT TABLE TESTS ======\n";

echo "\n--- TEST 1: Products table has NO location_id ---\n";
test("Products table: no location_id column", !in_array('location_id', $productCols));

echo "\n--- TEST 2: Products table has site_id ---\n";
test("Products table: has site_id column", in_array('site_id', $productCols));

echo "\n--- TEST 3: Create product with correct columns ---\n";
$testProductName = 'TEST_PRODUCT_' . time();
$stmt = $conn->prepare("INSERT INTO Compliance_wasteManagementproducts (product_name, par_level, site_id, prep_id, status, is_deleted) VALUES (?, ?, ?, ?, 1, 0)");
$parLevel = 50; $siteId = 1; $prepId = 1;
$stmt->bind_param("siii", $testProductName, $parLevel, $siteId, $prepId);
$insertOk = $stmt->execute();
$productId = $stmt->insert_id;
$stmt->close();
test("Product INSERT succeeds (id=$productId)", $insertOk && $productId > 0, $conn->error);

echo "\n--- TEST 4: Dashboard query finds product (status=1, is_deleted=0, NO location_id) ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagementproducts WHERE status=1 AND is_deleted=0 AND id=?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$found = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("Dashboard product query works (no location_id filter)", !empty($found));

echo "\n--- TEST 5: History page product query (status=1, is_deleted=0, NO location_id) ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagementproducts WHERE status=1 AND is_deleted=0 AND id=?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$historyProduct = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("History page product query works (no location_id filter)", !empty($historyProduct));

echo "\n--- TEST 6: Product has correct site_id and prep_id for view filters ---\n";
$viewMatch = ($found && $found['site_id'] == $siteId && $found['prep_id'] == $prepId);
test("Product has site_id=$siteId and prep_id=$prepId for view filtering", $viewMatch);

// ===================== HISTORY TABLE TESTS =====================

echo "\n====== HISTORY TABLE TESTS ======\n";

echo "\n--- TEST 7: History table HAS location_id ---\n";
test("History table: has location_id column", in_array('location_id', $historyCols));

echo "\n--- TEST 8: History table has required columns ---\n";
$requiredHistCols = ['id', 'product_id', 'prep_id', 'wasteM_value', 'entered_by', 'date_entered', 'location_id'];
$allPresent = true;
foreach ($requiredHistCols as $c) {
    if (!in_array($c, $historyCols)) { $allPresent = false; echo "  Missing: $c\n"; }
}
test("All required history columns exist", $allPresent);

// ===================== DASHBOARD SAVE TESTS =====================

echo "\n====== DASHBOARD SAVE (saveDashboardData) TESTS ======\n";

$today = date('Y-m-d');
$locationId = 44;

echo "\n--- TEST 9: Save wasteM_value from dashboard (INSERT new record) ---\n";
$stmt = $conn->prepare("INSERT INTO Compliance_wasteManagement_history (product_id, prep_id, wasteM_value, date_entered, location_id) VALUES (?, ?, ?, ?, ?)");
$wasteVal = '15.5';
$stmt->bind_param("iissi", $productId, $prepId, $wasteVal, $today, $locationId);
$insertOk = $stmt->execute();
$historyId = $stmt->insert_id;
$stmt->close();
test("Dashboard save INSERT succeeds (id=$historyId)", $insertOk && $historyId > 0, $conn->error);

echo "\n--- TEST 10: Verify saved record has correct prep_id (not site_id) ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE id=?");
$stmt->bind_param("i", $historyId);
$stmt->execute();
$saved = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("Saved prep_id matches product's prep_id ($prepId)", $saved && $saved['prep_id'] == $prepId);
test("Saved wasteM_value is correct ('$wasteVal')", $saved && $saved['wasteM_value'] === $wasteVal);
test("Saved product_id is correct ($productId)", $saved && $saved['product_id'] == $productId);

echo "\n--- TEST 11: Dashboard save UPDATE existing record ---\n";
$newWasteVal = '20.0';
$stmt = $conn->prepare("UPDATE Compliance_wasteManagement_history SET wasteM_value=? WHERE product_id=? AND date_entered=?");
$stmt->bind_param("sis", $newWasteVal, $productId, $today);
$updateOk = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
test("Dashboard save UPDATE succeeds", $updateOk && $affected > 0);

echo "\n--- TEST 12: Verify updated value ---\n";
$stmt = $conn->prepare("SELECT wasteM_value FROM Compliance_wasteManagement_history WHERE id=?");
$stmt->bind_param("i", $historyId);
$stmt->execute();
$updated = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("Updated wasteM_value is '$newWasteVal'", $updated && $updated['wasteM_value'] === $newWasteVal);

echo "\n--- TEST 13: Save entered_by field ---\n";
$enteredBy = 'John Doe';
$stmt = $conn->prepare("UPDATE Compliance_wasteManagement_history SET entered_by=? WHERE id=?");
$stmt->bind_param("si", $enteredBy, $historyId);
$updateOk = $stmt->execute();
$stmt->close();
$stmt = $conn->prepare("SELECT entered_by FROM Compliance_wasteManagement_history WHERE id=?");
$stmt->bind_param("i", $historyId);
$stmt->execute();
$enteredRecord = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("entered_by saved and retrieved correctly", $enteredRecord && $enteredRecord['entered_by'] === $enteredBy);

// ===================== HISTORY PAGE FETCH TESTS =====================

echo "\n====== HISTORY PAGE FETCH (historyData) TESTS ======\n";

echo "\n--- TEST 13b: History table has items_wasted column ---\n";
test("History table: has items_wasted column", in_array('items_wasted', $historyCols));

echo "\n--- TEST 13c: Save items_wasted from dashboard ---\n";
$itemsWasted = '5';
$conn->query("UPDATE Compliance_wasteManagement_history SET items_wasted='$itemsWasted' WHERE id=$historyId");
$stmt = $conn->prepare("SELECT items_wasted FROM Compliance_wasteManagement_history WHERE id=?");
$stmt->bind_param("i", $historyId);
$stmt->execute();
$itemsRecord = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("items_wasted saved and retrieved correctly ('$itemsWasted')", $itemsRecord && $itemsRecord['items_wasted'] === $itemsWasted);

echo "\n--- TEST 13d: Update items_wasted from history page ---\n";
$newItemsWasted = '12';
$conn->query("UPDATE Compliance_wasteManagement_history SET items_wasted='$newItemsWasted' WHERE product_id=$productId AND date_entered='$today' AND prep_id=$prepId AND location_id=$locationId");
$stmt = $conn->prepare("SELECT items_wasted FROM Compliance_wasteManagement_history WHERE product_id=? AND date_entered=? AND prep_id=? AND location_id=?");
$stmt->bind_param("isii", $productId, $today, $prepId, $locationId);
$stmt->execute();
$updatedItems = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("items_wasted updated from history page ('$newItemsWasted')", $updatedItems && $updatedItems['items_wasted'] === $newItemsWasted);

echo "\n--- TEST 13e: Insert new history record with items_wasted ---\n";
$stmt = $conn->prepare("INSERT INTO Compliance_wasteManagement_history (product_id, prep_id, wasteM_value, entered_by, items_wasted, date_entered, location_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$twoDaysAgo = date('Y-m-d', strtotime('-2 days'));
$testVal = '7.0'; $testBy = 'Test'; $testItems = '3';
$stmt->bind_param("iissssi", $productId, $prepId, $testVal, $testBy, $testItems, $twoDaysAgo, $locationId);
$insertOk = $stmt->execute();
$historyId3 = $stmt->insert_id;
$stmt->close();
test("INSERT with items_wasted succeeds (id=$historyId3)", $insertOk && $historyId3 > 0, $conn->error);

echo "\n--- TEST 13f: Fetch record includes items_wasted ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE id=?");
$stmt->bind_param("i", $historyId3);
$stmt->execute();
$fullRecord = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("Fetched record has items_wasted='$testItems'", $fullRecord && $fullRecord['items_wasted'] === $testItems);

echo "\n--- TEST 14: History fetches by date range + location_id ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE date_entered >= ? AND date_entered <= ? AND location_id = ?");
$stmt->bind_param("ssi", $today, $today, $locationId);
$stmt->execute();
$result = $stmt->get_result();
$historyRows = [];
while ($row = $result->fetch_assoc()) $historyRows[] = $row;
$stmt->close();
$foundInHistory = false;
foreach ($historyRows as $row) {
    if ($row['product_id'] == $productId) { $foundInHistory = true; break; }
}
test("History date range query finds test record", $foundInHistory);

echo "\n--- TEST 15: History fetch with prep_id filter ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE date_entered >= ? AND date_entered <= ? AND location_id = ? AND prep_id = ?");
$stmt->bind_param("ssii", $today, $today, $locationId, $prepId);
$stmt->execute();
$result = $stmt->get_result();
$filteredRows = [];
while ($row = $result->fetch_assoc()) $filteredRows[] = $row;
$stmt->close();
$foundFiltered = false;
foreach ($filteredRows as $row) {
    if ($row['product_id'] == $productId) { $foundFiltered = true; break; }
}
test("History prep_id filter finds test record (prep_id=$prepId)", $foundFiltered);

// ===================== HISTORY PAGE SAVE (updateWasteHistory) TESTS =====================

echo "\n====== HISTORY PAGE SAVE (updateWasteHistory) TESTS ======\n";

echo "\n--- TEST 16: Update existing history record (wasteM_value) ---\n";
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE product_id=? AND date_entered=? AND prep_id=? AND location_id=?");
$stmt->bind_param("isii", $productId, $today, $prepId, $locationId);
$stmt->execute();
$existsForUpdate = $stmt->get_result()->fetch_assoc();
$stmt->close();

$historyUpdateVal = '35.0';
if ($existsForUpdate) {
    $conn->query("UPDATE Compliance_wasteManagement_history SET wasteM_value='$historyUpdateVal' WHERE product_id=$productId AND date_entered='$today' AND prep_id=$prepId AND location_id=$locationId");
    $stmt = $conn->prepare("SELECT wasteM_value FROM Compliance_wasteManagement_history WHERE product_id=? AND date_entered=? AND prep_id=? AND location_id=?");
    $stmt->bind_param("isii", $productId, $today, $prepId, $locationId);
    $stmt->execute();
    $afterUpdate = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    test("updateWasteHistory UPDATE works", $afterUpdate && $afterUpdate['wasteM_value'] === $historyUpdateVal);
} else {
    test("updateWasteHistory UPDATE works", false, "Record not found for update condition");
}

echo "\n--- TEST 17: Insert new history record for different date ---\n";
$yesterday = date('Y-m-d', strtotime('-1 day'));
$stmt = $conn->prepare("INSERT INTO Compliance_wasteManagement_history (product_id, prep_id, wasteM_value, entered_by, date_entered, location_id) VALUES (?, ?, ?, ?, ?, ?)");
$newVal = '12.0';
$newEnteredBy = 'Jane';
$stmt->bind_param("iisssi", $productId, $prepId, $newVal, $newEnteredBy, $yesterday, $locationId);
$insertOk = $stmt->execute();
$historyId2 = $stmt->insert_id;
$stmt->close();
test("Insert history for different date succeeds (id=$historyId2)", $insertOk && $historyId2 > 0, $conn->error);

echo "\n--- TEST 18: Date range query returns multiple dates ---\n";
$twoDaysAgoForRange = date('Y-m-d', strtotime('-2 days'));
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagement_history WHERE date_entered >= ? AND date_entered <= ? AND location_id = ? AND product_id = ?");
$stmt->bind_param("ssii", $twoDaysAgoForRange, $today, $locationId, $productId);
$stmt->execute();
$result = $stmt->get_result();
$multiDayRows = [];
while ($row = $result->fetch_assoc()) $multiDayRows[] = $row;
$stmt->close();
test("Date range query returns both days (" . count($multiDayRows) . " rows)", count($multiDayRows) >= 2);

// ===================== SOFT DELETE TESTS =====================

echo "\n====== SOFT DELETE TESTS ======\n";

echo "\n--- TEST 19: Soft delete hides product from all views ---\n";
$conn->query("UPDATE Compliance_wasteManagementproducts SET is_deleted=1 WHERE id=$productId");
$stmt = $conn->prepare("SELECT * FROM Compliance_wasteManagementproducts WHERE status=1 AND is_deleted=0 AND id=?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$afterDelete = $stmt->get_result()->fetch_assoc();
$stmt->close();
test("Soft-deleted product hidden from dashboard and listing", empty($afterDelete));

// ===================== CLEANUP =====================

echo "\n====== CLEANUP ======\n";
$conn->query("DELETE FROM Compliance_wasteManagement_history WHERE product_id=$productId AND location_id=$locationId");
$conn->query("DELETE FROM Compliance_wasteManagementproducts WHERE id=$productId");
echo "  Removed test product id=$productId and all history records\n";

$conn->close();

echo "\n========================================\n";
$total = $passed + $failed;
echo "Results: $passed passed, $failed failed out of $total tests\n";
echo "========================================\n";
exit($failed > 0 ? 1 : 0);
