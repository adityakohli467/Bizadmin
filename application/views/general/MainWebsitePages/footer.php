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


        <div class="max-w-4xl mx-auto bg-white p-6 md:p-10 rounded-2xl shadow-xl border border-gray-100 text-left">
    <div class="mb-8 text-center">
        <h3 class="text-2xl font-bold text-primary mb-2">Get in touch</h3>
        <p class="text-gray-500 text-sm">Fill in your details and our team will reach out within one business day.</p>
    </div>
    <form id="contact-form" action="<?php echo site_url('home/submit'); ?>" method="POST" class="space-y-6">

        <!-- Grid container for all fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-left text-primary font-medium mb-2">Name</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="name" name="name" placeholder="Your full name" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" required>
                </div>
            </div>
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
        <button type="submit" id="submit-btn" class="w-full bg-accent hover:bg-orange-600 text-white font-semibold px-8 py-4 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-orange-500/20">
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
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const contactNumber = document.getElementById('contact_number').value.trim();
            const captcha = document.getElementById('captcha').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            

            if (!name || !email || !contactNumber || !captcha) {
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
  <footer id="footer" class="bg-white border-t border-gray-200 py-12">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center space-x-4 mb-6">
               <img class="logo-img" alt="bizadmin" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/logo.jpg" style="height: 36px;width: 130px;">
            </div>
            <p class="text-gray-600 mb-6">info@bizadmin.com.au<br>+61 0411 114 916</p>
            <div class="flex justify-center space-x-4 mb-8">
                <span class="text-gray-600 hover:text-primary transition-colors cursor-pointer">
                    <i class="text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-facebook-f" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook-f" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg></i>
                </span>
                
                <span class="text-gray-600 hover:text-primary transition-colors cursor-pointer">
                    <i class="text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-linkedin-in" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="linkedin-in" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg></i>
                </span>
                <span class="text-gray-600 hover:text-primary transition-colors cursor-pointer">
                    <i class="text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-instagram" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="instagram" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg></i>
                </span>
            </div>
            <p class="text-gray-500 text-sm">© <?php echo date('Y') ?> BizAdmin. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>