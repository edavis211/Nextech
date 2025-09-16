/**
 * Grade Level Dual-Handle Range Slider Handler
 * Manages a single slider with two handles for grade level filtering
 */

document.addEventListener('DOMContentLoaded', function() {
  const sliderContainer = document.querySelector('.grade-level-slider-container');
  console.log("Slider Container:", sliderContainer);
  if (!sliderContainer) return;
  
  // Get grade mapping data from the container
  const gradeData = JSON.parse(sliderContainer.dataset.grades || '[]');
  
  // Get slider elements
  const minSlider = document.getElementById('grade-min-range');
  const maxSlider = document.getElementById('grade-max-range');
  const minDisplay = document.querySelector('.range-label-min');
  const maxDisplay = document.querySelector('.range-label-max');
  const minHidden = document.getElementById('grade-min-hidden');
  const maxHidden = document.getElementById('grade-max-hidden');
  const rangeText = document.getElementById('grade-range-text');
  const sliderTrack = document.querySelector('.slider-track');
  
  if (!minSlider || !maxSlider) return;
  
  /**
   * Convert numeric value to grade info
   */
  function getGradeByValue(value) {
    return gradeData.find(grade => grade.value == value) || { name: 'Unknown', slug: '' };
  }
  
  /**
   * Update the visual track between the two handles
   */
  function updateSliderTrack() {
    const minVal = parseInt(minSlider.value);
    const maxVal = parseInt(maxSlider.value);
    const minRange = parseInt(minSlider.min);
    const maxRange = parseInt(minSlider.max);
    
    const minPercent = ((minVal - minRange) / (maxRange - minRange)) * 100;
    const maxPercent = ((maxVal - minRange) / (maxRange - minRange)) * 100;
    
    if (sliderTrack) {
      sliderTrack.style.background = `linear-gradient(
        to right,
        #fff 0%,
        #fff ${minPercent}%,
        #e98300 ${minPercent}%,
        #e98300 ${maxPercent}%,
        #fff ${maxPercent}%,
        #fff 100%
      )`;
    }
  }
  
  /**
   * Update display text and hidden values
   */
  function updateDisplays() {
    const minValue = parseInt(minSlider.value);
    const maxValue = parseInt(maxSlider.value);
    const minGrade = getGradeByValue(minValue);
    const maxGrade = getGradeByValue(maxValue);
    
    // Update individual displays
    if (minDisplay) minDisplay.textContent = minGrade.name;
    if (maxDisplay) maxDisplay.textContent = maxGrade.name;
    
    // Update hidden form values
    if (minHidden) minHidden.value = minGrade.slug;
    if (maxHidden) maxHidden.value = maxGrade.slug;
    
    // Update range summary
    if (rangeText) {
      if (minValue === parseInt(minSlider.min) && maxValue === parseInt(maxSlider.max)) {
        rangeText.textContent = 'All Grade Levels';
      } else if (minValue === maxValue) {
        rangeText.textContent = minGrade.name;
      } else {
        rangeText.textContent = `${minGrade.name} to ${maxGrade.name}`;
      }
    }
    
    updateSliderTrack();
  }
  
  /**
   * Ensure min slider doesn't exceed max slider
   */
  function validateMinSlider() {
    if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
      maxSlider.value = minSlider.value;
    }
    updateDisplays();
  }
  
  /**
   * Ensure max slider doesn't go below min slider
   */
  function validateMaxSlider() {
    if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
      minSlider.value = maxSlider.value;
    }
    updateDisplays();
  }
  
  // Initialize displays
  updateDisplays();
  
  // Add event listeners
  minSlider.addEventListener('input', validateMinSlider);
  maxSlider.addEventListener('input', validateMaxSlider);
  
  // Handle clear filters functionality
  const clearButton = document.getElementById('clear-filters');
  if (clearButton) {
    clearButton.addEventListener('click', function() {
      // Reset sliders to full range
      minSlider.value = minSlider.min;
      maxSlider.value = maxSlider.max;
      
      // Update displays
      updateDisplays();
      
      // Clear hidden values for "any" selection
      if (minHidden) minHidden.value = '';
      if (maxHidden) maxHidden.value = '';
    });
  }
});
