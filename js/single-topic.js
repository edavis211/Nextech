class SingleTopicManager {
  constructor() {
    this.breakpoint = 900;
    this.tocButton = null;
    this.toc = null;
    this.tocLinks = null;
    this.isInitialized = false;
    this.observer = null;
    this.sections = [];
    this.navLinks = [];
    
    this.init();
  }
  
  init() {
    if (this.isInitialized) return;
    
    this.tocButton = document.querySelector('button.toc');
    this.toc = document.querySelector('#table-of-contents');
    this.tocLinks = document.querySelectorAll('#table-of-contents nav ul li a');
    
    this.setupEventListeners();
    this.handleInitialLoad();
    this.setupIntersectionObserver();
    
    this.isInitialized = true;
  }
  
  setupEventListeners() {
    window.addEventListener('DOMContentLoaded', () => {
      this.handleInitialLoad();
    });
    
    window.addEventListener('resize', () => {
      this.handleResize();
    });
    
    if (this.tocButton) {
      this.tocButton.addEventListener('click', () => {
        this.handleTocToggle();
      });
    }
    
    // Add click event listeners to TOC links
    if (this.tocLinks) {
      this.tocLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          this.handleTocLinkClick(e);
        });
      });
    }
  }
  
  handleInitialLoad() {
    if (this.getScreenWidth() < this.breakpoint && this.toc) {
      this.toc.setAttribute('aria-hidden', 'true');
    }
  }
  
  handleResize() {
    // Handle resize-specific logic - hide TOC on mobile, show on desktop
    if (!this.toc) return;
    
    const screenWidth = this.getScreenWidth();
    if (screenWidth < this.breakpoint) {
      // Hide TOC when resizing to mobile
      this.toc.setAttribute('aria-hidden', 'true');
    } else {
      // Show TOC when resizing to desktop - always visible on desktop
      this.toc.setAttribute('aria-hidden', 'false');
    }
  }
  
  handleTocLinkClick(event) {
    // On mobile, collapse the TOC when a link is clicked
    const screenWidth = this.getScreenWidth();
    if (screenWidth < this.breakpoint && this.toc) {
      this.toc.setAttribute('aria-hidden', 'true');
    }
    // Let the default anchor behavior handle the scrolling
  }
  
  handleTocToggle() {
    if (!this.toc) return;
    
    const screenWidth = this.getScreenWidth();
    if (screenWidth < this.breakpoint) {
      const currentState = this.toc.getAttribute('aria-hidden');
      const newState = currentState === 'true' ? 'false' : 'true';
      this.toc.setAttribute('aria-hidden', newState);
    }
  }
  
  getScreenWidth() {
    return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  }
  
  // Public method to manually show TOC
  showToc() {
    if (this.toc) {
      this.toc.setAttribute('aria-hidden', 'false');
    }
  }
  
  // Public method to manually hide TOC
  hideToc() {
    if (this.toc) {
      this.toc.setAttribute('aria-hidden', 'true');
    }
  }
  
  // Public method to destroy the instance
  destroy() {
    if (this.tocButton) {
      this.tocButton.removeEventListener('click', this.handleTocToggle);
    }
    
    // Remove TOC link event listeners
    if (this.tocLinks) {
      this.tocLinks.forEach(link => {
        link.removeEventListener('click', this.handleTocLinkClick);
      });
    }
    
    window.removeEventListener('resize', this.handleResize);
    window.removeEventListener('DOMContentLoaded', this.handleInitialLoad);
    
    // Clean up intersection observer
    if (this.observer) {
      this.observer.disconnect();
      this.observer = null;
    }
    
    this.isInitialized = false;
  }
  
  setupIntersectionObserver() {
    // Find all sections by their IDs that correspond to navigation data-section values
    const sectionIds = ['overview', 'about', 'activities', 'related-topics'];
    this.sections = sectionIds.map(id => document.getElementById(id)).filter(section => section !== null);
    this.navLinks = document.querySelectorAll('#table-of-contents nav ul li');
    
    if (this.sections.length === 0) return;
    
    // Create intersection observer that triggers when section tops cross the upper portion of viewport
    const options = {
      root: null,
      rootMargin: '-10% 0px -80% 0px', // Trigger when section top is 10% from viewport top
      threshold: 0
    };
    
    this.observer = new IntersectionObserver((entries) => {
      this.handleIntersection(entries);
    }, options);
    
    // Observe all sections
    this.sections.forEach(section => {
      this.observer.observe(section);
    });
  }
  
  handleIntersection(entries) {
    // Get all currently intersecting sections
    const intersectingSections = entries.filter(entry => entry.isIntersecting);
    
    if (intersectingSections.length === 0) return;
    
    // Find the section that should be active based on scroll position
    let activeSection = null;
    let closestDistance = Infinity;
    
    intersectingSections.forEach(entry => {
      const rect = entry.target.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      
      // Calculate distance from section top to the ideal trigger point (10% from viewport top)
      const idealTriggerPoint = viewportHeight * 0.1;
      const distanceFromTrigger = Math.abs(rect.top - idealTriggerPoint);
      
      // Prefer sections whose top is closest to our trigger point
      if (distanceFromTrigger < closestDistance) {
        closestDistance = distanceFromTrigger;
        activeSection = entry.target;
      }
    });
    
    if (activeSection) {
      const sectionId = activeSection.id;
      this.setActiveNavItem(sectionId);
    }
  }
  
  setActiveNavItem(sectionId) {
    // Remove active class from all nav items
    this.navLinks.forEach(link => {
      link.classList.remove('active');
    });
    
    // Add active class to the corresponding nav item
    const activeNavItem = document.querySelector(`#table-of-contents nav ul li[data-section="${sectionId}"]`);
    if (activeNavItem) {
      activeNavItem.classList.add('active');
    }
  }
}

// Initialize the SingleTopicManager when the script loads
const singleTopicManager = new SingleTopicManager();