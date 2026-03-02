-- =====================================================
-- Indexes for Supplier Internal Order tables
-- Run on each tenant database to optimize data fetching
-- =====================================================

-- SUPPLIERS_internalOrderProducts
-- Used by fetchProducts() to filter active products and sort
ALTER TABLE `SUPPLIERS_internalOrderProducts` 
  ADD INDEX `idx_products_location_deleted` (`location_id`, `is_deleted`),
  ADD INDEX `idx_products_sort_order` (`sort_order`),
  ADD INDEX `idx_products_name_location` (`name`(100), `location_id`, `is_deleted`),
  ADD INDEX `idx_products_category` (`category_id`);

-- SUPPLIERS_internalOrderProductsToSubLocation
-- Used in JOIN with products table — critical for fetchProducts() performance
ALTER TABLE `SUPPLIERS_internalOrderProductsToSubLocation` 
  ADD INDEX `idx_sublocproduct_product` (`product_id`),
  ADD INDEX `idx_sublocproduct_sublocation` (`sublocation_id`),
  ADD INDEX `idx_sublocproduct_product_subloc` (`product_id`, `sublocation_id`);

-- SUPPLIERS_internalOrderLocations
-- Used by fetchLocations() and other queries filtering by location_id
ALTER TABLE `SUPPLIERS_internalOrderLocations` 
  ADD INDEX `idx_locations_location_deleted` (`location_id`, `is_deleted`),
  ADD INDEX `idx_locations_kitchen` (`is_kitchen`, `is_deleted`, `status`);

-- SUPPLIERS_internalOrderCategory
-- Used in JOIN with products and filtered by is_deleted
ALTER TABLE `SUPPLIERS_internalOrderCategory` 
  ADD INDEX `idx_category_deleted` (`is_deleted`);

-- SUPPLIERS_internalOrderPlacedOrders
-- Used by orderHistory(), filterOrder(), getFilteredOrders()
ALTER TABLE `SUPPLIERS_internalOrderPlacedOrders` 
  ADD INDEX `idx_orders_sublocation_deleted` (`sublocation_id`, `is_deleted`),
  ADD INDEX `idx_orders_delivery_date` (`delivery_date`),
  ADD INDEX `idx_orders_location_delivery` (`location_id`, `delivery_date`);

-- SUPPLIERS_internalOrderPlacedOrdersProducts
-- Used in JOIN with orders and products
ALTER TABLE `SUPPLIERS_internalOrderPlacedOrdersProducts` 
  ADD INDEX `idx_orderproducts_order` (`order_id`),
  ADD INDEX `idx_orderproducts_product` (`product_id`);

-- SUPPLIERS_internalOrderProductCount
-- Used by fetchProductCountData() filtering by location, date, product, sublocation
ALTER TABLE `SUPPLIERS_internalOrderProductCount` 
  ADD INDEX `idx_productcount_location_date` (`location_id`, `date_completed`),
  ADD INDEX `idx_productcount_product_subloc` (`product_id`, `sublocation_id`);

-- SUPPLIERS_product_UOM
-- Used in dropdowns, filtered by is_deleted
ALTER TABLE `SUPPLIERS_product_UOM` 
  ADD INDEX `idx_uom_deleted` (`is_deleted`);
