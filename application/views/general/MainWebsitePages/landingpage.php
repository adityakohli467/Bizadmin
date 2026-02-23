 <!-- Hero Section -->
 <div class="font-inter text-gray-800 ">
   <section id="hero" class="pt-12 pb-16 md:pt-32 md:pb-24 bg-light">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 md:pr-12">
                    <span class="text-accent font-semibold">AI-powered Workforce Intelligence Platform</span>
                    <h1 class="text-4xl md:text-5xl font-bold text-primary mt-3 mb-6">All-in-One Admin System for Smooth Business Operations.</h1>
                    <p class="text-gray-600 text-lg mb-8">Streamline your business operations with our comprehensive system that handles task management, schedules, HR processes, and more—all in one place.</p>
                    <div class="flex space-x-4">
                        <a href="#contact-form"><span class="bg-primary hover:bg-primary/90 text-white font-medium px-6 py-3 rounded-lg transition-colors cursor-pointer">Let's Talk</span></a>
                        <a href="#feature-section"><span class="text-primary border border-primary hover:bg-primary/5 font-medium px-6 py-3 rounded-lg transition-colors cursor-pointer">Discover Features</span></a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img class="w-full max-w-lg rounded-lg" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/banner-image.png" alt="modern laptop mockup showing business admin dashboard interface with dark blue and orange color scheme, professional UI">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid Section -->
    <section id="feature-section" class="bg-primary py-16 lg:py-24">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-accent mb-4">Everything you need to build a great business.</h2>
                <p class="text-white text-lg max-w-2xl mx-auto">Streamline operations and boost productivity with our comprehensive solution.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature Card 1 -->

                <div class="bg-white rounded-lg p-6 shadow-lg">
    <div class="text-accent mb-4">
        <i class="fa-solid fa-users text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold mb-3">Human Resources</h3>
    <p class="text-gray-600 mb-4">
        Streamline staff scheduling and timesheets with AI-powered workforce intelligence. 
        Automate roster optimization, detect overtime risks, monitor attendance patterns, 
        and gain real-time insights into employee productivity — all in one smart dashboard.
    </p>
    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
        <a href="javascript:void(0)" onclick="toggleAiFeatures()" class="flex items-center">
    Explore AI HR Features <i class="fa-solid fa-chevron-down ml-2 text-xs" id="aiFeatureChevron"></i>
</a>

    </span>
</div>

<div id="aiFeatures" class="hidden mt-6 bg-gray-50 p-6 rounded-lg shadow-inner">
    <h4 class="text-lg font-bold mb-4">AI-Powered Features</h4>

    <ul class="space-y-3 text-gray-700">
        <li>🤖 Smart Overtime Alerts – Automatically detect employees exceeding weekly hour limits.</li>
        <li>📊 Attendance Pattern Insights – Identify frequent late arrivals and absentee trends.</li>
        <li>🧠 Leave Pattern Detection – Highlight unusual leave behavior (e.g., repeated Mondays/Fridays).</li>
        <li>💬 Natural Language Dashboard – Ask questions like “Who worked more than 45 hours last week?”</li>
        <li>📈 Workforce Summary Reports – Auto-generate payroll and shift summaries instantly.</li>
    </ul>
</div>


<script>
function toggleAiFeatures() {
    const section = document.getElementById('aiFeatures');
    const chevron = document.getElementById('aiFeatureChevron');
    section.classList.toggle('hidden');
    if (chevron) chevron.classList.toggle('rotate-180');
}
</script>


                <!-- Feature Card 2 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-temperature-low text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Temperature Recording</h3>
                    <p class="text-gray-600 mb-4">Record and monitor daily temperature readings to maintain compliance.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
                
                <!-- Feature Card 3 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-broom text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Cleaning Tasks Management</h3>
                    <p class="text-gray-600 mb-4">Assign tasks and capture visual proof of completion with photos.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
                
                <!-- Feature Card 4 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-boxes-stacked text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Supplies &amp; Inventory</h3>
                    <p class="text-gray-600 mb-4">Know what's in stock, what's low, and what needs ordering.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                     <a href="https://supplier.bizadmin.com.au/">   </a>
                    </span>
                </div>
                
                <!-- Feature Card 5 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-list-check text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Checklist Management</h3>
                    <p class="text-gray-600 mb-4">Ensure staff are performing tasks to standard and at the right time.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
                
                <!-- Feature Card 6 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-file-lines text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Document Management</h3>
                    <p class="text-gray-600 mb-4">Store business-wide policies and documents all in one place.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
                
                <!-- Feature Card 7 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-cash-register text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Cash Management</h3>
                    <p class="text-gray-600 mb-4">Track finances and manage cash flow to optimize your business performance.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
                
                <!-- Feature Card 8 -->
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="text-accent mb-4">
                        <i class="fa-solid fa-cart-shopping text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Ordering Admin</h3>
                    <p class="text-gray-600 mb-4">Simplify your ordering process with automated purchase orders and tracking.</p>
                    <span class="text-primary font-medium hover:text-accent transition flex items-center cursor-pointer">
                        
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Storytelling Timeline Section -->
  <section id="timeline" class="py-20 bg-white">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl text-center text-[#132f65] mb-2">Alex’s Café, Reimagined with Bizadmin</h2>
      <p class="text-center text-neutral-600 mb-16 max-w-2xl mx-auto">Follow Alex's journey from overwhelmed café manager to operational excellence with Bizadmin.</p>
      
      <div class="relative">
        <!-- Vertical timeline line -->
        <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-neutral-200 z-0"></div>
        
        <!-- Timeline Item 1 -->
        <div class="relative z-10 mb-20">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 md:pr-12 mb-8 md:mb-0 md:text-right">
              <h3 class="text-xl text-[#132f65] mb-3">A Dream in Peril</h3>
              <p class="text-neutral-600 mb-4">Alex opened the café with passion—fresh brews, cozy vibes, and a community hub. But behind the smiles and espresso shots, chaos brewed. Paper rosters vanished, supplier calls interrupted rush hours, and hygiene checklists went unchecked.</p>
              <blockquote class="italic text-neutral-700 border-l-4 border-[#f97316] pl-4 md:border-l-0 md:border-r-4 md:pr-4 md:pl-0">"Running a café was my dream, but I was drowning in admin work. I barely had time to enjoy the coffee I served."</blockquote>
            </div>
            <div class="w-10 h-10 bg-[#f97316] rounded-full flex items-center justify-center text-white">1</div>
            <div class="md:w-1/2 md:pl-12">
              <div class=" rounded-lg h-48 flex items-center justify-center">
                <img alt="A Dream in Peril" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/story/1-story.png">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Timeline Item 2 -->
        <div class="relative z-10 mb-20">
          <div class="flex flex-col md:flex-row-reverse items-center">
            <div class="md:w-1/2 md:pl-12 mb-8 md:mb-0 md:text-left">
              <h3 class="text-xl text-[#132f65] mb-3">A Spark of Hope</h3>
              <p class="text-neutral-600 mb-4">At a networking event, Alex met Maya, another café owner whose face radiated peace. She introduced Alex to Bizadmin—a complete café management system designed to simplify operations.</p>
              <blockquote class="italic text-neutral-700 border-l-4 border-[#f97316] pl-4">"I couldn’t believe someone had actually built something for *us* café folks. Maya swore by it. I figured—why not?"</blockquote>
            </div>
            <div class="w-10 h-10 bg-[#f97316] rounded-full flex items-center justify-center text-white">2</div>
            <div class="md:w-1/2 md:pr-12">
              <div class=" rounded-lg h-48 flex items-center justify-center">
                <img alt="A Spark of Hope" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/story/2-story.png">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Timeline Item 3 -->
        <div class="relative z-10 mb-20">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 md:pr-12 mb-8 md:mb-0 md:text-right">
              <h3 class="text-xl text-[#132f65] mb-3">Plugging in the Power</h3>
              <p class="text-neutral-600 mb-4">Alex started small with digital checklists. Suddenly, daily tasks were tracked, staff were notified automatically, and no one could 'forget' a job. Encouraged, Alex rolled out rosters, onboarding, inventory tracking, and temperature logs.</p>
              <blockquote class="italic text-neutral-700 border-l-4 border-[#f97316] pl-4 md:border-l-0 md:border-r-4 md:pr-4 md:pl-0">"It felt like I hired a full-time manager. Except this one lived in my tablet and never called in sick."</blockquote>
            </div>
            <div class="w-10 h-10 bg-[#f97316] rounded-full flex items-center justify-center text-white">3</div>
            <div class="md:w-1/2 md:pl-12">
              <div class=" rounded-lg h-48 flex items-center justify-center">
                <img alt="Plugging in the Power" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/story/3-story.png">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Timeline Item 4 -->
        <div class="relative z-10 mb-20">
          <div class="flex flex-col md:flex-row-reverse items-center">
            <div class="md:w-1/2 md:pl-12 mb-8 md:mb-0 md:text-left">
              <h3 class="text-xl text-[#132f65] mb-3">Running on Autopilot</h3>
              <p class="text-neutral-600 mb-4">Weeks turned into months. Bizadmin synced staff timesheets, tracked deliveries, and reminded teams of pending tasks. Supplier orders were one tap away. The café felt alive—but finally, under control.</p>
              <blockquote class="italic text-neutral-700 border-l-4 border-[#f97316] pl-4">"I stopped reacting and started leading again. My team knew what to do, and I could finally breathe."</blockquote>
            </div>
            <div class="w-10 h-10 bg-[#f97316] rounded-full flex items-center justify-center text-white">4</div>
            <div class="md:w-1/2 md:pr-12">
              <div class=" rounded-lg h-48 flex items-center justify-center">
                <img alt="Running on Autopilot" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/story/4-story.png">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Timeline Item 5 -->
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 md:pr-12 mb-8 md:mb-0 md:text-right">
              <h3 class="text-xl text-[#132f65] mb-3">The New Normal</h3>
              <p class="text-neutral-600 mb-4">Today, Alex’s café hums with purpose. Customers linger longer. Staff are happier. And Alex? He enjoys his morning flat white in peace—finally working *on* the business, not *in* it.</p>
              <blockquote class="italic text-neutral-700 border-l-4 border-[#f97316] pl-4 md:border-l-0 md:border-r-4 md:pr-4 md:pl-0">"Bizadmin didn’t just fix my café’s chaos—it gave me back my dream."</blockquote>
            </div>
            <div class="w-10 h-10 bg-[#f97316] rounded-full flex items-center justify-center text-white">5</div>
            <div class="md:w-1/2 md:pl-12">
              <div class=" rounded-lg h-48 flex items-center justify-center">
                <img alt="The New Normal" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/story/5-story.png">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

    <!-- Highlight Section -->
  <section id="benefits" class="py-20 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Discover what BizAdmin can do for your store.</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Our platform adapts to your unique business needs with powerful, easy-to-use tools.</p>
            </div>
            
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 md:pr-12">
                    <div class="space-y-6">
                         <div id="benefit-1" class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="text-primary text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-clipboard-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipboard-check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM305 273L177 401c-9.4 9.4-24.6 9.4-33.9 0L79 337c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L271 239c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"></path></svg></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2">Operations & Compliance Management</h3>
                                <p class="text-gray-600">Streamline daily operations with checklist tracking, temperature recording, and cleaning task automation.</p>
                            </div>
                        </div>
                        
                        <div id="benefit-2" class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="text-primary text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-boxes-stacked" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="boxes-stacked" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M248 0H208c-26.5 0-48 21.5-48 48V160c0 35.3 28.7 64 64 64H352c35.3 0 64-28.7 64-64V48c0-26.5-21.5-48-48-48H328V80c0 8.8-7.2 16-16 16H264c-8.8 0-16-7.2-16-16V0zM64 256c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H224c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H184v80c0 8.8-7.2 16-16 16H120c-8.8 0-16-7.2-16-16V256H64zM352 512H512c35.3 0 64-28.7 64-64V320c0-35.3-28.7-64-64-64H472v80c0 8.8-7.2 16-16 16H408c-8.8 0-16-7.2-16-16V256H352c-15 0-28.8 5.1-39.7 13.8c4.9 10.4 7.7 22 7.7 34.2V464c0 12.2-2.8 23.8-7.7 34.2C323.2 506.9 337 512 352 512z"></path></svg></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2">Inventory & Supplier Management</h3>
                                <p class="text-gray-600">Monitor stock levels, manage suppliers, and track orders efficiently.</p>
                            </div>
                        </div>
                        
                        <div id="benefit-3" class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="text-primary text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-users-gear" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users-gear" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M144 160A80 80 0 1 0 144 0a80 80 0 1 0 0 160zM512 160A80 80 0 1 0 512 0a80 80 0 1 0 0 160zM0 298.7C0 310.4 9.6 320 21.3 320H234.7c.2 0 .4 0 .7 0c-26.6-23.5-43.3-57.8-43.3-96c0-7.6 .7-15 1.9-22.3c-13.6-6.3-28.7-9.7-44.6-9.7H106.7C47.8 192 0 239.8 0 298.7zM320 320c24 0 45.9-8.8 62.7-23.3c2.5-3.7 5.2-7.3 8-10.7c2.7-3.3 5.7-6.1 9-8.3C410 262.3 416 243.9 416 224c0-53-43-96-96-96s-96 43-96 96s43 96 96 96zm65.4 60.2c-10.3-5.9-18.1-16.2-20.8-28.2H261.3C187.7 352 128 411.7 128 485.3c0 14.7 11.9 26.7 26.7 26.7H455.2c-2.1-5.2-3.2-10.9-3.2-16.4v-3c-1.3-.7-2.7-1.5-4-2.3l-2.6 1.5c-16.8 9.7-40.5 8-54.7-9.7c-4.5-5.6-8.6-11.5-12.4-17.6l-.1-.2-.1-.2-2.4-4.1-.1-.2-.1-.2c-3.4-6.2-6.4-12.6-9-19.3c-8.2-21.2 2.2-42.6 19-52.3l2.7-1.5c0-.8 0-1.5 0-2.3s0-1.5 0-2.3l-2.7-1.5zM533.3 192H490.7c-15.9 0-31 3.5-44.6 9.7c1.3 7.2 1.9 14.7 1.9 22.3c0 17.4-3.5 33.9-9.7 49c2.5 .9 4.9 2 7.1 3.3l2.6 1.5c1.3-.8 2.6-1.6 4-2.3v-3c0-19.4 13.3-39.1 35.8-42.6c7.9-1.2 16-1.9 24.2-1.9s16.3 .6 24.2 1.9c22.5 3.5 35.8 23.2 35.8 42.6v3c1.3 .7 2.7 1.5 4 2.3l2.6-1.5c16.8-9.7 40.5-8 54.7 9.7c2.3 2.8 4.5 5.8 6.6 8.7c-2.1-57.1-49-102.7-106.6-102.7zm91.3 163.9c6.3-3.6 9.5-11.1 6.8-18c-2.1-5.5-4.6-10.8-7.4-15.9l-2.3-4c-3.1-5.1-6.5-9.9-10.2-14.5c-4.6-5.7-12.7-6.7-19-3L574.4 311c-8.9-7.6-19.1-13.6-30.4-17.6v-21c0-7.3-4.9-13.8-12.1-14.9c-6.5-1-13.1-1.5-19.9-1.5s-13.4 .5-19.9 1.5c-7.2 1.1-12.1 7.6-12.1 14.9v21c-11.2 4-21.5 10-30.4 17.6l-18.2-10.5c-6.3-3.6-14.4-2.6-19 3c-3.7 4.6-7.1 9.5-10.2 14.6l-2.3 3.9c-2.8 5.1-5.3 10.4-7.4 15.9c-2.6 6.8 .5 14.3 6.8 17.9l18.2 10.5c-1 5.7-1.6 11.6-1.6 17.6s.6 11.9 1.6 17.5l-18.2 10.5c-6.3 3.6-9.5 11.1-6.8 17.9c2.1 5.5 4.6 10.7 7.4 15.8l2.4 4.1c3 5.1 6.4 9.9 10.1 14.5c4.6 5.7 12.7 6.7 19 3L449.6 457c8.9 7.6 19.2 13.6 30.4 17.6v21c0 7.3 4.9 13.8 12.1 14.9c6.5 1 13.1 1.5 19.9 1.5s13.4-.5 19.9-1.5c7.2-1.1 12.1-7.6 12.1-14.9v-21c11.2-4 21.5-10 30.4-17.6l18.2 10.5c6.3 3.6 14.4 2.6 19-3c3.7-4.6 7.1-9.4 10.1-14.5l2.4-4.2c2.8-5.1 5.3-10.3 7.4-15.8c2.6-6.8-.5-14.3-6.8-17.9l-18.2-10.5c1-5.7 1.6-11.6 1.6-17.5s-.6-11.9-1.6-17.6l18.2-10.5zM472 384a40 40 0 1 1 80 0 40 40 0 1 1 -80 0z"></path></svg></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2">HR & Workforce Management</h3>
                                <p class="text-gray-600">Simplify employee scheduling, timesheets, and HR processes in one place.</p>
                            </div>
                        </div>
                        
                        <div id="benefit-4" class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-full mr-4">
                                <i class="text-primary text-xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-file-invoice-dollar" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-invoice-dollar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM64 80c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16zm128 72c8.8 0 16 7.2 16 16v17.3c8.5 1.2 16.7 3.1 24.1 5.1c8.5 2.3 13.6 11 11.3 19.6s-11 13.6-19.6 11.3c-11.1-3-22-5.2-32.1-5.3c-8.4-.1-17.4 1.8-23.6 5.5c-5.7 3.4-8.1 7.3-8.1 12.8c0 3.7 1.3 6.5 7.3 10.1c6.9 4.1 16.6 7.1 29.2 10.9l.5 .1 0 0 0 0c11.3 3.4 25.3 7.6 36.3 14.6c12.1 7.6 22.4 19.7 22.7 38.2c.3 19.3-9.6 33.3-22.9 41.6c-7.7 4.8-16.4 7.6-25.1 9.1V440c0 8.8-7.2 16-16 16s-16-7.2-16-16V422.2c-11.2-2.1-21.7-5.7-30.9-8.9l0 0c-2.1-.7-4.2-1.4-6.2-2.1c-8.4-2.8-12.9-11.9-10.1-20.2s11.9-12.9 20.2-10.1c2.5 .8 4.8 1.6 7.1 2.4l0 0 0 0 0 0c13.6 4.6 24.6 8.4 36.3 8.7c9.1 .3 17.9-1.7 23.7-5.3c5.1-3.2 7.9-7.3 7.8-14c-.1-4.6-1.8-7.8-7.7-11.6c-6.8-4.3-16.5-7.4-29-11.2l-1.6-.5 0 0c-11-3.3-24.3-7.3-34.8-13.7c-12-7.2-22.6-18.9-22.7-37.3c-.1-19.4 10.8-32.8 23.8-40.5c7.5-4.4 15.8-7.2 24.1-8.7V232c0-8.8 7.2-16 16-16z"></path></svg></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-primary mb-2">Finance & Documentation Control</h3>
                                <p class="text-gray-600">Manage cash flow, organize important documents, and track financial records seamlessly.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="md:w-1/2 laptopScreen">
                    <img class="rounded-xl shadow-xl" src="https://bizadmin.com.au/theme-assets/Landingpageassets/assets/inventory-supplier-feature.jpg" alt="professional business admin dashboard UI with charts, inventory management, and staff scheduling features in dark blue and orange color scheme">
                </div>
            </div>
        </div>
    </section>

    <!-- Final Features Section -->
    <section id="why-choose" class="py-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Block 1 -->
                <div id="exp-block-1" class="bg-primary text-white rounded-xl p-10">
                    <div class="text-accent mb-6">
                        <i class="text-4xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-headset" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="headset" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 48C141.1 48 48 141.1 48 256v40c0 13.3-10.7 24-24 24s-24-10.7-24-24V256C0 114.6 114.6 0 256 0S512 114.6 512 256V400.1c0 48.6-39.4 88-88.1 88L313.6 488c-8.3 14.3-23.8 24-41.6 24H240c-26.5 0-48-21.5-48-48s21.5-48 48-48h32c17.8 0 33.3 9.7 41.6 24l110.4 .1c22.1 0 40-17.9 40-40V256c0-114.9-93.1-208-208-208zM144 208h16c17.7 0 32 14.3 32 32V352c0 17.7-14.3 32-32 32H144c-35.3 0-64-28.7-64-64V272c0-35.3 28.7-64 64-64zm224 0c35.3 0 64 28.7 64 64v48c0 35.3-28.7 64-64 64H352c-17.7 0-32-14.3-32-32V240c0-17.7 14.3-32 32-32h16z"></path></svg></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Support That Cares</h3>
                    <p class="text-white/80 mb-6">Our dedicated team provides personalized onboarding and ongoing support to ensure your success with BizAdmin.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span> technical support whenever needed</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Personalized onboarding</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Regular check-ins</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Block 2 -->
                <div id="exp-block-2" class="bg-white rounded-xl p-10 shadow-lg">
                    <div class="text-accent mb-6">
                        <i class="text-4xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-layer-group" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="layer-group" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z"></path></svg></i>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-4">All-in-One Platform</h3>
                    <p class="text-gray-600 mb-6">Eliminate the need for multiple software solutions with our comprehensive platform that handles all aspects of business administration.</p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Unified dashboard</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Seamless integrations</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Centralized data</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Block 3 -->
                <div id="exp-block-3" class="bg-white rounded-xl p-10 shadow-lg">
                    <div class="text-accent mb-6">
                        <i class="text-4xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-sliders" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sliders" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M0 416c0 17.7 14.3 32 32 32l54.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 448c17.7 0 32-14.3 32-32s-14.3-32-32-32l-246.7 0c-12.3-28.3-40.5-48-73.3-48s-61 19.7-73.3 48L32 384c-17.7 0-32 14.3-32 32zm128 0a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zM320 256a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm32-80c-32.8 0-61 19.7-73.3 48L32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l246.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48l54.7 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-54.7 0c-12.3-28.3-40.5-48-73.3-48zM192 128a32 32 0 1 1 0-64 32 32 0 1 1 0 64zm73.3-64C253 35.7 224.8 16 192 16s-61 19.7-73.3 48L32 64C14.3 64 0 78.3 0 96s14.3 32 32 32l86.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 128c17.7 0 32-14.3 32-32s-14.3-32-32-32L265.3 64z"></path></svg></i>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-4">Customizable &amp; Scalable</h3>
                    <p class="text-gray-600 mb-6">Whether you run a single location or multiple franchises, BizAdmin adapts to your business size and specific needs.</p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Flexible configurations</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Multi-location management</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Role-based permissions</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Block 4 -->
                <div id="exp-block-4" class="bg-primary text-white rounded-xl p-10">
                    <div class="text-accent mb-6">
                        <i class="text-4xl" data-fa-i2svg=""><svg class="svg-inline--fa fa-rocket" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="rocket" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-88.5c13-21.9 36.5-35.3 61.9-35.3l82.3 0c2.4-4 4.8-7.7 7.2-11.3C289.1-4.1 411.1-8.1 483.9 5.3c11.6 2.1 20.6 11.2 22.8 22.8c13.4 72.9 9.3 194.8-111.4 276.7c-3.5 2.4-7.3 4.8-11.3 7.2v82.3c0 25.4-13.4 49-35.3 61.9l-88.5 52.5c-7.4 4.4-16.6 4.5-24.1 .2s-12.1-12.2-12.1-20.9V380.8c-14.1 4.9-26.4 8.9-35.7 11.9c-11.2 3.6-23.4 .5-31.8-7.8zM384 168a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"></path></svg></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">The BizAdmin Experience</h3>
                    <p class="text-white/80 mb-6">Join hundreds of businesses that have transformed their operations and achieved sustainable growth with BizAdmin.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Proven results</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Continuous innovation</span>
                        </li>
                        <li class="flex items-center">
                            <i class="text-accent mr-3" data-fa-i2svg=""><svg class="svg-inline--fa fa-check" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"></path></svg></i>
                            <span>Business growth focus</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    </div>