<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.3.5/jquery.timepicker.min.css">
    

    <style>
        .dropdown-open { display: block; }
        .dropdown-closed { display: none; }
    </style>
<style>
  .highlighted-section {
    outline: 2px solid #3F20FB;
    background-color: rgba(63, 32, 251, 0.1);
  }

  .edit-button {
    position: absolute;
    z-index: 1000;
  }

  ::-webkit-scrollbar {
    display: none;
  }

  html, body {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  </style></head>
<body class="">

<main class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div id="" class="card">
    
    <!-- Modal Container -->
  
        
        <!-- Modal Header -->
        <div id="card-header" class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-white ">
            <div>
                <h1 class="text-2xl font-bold text-primary"><?php echo $pageHeading; ?></h1>
                <p class="text-gray-600 text-sm mt-1">Fill in the details to generate your quote</p>
            </div>
            <button id="close-modal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fa-solid fa-times text-xl text-gray-500"></i>
            </button>
        </div>

        <!-- Modal Body -->
      <div id="card-body" >
            <div class="p-6">
                
                <!-- Form Container -->
              
                    <form action="<?php echo base_url('Catering/new_quotes_save'); ?>" class="space-y-6" method="POST" id="new_order_form" novalidate>
            <?php $editOrderId = $editOrderId ?? ''; ?>
            <input type="hidden" name="editOrderId" value='<?php echo $editOrderId; ?>'>   
                    <!-- Desktop Two Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Left Card: Customer Details -->
                        <div id="customer-details-card" class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-primary flex items-center">
                                    <i class="fa-solid fa-user-circle text-accent mr-2"></i>
                                    Customer Details
                                </h2>
                                <p class="text-gray-600 text-sm mt-1">Select customer information for this quote</p>
                            </div>

                            <div class="space-y-4">
                                <!-- Company Field -->
                               <!-- Company Field -->
<div class="form-group">
    <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-building text-gray-400"></i>
        </div>
        <select name="company_id" required id="company_id" class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
            <option value="0" selected disabled>Select Company</option>
            <?php if (!empty($companies)) {
                foreach ($companies as $company) {
                    $company = (array)$company;
                    $selected = !empty($orderData['company_id']) && $orderData['company_id'] == $company['company_id'] ? 'selected' : '';
                    echo "<option value=\"" . $company['company_id'] . "\" $selected>" . $company['company_name'] . "</option>";
                }
            } ?>
        </select>
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-chevron-down text-gray-400"></i>
        </div>
    </div>
</div>

<!-- Department Field -->
<div class="form-group">
    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-sitemap text-gray-400"></i>
        </div>
        <select name="department_id" required id="department_id" class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
            <option value="0" selected>All Departments</option>
            <?php if (!empty($departments)) {
                foreach ($departments as $department) {
                    $department = (array)$department;
                    $selected = !empty($orderData['department_id']) && $orderData['department_id'] == $department['department_id'] ? 'selected' : '';
                    echo "<option value=\"" . $department['department_id'] . "\" $selected>" . $department['department_name'] . "</option>";
                }
            } ?>
        </select>
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-chevron-down text-gray-400"></i>
        </div>
    </div>
</div>

<!-- Customer Field -->
<div class="form-group">
    <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-user text-gray-400"></i>
        </div>
        <select name="customer_id" required id="customer_id" class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors">
            <option value="0" selected disabled>Select Customer</option>
            <?php if (!empty($customers)) {
                foreach ($customers as $customer) {
                    $customer = (array)$customer;
                    $selected = !empty($orderData['customer_id']) && $orderData['customer_id'] == $customer['customer_id'] ? 'selected' : '';
                    echo "<option value=\"" . $customer['customer_id'] . "\" $selected>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
                }
            } ?>
        </select>
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <i class="fa-solid fa-chevron-down text-gray-400"></i>
        </div>
    </div>

    <button type="button" class="mt-2 text-accent hover:text-green-700 text-sm font-medium flex items-center" onclick="add_customer()">
        <i class="fa-solid fa-plus mr-1"></i>
        Create New Customer
    </button>
</div>


                                <!-- Contact Information Group -->
                                <div class="border-t border-gray-200 pt-4 mt-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-3">Contact Information</h3>

    <!-- Phone Field -->
    <div class="form-group mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-phone-alt text-gray-400"></i>
            </div>
            <input type="tel" name="phone" id="phone"
                value="<?php echo !empty($orderData['delivery_contact']) ? $orderData['delivery_contact'] : ''; ?>"
                class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                placeholder="Enter phone number" required>
        </div>
    </div>

    <!-- Email Field -->
    <div class="form-group">
        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-envelope text-gray-400"></i>
            </div>
            <input type="email" name="email" id="email"
                value="<?php echo !empty($orderData['accounts_email']) ? $orderData['accounts_email'] : ''; ?>"
                class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                placeholder="Enter email address" required>
        </div>
    </div>
</div>
                            </div>
                        </div>

                        <!-- Right Card: Delivery Details -->
                        <div id="delivery-details-card" class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-primary flex items-center">
            <i class="fa-solid fa-truck text-accent mr-2"></i>
            Delivery Details
        </h2>
        <p class="text-gray-600 text-sm mt-1">Configure delivery information and preferences</p>
    </div>

    <div class="space-y-4">
        
        <!-- Delivery Date & Time -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-calendar-alt text-gray-400"></i>
                    </div>
                    <input type="text" name="delivery_date" id="delivery_date"
                        value="<?php echo !empty($orderData['delivery_date']) ? date('Y-m-d', strtotime($orderData['delivery_date'])) : ''; ?>"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors flatpickr-input"
                        data-provider="flatpickr" data-date-format="Y-m-d" readonly required>
                </div>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-clock text-gray-400"></i>
                    </div>
                    <input type="text" name="delivery_time" id="delivery_time"
                        value="<?php echo !empty($orderData['delivery_time']) ? $orderData['delivery_time'] : ''; ?>"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors timepicker-input"
                        autocomplete="off" required>
                </div>
            </div>
        </div>

        <!-- Account Email -->
        <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">Account Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-envelope-open-text text-gray-400"></i>
                </div>
                <input type="email" name="accounts_email" id="accounts_email"
                    value="<?php echo !empty($orderData['accounts_email']) ? $orderData['accounts_email'] : ''; ?>"
                    class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                    placeholder="Enter account email">
            </div>
        </div>

        <!-- Delivery Contact & Cost Center -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Contact</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-mobile-alt text-gray-400"></i>
                    </div>
                    <input type="tel" name="delivery_contact" id="delivery_contact"
                        value="<?php echo !empty($orderData['delivery_contact']) ? $orderData['delivery_contact'] : ''; ?>"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                        placeholder="Enter contact number">
                </div>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Center</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-briefcase text-gray-400"></i>
                    </div>
                    <input type="text" name="cost_center" id="cost_center"
                        value="<?php echo !empty($orderData['cost_center']) ? $orderData['cost_center'] : ''; ?>"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                        placeholder="Optional: Cost center">
                </div>
            </div>
        </div>

        <!-- Delivery Method -->
        <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-3">Delivery Method</label>
            <div class="flex gap-4">
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="shipping_method" value="1" class="sr-only shipping_radio"
                        <?php echo !empty($orderData['shipping_method']) && $orderData['shipping_method'] == 1 ? 'checked' : ''; ?>>
                    <div class="delivery-option border-2 <?php echo !empty($orderData['shipping_method']) && $orderData['shipping_method'] == 1 ? 'border-accent bg-accent/10 text-accent' : 'border-gray-300 text-gray-600'; ?> rounded-lg p-4 text-center transition-all hover:bg-accent/20">
                        <i class="fa-solid fa-truck text-2xl mb-2"></i>
                        <div class="font-medium">Delivery</div>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="shipping_method" value="2" class="sr-only shipping_radio"
                        <?php echo !empty($orderData['shipping_method']) && $orderData['shipping_method'] == 2 ? 'checked' : ''; ?>>
                    <div class="delivery-option border-2 <?php echo !empty($orderData['shipping_method']) && $orderData['shipping_method'] == 2 ? 'border-accent bg-accent/10 text-accent' : 'border-gray-300 text-gray-600'; ?> rounded-lg p-4 text-center transition-all hover:border-gray-400">
                        <i class="fa-solid fa-store text-2xl mb-2"></i>
                        <div class="font-medium">Pickup</div>
                    </div>
                </label>
                
               
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="form-group delivery">
            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
            <div class="relative">
                <div class="absolute top-3 left-3 pointer-events-none">
                    <i class="fa-solid fa-map-marker-alt text-gray-400"></i>
                </div>
                <textarea name="delivery_address" id="delivery_address"
                    class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors resize-none"
                    rows="3"
                    placeholder="Enter delivery address"><?php echo !empty($orderData['delivery_address']) ? $orderData['delivery_address'] : ''; ?></textarea>
            </div>
        </div>
        
          <div class="form-group pickup mb-5">
              <div class="relative">
                  <div class="absolute top-3 left-3 pointer-events-none">
                    <i class="fa-solid fa-map-marker-alt text-gray-400"></i>
                    Pickup Address
                </div>
			
			<div class="w-full pl-10 pr-3 py-3" id="pickup_address"></div>
		    <input type="hidden"  value="" name="customer_pickup_address" id="customer_pickup_address">
		</div>
		</div>

        <!-- Delivery Fee & Notes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group delivery">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Fee</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-dollar-sign text-gray-400"></i>
                    </div>
                    <input type="text" name="delivery_fee" id="delivery_fee"
                        value="<?php echo !empty($orderData['delivery_fee']) ? $orderData['delivery_fee'] : ''; ?>"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors"
                        placeholder="Enter fee">
                </div>
            </div>

            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                <div class="relative">
                    <div class="absolute top-3 left-3 pointer-events-none">
                        <i class="fa-solid fa-sticky-note text-gray-400"></i>
                    </div>
                    <textarea name="delivery_notes" id="deliver_notes_customer"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-colors resize-none"
                        rows="3"><?php echo !empty($orderData['delivery_notes']) ? $orderData['delivery_notes'] : "Time:\nLocation:\nNumber:\nName:"; ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
                    </div>
                    
                    
                </form>
            </div>
      </div>

       <div id="card-footer" class="px-6 py-4 border-t border-gray-200 bg-gray-50 sticky bottom-0 justify-end">
         <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <button type="button" id="cancel-btn" class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="proceed-btn" class="w-full sm:w-auto px-6 py-3 bg-accent text-white rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-shopping-cart mr-2"></i>
                    <span>Proceed</span>
                    <div id="loading-spinner" class="ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin hidden"></div>
                </button>
            </div>
             </div>
    
</div>
</main>
<?php // $this->load->view('customer/customerCommon'); ?>
<script>

// Delivery method toggle
document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.delivery-option').forEach(option => {
            option.classList.remove('border-accent', 'bg-accent/10', 'text-accent');
            option.classList.add('border-gray-300', 'text-gray-600');
        });
        
        if (this.checked) {
            const option = this.nextElementSibling;
            option.classList.remove('border-gray-300', 'text-gray-600');
            option.classList.add('border-accent', 'bg-accent/10', 'text-accent');
        }
    });
});

// Form submission
document.getElementById('proceed-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Show loading state
    this.disabled = true;
    document.getElementById('loading-spinner').classList.remove('hidden');
    this.querySelector('span').textContent = 'Processing...';
    
    $("#new_order_form").submit();
});



// Form validation
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('border-red-500');
        } else {
            this.classList.remove('border-red-500');
        }
    });
    
    field.addEventListener('focus', function() {
        this.classList.remove('border-red-500');
    });
});
</script>

<script>
$(function(){
    
	company_map=<?php echo json_encode($companies);?>;
	customer_map=<?php echo json_encode($customers);?>;
	department_map=<?php echo json_encode($departments);?>;

	pickupAddressList=<?php echo json_encode($pickupAddressList);?>;

	$(".pickup").hide();
	$(".shipping_radio").on('change',function(){
		if($(".shipping_radio:checked").val()==1){
			$(".pickup").hide();
			$(".delivery").show();
			 $('#delivery_fee').prop("disabled", false);
		}
		else{
			$(".delivery").hide();
// 			$("#delivery").remove();
			$(".pickup").show();
			 $('#delivery_fee').prop("disabled", true);
		}
	})

	
	if($("#company_id").val()!=0){
	    
		for(var i=0;i<customer_map.length;i++){
			if(customer_map[i].company_id==$("#company_id").val()){
				if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
					$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
				else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
			}
		}
	}
	
	
	$("#company_id").on('change',function(){
     console.log("department_map ",department_map);

		$("#department_id").empty();
		$("#department_id").append("<option value=\"0\" selected>All Departments</option>");
		for(var i=0;i<department_map.length;i++){
			if(department_map[i].company_id==$(this).val()){
				$("#department_id").append("<option value=\""+department_map[i].department_id+"\">"+department_map[i].department_name+"</option>");
			}
		}
				//Also customers that don't have a department
		$("#customer_id").empty();
		$("#customer_id").append("<option value=\"0\" selected disabled>Select Customer</option>");
		for(var i=0;i<customer_map.length;i++){
			if(customer_map[i].company_id==$(this).val()){
				if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
					$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
				else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
			}
		}
	})
	$("#department_id").on('change',function(){
		$("#customer_id").empty();
		$("#customer_id").append("<option value=\"0\" selected disabled>Select Customer</option>");
		
		for(var i=0;i<customer_map.length;i++){
		    if(typeof customer_map[i].department_id === 'undefined') {
		        // because in frontend customer db we have column name as department_id and in backend we have it department
           if((customer_map[i].department==$(this).val()&&customer_map[i].company_id==$("#company_id").val()) || (customer_map[i].department_id==$(this).val())){
    				if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
    					$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    				else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    			}
          }else if(customer_map[i].department != ''){
    			if(customer_map[i].department_id==$(this).val()&&customer_map[i].company_id==$("#company_id").val()){
    				if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
    					$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    				else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    			}
		    }else{
		        if(customer_map[i].department==$(this).val()&&customer_map[i].company_id==$("#company_id").val()){
    				if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
    					$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    				else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
    			}
		    }
		}
		if($(this).val()==0){
			for(var i=0;i<customer_map.length;i++){
				if(customer_map[i].company_id==$("#company_id").val()){
					if($.trim(<?php echo empty($pre_customer)?"''":$pre_customer;?>)!=''&&<?php echo empty($pre_customer)?"''":$pre_customer;?>==customer_map[i].customer_id)
						$("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\" selected>"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
					else $("#customer_id").append("<option value=\""+customer_map[i].customer_id+"\">"+customer_map[i].firstname+" "+customer_map[i].lastname+"</option>");
				}
			}
		}
	})
	$("#customer_id").on('change',function(){
		for(var i=0;i<customer_map.length;i++){
			if($(this).val()==customer_map[i].customer_id){
				$("#phone").val(customer_map[i].telephone);
				$("#email").val(customer_map[i].email);
				// $("#cost_centre").val(customer_map[i].customer_cost_centre);
			}
		}
	})
	

// 	$("#new_order_form").on('submit',function(e){
// 		var flag=0;
	
// 		$(".is-invalid").removeClass('is-invalid');
// 		if($("#customer_id option").filter(':selected').val()==0){
// 			$("#customer_id").addClass('is-invalid');
// 			flag=1;
// 		}
// 		if($.trim($("#delivery_date").val())=='')
// 		{
// 			$("#delivery_date").addClass('is-invalid');
// 			flag=1;
// 		}
// 		if($.trim($("#delivery_time").val())=='')
// 		{
// 			$("#delivery_time").addClass('is-invalid');
// 			flag=1;
// 		}
// 		if($.trim($("#delivery_contact").val())=='')
// 		{
// 			$("#delivery_contact").addClass('is-invalid');
// 			flag=1;
// 		}
	
	
// 		let regexp=/^\d*$/;
// 		if(!regexp.test($("#delivery_fee").val())){
// 		flag=1;
// 		$("#delivery_fee").addClass("is-invalid");
// 			}
// 		if(flag===1){
// 			e.preventDefault();
// 		}
// 	})
})


$(document).ready(function() {
    $('.timepicker-input').timepicker({
        timeFormat: 'h:mm p',
        interval: 05,
        minTime: '12:00am',
        maxTime: '11:30pm',
        startTime: '12:00am',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
    
//     if($(".shipping_radio:checked").val()==1){
// 			$(".pickup").hide();
// 			$(".delivery").show();
			
// 			$('#delivery_fee').removeAttr("disabled");    
// 			}
// 		else{
// 		    $("#delivery_fee").attr('disabled', 'disabled');
// 			$(".delivery").hide();
// 			$(".pickup").show();
		
// 		}

})
</script>
</body></html>