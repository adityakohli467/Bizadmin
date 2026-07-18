<script>
    if (typeof tailwind !== 'undefined') {
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#132f65",
                        accent: "#f97316"
                    },
                    fontFamily: {
                        inter: ["Inter", "sans-serif"],
                        sans: ["Inter", "sans-serif"]
                    }
                }
            }
        };
    }

    $(document).ready(function() {
        // Desktop Dropdown
        const $platformsLink = $('#platforms-link');
        const $platformsDropdown = $('#platforms-dropdown');
        const $platformItems = $('.platform-item');
        const $platformContents = $('.platform-content');

        $platformsLink.on('click', function(e) {
            e.preventDefault();
            $platformsDropdown.slideToggle(200);
        });

        $platformItems.on('click', function(e) {
            e.preventDefault();
            const targetContent = $(this).data('target');
            $platformContents.addClass('hidden');
            $('#' + targetContent).removeClass('hidden');
            const page = $(this).data('page');
            if (page) {
                loadContent(page);
                $platformsDropdown.slideUp(200); // Close dropdown after selection
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$platformsLink.is(e.target) && !$platformsLink.find('*').is(e.target) && !$platformsDropdown.is(e.target) && !$platformsDropdown.find('*').is(e.target)) {
                $platformsDropdown.slideUp(200);
            }
        });

        // Mobile Menu Toggle
        const $burgerMenu = $('#burgerBtn');
        const $mobileMenu = $('#mobileMenu');
        const $closeMenu = $('#close-menu');

        $burgerMenu.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $mobileMenu.toggleClass('is-open');
        });

        $closeMenu.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $mobileMenu.removeClass('is-open');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$mobileMenu.is(e.target) && !$mobileMenu.find('*').is(e.target) && !$burgerMenu.is(e.target) && !$burgerMenu.find('*').is(e.target)) {
                $mobileMenu.removeClass('is-open');
            }
        });

        // Menu item click handler (desktop and mobile)
        $('.menu-item').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                loadContent(page);
                $mobileMenu.removeClass('is-open'); // Close mobile menu
                $('.menu-item').removeClass('active');
                $(this).addClass('active');
            }
        });

        // AJAX content loading
       function loadContent(page) {
    const $dynamicContent = $('#dynamic-content-wrapper');
    
    // Create overlay and larger loader
    $('body').append(`
        <div id="loading-overlay" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <svg class="animate-spin h-32 w-32 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"></path>
            </svg>
        </div>
    `);
    
    $.get('<?php echo site_url('home/load_page'); ?>?page=' + page, function(data) {
        $dynamicContent.html(data);
        history.pushState({ page: page }, '', '<?php echo base_url(); ?>' + page);
        $('#loading-overlay').remove(); // Remove overlay on success
    }).fail(function(jqXHR, textStatus, errorThrown) {
        $dynamicContent.html('<p class="text-red-600 text-center">Error loading content. Please try again.</p>');
        $('#loading-overlay').remove(); // Remove overlay on error
        console.error('Error loading page:', page, 'Status:', textStatus, 'Error:', errorThrown);
    });
}

        // Handle back/forward navigation
        $(window).on('popstate', function(event) {
            if (event.originalEvent.state && event.originalEvent.state.page) {
                loadContent(event.originalEvent.state.page);
                $('.menu-item').removeClass('active');
                $(`.menu-item[data-page="${event.originalEvent.state.page}"]`).addClass('active');
            }
        });

        // Show HR content by default in dropdown
        $('#hr-content').removeClass('hidden');
    });
</script>

<header id="header" class="bg-white py-4 px-6 lg:px-12 border-b border-gray-200 relative z-30">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <span class="text-2xl font-bold cursor-pointer">
                    <a href="<?php echo site_url('home'); ?>">
                        <img class="logo-img" alt="bizadmin" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/logo.jpg" style="height: 36px;width: 130px;">
                    </a>
                </span>
            </div>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center space-x-7">
                <span class="text-gray-700 hover:text-blue-600 cursor-pointer"><a href="#features">Features</a></span>
                <span class="text-gray-700 hover:text-blue-600 flex items-center cursor-pointer" id="platforms-link">Solutions <i class="fa-solid fa-chevron-down ml-1 text-xs"></i></span>
                <span class="text-gray-700 hover:text-blue-600 cursor-pointer flex items-center gap-1"><a href="#features">AI Assistant</a><span class="bg-blue-100 text-blue-600 text-[10px] font-semibold px-1.5 py-0.5 rounded">New</span></span>
                <span class="text-gray-700 hover:text-blue-600 cursor-pointer"><a href="#">Pricing</a></span>
                <span class="text-gray-700 hover:text-blue-600 cursor-pointer"><a href="#">Success Stories</a></span>
                <span class="text-gray-700 hover:text-blue-600 flex items-center cursor-pointer"><a href="#">Resources</a> <i class="fa-solid fa-chevron-down ml-1 text-xs"></i></span>
                <div id="platforms-dropdown" class="hidden absolute top-full left-0 mt-2 bg-white shadow-xl rounded-lg w-[800px] border border-gray-200 z-40">
                    <div class="flex">
                        <!-- Left Menu -->
                        <div class="w-1/3 border-r border-gray-200 py-4">
                            <ul>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="hr-content" data-page="hrm"><span class="font-semibold">HR & Onboarding</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="suppliers-content" data-page="suppliers"><span class="font-semibold">Suppliers</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="ordering-content" data-page="catering"><span class="font-semibold">Ordering Portal</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="compliance-content" data-page="checklists"><span class="font-semibold">Checklists</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="compliance-content" data-page="cleaning"><span class="font-semibold">Cleaning Schedule</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="compliance-content" data-page="temperature"><span class="font-semibold">Temperature Recording</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="compliance-content" data-page="documents"><span class="font-semibold">Document Manage</span></li>
                                <li class="platform-item hover:bg-gray-100 px-6 py-3 cursor-pointer" data-target="compliance-content" data-page="cash"><span class="font-semibold">Cash Management</span></li>
                            </ul>
                        </div>
                        <!-- Right Content -->
                        <div class="w-2/3 p-6">
                            <div id="hr-content" class="platform-content hidden">
                                <div class="flex">
                                    <div class="w-2/3 pr-6">
                                        <h3 class="text-xl font-bold mb-2 text-primary">HR & Onboarding</h3>
                                        <p class="text-gray-600 mb-4">Simplify new hires with automated onboarding and employee tracking. Streamline the entire HR process from recruitment to retirement.</p>
                                        <span class="inline-block px-4 py-2 bg-accent text-white rounded-md hover:bg-accent/90 transition cursor-pointer menu-item" data-page="hrm">Know More</span>
                                    </div>
                                    <div class="w-1/3 relative">
                                        <div class="bg-primary rounded-lg overflow-hidden h-40">
                                            <img class="object-cover w-full h-full opacity-50" src="https://storage.googleapis.com/uxpilot-auth.appspot.com/5bc89ba1d8-59cf72adf5f19865f877.png" alt="modern HR dashboard interface with employee profiles and onboarding tasks">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <h4 class="text-white font-bold text-xl">HR Onboarding</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="suppliers-content" class="platform-content hidden">
                                <div class="flex">
                                    <div class="w-2/3 pr-6">
                                        <h3 class="text-xl font-bold mb-2 text-primary">Suppliers</h3>
                                        <p class="text-gray-600 mb-4">Manage all your suppliers in one place. Track orders, invoices, and communications for streamlined procurement.</p>
                                        <span class="inline-block px-4 py-2 bg-accent text-white rounded-md hover:bg-accent/90 transition cursor-pointer menu-item" data-page="suppliers">Know More</span>
                                    </div>
                                    <div class="w-1/3 relative">
                                        <div class="bg-primary rounded-lg overflow-hidden h-40">
                                            <img class="object-cover w-full h-full opacity-50" src="https://storage.googleapis.com/uxpilot-auth.appspot.com/84b01d059c-a22be8d6d0bbfcf9dbca.png" alt="supplier management dashboard with inventory tracking and order history">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <h4 class="text-white font-bold text-xl">Supplier Portal</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="ordering-content" class="platform-content hidden">
                                <div class="flex">
                                    <div class="w-2/3 pr-6">
                                        <h3 class="text-xl font-bold mb-2 text-primary">Ordering Portal</h3>
                                        <p class="text-gray-600 mb-4">Simplify your ordering process with our intuitive portal. Track orders, manage inventory, and streamline procurement.</p>
                                        <span class="inline-block px-4 py-2 bg-accent text-white rounded-md hover:bg-accent/90 transition cursor-pointer menu-item" data-page="ordering">Know More</span>
                                    </div>
                                    <div class="w-1/3 relative">
                                        <div class="bg-primary rounded-lg overflow-hidden h-40">
                                            <img class="object-cover w-full h-full opacity-50" src="https://storage.googleapis.com/uxpilot-auth.appspot.com/dfe6905bd3-d58a5a386051270d2c5f.png" alt="ordering system interface with product catalog and cart functionality">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <h4 class="text-white font-bold text-xl">Ordering System</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="compliance-content" class="platform-content hidden">
                                <div class="flex">
                                    <div class="w-2/3 pr-6">
                                        <h3 class="text-xl font-bold mb-2 text-primary">Compliance</h3>
                                        <p class="text-gray-600 mb-4">Stay compliant with regulatory requirements. Automate reporting, track certifications, and maintain audit trails.</p>
                                        <span class="inline-block px-4 py-2 bg-accent text-white rounded-md hover:bg-accent/90 transition cursor-pointer menu-item" data-page="checklists">Know More</span>
                                    </div>
                                    <div class="w-1/3 relative">
                                        <div class="bg-primary rounded-lg overflow-hidden h-40">
                                            <img class="object-cover w-full h-full opacity-50" src="https://storage.googleapis.com/uxpilot-auth.appspot.com/2e5a5c3ba1-d1b7ffe95ee76e6bf18c.png" alt="compliance dashboard with regulatory tracking and certification management">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <h4 class="text-white font-bold text-xl">Compliance Tools</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Burger Icon -->
            <button id="burgerBtn" class="md:hidden flex items-center text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Call to Action -->
            <div class="hidden md:flex items-center gap-5">
                <a href="tel:+61411114916" class="flex items-center gap-2 text-slate-700 font-medium"><i class="fa-solid fa-phone text-blue-600"></i> +61 0411 114 916</a>
                <a href="#contact"><span class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition cursor-pointer font-semibold">Book Free Demo</span></a>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden mt-4 space-y-4 fixed top-0 right-0 h-full w-64 bg-white shadow-lg z-50 p-6 overflow-y-auto">
            <a href="https://bizadmin.com.au/" class="block text-gray-700 hover:text-primary" data-page="homepage">Home</a>
            <a href="#feature-section" class="block text-gray-700 hover:text-primary" >Features</a>
            <a href="#benefits" class="block text-gray-700 hover:text-primary ">Benefits</a>
            <a href="#why-choose" class="block text-gray-700 hover:text-primary">Why Choose Us</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="hrm">HR & Onboarding</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="suppliers">Suppliers</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="catering">Ordering Portal</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="checklists">Checklists</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="cleaning">Cleaning Schedule</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="temperature">Temperature Recording</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="documents">Document Manage</a>
            <a href="#" class="block text-gray-700 hover:text-primary menu-item" data-page="cash">Cash Management</a>
            <a href="tel:+61411114916" class="block text-slate-700"><i class="fa-solid fa-phone text-blue-600 mr-1"></i> +61 0411 114 916</a>
            <a href="#contact" class="block text-white bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-md text-center ">Book Free Demo</a>
            <button id="close-menu" class="block text-gray-700 hover:text-primary">Close</button>
        </div>
    </div>
</header>