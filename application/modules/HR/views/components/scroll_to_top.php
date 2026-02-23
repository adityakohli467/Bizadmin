<!-- Scroll to Top Button - Reusable Component -->
<style>
#scrollToTopBtn {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    width: 48px !important;
    height: 48px !important;
    background-color: #1F3A61 !important;
    color: white !important;
    border: none !important;
    border-radius: 50% !important;
    cursor: pointer !important;
    box-shadow: 0 4px 14px rgba(31, 58, 97, 0.4) !important;
    z-index: 999999 !important;
    display: none;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease !important;
}

#scrollToTopBtn:hover {
    background-color: #2d4a7c !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(31, 58, 97, 0.5) !important;
}

#scrollToTopBtn.show {
    display: flex !important;
}

#scrollToTopBtn svg {
    width: 24px;
    height: 24px;
    transition: transform 0.2s ease;
}

#scrollToTopBtn:hover svg {
    transform: translateY(-2px);
}
</style>

<script>
// Scroll to Top Functionality
(function() {
    function createAndInitScrollButton() {
        // Remove existing button if any
        var existing = document.getElementById('scrollToTopBtn');
        if (existing) {
            existing.remove();
        }
        
        // Create button and append directly to body
        var btn = document.createElement('button');
        btn.id = 'scrollToTopBtn';
        btn.title = 'Scroll to top';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>';
        btn.onclick = function() { scrollToTop(); };
        document.body.appendChild(btn);
        
        var scrollThreshold = 150;
        var activeScrollContainer = null;
        var containerSelectors = ['#main-content', '#schedule-grid'];
        
        function toggleScrollButton() {
            var showButton = false;
            
            // Check window/body scroll
            if (window.pageYOffset > scrollThreshold || 
                document.documentElement.scrollTop > scrollThreshold || 
                document.body.scrollTop > scrollThreshold) {
                showButton = true;
                activeScrollContainer = window;
            }
            
            // Check container scroll
            containerSelectors.forEach(function(selector) {
                var container = document.querySelector(selector);
                if (container && container.scrollTop > scrollThreshold) {
                    showButton = true;
                    activeScrollContainer = container;
                }
            });
            
            if (showButton) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        }
        
        // Global scroll to top function
        window.scrollToTop = function() {
            // Scroll containers
            containerSelectors.forEach(function(selector) {
                var container = document.querySelector(selector);
                if (container) {
                    container.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
            // Scroll window
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
        
        // Attach listeners
        window.addEventListener('scroll', toggleScrollButton, { passive: true });
        document.addEventListener('scroll', toggleScrollButton, { passive: true });
        
        containerSelectors.forEach(function(selector) {
            var container = document.querySelector(selector);
            if (container) {
                container.addEventListener('scroll', toggleScrollButton, { passive: true });
            }
        });
        
        // Check initially and after delays
        toggleScrollButton();
        setTimeout(function() {
            containerSelectors.forEach(function(selector) {
                var container = document.querySelector(selector);
                if (container) {
                    container.addEventListener('scroll', toggleScrollButton, { passive: true });
                }
            });
            toggleScrollButton();
        }, 500);
    }
    
    // Initialize when ready
    if (document.readyState === 'complete') {
        createAndInitScrollButton();
    } else {
        window.addEventListener('load', createAndInitScrollButton);
    }
})();
</script>
