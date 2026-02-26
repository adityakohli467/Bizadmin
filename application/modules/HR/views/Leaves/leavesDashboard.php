<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizadmin</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <script src="https://cdn.plot.ly/plotly-3.1.1.min.js"></script>
    <?php $this->load->view('general/tailwind_common_assets'); ?>
    <style>
    .text-gray-900{
        color:black !important;
    }
    </style>
</head>
<body class="bg-gray-50">

<main class=" pt-16 p-8">
    <div id="stats-section" class="mb-8">
        <div class="grid grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">+3 Today</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">12</h3>
                <p class="text-sm text-gray-600">Pending Requests</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">This Month</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">48</h3>
                <p class="text-sm text-gray-600">Approved Leaves</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">This Month</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">5</h3>
                <p class="text-sm text-gray-600">Rejected Leaves</p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-calendar-day text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">Next 30 Days</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-1">23</h3>
                <p class="text-sm text-gray-600">Upcoming Leaves</p>
            </div>
        </div>
    </div>
    
    <div id="main-content-grid" class="grid grid-cols-3 gap-6 mb-8">
        <div id="requests-section" class="col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Recent Leave Requests</h3>
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View All</a>
                </div>
            </div>
            
            <div class="divide-y divide-gray-200">
                <div class="p-6 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-4">
                       
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Aditya Singh</h4>
                                    <p class="text-sm text-gray-500">Senior Developer • Engineering</p>
                                </div>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Pending</span>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-umbrella-beach text-indigo-600"></i>
                                    <span>Annual Leave</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Dec 20 - Dec 27, 2023</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>8 days</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Family vacation during Christmas holidays</p>
                            <div class="flex items-center gap-2">
                                <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                    <i class="fa-solid fa-check mr-2"></i>Approve
                                </button>
                                <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                    <i class="fa-solid fa-times mr-2"></i>Reject
                                </button>
                                <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-4">
                       
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">Kaushika Jinna</h4>
                                    <p class="text-sm text-gray-500">Product Manager • Product</p>
                                </div>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">Pending</span>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-hospital text-red-600"></i>
                                    <span>Sick Leave</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span>Dec 18 - Dec 19, 2023</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>2 days</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Doctor's appointment and recovery</p>
                            <div class="flex items-center gap-2">
                                <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                    <i class="fa-solid fa-check mr-2"></i>Approve
                                </button>
                                <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                    <i class="fa-solid fa-times mr-2"></i>Reject
                                </button>
                                <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
              
            </div>
        </div>
        
        <div id="notifications-section" class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Notifications & Alerts</h3>
            </div>
            
            <div class="divide-y divide-gray-200">
                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Overlapping Leave Conflict</p>
                            <p class="text-xs text-gray-600 mb-2">3 employees from Engineering team have overlapping leaves Dec 20-27</p>
                            <p class="text-xs text-gray-400">2 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-bell text-orange-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-1">New Leave Request</p>
                            <p class="text-xs text-gray-600 mb-2">Aditya Singh submitted annual leave request for 8 days</p>
                            <p class="text-xs text-gray-400">3 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-calendar-check text-indigo-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Upcoming Leave</p>
                            <p class="text-xs text-gray-600 mb-2">5 team members will be on leave next week</p>
                            <p class="text-xs text-gray-400">5 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-1">Leave Approved</p>
                            <p class="text-xs text-gray-600 mb-2">You approved David Kim's sick leave request</p>
                            <p class="text-xs text-gray-400">Yesterday</p>
                        </div>
                    </div>
                </div>
                
               
            </div>
            
           
        </div>
    </div>
    
    <div id="calendar-chart-section" class="grid grid-cols-2 gap-6 mb-8">
        <div id="calendar-widget" class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Leave Calendar - Nov 2025</h3>
                    <small>see on which date employees are one leave in this month </small>
                    <div class="flex items-center gap-2">
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="fa-solid fa-chevron-left text-gray-600"></i>
                        </button>
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="fa-solid fa-chevron-right text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-7 gap-2 mb-3">
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Sun</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Mon</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Tue</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Wed</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Thu</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Fri</div>
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">Sat</div>
                </div>
                
                <div class="grid grid-cols-7 gap-2">
                    <div class="aspect-square"></div>
                    <div class="aspect-square"></div>
                    <div class="aspect-square"></div>
                    <div class="aspect-square"></div>
                    <div class="aspect-square"></div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">1</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">2</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">3</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">4</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">5</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">6</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">7</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">8</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">9</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">10</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">11</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">12</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">13</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">14</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">15</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">16</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-indigo-50 border-indigo-200 relative group">
                        <span class="text-sm font-medium">17</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                            <div class="w-1.5 h-1.5 bg-green-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-red-50 border-red-200 relative">
                        <span class="text-sm font-medium">18</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-red-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-red-50 border-red-200 relative">
                        <span class="text-sm font-medium">19</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-red-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-indigo-300 rounded-lg bg-indigo-100 relative">
                        <span class="text-sm font-semibold text-indigo-700">20</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                            <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                            <div class="w-1.5 h-1.5 bg-orange-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-indigo-50 border-indigo-200 relative">
                        <span class="text-sm font-medium">21</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                            <div class="w-1.5 h-1.5 bg-purple-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-purple-50 border-purple-200 relative">
                        <span class="text-sm font-medium">22</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-purple-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">23</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">24</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">25</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">26</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg bg-indigo-50 border-indigo-200 relative">
                        <span class="text-sm font-medium">27</span>
                        <div class="absolute bottom-1 left-1 right-1 flex gap-0.5">
                            <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                        </div>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">28</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">29</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">30</span>
                    </div>
                    <div class="aspect-square p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <span class="text-sm font-medium">31</span>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-indigo-600 rounded-full"></div>
                        <span class="text-gray-600">Annual Leave</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-600 rounded-full"></div>
                        <span class="text-gray-600">Sick Leave</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                        <span class="text-gray-600">Remote</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
                        <span class="text-gray-600">Other</span>
                    </div>
                </div>
            </div>
        </div>
        
       
    </div>
    
   
</main>



</body>
</html>