<script>
    if (typeof tailwind !== 'undefined') {
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#1A2942",
                        accent: "#F05D5E",
                        coral: "#FF7A59",
                        cream: "#FFF9F2",
                        sand: "#F7EBDD"
                    },
                    fontFamily: {
                        inter: ["Inter", "sans-serif"],
                        sans: ["Inter", "sans-serif"],
                        display: ["Gelasio", "serif"]
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

<header id="header" class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-2 cursor-pointer">
                <a href="<?php echo site_url('home'); ?>">
                    <img class="logo-img" alt="bizadmin" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/logo.jpg" style="height: 36px;width: 130px;">
                </a>
            </div>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex space-x-8">
                <a href="#features" class="text-[#4A5568] hover:text-accent font-medium transition-colors">Features</a>
                <a href="#problem" class="text-[#4A5568] hover:text-accent font-medium transition-colors">Why Bizadmin</a>
                <a href="#pricing" class="text-[#4A5568] hover:text-accent font-medium transition-colors">Pricing</a>
                <a href="#timeline" class="text-[#4A5568] hover:text-accent font-medium transition-colors">Stories</a>
            </nav>

            <!-- Call to Action -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="#contact" class="text-primary font-medium hover:text-accent transition-colors">Book a Demo</a>
                <a href="#pricing" class="bg-accent hover:bg-coral text-white px-6 py-2.5 rounded-full font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Start Free Trial</a>
            </div>

            <!-- Burger Icon -->
            <button id="burgerBtn" class="md:hidden flex items-center text-primary focus:outline-none">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden fixed top-0 right-0 h-full w-64 bg-white shadow-lg z-50 p-6 overflow-y-auto">
        <button id="close-menu" class="block text-primary hover:text-accent mb-6 text-right w-full">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
        <a href="<?php echo site_url('home'); ?>" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Home</a>
        <a href="#features" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Features</a>
        <a href="#problem" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Why Bizadmin</a>
        <a href="#pricing" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Pricing</a>
        <a href="#timeline" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Stories</a>
        <a href="#contact" class="block text-primary hover:text-accent font-medium py-3 border-b border-gray-100">Book a Demo</a>
        <a href="#pricing" class="block text-white bg-accent hover:bg-coral px-5 py-3 rounded-full text-center font-medium mt-6 shadow-md">Start Free Trial</a>
    </div>
</header>