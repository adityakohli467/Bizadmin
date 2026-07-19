<section id="contact" class="py-20 bg-light">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-primary mb-6">Ready to transform your business operations?</h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto mb-10">Join thousands of businesses that have streamlined their operations with BizAdmin.</p>
        
      <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4 mb-10">
    
    <!-- Email Button -->
    <span class="bg-accent hover:bg-orange-600 text-white font-medium px-8 py-4 rounded-lg transition-colors cursor-pointer flex items-center justify-center space-x-2">
        <i class="fas fa-envelope text-white text-lg"></i>
        <span>info@bizadmin.com.au</span>
    </span>

    <!-- Phone Button -->
    <span class="bg-white border border-primary text-primary hover:bg-primary/5 font-medium px-8 py-4 rounded-lg transition-colors cursor-pointer flex items-center justify-center space-x-2">
        <i class="fas fa-phone text-primary text-lg"></i>
        <span>+61 0411 114 916</span>
    </span>

</div>


        <div class="w-full mx-auto bg-white p-6 md:p-10 rounded-2xl border border-gray-100 text-left">
    <div class="mb-8 text-center">
        <h3 class="text-2xl font-bold text-primary mb-2">Get in touch</h3>
        <p class="text-gray-500 text-sm">Fill in your details and our team will reach out within one business day.</p>
    </div>
    <form id="contact-form" action="<?php echo site_url('home/submit'); ?>" method="POST" class="space-y-6">

        <!-- Grid container for all fields -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="email" class="block text-left text-primary font-medium mb-2">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" id="email" name="email" placeholder="you@example.com" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" required>
                </div>
            </div>

            <div>
                <label for="contact_number" class="block text-left text-primary font-medium mb-2">Contact Number</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="contact_number" name="contact_number" placeholder="+61 4XX XXX XXX" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" title="Please enter a  phone number" required>
                </div>
            </div>

            <div>
                <label for="captcha" class="block text-left text-primary font-medium mb-2">CAPTCHA: Enter code <?php echo $captcha_question; ?></label>
                <div class="relative">
                    <i class="fas fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="captcha" name="captcha" placeholder="4-digit code" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" pattern="[0-9]{4}" title="Please enter a 4-digit code" required>
                    <input type="hidden" name="captcha_answer" value="<?php echo $captcha_answer; ?>">
                </div>
            </div>
        </div>

        <!-- Submit button -->
        <button type="submit" id="submit-btn" class="w-full bg-accent hover:bg-orange-600 text-white font-semibold px-8 py-4 rounded-xl transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i>
            <span id="btn-text">Send Message</span>
            <svg id="btn-loader" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"></path>
            </svg>
        </button>

        <div id="form-message" class="hidden text-center text-lg"></div>
    </form>
</div>


    </div>
</section>
<script>




    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');
        const formMessage = document.getElementById('form-message');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Client-side validation
            const email = document.getElementById('email').value.trim();
            const contactNumber = document.getElementById('contact_number').value.trim();
            const captcha = document.getElementById('captcha').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            

            if (!email || !contactNumber || !captcha) {
                formMessage.classList.remove('hidden', 'text-green-600');
                formMessage.classList.add('text-red-600');
                formMessage.textContent = 'Please fill out all fields.';
                return;
            }
            if (!emailRegex.test(email)) {
                formMessage.classList.remove('hidden', 'text-green-600');
                formMessage.classList.add('text-red-600');
                formMessage.textContent = 'Please enter a valid email address.';
                return;
            }
            

            // Show loader
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
            submitBtn.disabled = true;

            // Submit form via AJAX
            
            // form controller code is in application/controllers/Home.php
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
                submitBtn.disabled = false;

                formMessage.classList.remove('hidden');
                if (data.success) {
                    formMessage.classList.remove('text-red-600');
                    formMessage.classList.add('text-green-600');
                    formMessage.textContent = 'Your message has been sent successfully!';
                    form.reset();
                    if (typeof gtag_report_conversion === 'function') {
                        gtag_report_conversion();
                    }
                } else {
                    formMessage.classList.remove('text-green-600');
                    formMessage.classList.add('text-red-600');
                    formMessage.textContent = data.message || 'An error occurred. Please try again.';
                }
            })
            .catch(error => {
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
                submitBtn.disabled = false;
                formMessage.classList.remove('hidden', 'text-green-600');
                formMessage.classList.add('text-red-600');
                formMessage.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            });
        });
    });
</script>
    
    <!-- Footer -->
  <footer id="footer" class="bg-white border-t border-gray-200 pt-14 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-8">

                <!-- Brand column -->
                <div class="lg:col-span-2">
                    <img class="logo-img" alt="bizadmin" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/logo.jpg" style="height: 36px;width: 130px;">
                    <p class="text-gray-500 text-sm mt-4 max-w-xs leading-relaxed">The all-in-one platform to run, grow and manage your café — orders, bookings, HR, compliance and more.</p>
                    <div class="mt-5 space-y-1 text-sm text-gray-600">
                        <p><i class="fas fa-envelope text-primary mr-2"></i>info@bizadmin.com.au</p>
                        <p><i class="fas fa-phone text-primary mr-2"></i>+61 0411 114 916</p>
                    </div>
                    <div class="flex space-x-3 mt-5">
                        <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-primary hover:text-white text-gray-600 flex items-center justify-center transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="LinkedIn" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-primary hover:text-white text-gray-600 flex items-center justify-center transition-colors"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-primary hover:text-white text-gray-600 flex items-center justify-center transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Product -->
                <div>
                    <h4 class="text-primary font-semibold mb-4">Product</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a href="#features" class="hover:text-primary transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-primary transition-colors">Pricing</a></li>
                        <li><a href="#features" class="hover:text-primary transition-colors">AI Assistant</a></li>
                        <li><a href="#contact" class="hover:text-primary transition-colors">Book a Demo</a></li>
                    </ul>
                </div>

                <!-- Solutions -->
                <div>
                    <h4 class="text-primary font-semibold mb-4">Solutions</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-primary transition-colors">HR &amp; Onboarding</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Suppliers</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Ordering Portal</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Checklists</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="text-primary font-semibold mb-4">Company</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="#contact" class="hover:text-primary transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Success Stories</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Resources</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-gray-500 text-sm">© <?php echo date('Y') ?> BizAdmin. All rights reserved.</p>
                <div class="flex gap-5 text-sm text-gray-500">
                    <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-primary transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>