/**
 * Resource Filter Handler Class
 * Manages all filter interactions and real-time AJAX filtering
 */

class ResourceFilterHandler {
  constructor() {
    this.form = document.getElementById('resource-filters');
    this.infoBar = document.querySelector('#resource-info-bar');
    this.resultsCount = document.getElementById('resource-results-count');
    this.container = document.querySelector('.resource-filtergroup');
    this.resultsContainer = document.querySelector('#resource-results-container');
    this.sortSelect = document.getElementById('sort-by'); // Add sort selector
    this.filterToggleButtons = document.querySelectorAll('.filter-visibility-toggle'); // Changed to class selector for multiple buttons
    this.filterSection = document.getElementById('resource-filtergroup'); // Add filter section
    this.debounceTimer = null;
    this.debounceDelay = 300; // ms
    this.clearFiltersButtonContainer = document.getElementById('clear-filters-container');
    
    // Infinite scroll properties
    this.currentPage = 1;
    this.postsPerPage = parseInt(this.form?.dataset.postsPerPage) || 12;
    this.isLoading = false;
    this.hasMorePosts = true;
    this.totalPages = 1;
    
    // Grade slider specific properties
    this.gradeSliderContainer = document.querySelector('.grade-level-slider-container');
    this.gradeData = [];
    this.minSlider = null;
    this.maxSlider = null;
    
    this.init();
  }
  
  /**
   * Initialize the filter handler
   */
  init() {
    if (!this.form) return;
    
    this.createLoadingIndicator();
    this.initializeGradeSlider();
    this.setInitialFilterVisibility();
    this.bindEvents();
    this.initializeFromURL();
    this.initializeInfiniteScroll();
  }
  
  /**
   * Create loading indicator
   */
  createLoadingIndicator() {
    this.loadingIndicator = document.createElement('div');
    this.loadingIndicator.className = 'filter-loading';
    this.loadingIndicator.innerHTML = '<div class="loading-spinner"></div><span>Updating results...</span>';
    this.loadingIndicator.style.display = 'none';
    
    if (this.resultsContainer) {
      this.resultsContainer.parentNode.insertBefore(this.loadingIndicator, this.resultsContainer);
    }
  }
  
  /**
   * Initialize grade level slider functionality
   */
  initializeGradeSlider() {
    if (!this.gradeSliderContainer) return;
    
    // Get grade mapping data
    try {
      this.gradeData = JSON.parse(this.gradeSliderContainer.dataset.grades || '[]');
    } catch (e) {
      console.warn('Could not parse grade data:', e);
      return;
    }
    
    // Get slider elements
    this.minSlider = document.getElementById('grade-min-range');
    this.maxSlider = document.getElementById('grade-max-range');
    this.minDisplay = document.querySelector('.range-label-min');
    this.maxDisplay = document.querySelector('.range-label-max');
    this.minHidden = document.getElementById('grade-min-hidden');
    this.maxHidden = document.getElementById('grade-max-hidden');
    this.rangeText = document.getElementById('grade-range-text');
    this.sliderTrack = document.querySelector('.slider-track');
    
    if (this.minSlider && this.maxSlider) {
      this.updateGradeDisplays();
      this.bindGradeSliderEvents();
    }
  }
  
  /**
   * Bind all event listeners
   */
  bindEvents() {
    // Search input
    const searchInput = document.getElementById('resource-search');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.updateSelectedFilters();
        this.debouncedFilter();
      });
    }
    
    // Sort dropdown
    if (this.sortSelect) {
      this.sortSelect.addEventListener('change', () => {
        this.updateSelectedFilters();
        this.debouncedFilter();
      });
    }
    
    // Taxonomy checkboxes
    const checkboxes = this.form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        this.updateSelectedFilters();
        this.debouncedFilter();
      });
    });
    
    // Clear filters button
    const clearFiltersButtons = this.form.querySelectorAll('.clear-filters');
    clearFiltersButtons.forEach(button => {
      button.addEventListener('click', () => {
        this.clearAllFilters();
      });
    });
    
    // Event delegation for dynamically created clear filters button in no-results message
    if (this.resultsContainer) {
      this.resultsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('clear-filters-btn')) {
          this.clearAllFilters();
        }
      });
    }
    
    // Filter visibility toggle buttons (multiple buttons support)
    if (this.filterToggleButtons.length > 0 && this.filterSection) {
      this.filterToggleButtons.forEach(button => {
        button.addEventListener('click', () => {
          this.toggleFilterVisibility();
        });
      });
    }
    
    // Handle resize events to reset visibility on screen size change
    window.addEventListener('resize', () => {
      this.handleResize();
    });
    
    // Form submission (prevent default, handle via AJAX)
    this.form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.performFilter();

      // Close filters on mobile after submit
      if (window.innerWidth < 1200 && this.filterSection) {
        this.filterSection.setAttribute('aria-expanded', 'false');
        document.body.setAttribute('data-filters-expanded', 'false');
        this.filterToggleButtons.forEach(button => {
          button.setAttribute('aria-expanded', 'false');
          const buttonText = button.querySelector('.visually-hidden');
          if (buttonText) buttonText.textContent = 'Show Filters';
          button.setAttribute('title', 'Show resource filters');
        });
      }

      // Scroll to main#primary on filter submit
      // const primarySection = document.getElementById('primary');
      // if (primarySection) {
      //   primarySection.scrollIntoView({ behavior: 'smooth' });
      // }

      // Focus results count for accessibility
      if (this.resultsCount) {
        this.resultsCount.focus();
      }
      //scroll to H1 
      const h1 = document.querySelector('main#primary h1');
      if (h1) {
        h1.scrollIntoView({ behavior: 'smooth' });
      }

    });
  }
  
  /**
   * Bind grade slider specific events
   */
  bindGradeSliderEvents() {
    this.minSlider.addEventListener('input', () => {
      this.validateMinSlider();
      this.updateSelectedFilters();
      this.debouncedFilter();
    });
    
    this.maxSlider.addEventListener('input', () => {
      this.validateMaxSlider();
      this.updateSelectedFilters();
      this.debouncedFilter();
    });
  }
  
  /**
   * Grade slider validation and display updates
   */
  validateMinSlider() {
    if (parseInt(this.minSlider.value) > parseInt(this.maxSlider.value)) {
      this.maxSlider.value = this.minSlider.value;
    }
    this.updateGradeDisplays();
  }
  
  validateMaxSlider() {
    if (parseInt(this.maxSlider.value) < parseInt(this.minSlider.value)) {
      this.minSlider.value = this.maxSlider.value;
    }
    this.updateGradeDisplays();
  }
  
  /**
   * Update grade slider displays and track
   */
  updateGradeDisplays() {
    const minValue = parseInt(this.minSlider.value);
    const maxValue = parseInt(this.maxSlider.value);
    const minGrade = this.getGradeByValue(minValue);
    const maxGrade = this.getGradeByValue(maxValue);
    
    // Check if full range is selected
    const isFullRange = minValue === parseInt(this.minSlider.min) && 
                        maxValue === parseInt(this.maxSlider.max);
    
    // Update individual displays
    if (this.minDisplay) this.minDisplay.textContent = minGrade.name;
    if (this.maxDisplay) this.maxDisplay.textContent = maxGrade.name;
    
    // Update hidden form values (clear if full range)
    if (this.minHidden) this.minHidden.value = isFullRange ? '' : minGrade.slug;
    if (this.maxHidden) this.maxHidden.value = isFullRange ? '' : maxGrade.slug;
    
    // Update range summary
    if (this.rangeText) {
      if (isFullRange) {
        this.rangeText.textContent = 'All Grade Levels';
      } else if (minValue === maxValue) {
        this.rangeText.textContent = minGrade.name;
      } else {
        this.rangeText.textContent = `${minGrade.name} to ${maxGrade.name}`;
      }
    }
    
    this.updateSliderTrack();
  }
  
  /**
   * Update visual track between slider handles
   */
  updateSliderTrack() {
    if (!this.sliderTrack) return;
    
    const minVal = parseInt(this.minSlider.value);
    const maxVal = parseInt(this.maxSlider.value);
    const minRange = parseInt(this.minSlider.min);
    const maxRange = parseInt(this.minSlider.max);
    
    const minPercent = ((minVal - minRange) / (maxRange - minRange)) * 100;
    const maxPercent = ((maxVal - minRange) / (maxRange - minRange)) * 100;
    
    this.sliderTrack.style.background = `linear-gradient(
      to right,
      #ddd 0%,
      #ddd ${minPercent}%,
      #e98300 ${minPercent}%,
      #e98300 ${maxPercent}%,
      #ddd ${maxPercent}%,
      #ddd 100%
    )`;
  }
  
  /**
   * Get grade info by numeric value
   */
  getGradeByValue(value) {
    return this.gradeData.find(grade => grade.value == value) || { name: 'Unknown', slug: '' };
  }
  
  /**
   * Debounced filter execution
   */
  debouncedFilter() {
    clearTimeout(this.debounceTimer);
    this.debounceTimer = setTimeout(() => {
      this.performFilter();
    }, this.debounceDelay);
  }
  
  /**
   * Collect all filter data
   */
  getFilterData() {
    const formData = new FormData(this.form);
    
    // Get raw grade values
    let gradeMin = formData.get('filter-grade-level-min') || '';
    let gradeMax = formData.get('filter-grade-level-max') || '';
    
    // Check if full range is selected (treat as no filter)
    if (this.minSlider && this.maxSlider) {
      const isFullRange = parseInt(this.minSlider.value) === parseInt(this.minSlider.min) && 
                          parseInt(this.maxSlider.value) === parseInt(this.maxSlider.max);
      
      if (isFullRange) {
        gradeMin = '';
        gradeMax = '';
      }
    }
    
    const filterData = {
      search: formData.get('search') || '',
      subject_matter: formData.getAll('filter-subject[]'),
      resource_type: formData.getAll('filter-type[]'),
      grade_level_min: gradeMin,
      grade_level_max: gradeMax,
      academic_standard: formData.getAll('filter-standard[]'),
      queried_post_types: formData.get('queried_post_types') || 'resource,topic',
      sort_by: this.sortSelect ? this.sortSelect.value : 'newest', // Add sort option
      posts_per_page: this.postsPerPage,
      page: this.currentPage,
      action: 'filter_resources',
      nonce: resourceFilterData.nonce || ''
    };
    
    return filterData;
  }
  
  /**
   * Perform AJAX filter request
   */
  async performFilter() {
    // Reset pagination for new filter request
    this.resetPagination();
    
    const filterData = this.getFilterData();
    
    this.showLoading(true);
    
    try {
      // Create FormData with proper array formatting for PHP
      const formData = new FormData();
      
      // Add simple fields
      formData.append('search', filterData.search);
      formData.append('grade_level_min', filterData.grade_level_min);
      formData.append('grade_level_max', filterData.grade_level_max);
      formData.append('queried_post_types', filterData.queried_post_types);
      formData.append('sort_by', filterData.sort_by); // Add sort parameter
      formData.append('posts_per_page', this.postsPerPage);
      formData.append('page', 1); // Always start at page 1 for new filters
      formData.append('action', filterData.action);
      formData.append('nonce', filterData.nonce);
      
      // Add arrays with proper PHP naming convention
      filterData.subject_matter.forEach(value => {
        formData.append('subject_matter[]', value);
      });
      
      filterData.resource_type.forEach(value => {
        formData.append('resource_type[]', value);
      });
      
      filterData.academic_standard.forEach(value => {
        formData.append('academic_standard[]', value);
      });
      
      const response = await fetch(resourceFilterData.ajaxurl, {
        method: 'POST',
        body: formData
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (data.success) {
        this.updateResults(data.data);
        this.updateURL(filterData);
      } else {
        console.error('Filter error:', data.data);
        this.showError('Failed to load results. Please try again.');
      }
      
    } catch (error) {
      console.error('AJAX error:', error);
      this.showError('Network error. Please check your connection.');
    } finally {
      this.showLoading(false);
    }
  }
  
  /**
   * Update results display
   */
  updateResults(data) {
    if (this.resultsContainer) {
      this.resultsContainer.innerHTML = data.html || '<p>No resources found.</p>';
    }
    
    // Update pagination state
    if (data.pagination) {
      this.currentPage = data.pagination.current_page;
      this.totalPages = data.pagination.total_pages;
      this.hasMorePosts = this.currentPage < this.totalPages;
    }
    
    // Update result count if element exists
    const countElement = document.querySelector('.results-count');
    if (countElement && data.count !== undefined) {
      if (data.showing !== undefined) {
        // Use the detailed showing format for filtered results
        if (data.count === 1) {
          countElement.textContent = `Showing 1 of 1 item`;
        } else {
          countElement.textContent = `Showing ${data.showing} of ${data.count} items`;
        }
      } else {
        // Fallback to simple count
        countElement.textContent = `${data.count} results found`;
      }
    }
    
    // Update submit button text with current results count
    const submitButton = document.getElementById('show-results');
    if (submitButton && data.count !== undefined) {
      const count = data.count || 0;
      const resultText = count === 1 ? 'Result' : 'Results';
      submitButton.textContent = `Show ${count} ${resultText}`;
      
      // Hide submit button when no results, show when results exist
      if (count === 0) {
        submitButton.setAttribute('hidden', '');
      } else {
        submitButton.removeAttribute('hidden');
      }
    }
    
    // Show/hide the no-results reset button and message based on results count
    const noResultsResetButton = document.getElementById('no-results-reset');
    const noResultsMessage = document.getElementById('no-results-message');
    
    if (data.count === 0) {
      // No results found - show the reset button and message
      if (noResultsResetButton) {
        noResultsResetButton.removeAttribute('hidden');
      }
      if (noResultsMessage) {
        noResultsMessage.removeAttribute('hidden');
      }
    } else {
      // Results found - hide the reset button and message
      if (noResultsResetButton) {
        noResultsResetButton.setAttribute('hidden', '');
      }
      if (noResultsMessage) {
        noResultsMessage.setAttribute('hidden', '');
      }
    }
    
    // Trigger custom event for other scripts
    document.dispatchEvent(new CustomEvent('resourcesFiltered', {
      detail: { data, filters: this.getFilterData() }
    }));
  }
  
  /**
   * Update selected filters display
   */
  updateSelectedFilters() {
    const selectedContainer = document.getElementById('selected-filters-container');
    if (!selectedContainer) return;
    
    const filterData = this.getFilterData();
    const selectedTags = [];
    
    // Add search term
    if (filterData.search) {
      selectedTags.push({
        type: 'search',
        value: filterData.search,
        label: `Search: "${filterData.search}"`
      });
    }
    
    // Add taxonomy filters
    ['subject_matter', 'resource_type', 'academic_standard'].forEach(taxonomy => {
      if (filterData[taxonomy] && filterData[taxonomy].length > 0) {
        filterData[taxonomy].forEach(slug => {
          const element = this.form.querySelector(`input[value="${slug}"]`);
          if (element) {
            const label = element.closest('label').textContent.trim();
            selectedTags.push({
              type: taxonomy,
              value: slug,
              label: label
            });
          }
        });
      }
    });
    
    // Add grade range
    if (filterData.grade_level_min || filterData.grade_level_max) {
      const minGrade = this.getGradeByValue(parseInt(this.minSlider.value));
      const maxGrade = this.getGradeByValue(parseInt(this.maxSlider.value));
      
      let gradeLabel;
      if (minGrade.slug === maxGrade.slug) {
        gradeLabel = minGrade.name;
      } else {
        gradeLabel = `${minGrade.name} to ${maxGrade.name}`;
      }
      
      selectedTags.push({
        type: 'grade_range',
        value: `${filterData.grade_level_min}-${filterData.grade_level_max}`,
        label: `Grade: ${gradeLabel}`
      });
    }
    
    // Update display
    if (selectedTags.length === 0) {
      selectedContainer.innerHTML = '<span class="no-filters-message">No filters selected</span>';
      this.clearFiltersButtonContainer?.setAttribute('hidden', '');
    } else {
      this.clearFiltersButtonContainer?.removeAttribute('hidden');
      const tagsHtml = selectedTags.map(tag => 
        `<span class="filter-tag" data-type="${tag.type}" data-value="${tag.value}">
          ${tag.label}
          <button type="button" class="remove-filter" aria-label="Remove ${tag.label}">×</button>
        </span>`
      ).join('');
      
      selectedContainer.innerHTML = tagsHtml;
      
      // Bind remove events
      selectedContainer.querySelectorAll('.remove-filter').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const tag = e.target.closest('.filter-tag');
          this.removeFilter(tag.dataset.type, tag.dataset.value);
        });
      });
    }
  }
  
  /**
   * Remove individual filter
   */
  removeFilter(type, value) {
    if (type === 'search') {
      const searchInput = document.getElementById('resource-search');
      if (searchInput) searchInput.value = '';
    } else if (type === 'grade_range') {
      this.resetGradeSlider();
    } else {
      const checkbox = this.form.querySelector(`input[value="${value}"]`);
      if (checkbox) checkbox.checked = false;
    }
    
    this.updateSelectedFilters();
    this.performFilter();
  }
  
  /**
   * Clear all filters
   */
  clearAllFilters() {
    // Clear search
    const searchInput = document.getElementById('resource-search');
    if (searchInput) searchInput.value = '';
    
    // Clear checkboxes
    const checkboxes = this.form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Reset grade slider
    this.resetGradeSlider();
    
    // Update displays
    this.updateSelectedFilters();
    this.performFilter();
  }

  /**
   * Toggle filter visibility
   */
  toggleFilterVisibility() {
    if (this.filterToggleButtons.length === 0 || !this.filterSection) return;
    
    // Get current state from the first button (they should all be in sync)
    const isExpanded = this.filterToggleButtons[0].getAttribute('aria-expanded') === 'true';
    const newState = !isExpanded;
    
    // Update all toggle buttons
    this.filterToggleButtons.forEach(button => {
      button.setAttribute('aria-expanded', newState.toString());
      
      // Update button text for screen readers
      const buttonText = button.querySelector('.visually-hidden');
      if (buttonText) {
        buttonText.textContent = newState ? 'Hide Filters' : 'Show Filters';
      }
      
      // Update button title
      button.setAttribute('title', newState ? 'Hide resource filters' : 'Show resource filters');
    });
    
    // Update section aria-expanded
    this.filterSection.setAttribute('aria-expanded', newState.toString());
    
    // Update body data attribute
    document.body.setAttribute('data-filters-expanded', newState.toString());
  }

  /**
   * Set initial filter visibility based on screen size
   */
  setInitialFilterVisibility() {
    if (this.filterToggleButtons.length === 0 || !this.filterSection) return;
    
    // Check if mobile or tablet
    const isMobile = window.innerWidth < 1200;
    
    // Set initial state: expanded on desktop, collapsed on mobile
    const initialState = !isMobile;
    
    // Update all toggle buttons
    this.filterToggleButtons.forEach(button => {
      button.setAttribute('aria-expanded', initialState.toString());
      
      // Update button text
      const buttonText = button.querySelector('.visually-hidden');
      if (buttonText) {
        buttonText.textContent = initialState ? 'Hide Filters' : 'Show Filters';
      }
      
      // Update button title
      button.setAttribute('title', initialState ? 'Hide resource filters' : 'Show resource filters');
    });
    
    // Update section aria-expanded
    this.filterSection.setAttribute('aria-expanded', initialState.toString());
    
    // Update body data attribute
    document.body.setAttribute('data-filters-expanded', initialState.toString());
  }

  /**
   * Handle window resize to adjust filter visibility
   */
  handleResize() {
    // Debounce resize events
    clearTimeout(this.resizeTimer);
    this.resizeTimer = setTimeout(() => {
      this.setInitialFilterVisibility();
    }, 150);
  }
  
  /**
   * Reset grade slider to full range
   */
  resetGradeSlider() {
    if (!this.minSlider || !this.maxSlider) return;
    
    this.minSlider.value = this.minSlider.min;
    this.maxSlider.value = this.maxSlider.max;
    
    if (this.minHidden) this.minHidden.value = '';
    if (this.maxHidden) this.maxHidden.value = '';
    
    this.updateGradeDisplays();
  }
  
  /**
   * Update URL with current filters (for bookmarking/sharing)
   */
  updateURL(filterData) {
    const url = new URL(window.location);
    
    // Clear existing filter params
    ['search', 'subject_matter', 'resource_type', 'grade_min', 'grade_max', 'academic_standard'].forEach(param => {
      url.searchParams.delete(param);
    });
    
    // Add current filters
    if (filterData.search) url.searchParams.set('search', filterData.search);
    if (filterData.subject_matter.length) url.searchParams.set('subject_matter', filterData.subject_matter.join(','));
    if (filterData.resource_type.length) url.searchParams.set('resource_type', filterData.resource_type.join(','));
    if (filterData.grade_level_min) url.searchParams.set('grade_min', filterData.grade_level_min);
    if (filterData.grade_level_max) url.searchParams.set('grade_max', filterData.grade_level_max);
    if (filterData.academic_standard.length) url.searchParams.set('academic_standard', filterData.academic_standard.join(','));
    
    window.history.replaceState({}, '', url);
  }
  
  /**
   * Initialize filters from URL parameters
   */
  initializeFromURL() {
    const url = new URL(window.location);
    
    // Set search
    const search = url.searchParams.get('search');
    if (search) {
      const searchInput = document.getElementById('resource-search');
      if (searchInput) searchInput.value = search;
    }
    
    // Set checkboxes
    ['subject_matter', 'resource_type', 'academic_standard'].forEach(taxonomy => {
      const values = url.searchParams.get(taxonomy);
      if (values) {
        values.split(',').forEach(value => {
          const checkbox = this.form.querySelector(`input[value="${value}"]`);
          if (checkbox) checkbox.checked = true;
        });
      }
    });
    
    // Set grade range
    const gradeMin = url.searchParams.get('grade_min');
    const gradeMax = url.searchParams.get('grade_max');
    if (gradeMin || gradeMax) {
      // Find corresponding numeric values and set sliders
      if (gradeMin && this.minSlider) {
        const minGrade = this.gradeData.find(g => g.slug === gradeMin);
        if (minGrade) this.minSlider.value = minGrade.value;
      }
      if (gradeMax && this.maxSlider) {
        const maxGrade = this.gradeData.find(g => g.slug === gradeMax);
        if (maxGrade) this.maxSlider.value = maxGrade.value;
      }
      this.updateGradeDisplays();
    }
    
    // Update displays and perform initial filter
    this.updateSelectedFilters();
    this.performFilter();
  }
  
  /**
   * Show/hide loading indicator
   */
  showLoading(show) {
    if (this.loadingIndicator) {
      this.loadingIndicator.style.display = show ? 'block' : 'none';
    }
    
    if (this.resultsContainer) {
      this.resultsContainer.style.opacity = show ? '0.5' : '1';
    }
  }

  /**
   * Initialize infinite scroll
   */
  initializeInfiniteScroll() {
    this.throttleTimer = null;
    this.scrollThreshold = 800; // pixels from bottom to trigger load (increased from 300)
    
    window.addEventListener('scroll', () => {
      if (this.throttleTimer) return;
      
      this.throttleTimer = setTimeout(() => {
        this.checkScrollPosition();
        this.throttleTimer = null;
      }, 100);
    });
  }

  /**
   * Check if we should load more posts
   */
  checkScrollPosition() {
    if (this.isLoading || !this.hasMorePosts) return;
    
    const scrollPosition = window.innerHeight + window.scrollY;
    const documentHeight = document.documentElement.scrollHeight;
    
    if (scrollPosition >= documentHeight - this.scrollThreshold) {
      this.loadMorePosts();
    }
  }

  /**
   * Load more posts for infinite scroll
   */
  async loadMorePosts() {
    if (this.isLoading || !this.hasMorePosts) return;
    
    this.isLoading = true;
    this.showInfiniteScrollLoading();
    
    try {
      const filterData = this.getFilterData();
      filterData.page = this.currentPage + 1;
      
      const formData = new FormData();
      
      // Add all filter data
      Object.keys(filterData).forEach(key => {
        if (Array.isArray(filterData[key])) {
          filterData[key].forEach(value => {
            formData.append(`${key}[]`, value);
          });
        } else {
          formData.append(key, filterData[key]);
        }
      });
      
      const response = await fetch(resourceFilterData.ajaxurl, {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success && data.data.html) {
        // Append new content instead of replacing
        this.resultsContainer.insertAdjacentHTML('beforeend', data.data.html);
        
        this.currentPage++;
        this.totalPages = data.data.pagination.total_pages;
        this.hasMorePosts = this.currentPage < this.totalPages;
        
        // Update results count for infinite scroll
        const countElement = document.querySelector('.results-count');
        if (countElement && data.data.showing !== undefined) {
          if (data.data.count === 1) {
            countElement.textContent = `Showing 1 of 1 resource`;
          } else {
            countElement.textContent = `Showing ${data.data.showing} of ${data.data.count} resources`;
          }
        }
        
        // Update URL without scrolling
        this.updateURL(filterData, false);
        
        // Trigger custom event
        document.dispatchEvent(new CustomEvent('moreResourcesLoaded', {
          detail: { data: data.data, page: this.currentPage }
        }));
      } else {
        this.hasMorePosts = false;
      }
      
    } catch (error) {
      console.error('Error loading more posts:', error);
      this.hasMorePosts = false;
    } finally {
      this.isLoading = false;
      this.hideInfiniteScrollLoading();
    }
  }

  /**
   * Reset pagination for new filters
   */
  resetPagination() {
    this.currentPage = 1;
    this.hasMorePosts = true;
    this.totalPages = 1;
  }

  /**
   * Show loading indicator for infinite scroll
   */
  showInfiniteScrollLoading() {
    let indicator = document.querySelector('.infinite-scroll-loading');
    if (!indicator) {
      indicator = document.createElement('div');
      indicator.className = 'infinite-scroll-loading';
      indicator.innerHTML = '<div class="loading-spinner"></div><span>Loading more resources...</span>';
      this.resultsContainer.insertAdjacentElement('afterend', indicator);
    }
    indicator.style.display = 'block';
  }

  /**
   * Hide loading indicator for infinite scroll
   */
  hideInfiniteScrollLoading() {
    const indicator = document.querySelector('.infinite-scroll-loading');
    if (indicator) {
      indicator.style.display = 'none';
    }
  }
  
  /**
   * Show error message
   */
  showError(message) {
    // You can customize this to match your site's error handling
    console.error(message);
    
    // Optional: Show error in UI
    const errorElement = document.querySelector('.filter-error');
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = 'block';
      setTimeout(() => {
        errorElement.style.display = 'none';
      }, 5000);
    }
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  window.resourceFilterHandler = new ResourceFilterHandler();
});
