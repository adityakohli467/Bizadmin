<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .highlighted-section { outline: 2px solid #3F20FB; background-color: rgba(63, 32, 251, 0.1); }
        .edit-button { position: absolute; z-index: 1000; }
    </style>
</head>
<body class="bg-light-gray">

<!-- Header -->
<header id="header" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-primary">Bizadmin</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-accent text-white px-4 py-2 rounded-full text-sm font-medium">
                    Running Total: $<span id="runningTotal">0.00</span>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-700">
                    <i class="fa-solid fa-bell text-lg"></i>
                </button>
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">A</span>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (isset($editOrderId) && $editOrderId != '') { ?>
        <form action="<?php echo base_url('Catering/edit_quote_products'); ?>" method="POST" id="new_order_form" novalidate>
            <input type="hidden" name="editOrderId" value="<?php echo htmlspecialchars($editOrderId); ?>" />
    <?php } else { ?>
        <form action="<?php echo base_url('Catering/new_quote_products'); ?>" method="POST" id="new_order_form" novalidate>
    <?php } ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Product Catalog (Left Panel) -->
        <div id="productCatalog" class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Top Bar -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <h2 class="text-2xl font-semibold text-primary">Select Products</h2>
                        <button type="button" class="bg-accent hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-plus"></i>
                            <span>New Product</span>
                        </button>
                    </div>
                    <!-- Search Bar -->
                    <div class="mt-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-gray-400"></i>
                            </div>
                            <input type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent" placeholder="Search for products...">
                        </div>
                    </div>
                    <!-- Quick Filters -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-medium transition-colors">All</button>
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-medium transition-colors">Beverages</button>
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-medium transition-colors">Food</button>
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full text-sm font-medium transition-colors">All Day</button>
                    </div>
                </div>
                <!-- Product List -->
                <div class="p-6">
                    <!-- Frequently Ordered Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fa-solid fa-star text-yellow-500 mr-2"></i>
                         All Day
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php if (!empty($products)) { ?>
                                <?php foreach ($products as $product) { ?>
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div>
                                                    <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                                    <p class="text-lg font-semibold text-accent">$ <?php echo number_format($product['product_price'], 2, '.', ','); ?></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <div class="flex items-center border border-gray-300 rounded-lg">
                                                    <button type="button" class="px-2 py-1 hover:bg-gray-100 rounded-l-lg decreaseQty" data-product-id="<?php echo $product['product_id']; ?>">
                                                        <i class="fa-solid fa-minus text-sm"></i>
                                                    </button>
                                                    <span class="px-3 py-1 border-x border-gray-300 min-w-[2rem] text-center qty" data-product-id="<?php echo $product['product_id']; ?>">1</span>
                                                    <button type="button" class="px-2 py-1 hover:bg-gray-100 rounded-r-lg increaseQty" data-product-id="<?php echo $product['product_id']; ?>">
                                                        <i class="fa-solid fa-plus text-sm"></i>
                                                    </button>
                                                </div>
                                                <button type="button" class="bg-accent hover:bg-green-600 text-white p-2 rounded-lg transition-colors addToCart" data-product-id="<?php echo $product['product_id']; ?>" data-name="<?php echo htmlspecialchars($product['product_name']); ?>" data-price="<?php echo $product['product_price']; ?>">
                                                    <i class="fa-solid fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- Beverages Category -->
                    <div class="mb-6">
                        <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fa-solid fa-coffee text-amber-600 mr-3"></i>
                                Beverages
                                <span class="ml-2 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">8 items</span>
                            </h3>
                            <i class="fa-solid fa-chevron-down text-gray-500"></i>
                        </button>
                        <div class="mt-4 space-y-3">
                              <div class="mt-4 space-y-4">
                            <div class="bg-white rounded-lg p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4 mb-4">
                                            <div>
                                                <h4 class="text-xl font-bold text-gray-900">Morning Tea Set Includes</h4>
                                                <p class="text-lg font-semibold text-accent">$12.99 per person</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Sandwiches</span>
                                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Coffee</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-600 space-y-1">
                                            <p><i class="text-green-500 mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>Sandwiches</p>
                                            <p><i class="text-green-500 mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>Cofeee</p>
                                            <p><i class="text-green-500 mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>Muffins</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-3 ml-6">
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button class="px-3 py-2 hover:bg-gray-100 rounded-l-lg transition-colors">
                                                <i class="text-sm" data-fa-i2svg=""><svg class="svg-inline--fa fa-minus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="minus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"></path></svg></i>
                                            </button>
                                            <span class="px-4 py-2 border-x border-gray-300 min-w-[3rem] text-center font-medium">0</span>
                                            <button class="px-3 py-2 hover:bg-gray-100 rounded-r-lg transition-colors">
                                                <i class="text-sm" data-fa-i2svg=""><svg class="svg-inline--fa fa-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"></path></svg></i>
                                            </button>
                                        </div>
                                        <button class="bg-accent hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                            <i data-fa-i2svg=""><svg class="svg-inline--fa fa-cart-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cart-plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM252 160c0 11 9 20 20 20h44v44c0 11 9 20 20 20s20-9 20-20V180h44c11 0 20-9 20-20s-9-20-20-20H356V96c0-11-9-20-20-20s-20 9-20 20v44H272c-11 0-20 9-20 20z"></path></svg></i>
                                            <span>Add </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                            <?php if (!empty($products)) { ?>
                                <?php foreach ($products as $index => $product) { ?>
                                    <?php if ($index >= 2) { // Start from the third product for Beverages ?>
                                  
                                        <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <img class="w-16 h-16 rounded-lg object-cover" src="https://storage.googleapis.com/uxpilot-auth.appspot.com/07c23b7ccc-608305cc753f6b963868.png" alt="iced latte coffee drink with foam art, professional photography">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                                        <p class="text-sm text-gray-500 mb-1">Cold Coffee • 16oz</p>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Popular</span>
                                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Iced</span>
                                                        </div>
                                                        <p class="text-xl font-bold text-accent mt-2">$<?php echo number_format($product['product_price'], 2, '.', ','); ?></p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                                        <button type="button" class="px-3 py-2 hover:bg-gray-100 rounded-l-lg decreaseQty" data-product-id="<?php echo $product['product_id']; ?>">
                                                            <i class="fa-solid fa-minus text-sm"></i>
                                                        </button>
                                                        <span class="px-4 py-2 border-x border-gray-300 min-w-[3rem] text-center qty" data-product-id="<?php echo $product['product_id']; ?>">0</span>
                                                        <button type="button" class="px-3 py-2 hover:bg-gray-100 rounded-r-lg increaseQty" data-product-id="<?php echo $product['product_id']; ?>">
                                                            <i class="fa-solid fa-plus text-sm"></i>
                                                        </button>
                                                    </div>
                                                    <button type="button" class="bg-accent hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2 addToCart" data-product-id="<?php echo $product['product_id']; ?>" data-name="<?php echo htmlspecialchars($product['product_name']); ?>" data-price="<?php echo $product['product_price']; ?>">
                                                        <i class="fa-solid fa-cart-plus"></i>
                                                        <span class="hidden sm:inline">Add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary (Right Panel) -->
        <div id="orderSummary" class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-24">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-semibold text-primary flex items-center">
                        <i class="fa-solid fa-receipt mr-3 text-accent"></i>
                        Order Summary
                    </h2>
                </div>
                <!-- Order Items -->
                <div class="p-6">
                    <div id="orderItems" class="space-y-4 mb-6 min-h-[200px]">
                        <!-- Populated dynamically by JavaScript -->
                    </div>
                    <!-- Coupon Section -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <div class="flex space-x-2">
                            <input type="text" id="coupon_code" value="<?php echo $coupon_code ?? ''; ?>" name="coupon_code" data-discount="0" data-type="F" placeholder="Enter coupon code" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent">
                            <button type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors" id="applyCoupon">Apply</button>
                            <button type="button" class="bg-red-400 hover:bg-red-200 text-white px-4 py-2 rounded-lg font-medium transition-colors" onclick="removeCoupon(<?php echo $editOrderId ?? 0; ?>)">X</button>
                        </div>
                    </div>
                    <!-- Totals -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Discount</span>
                            <span id="discount">-$0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span id="tax">$0.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-xl font-bold text-primary">
                                <span>Total</span>
                                <span id="total">$0.00</span>
                            </div>
                            <input type="hidden" name="cart_total" id="cart_total" value="0.00">
                        </div>
                    </div>
                    <!-- Order Comments -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Comments</label>
                        <textarea name="order_comments" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent resize-none" rows="3" placeholder="Special instructions or notes..."></textarea>
                    </div>
                    <?php if (isset($editOrderId) && $editOrderId != '') { ?>
                        <div class="space-y-3">
                            <button type="submit" class="w-full bg-accent hover:bg-green-600 text-white py-3 rounded-lg font-semibold transition-colors flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-save"></i>
                                <span>Update Order</span>
                            </button>
                        </div>
                    <?php } else { ?>
                        <div class="space-y-3">
                            <button type="submit" class="w-full bg-accent hover:bg-green-600 text-white py-3 rounded-lg font-semibold transition-colors flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-save"></i>
                                <span>Save Order</span>
                            </button>
                            <button type="button" onclick="openApproveMail_modal()" class="w-full bg-primary hover:bg-gray-800 text-white py-3 rounded-lg font-semibold transition-colors flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Send to Customer</span>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="coupon_id" id="applied_coupon_id" value="<?php echo $coupon_id ?? ''; ?>">
    <input type="hidden" id="delivery_fee" value="<?php echo $delivery_fee ?? ''; ?>">
    <input type="hidden" name="saveAndSend" id="saveAndSend" value="">
    <input type="hidden" name="customerOrderEmailToSendApprovalMail" id="customerOrderEmailToSendApprovalMail">
    </form>

    <!-- Modal -->
    <div id="email_modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-800 bg-opacity-50"></div>
        <div class="flex items-center justify-center min-h-screen px-4 relative z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
                <div class="flex justify-between items-center border-b px-4 py-2">
                    <h5 class="text-lg font-semibold text-gray-800">Email</h5>
                    <button type="button" class="text-gray-500 hover:text-red-500" onclick="closeModal('email_modal')">
                        ✕
                    </button>
                </div>
                <div class="px-4 py-4">
                    <p class="text-sm text-gray-700 mb-3">Please enter the email ID to send to:</p>
                    <input type="email" id="customerOrderEmail" value="<?php echo $customer_order_email ?? ''; ?>" class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="hidden text-red-500 text-sm mt-1" id="emailError">Please enter an email address!</p>
                </div>
                <div class="flex justify-end space-x-2 border-t px-4 py-2">
                    <button type="button" onclick="send_mailAndSubmitForm()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Send Mail
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
let orderItems = <?php echo json_encode($order_products ?? []); ?> || [];
let subtotal = 0;
let discount = 0;
let tax = 0;
let total = 0;
updateOrderSummary();
function updateRunningTotal() {
    document.getElementById('runningTotal').textContent = total.toFixed(2);
}

function updateOrderSummary() {
    const orderItemsContainer = document.getElementById('orderItems');
    console.log("orderItems",orderItems);
     console.log("orderItems",orderItems.length);
    if (orderItems.length === 0) {
        orderItemsContainer.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-shopping-cart text-4xl mb-4 opacity-50"></i>
                <p class="text-lg font-medium">No items in order</p>
                <p class="text-sm">Add products to get started</p>
            </div>
        `;
    } else {
        orderItemsContainer.innerHTML = orderItems.map(item => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg fade-in" id="cart-product-${item.id}">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${item.name}</h4>
                    <p class="text-sm text-gray-500">$${item.price.toFixed(2)} each</p>
                    <input type="text" class="mt-5 w-full border border-gray-300 rounded-lg p-1 text-sm" 
                           name="order_product_comment[${item.id}]" 
                           value="${item.comment || ''}" 
                           placeholder="Add comment...">
                    <input type="hidden" name="qty[${item.id}]" value="${item.quantity}">
                    <input type="hidden" name="product_price[${item.id}]" value="${item.price}">
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button" onclick="updateQuantity('${item.id}', ${item.quantity - 1})" class="px-2 py-1 hover:bg-gray-100 rounded-l-lg">
                            <i class="fa-solid fa-minus text-sm"></i>
                        </button>
                        <span class="px-3 py-1 border-x border-gray-300 min-w-[2rem] text-center">${item.quantity}</span>
                        <button type="button" onclick="updateQuantity('${item.id}', ${item.quantity + 1})" class="px-2 py-1 hover:bg-gray-100 rounded-r-lg">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </button>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">$${(item.price * item.quantity).toFixed(2)}</p>
                    </div>
                    <button type="button" onclick="removeItem('${item.id}')" class="text-red-500 hover:text-red-700 p-1">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    // Calculate totals
    subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    total = subtotal - discount + tax;
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('discount').textContent = `$${(discount * -1).toFixed(2)}`;
    document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    document.getElementById('cart_total').value = total.toFixed(2);
    updateRunningTotal();
}

function addToOrder(id, name, price) {
    const existingItem = orderItems.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        orderItems.push({
            id: id,
            name: name,
            price: parseFloat(price),
            quantity: 1,
            comment: ''
        });
    }
    updateOrderSummary();
}

function updateQuantity(id, newQuantity) {
    if (newQuantity <= 0) {
        removeItem(id);
        return;
    }
    const item = orderItems.find(item => item.id === id);
    if (item) {
        item.quantity = newQuantity;
        updateOrderSummary();
    }
}

function removeItem(id) {
    orderItems = orderItems.filter(item => item.id !== id);
    updateOrderSummary();
}

function updateProductQuantities() {
    orderItems.forEach(item => {
        $(`.qty[data-product-id="${item.id}"]`).text(item.quantity);
    });
}

function applyCoupon() {
    const couponCode = $('#coupon_code').val();
    // Simulate coupon application (replace with actual API call)
    if (couponCode === 'DISCOUNT10') {
        discount = subtotal * 0.10;
        $('#applied_coupon_id').val('1'); // Example coupon ID
    } else {
        discount = 0;
        $('#applied_coupon_id').val('');
    }
    updateOrderSummary();
}

function removeCoupon(orderId) {
    discount = 0;
    $('#coupon_code').val('');
    $('#applied_coupon_id').val('');
    updateOrderSummary();
    // Optionally call API to remove coupon from server
}

function closeModal(modalId) {
    $('#' + modalId).hide();
}

function openApproveMail_modal() {
    $('#email_modal').show();
}

function send_mailAndSubmitForm() {
    const email = $('#customerOrderEmail').val();
    if (!email) {
        $('#emailError').show();
        return;
    }
    $('#emailError').hide();
    $('#saveAndSend').val(true);
    $('#customerOrderEmailToSendApprovalMail').val(email);
    $('#new_order_form').submit();
}

$(document).ready(function() {
    const sampleProducts = <?php echo json_encode($products); ?>;

    // Initialize quantities and event listeners
    $('.increaseQty').on('click', function() {
        const productId = $(this).data('product-id');
        const qtyElement = $(`.qty[data-product-id="${productId}"]`);
        let qty = parseInt(qtyElement.text()) + 1;
        qtyElement.text(qty);
        addToOrder(productId, sampleProducts.find(p => p.product_id == productId).product_name, sampleProducts.find(p => p.product_id == productId).product_price);
    });

    $('.decreaseQty').on('click', function() {
        const productId = $(this).data('product-id');
        const qtyElement = $(`.qty[data-product-id="${productId}"]`);
        let qty = parseInt(qtyElement.text());
        if (qty > 0) {
            qty--;
            qtyElement.text(qty);
            updateQuantity(productId, qty);
        }
    });

    $('.addToCart').on('click', function() {
        const productId = $(this).data('product-id');
        const name = $(this).data('name');
        const price = $(this).data('price');
        addToOrder(productId, name, price);
    });

    $('#applyCoupon').on('click', applyCoupon);

    // Initialize with existing order items if any
    if (orderItems.length > 0) {
        updateOrderSummary();
    }
});
</script>

	<script>
		
    
		 $('.grid tbody').sortable({
         axis: 'y',
         update: function (event, ui) {
         let sortedIDs = $(this).sortable('toArray', { attribute: 'data-product-id' });
         let $hiddenFieldsContainer = $('#new_order_form');
         console.log("sortedIDs",sortedIDs)
         $.each(sortedIDs, function(index, productId) {
                var $hiddenField = $('#hidden-product-' + productId);
                $hiddenFieldsContainer.append($hiddenField);
            });
            
        var data = $(this).sortable('serialize');
        // var data = data.replace("cart-product", "cart-existing-item");
         $.ajax({
		 url:"<?php echo base_url('Catering/chnage_product_sort_order');?>",
		 method:"POST",
		 data:data,
		 success:function(data){
		 console.log("position saved");
		}
		})
    }
});	
      

		 function openApproveMail_modal()
		{   
			openModal('email_modal');
		}
		function send_mailAndSubmitForm(){
		    // when creating quote manager has 2 option either send mail to customer directly or just save the quote, in case of sendmail to customer this code is executed
		    $(".buttonContent").html('Sending...')
		    $("#saveAndSend").val('true');
		    $("#customerOrderEmailToSendApprovalMail").val($("#customerOrderEmail").val());
		    $("#new_order_form").submit();
		}
		
		$(document).on('click', function(event) {
        if (!$(event.target).closest($couponCode).length) {
            if ($couponCode.val() !== '') {
                // Show loader
                $loader.show();
                // Perform AJAX request
                let coupon_code = $couponCode.val();
                $couponCode.removeClass('is-invalid');
                console.log("VALLL c",$("#coupon_code").val())
                $.ajax({
                    url: '<?php echo base_url("Catering/validateCoupon/"); ?>' + coupon_code,
                    method: "POST",
                    success: function(data) {
                        console.log('AJAX Response:', data);

                        if (data == "0") {
                            $couponCode.addClass('is-invalid');
                            $couponCode.data('discount', 0);
                            $couponCode.data('type', 'F');
                            $("#applied_coupon_id").val('')
                        } else {
                            $couponCode.removeClass('is-invalid');
                            data = JSON.parse(data);
                            $couponCode.data('discount', data[0].coupon_discount);
                            $couponCode.data('type', data[0].type);
                            $("#applied_coupon_id").val(data[0].coupon_id)
                            console.log('Coupon Data:', data[0]);
                        }

                        // Hide loader after response is received
                        $loader.hide();

                        // Call to recalculate total after setting discount and type
                        calculate_total();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);

                        // Hide loader if an error occurs
                        $loader.hide();
                    }
                });
            }else{
                $couponCode.data('discount', 0);
                $couponCode.data('type', 'F');
                $("#applied_coupon_id").val('')
                
                updateOrderSummary();
            }
        }
    });

    // Prevent triggering outside click event when clicking inside the input
    $couponCode.on('click', function(event) {
        event.stopPropagation();
    });
    
    function removeCoupon(orderId){
    $couponCode = $("#coupon_code");
      
      $("#applied_coupon_id").val('');
      $couponCode.val('');
      $couponCode.data('discount', 0);
      $couponCode.data('type', 'F');
      $("#applied_coupon_id").val('')
                
     updateOrderSummary();
     $.ajax({
     url: '<?php echo base_url("Catering/removeCoupon/"); ?>' + orderId,
     method: "POST",
     success: function(data) {
      
      },
     error: function(xhr, status, error) {
     console.error('AJAX Error:', error);
        }
       });
    
    }
    
    function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}
function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
}
		
		</script>
</body>
</html>