<section id="contact" class="py-20 bg-cream">
    <div class="container mx-auto px-4 text-center max-w-4xl">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-primary mb-6">Book a Free 30-Minute Demo</h2>
        <p class="text-[#4A5568] text-lg max-w-2xl mx-auto mb-10">Join caf&eacute; owners across Australia who have streamlined their operations with Bizadmin.</p>
        
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


        <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg">
    <form id="contact-form" action="<?php echo site_url('home/submit'); ?>" method="POST" class="space-y-6">
        
        <!-- Grid container for all fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label for="name" class="block text-left text-primary font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>
            <div>
                <label for="email" class="block text-left text-primary font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>
            
            <div>
                <label for="contact_number" class="block text-left text-primary font-medium mb-2">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"  title="Please enter a  phone number" required>
            </div>
            
             <div>
                    <label for="captcha" class="block text-left text-primary font-medium mb-2">CAPTCHA: Enter code <?php echo $captcha_question; ?></label>
                    <input type="text" id="captcha" name="captcha" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" pattern="[0-9]{4}" title="Please enter a 4-digit code" required>
                    <input type="hidden" name="captcha_answer" value="<?php echo $captcha_answer; ?>">
                </div>
          
          <div>
            <button type="submit" id="submit-btn" class="md:mt-5 w-full bg-primary hover:bg-orange-600 text-white font-medium px-8 py-4 rounded-lg transition-colors flex items-center justify-center">
                <span id="btn-text">Submit</span>
                <svg id="btn-loader" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z"></path>
                </svg>
            </button>
        </div>
        </div>

        <!-- Submit button -->
        

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
  <footer id="footer" class="bg-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-white/10 pb-12 mb-8">
                <div class="md:col-span-1">
                    <div class="flex items-center space-x-2 mb-6">
                        <img class="logo-img" alt="bizadmin" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/logo.jpg" style="height: 36px;width: 130px; filter: brightness(0) invert(1);">
                    </div>
                    <p class="text-gray-400 text-sm mb-6">The all-in-one operations platform built exclusively for independent Australian caf&eacute;s.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-instagram text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fa-brands fa-linkedin text-xl"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4 text-lg">Product</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#features" class="hover:text-accent transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-accent transition-colors">Pricing</a></li>
                        <li><a href="#contact" class="hover:text-accent transition-colors">Book a Demo</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4 text-lg">Resources</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-accent transition-colors">Fair Work Guide</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">HACCP Templates</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Help Centre</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Contact</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li>info@bizadmin.com.au</li>
                        <li>+61 0411 114 916</li>
                        <li>Sydney, Australia</li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; <?php echo date('Y') ?> Bizadmin. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>