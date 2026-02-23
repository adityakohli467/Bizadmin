<!-- Scroll to Top Button - Reusable Component -->
<button id="scrollToTopBtn" 
    onclick="scrollToTop()" 
    class="fixed bottom-6 right-6 z-50 w-12 h-12 bg-navy hover:bg-blue-800 text-white rounded-full shadow-lg transition-all duration-300 opacity-0 invisible flex items-center justify-center"
    title="Scroll to top">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
    </svg>
</button>

<style>
#scrollToTopBtn {
    background-color: #1F3A61;
    box-shadow: 0 4px 14px rgba(31, 58, 97, 0.4);
}

#scrollToTopBtn:hover {
    background-color: #2d4a7c;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(31, 58, 97, 0.5);
}

#scrollToTopBtn.show {
    opacity: 1;
    visibility: visible;
}

#scrollToTopBtn svg {
    transition: transform 0.2s ease;
}

#scrollToTopBtn:hover svg {
    transform: translateY(-2px);
}
</style>

<script>
// Scroll to Top Functionality
(function() {
    var scrollBtn = document.getElementById('scrollToTopBtn');
    var scrollThreshold = 300; // Show button after scrolling 300px
    
    // Show/hide button based on scroll position
    function toggleScrollButton() {
        if (window.pageYOffset > scrollThreshold || document.documentElement.scrollTop > scrollThreshold) {
            scrollBtn.classList.add('show');
        } else {
            scrollBtn.classList.remove('show');
        }
    }
    
    // Listen for scroll events
    window.addEventListener('scroll', toggleScrollButton);
    
    // Initial check
    toggleScrollButton();
})();

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>
