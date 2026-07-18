<?php
/**
 * BizAdmin main website landing page (café management platform).
 * Rebuilt to match approved design. Uses Tailwind + Font Awesome (loaded in header).
 * Missing photos/screenshots use styled placeholders that show the required
 * filename + dimensions until the real asset is dropped into:
 *   theme-assets/Landingpageassets/assets/landing/
 */
$landing_assets = base_url('theme-assets/Landingpageassets/assets/landing/');
?>

<div class="font-inter text-gray-800 bg-white overflow-hidden">

    <!-- ============================================================
         1. HERO
         ============================================================ -->
    <section id="hero" class="relative bg-gradient-to-b from-blue-50 via-blue-50/40 to-white pt-12 pb-16 md:pt-20 md:pb-24">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <!-- Left copy -->
                <div class="lg:w-1/2">
                    <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold tracking-wide uppercase px-3 py-1 rounded-full mb-5">AI Enabled Café Management Platform</span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight text-slate-900 mb-4">
                        AI enabled system<br>to grow<br>
                        <span class="text-blue-600">your business</span>
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 max-w-xl">One intelligent platform to manage orders, POS, staff, bookings, inventory, loyalty, marketing and more — while reducing costs and increasing profits.</p>

                    <!-- Feature bullets -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 mb-8 max-w-xl">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-600 flex-shrink-0"><i class="fa-solid fa-circle-dollar-to-slot"></i></span>
                            <span class="text-sm font-medium text-slate-700">No Commission<br>Online Ordering</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 flex-shrink-0"><i class="fa-solid fa-robot"></i></span>
                            <span class="text-sm font-medium text-slate-700">AI Assistant<br>24/7 Support</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 text-red-500 flex-shrink-0"><i class="fa-brands fa-google"></i></span>
                            <span class="text-sm font-medium text-slate-700">Google Order &amp;<br>Reserve Partner</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-1 w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100 text-amber-500 flex-shrink-0"><i class="fa-solid fa-gift"></i></span>
                            <span class="text-sm font-medium text-slate-700">Reward &amp; Loyalty<br>Increase Repeat Sales</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="#contact" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-7 py-3.5 rounded-lg shadow-lg shadow-blue-600/20 transition">Book a Free Demo</a>
                        <a href="#features" class="inline-flex items-center gap-2 border border-slate-300 hover:border-blue-600 text-slate-700 hover:text-blue-600 font-semibold px-7 py-3.5 rounded-lg transition">
                            See How It Works <i class="fa-solid fa-circle-play"></i>
                        </a>
                    </div>

                    <!-- Trust -->
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-3">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <span class="w-10 h-10 rounded-full ring-2 ring-white bg-slate-200 overflow-hidden inline-flex items-center justify-center text-slate-400 text-xs">
                                <img src="<?php echo $landing_assets; ?>trust-avatar-<?php echo $i; ?>.jpg" alt="Café owner" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentNode.innerHTML='<i class=&quot;fa-solid fa-user&quot;></i>';">
                            </span>
                            <?php endfor; ?>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Trusted by 500+ cafés across Australia</p>
                            <p class="text-sm text-amber-500">★★★★★ <span class="text-slate-500">4.9/5</span></p>
                        </div>
                    </div>
                </div>

                <!-- Right hero image -->
                <div class="lg:w-1/2 w-full relative">
                    <div class="relative rounded-2xl overflow-hidden flex items-center justify-center">
                        <img src="<?php echo $landing_assets; ?>hero-product.png" alt="BizAdmin dashboard and Bizzy AI on mobile" class="relative z-10 w-full h-auto object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="absolute inset-0 hidden items-center justify-center text-center text-slate-400 text-sm p-6 bg-slate-100 min-h-[340px]" style="flex-direction:column;">
                            <i class="fa-solid fa-image text-3xl mb-2"></i>
                            <span class="font-semibold">hero-product.png</span>
                            <span class="text-xs">1280 × 840 (dashboard + phone)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         2. PRICING COMPARISON
         ============================================================ -->
    <section class="py-14 md:py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 md:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Intro -->
                    <div class="lg:col-span-3">
                        <h2 class="text-2xl font-bold text-slate-900 mb-3">Why pay separate fees for separate services?</h2>
                        <p class="text-gray-600 text-sm">BizAdmin offers entire café solution at one place — no hidden fees, no extra costs!</p>
                    </div>

                    <!-- Individual costs -->
                    <div class="lg:col-span-7">
                        <div class="bg-white border border-slate-200 rounded-2xl px-3 py-5">
                            <div class="flex items-stretch justify-between">
                                <?php
                                $services = [
                                    ['POS System', '$80', 'fa-cash-register'],
                                    ['Online Ordering', '$120', 'fa-basket-shopping'],
                                    ['Reservations', '$90', 'fa-calendar-check'],
                                    ['Loyalty Program', '$75', 'fa-crown'],
                                    ['Marketing Tools', '$150', 'fa-bullhorn'],
                                    ['AI Chatbot', '$200', 'fa-robot'],
                                    ['HR & Payroll', '$80', 'fa-sitemap'],
                                    ['Inventory', '$120', 'fa-boxes-stacked'],
                                ];
                                $last = count($services) - 1;
                                foreach ($services as $idx => $s): ?>
                                    <div class="flex-1 text-center px-1">
                                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-slate-100 flex items-center justify-center text-blue-600 text-sm">
                                            <i class="fa-solid <?php echo $s[2]; ?>"></i>
                                        </div>
                                        <p class="text-[10px] leading-tight text-slate-500 mb-1"><?php echo $s[0]; ?></p>
                                        <p class="text-sm font-extrabold text-slate-900 leading-none"><?php echo $s[1]; ?></p>
                                        <p class="text-[9px] text-slate-400 mt-0.5">/month</p>
                                    </div>
                                    <?php if ($idx !== $last): ?><div class="flex items-center text-slate-300 font-semibold text-sm">+</div><?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="lg:col-span-2">
                        <div class="bg-blue-50 rounded-2xl p-4 text-center h-full flex flex-col justify-center">
                            <p class="text-xs font-semibold text-blue-600 mb-1">Total Cost</p>
                            <p class="text-3xl font-extrabold text-blue-600 leading-none">$915</p>
                            <p class="text-xs text-slate-400 mt-1">/month</p>
                        </div>
                    </div>
                </div>

                <!-- Everything included banner -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-7">
                        <div class="bz-arrow flex items-center justify-center text-white font-bold text-base md:text-lg" style="clip-path:polygon(0 16%, 84% 16%, 84% 0, 100% 50%, 84% 100%, 84% 84%, 0 84%);min-height:70px;">
                            <span class="pr-[12%]">Everything Included with BizAdmin</span>
                        </div>
                    </div>
                    <div class="lg:col-span-5">
                        <div class="flex items-center gap-4">
                            <p class="text-lg font-extrabold text-slate-900 leading-tight">One Affordable<br>Platform</p>
                            <div>
                                <p class="text-4xl font-extrabold text-slate-900 leading-none">$149<span class="text-base font-normal text-slate-400">/month</span></p>
                                <span class="inline-block mt-2 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">Save $766 Every Month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
      .bz-arrow{background:#1d4ed8;}
    </style>

    <!-- ============================================================
         3. POWERFUL FEATURES
         ============================================================ -->
    <section id="features" class="py-14 md:py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-900 mb-12">Powerful Features That Help You Grow Faster</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php
                $features = [
                    ['fa-solid fa-motorcycle', 'text-purple-600', 'Commission-Free Delivery', 'Keep 100% of your profits. Zero commission on online orders.'],
                    ['fa-solid fa-calendar-check', 'text-blue-600', 'Accept Bookings 24/7', 'Take online reservations anytime, anywhere. Never miss a customer.'],
                    ['fa-solid fa-gift', 'text-red-500', 'Boost Sales with Rewards', 'Easy reward points system to turn first-time customers into loyal fans.'],
                    ['fa-solid fa-bullhorn', 'text-red-500', 'Free Marketing Assistance', 'Get personalised marketing support and grow your business — for free!'],
                    ['fa-solid fa-robot', 'text-blue-600', 'Bizzy AI – 24/7 Assistant', 'Handles customer queries, recommends, upsell and helps checkout in 2 clicks.'],
                    ['fa-brands fa-google', 'text-slate-700', 'Google Partner', 'Official Order &amp; Reserve with Google Partner. Get discovered. Get more orders.'],
                ];
                foreach ($features as $f): ?>
                    <div class="bg-white border border-slate-100 rounded-2xl px-4 py-6 text-center hover:shadow-md transition">
                        <div class="text-3xl mb-4 <?php echo $f[1]; ?>">
                            <i class="<?php echo $f[0]; ?>"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-2 leading-snug"><?php echo $f[2]; ?></h3>
                        <p class="text-xs text-gray-500 leading-relaxed"><?php echo $f[3]; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         4. MEET BIZZY AI
         ============================================================ -->
    <section class="py-14 md:py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="bizzy-ai relative rounded-3xl bg-[#0f1e3d] text-white overflow-hidden p-8 md:p-12"
                 style="background-image:radial-gradient(circle at 15% 15%, rgba(59,130,246,.28), transparent 45%),radial-gradient(circle at 85% 90%, rgba(37,99,235,.18), transparent 45%);">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-center">

                    <!-- Robot -->
                    <div class="lg:col-span-3 flex justify-center">
                        <div class="relative w-48 h-60 md:w-56 md:h-72 flex items-center justify-center">
                            <img src="<?php echo $landing_assets; ?>bizzy-robot.png" alt="Bizzy AI assistant" class="relative z-10 max-h-full object-contain drop-shadow-2xl" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="absolute inset-0 hidden items-center justify-center text-center text-blue-200/70 text-sm border border-dashed border-blue-300/30 rounded-2xl" style="flex-direction:column;">
                                <i class="fa-solid fa-robot text-4xl mb-2"></i>
                                <span class="font-semibold">bizzy-robot.png</span>
                                <span class="text-xs">480 × 600 (transparent)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Heading + tags -->
                    <div class="lg:col-span-4">
                        <h2 class="text-3xl md:text-4xl font-bold mb-1">Meet Bizzy AI</h2>
                        <p class="text-white text-xl md:text-2xl font-semibold mb-4">Your 24/7 Café Assistant</p>
                        <p class="text-blue-100/80 text-sm md:text-base mb-6">Bizzy AI handles customer questions, helps with orders, takes bookings, recommends items, and makes checkout incredibly easy — all in just 2 clicks.</p>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $tags = [
                                ['fa-bolt', 'Instant Responses'],
                                ['fa-arrows-rotate', 'Smart Recommendations'],
                                ['fa-tag', 'Upsell &amp; Offers'],
                                ['fa-truck-fast', 'Order Tracking'],
                                ['fa-language', 'Multi-language Support'],
                                ['fa-hand-pointer', 'Easy 2-Click Checkout'],
                            ];
                            foreach ($tags as $t): ?>
                                <span class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-3 py-1.5 text-xs whitespace-nowrap">
                                    <i class="fa-solid <?php echo $t[0]; ?> text-blue-300"></i> <?php echo $t[1]; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Middle: chat conversation -->
                    <div class="lg:col-span-3 flex flex-col gap-3">
                        <div class="bz-bubble bz-user self-end">Can I book a table for tonight?</div>
                        <div class="bz-bubble bz-bot self-start">Sure! How about 7 PM for 4 people?</div>
                        <div class="bz-bubble bz-user self-end">Yes, that works. Also, any specials?</div>
                        <div class="bz-bubble bz-bot self-start">Yes! Today's special is Truffle Pasta. Shall I add it to your order?</div>
                        <div class="bz-bubble bz-user self-end">Yes please! Proceed to checkout.</div>
                    </div>

                    <!-- Right: action cards -->
                    <div class="lg:col-span-2 flex flex-col gap-4">
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                            <p class="text-sm font-semibold">2-Click Checkout</p>
                            <p class="text-xs text-blue-200/70 mb-3">Fast, simple and secure</p>
                            <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-lg flex items-center justify-center gap-2">Order Now <i class="fa-solid fa-cart-shopping"></i></button>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                            <p class="text-sm font-semibold">Happy Customer</p>
                            <p class="text-xs text-blue-200/70 mb-1">95% satisfaction rate</p>
                            <p class="text-amber-400 text-base tracking-wide">★★★★★</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
      .bizzy-ai .bz-bubble{max-width:88%;padding:10px 14px;font-size:13px;line-height:1.45;position:relative;border-radius:16px;}
      .bizzy-ai .bz-user{background:#2563eb;color:#fff;border-top-right-radius:2px;}
      .bizzy-ai .bz-bot{background:#ffffff;color:#334155;border-top-left-radius:2px;}
      .bizzy-ai .bz-user::after{content:"";position:absolute;top:5px;right:-7px;width:0;height:0;border-top:7px solid transparent;border-bottom:7px solid transparent;border-left:8px solid #2563eb;}
      .bizzy-ai .bz-bot::after{content:"";position:absolute;top:5px;left:-7px;width:0;height:0;border-top:7px solid transparent;border-bottom:7px solid transparent;border-right:8px solid #ffffff;}
    </style>

    <!-- ============================================================
         5. REAL RESULTS
         ============================================================ -->
    <section class="py-14 md:py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-900 mb-12">Real Results. Real Impact.</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-center">
                <?php
                $stats = [
                    ['fa-crown', 'text-amber-500', '500+', 'Cafés Trust BizAdmin'],
                    ['fa-arrow-trend-up', 'text-green-500', '+28%', 'Average Revenue Increase'],
                    ['fa-chart-line', 'text-blue-500', '+63%', 'Increase in Online Orders'],
                    ['fa-shield-halved', 'text-purple-500', '-25%', 'Operational Costs'],
                    ['fa-clock', 'text-blue-500', '18 hrs', 'Time Saved Every Week'],
                    ['fa-headset', 'text-red-500', '24/7', 'Customer Support'],
                ];
                foreach ($stats as $s): ?>
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                        <i class="fa-solid <?php echo $s[0]; ?> <?php echo $s[1]; ?> text-2xl mb-2"></i>
                        <p class="text-2xl font-extrabold text-slate-900"><?php echo $s[2]; ?></p>
                        <p class="text-xs text-slate-500 mt-1"><?php echo $s[3]; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         6. ALL-IN-ONE SOLUTION GRID
         ============================================================ -->
    <section class="py-14 md:py-20 bg-slate-50">
        <div class="container mx-auto px-4 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-900 mb-12">All-in-One Café Management Solution</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4">
                <?php
                $modules = [
                    ['fa-cash-register', 'text-blue-500', 'POS &amp; Billing'],
                    ['fa-basket-shopping', 'text-purple-500', 'Online Ordering'],
                    ['fa-calendar-check', 'text-red-500', 'Reservations'],
                    ['fa-display', 'text-green-500', 'Kitchen Display'],
                    ['fa-boxes-stacked', 'text-amber-500', 'Inventory'],
                    ['fa-book-open', 'text-blue-500', 'Recipes &amp; Menu Costing'],
                    ['fa-users', 'text-purple-500', 'Staff &amp; Roster'],
                    ['fa-file-export', 'text-green-500', 'Payroll Export'],
                    ['fa-temperature-half', 'text-red-500', 'Temperature Logs'],
                    ['fa-broom', 'text-blue-500', 'Cleaning Tasks'],
                    ['fa-sack-dollar', 'text-amber-500', 'Cash Management'],
                    ['fa-file-lines', 'text-slate-500', 'Documents'],
                    ['fa-chart-pie', 'text-purple-500', 'Analytics &amp; Reports'],
                    ['fa-bullhorn', 'text-red-500', 'Marketing Tools'],
                    ['fa-star', 'text-amber-500', 'Loyalty &amp; Rewards'],
                    ['fa-gift', 'text-green-500', 'Gift Cards'],
                ];
                foreach ($modules as $m): ?>
                    <div class="bg-white rounded-xl p-4 text-center border border-slate-100 hover:shadow-md hover:-translate-y-0.5 transition">
                        <i class="fa-solid <?php echo $m[0]; ?> <?php echo $m[1]; ?> text-xl mb-2"></i>
                        <p class="text-xs font-medium text-slate-600 leading-tight"><?php echo $m[2]; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         7. THREE PROMO CARDS
         ============================================================ -->
    <section class="py-14 md:py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Card 1: Google Partner -->
                <div class="bg-[#0f1e3d] text-white rounded-2xl overflow-hidden flex flex-col sm:flex-row">
                    <div class="p-6 sm:w-1/2 flex flex-col">
                        <h3 class="text-lg font-bold mb-2 leading-snug">We're an Official Order &amp; Reserve with Google Partner</h3>
                        <p class="text-blue-100/70 text-xs mb-5">Get discovered on Google Search, Maps and Assistant. More visibility, more bookings, more orders.</p>
                        <span class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-semibold px-4 py-2 rounded-lg self-start mt-auto">
                            <svg class="w-5 h-5" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/>
                                <path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>
                                <path fill="#FBBC05" d="M11.69 28.18C11.25 26.86 11 25.45 11 24s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24s.85 6.91 2.34 9.88l7.35-5.7z"/>
                                <path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/>
                            </svg> Google Partner
                        </span>
                    </div>
                    <div class="sm:w-1/2 relative bg-slate-800/40 min-h-[240px]">
                        <img src="<?php echo $landing_assets; ?>promo-google-map.png" alt="Café on Google Maps" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="absolute inset-0 hidden items-center justify-center text-center text-blue-200/60 text-xs" style="flex-direction:column;">
                            <i class="fa-solid fa-map-location-dot text-2xl mb-1"></i>
                            <span class="font-semibold">promo-google-map.png</span>
                            <span>520 × 400</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Marketing -->
                <div class="bg-green-50 rounded-2xl overflow-hidden flex flex-col sm:flex-row">
                    <div class="p-6 sm:w-1/2 flex flex-col justify-center">
                        <h3 class="text-lg font-bold text-slate-900 mb-3 leading-snug">Personalized Sales &amp; Marketing Assistance for Free</h3>
                        <ul class="space-y-2.5">
                            <?php foreach (['SEO &amp; Google My Business', 'Social Media Strategy', 'Promotions &amp; Campaigns', 'Menu &amp; Pricing Optimization'] as $li): ?>
                                <li class="flex items-center gap-2 text-sm text-slate-700"><i class="fa-solid fa-circle-check text-green-500"></i> <?php echo $li; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="sm:w-1/2 relative bg-green-100 min-h-[240px]">
                        <img src="<?php echo $landing_assets; ?>promo-marketing-woman.jpg" alt="Marketing assistant" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="absolute inset-0 hidden items-center justify-center text-center text-slate-400 text-xs" style="flex-direction:column;">
                            <i class="fa-solid fa-user-tie text-2xl mb-1"></i>
                            <span class="font-semibold">promo-marketing-woman.jpg</span>
                            <span>660 × 600</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Commission Free -->
                <div class="bg-orange-50 rounded-2xl overflow-hidden flex flex-col sm:flex-row">
                    <div class="p-6 sm:w-1/2 flex flex-col justify-center">
                        <h3 class="text-lg font-bold text-slate-900 mb-3 leading-snug">Keep More of Every Order with Commission-Free Online Ordering</h3>
                        <p class="text-slate-600 text-sm mb-5">Create your own branding, build customer loyalty and increase profits. No middleman. No commission.</p>
                        <span class="inline-flex items-center gap-2 bg-white rounded-xl px-4 py-3 self-start shadow-sm">
                            <span class="text-2xl font-extrabold text-green-600 leading-none">0%</span>
                            <span class="text-sm font-semibold text-green-600">Commission</span>
                        </span>
                    </div>
                    <div class="sm:w-1/2 relative bg-orange-100 min-h-[240px]">
                        <img src="<?php echo $landing_assets; ?>promo-commission-food.png" alt="Commission-free ordering" class="absolute inset-0 w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="absolute inset-0 hidden items-center justify-center text-center text-slate-400 text-xs" style="flex-direction:column;">
                            <i class="fa-solid fa-mobile-screen text-2xl mb-1"></i>
                            <span class="font-semibold">promo-commission-food.png</span>
                            <span>600 × 520</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         8. TESTIMONIALS
         ============================================================ -->
    <section class="py-14 md:py-20 bg-slate-50">
        <div class="container mx-auto px-4 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-900 mb-12">Loved by Café Owners Across Australia</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $testimonials = [
                    ['testimonial-1.jpg', 'James Walker', '"BizAdmin increased our online orders by 60% in just 3 months. The AI assistant and booking system are game changers!"'],
                    ['testimonial-2.jpg', 'Sarah Mitchell', '"We save 18 hours every week and our staff love how easy everything is. Best decision for our business."'],
                    ['testimonial-3.jpg', 'Michael Chen', '"The reward points system brought back so many customers. Plus the marketing support is unbelievable!"'],
                ];
                foreach ($testimonials as $t): ?>
                    <div class="bg-white rounded-2xl p-7 border border-slate-100 shadow-sm">
                        <p class="text-amber-400 mb-3">★★★★★</p>
                        <p class="text-slate-600 text-sm mb-6"><?php echo $t[2]; ?></p>
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-full bg-slate-200 overflow-hidden inline-flex items-center justify-center text-slate-400">
                                <img src="<?php echo $landing_assets . $t[0]; ?>" alt="<?php echo $t[1]; ?>" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentNode.innerHTML='<i class=&quot;fa-solid fa-user&quot;></i>';">
                            </span>
                            <p class="font-semibold text-slate-800"><?php echo $t[1]; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</div>
