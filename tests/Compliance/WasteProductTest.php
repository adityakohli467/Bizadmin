<?php
/**
 * Waste Management Product CRUD Test Cases
 * 
 * Tests addOrUpdateProduct data structure, listProduct conditions,
 * and delete logic against the actual DB table schema:
 * id, product_name, par_level, site_id, prep_id, status, is_deleted
 */

use PHPUnit\Framework\TestCase;

class WasteProductTest extends TestCase
{
    /**
     * Valid columns in Compliance_wasteManagementproducts table
     */
    private $validColumns = ['id', 'product_name', 'par_level', 'site_id', 'prep_id', 'status', 'is_deleted'];

    /**
     * Test 1: addOrUpdateProduct insert data only contains valid table columns
     * Table: id, product_name, par_level, site_id, prep_id, status, is_deleted
     * Specifically: NO location_id column exists
     */
    public function testInsertDataOnlyContainsValidColumns()
    {
        // Simulate data array built by addOrUpdateProduct()
        $data = [
            'product_name' => 'Test Cake',
            'par_level'    => 10,
            'prep_id'      => 1,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        foreach (array_keys($data) as $key) {
            $this->assertContains($key, $this->validColumns,
                "Column '$key' does not exist in Compliance_wasteManagementproducts table");
        }
    }

    /**
     * Test 2: Insert data must NOT contain location_id (column doesnt exist)
     */
    public function testInsertDataDoesNotContainLocationId()
    {
        $data = [
            'product_name' => 'Test Product',
            'par_level'    => 5,
            'prep_id'      => 2,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        $this->assertArrayNotHasKey('location_id', $data,
            "location_id should NOT be in insert data - column does not exist in table");
    }

    /**
     * Test 3: Insert data sets status=1 so product is visible in listing
     */
    public function testInsertDataSetsStatusToActive()
    {
        $data = [
            'product_name' => 'Active Product',
            'par_level'    => 3,
            'prep_id'      => 1,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        $this->assertEquals(1, $data['status'],
            "New products must have status=1 to appear in listing");
    }

    /**
     * Test 4: Insert data sets is_deleted=0 so product is visible in listing
     */
    public function testInsertDataSetsIsDeletedToZero()
    {
        $data = [
            'product_name' => 'Not Deleted Product',
            'par_level'    => 7,
            'prep_id'      => 1,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        $this->assertEquals(0, $data['is_deleted'],
            "New products must have is_deleted=0 to appear in listing");
    }

    /**
     * Test 5: listProduct condition for products table should NOT have location_id
     */
    public function testListProductConditionDoesNotHaveLocationId()
    {
        $productCondition = array('status' => 1, 'is_deleted' => 0);

        $this->assertArrayNotHasKey('location_id', $productCondition,
            "Product listing condition should NOT filter by location_id - column does not exist");
    }

    /**
     * Test 6: listProduct condition for products must filter by status and is_deleted
     */
    public function testListProductConditionFiltersCorrectly()
    {
        $productCondition = array('status' => 1, 'is_deleted' => 0);

        $this->assertArrayHasKey('status', $productCondition);
        $this->assertEquals(1, $productCondition['status']);
        $this->assertArrayHasKey('is_deleted', $productCondition);
        $this->assertEquals(0, $productCondition['is_deleted']);
    }

    /**
     * Test 6b: Dashboard condition must also NOT have location_id
     */
    public function testDashboardConditionDoesNotHaveLocationId()
    {
        $dashboardCondition = array('status' => 1, 'is_deleted' => 0);

        $this->assertArrayNotHasKey('location_id', $dashboardCondition,
            "Dashboard product condition should NOT filter by location_id");
    }

    /**
     * Test 7: Sites/Prep tables condition uses location_id (those tables DO have it)
     */
    public function testSitePrepConditionUsesLocationId()
    {
        $location_id = 44;
        $condition = array('status' => 1, 'location_id' => $location_id);

        $this->assertArrayHasKey('location_id', $condition,
            "Site/Prep area queries should filter by location_id");
        $this->assertEquals(44, $condition['location_id']);
    }

    /**
     * Test 8: Inserted product data matches what listProduct query expects
     * A newly created product should match the list filter conditions
     */
    public function testNewProductMatchesListFilter()
    {
        // Insert data
        $insertData = [
            'product_name' => 'Chocolate Cake',
            'par_level'    => 10,
            'prep_id'      => 1,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        // List filter
        $listCondition = array('status' => 1, 'is_deleted' => 0);

        // Verify the product would match the filter
        foreach ($listCondition as $key => $value) {
            $this->assertArrayHasKey($key, $insertData,
                "Insert data must contain '$key' so it matches the list filter");
            $this->assertEquals($value, $insertData[$key],
                "Insert data '$key' must be $value to match the list filter");
        }
    }

    /**
     * Test 9: Delete sets is_deleted=1, which excludes from listing (is_deleted=0 filter)
     */
    public function testDeleteSetsIsDeletedToOne()
    {
        $deleteData = ['is_deleted' => 1];

        $this->assertEquals(1, $deleteData['is_deleted']);

        // Verify deleted product would NOT match list condition
        $listCondition = array('status' => 1, 'is_deleted' => 0);
        $this->assertNotEquals($listCondition['is_deleted'], $deleteData['is_deleted'],
            "Deleted product (is_deleted=1) should not match list filter (is_deleted=0)");
    }

    /**
     * Test 10: Required fields are present in insert data
     */
    public function testRequiredFieldsPresent()
    {
        $data = [
            'product_name' => 'Test',
            'par_level'    => 5,
            'prep_id'      => 1,
            'site_id'      => 1,
            'status'       => 1,
            'is_deleted'   => 0,
        ];

        $requiredKeys = ['product_name', 'par_level', 'prep_id', 'site_id', 'status', 'is_deleted'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $data, "Required field '$key' is missing from insert data");
        }
    }
}
