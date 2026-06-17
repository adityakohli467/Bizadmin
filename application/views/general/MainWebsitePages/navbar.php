<script>
    if (typeof tailwind !== 'undefined') {
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#0D1B35",
                        accent: "#F2690D"
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

        // Close mobile menu on link click
        $mobileMenu.find('a').on('click', function() {
            $mobileMenu.removeClass('is-open');
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: target.offset().top - 80 }, 600);
            }
        });
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
            <nav class="hidden md:flex items-center space-x-8">
                <span class="text-gray-700 hover:text-primary cursor-pointer"><a href="<?php echo site_url('home'); ?>" class="active">Home</a></span>
                <span class="text-gray-700 hover:text-primary cursor-pointer"><a href="#features">Features</a></span>
                <span class="text-gray-700 hover:text-primary cursor-pointer"><a href="#pricing">Pricing</a></span>
                <span class="text-gray-700 hover:text-primary cursor-pointer"><a href="#why-bizadmin">Why Bizadmin</a></span>
                <span class="text-gray-700 hover:text-primary cursor-pointer"><a href="#contact">Book a Demo</a></span>
            </nav>

            <!-- Burger Icon -->
            <button id="burgerBtn" class="md:hidden flex items-center text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Call to Action -->
            <div class="hidden md:block">
                <a href="#pricing"><span class="bg-accent hover:bg-accent/90 text-white px-5 py-2 rounded-md transition cursor-pointer font-medium">Start Free Trial</span></a>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden mt-4 space-y-4 fixed top-0 right-0 h-full w-64 bg-white shadow-lg z-50 p-6 overflow-y-auto">
            <button id="close-menu" class="block text-gray-700 hover:text-primary mb-4 text-right w-full">
                <svg class="w-6 h-6 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <a href="<?php echo site_url('home'); ?>" class="block text-gray-700 hover:text-primary font-medium">Home</a>
            <a href="#features" class="block text-gray-700 hover:text-primary font-medium">Features</a>
            <a href="#pricing" class="block text-gray-700 hover:text-primary font-medium">Pricing</a>
            <a href="#why-bizadmin" class="block text-gray-700 hover:text-primary font-medium">Why Bizadmin</a>
            <a href="#contact" class="block text-gray-700 hover:text-primary font-medium">Book a Demo</a>
            <a href="#pricing" class="block text-white bg-accent hover:bg-accent/90 px-5 py-2 rounded-md text-center font-medium mt-4">Start Free Trial</a>
        </div>
    </div>
</header>