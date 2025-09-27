/**
 * File navigation.js.
 *
 * Handles toggling of the mobile navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */

(function() {
    'use strict';

    // Mobile navigation elements
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenu = document.getElementById('close-menu');
    const body = document.body;
    const html = document.documentElement;

    // Track original overflow styles
    let originalBodyOverflow = '';
    let originalHtmlOverflow = '';

    // Focusable elements selector
    const focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable]';

    /**
     * Initialize mobile navigation
     */
    function initMobileNavigation() {
        if (!menuToggle || !mobileMenu || !closeMenu) {
            return;
        }

        // Set initial attributes
        mobileMenu.setAttribute('aria-hidden', 'true');
        menuToggle.setAttribute('aria-expanded', 'false');
        closeMenu.setAttribute('aria-expanded', 'false');

        // Bind events
        menuToggle.addEventListener('click', openMobileMenu);
        closeMenu.addEventListener('click', closeMobileMenu);
        mobileMenu.addEventListener('click', handleBackdropClick);
        document.addEventListener('keydown', handleKeyPress);

        // Handle window resize
        window.addEventListener('resize', handleResize);
    }

    /**
     * Open mobile menu
     */
    function openMobileMenu(event) {
        event.preventDefault();
        event.stopPropagation();

        // Store original overflow styles
        originalBodyOverflow = body.style.overflow;
        originalHtmlOverflow = html.style.overflow;

        // Prevent scrolling
        body.style.overflow = 'hidden';
        html.style.overflow = 'hidden';
        body.classList.add('mobile-menu-open');

        // Update attributes
        mobileMenu.setAttribute('aria-hidden', 'false');
        menuToggle.setAttribute('aria-expanded', 'true');
        closeMenu.setAttribute('aria-expanded', 'true');

        // Show dialog
        mobileMenu.showModal();

        // Trap focus
        trapFocus(mobileMenu);

        // Focus close button for accessibility
        setTimeout(() => {
            closeMenu.focus();
        }, 100);

        // Add escape key listener
        document.addEventListener('keydown', handleEscapeKey);

        // Initialize mobile dropdowns after menu opens
        setTimeout(() => {
            initMobileDropdowns();
        }, 150);
    }

    /**
     * Close mobile menu
     */
    function closeMobileMenu(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Restore scrolling
        body.style.overflow = originalBodyOverflow;
        html.style.overflow = originalHtmlOverflow;

        // Update attributes
        mobileMenu.setAttribute('aria-hidden', 'true');
        menuToggle.setAttribute('aria-expanded', 'false');
        closeMenu.setAttribute('aria-expanded', 'false');

        // Remove focus trap
        removeFocusTrap(mobileMenu);

        // No need to close dropdowns since they're always visible

        // Close dialog
        mobileMenu.close();

        // Return focus to menu toggle
        menuToggle.focus();

        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);

        // Remove mobile menu open class from body
        body.classList.remove('mobile-menu-open');
    }

    /**
     * Handle backdrop clicks (clicking outside menu content)
     */
    function handleBackdropClick(event) {
        // Close menu if clicking on the dialog backdrop (not the menu content)
        if (event.target === mobileMenu) {
            closeMobileMenu(event);
        }
    }

    /**
     * Handle key press events
     */
    function handleKeyPress(event) {
        // Handle Enter and Space for menu toggle
        if ((event.key === 'Enter' || event.key === ' ') && event.target === menuToggle) {
            event.preventDefault();
            openMobileMenu(event);
        }
    }

    /**
     * Handle escape key press
     */
    function handleEscapeKey(event) {
        if (event.key === 'Escape' && mobileMenu.hasAttribute('open')) {
            closeMobileMenu(event);
        }
    }

    /**
     * Handle window resize
     */
    function handleResize() {
        // Close mobile menu if window becomes large enough
        if (window.innerWidth > 800 && mobileMenu.hasAttribute('open')) {
            closeMobileMenu();
        }
    }

    /**
     * Trap focus within the mobile menu with enhanced functionality
     */
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(focusableElementsString);
        const firstFocusableElement = focusableElements[0];
        const lastFocusableElement = focusableElements[focusableElements.length - 1];

        // Store the previously focused element
        const previouslyFocusedElement = document.activeElement;

        function handleTabKey(event) {
            if (event.key === 'Tab') {
                if (focusableElements.length === 1) {
                    event.preventDefault();
                    return;
                }

                if (event.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === firstFocusableElement) {
                        event.preventDefault();
                        lastFocusableElement.focus();
                    }
                } else {
                    // Tab
                    if (document.activeElement === lastFocusableElement) {
                        event.preventDefault();
                        firstFocusableElement.focus();
                    }
                }
            }
        }

        // Add the event listener
        element.addEventListener('keydown', handleTabKey);

        // Store the handler so we can remove it later
        element._focusTrapHandler = handleTabKey;
        element._previouslyFocusedElement = previouslyFocusedElement;
    }

    /**
     * Remove focus trap and restore previous focus
     */
    function removeFocusTrap(element) {
        if (element._focusTrapHandler) {
            element.removeEventListener('keydown', element._focusTrapHandler);
            delete element._focusTrapHandler;
        }

        // Restore focus to previously focused element
        if (element._previouslyFocusedElement && element._previouslyFocusedElement.focus) {
            setTimeout(() => {
                element._previouslyFocusedElement.focus();
            }, 100);
            delete element._previouslyFocusedElement;
        }
    }

    /**
     * Enhanced close mobile menu with focus trap cleanup
     */
    function closeMobileMenu(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Restore scrolling
        body.style.overflow = originalBodyOverflow;
        html.style.overflow = originalHtmlOverflow;

        // Update attributes
        mobileMenu.setAttribute('aria-hidden', 'true');
        menuToggle.setAttribute('aria-expanded', 'false');
        closeMenu.setAttribute('aria-expanded', 'false');

        // Remove focus trap
        removeFocusTrap(mobileMenu);

        // Close all open mobile dropdowns
        const openDropdowns = mobileMenu.querySelectorAll('[aria-expanded="true"]');
        openDropdowns.forEach(function(dropdown) {
            const submenu = dropdown.parentElement.querySelector('ul');
            if (submenu) {
                closeMobileDropdown(dropdown, submenu);
            }
        });

        // Close dialog
        mobileMenu.close();

        // Return focus to menu toggle
        menuToggle.focus();

        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);

        // Remove mobile menu open class from body
        body.classList.remove('mobile-menu-open');
    }

    /**
     * Enhanced open mobile menu
     */
    function openMobileMenu(event) {
        event.preventDefault();
        event.stopPropagation();

        // Store original overflow styles
        originalBodyOverflow = body.style.overflow;
        originalHtmlOverflow = html.style.overflow;

        // Prevent scrolling
        body.style.overflow = 'hidden';
        html.style.overflow = 'hidden';
        body.classList.add('mobile-menu-open');

        // Update attributes
        mobileMenu.setAttribute('aria-hidden', 'false');
        menuToggle.setAttribute('aria-expanded', 'true');
        closeMenu.setAttribute('aria-expanded', 'true');

        // Show dialog
        mobileMenu.showModal();

        // Trap focus
        trapFocus(mobileMenu);

        // Focus close button for accessibility
        setTimeout(() => {
            closeMenu.focus();
        }, 100);

        // Add escape key listener
        document.addEventListener('keydown', handleEscapeKey);

        // Initialize mobile dropdowns after menu opens
        setTimeout(() => {
            initMobileDropdowns();
        }, 150);
    }

    /**
     * Initialize mobile dropdown functionality - submenus always visible
     */
    function initMobileDropdowns() {
        const mobileMenuItems = mobileMenu.querySelectorAll('.menu-item-has-children');
        
        mobileMenuItems.forEach(function(menuItem) {
            const link = menuItem.querySelector('a');
            const submenu = menuItem.querySelector('ul');

            if (link && submenu && !link._mobileInitialized) {
                // Mark as initialized to prevent duplicate listeners
                link._mobileInitialized = true;

                // Set submenu to always be visible
                link.removeAttribute('aria-expanded');
                submenu.removeAttribute('aria-hidden');
                submenu.style.display = 'block';

                // Remove click handler - no toggle functionality needed
                // Parent links still work as normal links
            }
        });
    }

    /**
     * Utility function to check if device is mobile/touch
     */
    function isTouchDevice() {
        return ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
    }

    /**
     * Utility function to get current breakpoint
     */
    function getCurrentBreakpoint() {
        return window.innerWidth <= 800 ? 'mobile' : 'desktop';
    }

    /**
     * Handle dropdown menus accessibility for both desktop and mobile
     */
    function initDropdownAccessibility() {
        const menuItems = document.querySelectorAll('.menu-item-has-children');

        menuItems.forEach(function(menuItem) {
            const link = menuItem.querySelector('a');
            const submenu = menuItem.querySelector('ul');

            if (link && submenu) {
                // Add aria attributes
                link.setAttribute('aria-haspopup', 'true');
                link.setAttribute('aria-expanded', 'false');
                submenu.setAttribute('aria-hidden', 'true');

                // Check if we're in mobile menu
                const isMobileMenu = menuItem.closest('#mobile-primary-menu');

                if (isMobileMenu) {
                    // Mobile menu - submenus always visible, no toggle needed
                    link.removeAttribute('aria-expanded');
                    submenu.removeAttribute('aria-hidden');
                    // Parent links work as normal navigation links
                } else {
                    // Desktop menu dropdown behavior
                    // Handle mouse events
                    menuItem.addEventListener('mouseenter', function() {
                        openDropdown(link, submenu);
                    });

                    menuItem.addEventListener('mouseleave', function() {
                        closeDropdown(link, submenu);
                    });

                    // Handle keyboard events
                    link.addEventListener('keydown', function(event) {
                        if (event.key === 'Enter' || event.key === ' ' || event.key === 'ArrowDown') {
                            event.preventDefault();
                            openDropdown(link, submenu);
                            // Focus first submenu item
                            const firstSubmenuLink = submenu.querySelector('a');
                            if (firstSubmenuLink) {
                                firstSubmenuLink.focus();
                            }
                        } else if (event.key === 'Escape') {
                            closeDropdown(link, submenu);
                            link.focus();
                        }
                    });

                    // Handle submenu keyboard navigation
                    const submenuLinks = submenu.querySelectorAll('a');
                    submenuLinks.forEach(function(submenuLink, index) {
                        submenuLink.addEventListener('keydown', function(event) {
                            if (event.key === 'ArrowDown') {
                                event.preventDefault();
                                const nextLink = submenuLinks[index + 1];
                                if (nextLink) {
                                    nextLink.focus();
                                }
                            } else if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                const prevLink = submenuLinks[index - 1];
                                if (prevLink) {
                                    prevLink.focus();
                                } else {
                                    link.focus();
                                }
                            } else if (event.key === 'Escape') {
                                closeDropdown(link, submenu);
                                link.focus();
                            }
                        });
                    });
                }
            }
        });
    }

    /**
     * Toggle mobile dropdown menu
     */
    function toggleMobileDropdown(link, submenu) {
        const isExpanded = link.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
            closeMobileDropdown(link, submenu);
        } else {
            openMobileDropdown(link, submenu);
        }
    }

    /**
     * Open mobile dropdown menu
     */
    function openMobileDropdown(link, submenu) {
        link.setAttribute('aria-expanded', 'true');
        submenu.setAttribute('aria-hidden', 'false');
        submenu.style.display = 'block';
        
        // Add smooth height animation
        submenu.style.height = 'auto';
        const height = submenu.scrollHeight + 'px';
        submenu.style.height = '0';
        submenu.offsetHeight; // Trigger reflow
        submenu.style.transition = 'height 0.3s ease';
        submenu.style.height = height;

        // Clean up after animation
        setTimeout(() => {
            submenu.style.height = 'auto';
            submenu.style.transition = '';
        }, 300);
    }

    /**
     * Close mobile dropdown menu
     */
    function closeMobileDropdown(link, submenu) {
        link.setAttribute('aria-expanded', 'false');
        submenu.setAttribute('aria-hidden', 'true');
        
        // Add smooth height animation
        const height = submenu.scrollHeight + 'px';
        submenu.style.height = height;
        submenu.offsetHeight; // Trigger reflow
        submenu.style.transition = 'height 0.3s ease';
        submenu.style.height = '0';

        // Hide after animation
        setTimeout(() => {
            submenu.style.display = 'none';
            submenu.style.height = '';
            submenu.style.transition = '';
        }, 300);
    }

    /**
     * Open dropdown menu
     */
    function openDropdown(link, submenu) {
        link.setAttribute('aria-expanded', 'true');
        submenu.setAttribute('aria-hidden', 'false');
        link.parentElement.classList.add('focus');
    }

    /**
     * Close dropdown menu
     */
    function closeDropdown(link, submenu) {
        link.setAttribute('aria-expanded', 'false');
        submenu.setAttribute('aria-hidden', 'true');
        link.parentElement.classList.remove('focus');
    }

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initMobileNavigation();
            initDropdownAccessibility();
        });
    } else {
        initMobileNavigation();
        initDropdownAccessibility();
    }

})();
