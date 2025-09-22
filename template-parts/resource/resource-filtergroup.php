<?php
// Taxonomy filter group for resources

// Get posts per page from passed args, fallback to default
$posts_per_page = $args['posts_per_page'] ?? 12;
$results_count_string = $args['results_count_string'] ?? '';
$total_published = $args['total_published'] ?? null;

// Function to build hierarchical term tree
function build_term_hierarchy( $terms, $parent_id = 0 ) {
  $hierarchy = array();
  foreach ( $terms as $term ) {
    if ( $term->parent == $parent_id ) {
      $term->children = build_term_hierarchy( $terms, $term->term_id );
      $hierarchy[] = $term;
    }
  }
  return $hierarchy;
}

// Function to render hierarchical checkbox inputs
function render_hierarchical_terms( $terms, $name, $level = 0, $collapsible = false ) {
  foreach ( $terms as $term ) {
    $has_children = ! empty( $term->children );
    
    if ( $has_children ) {
      $group_id = 'group-' . sanitize_title( $term->name );
      
      if ( $collapsible ) {
        // Parent term - collapsible details element
        echo '<details class="term-group collapsible" id="' . esc_attr( $group_id ) . '">';
        echo '<summary class="term-group-label" id="label-' . esc_attr( $group_id ) . '">' . esc_html( $term->name ) . '</summary>';
        echo '<div class="term-children" role="group" aria-labelledby="label-' . esc_attr( $group_id ) . '">';
        render_hierarchical_terms( $term->children, $name, $level + 1, $collapsible );
        echo '</div>';
        echo '</details>';
      } else {
        // Parent term - static group label, not interactive (original behavior)
        echo '<div class="term-group" id="' . esc_attr( $group_id ) . '">';
        echo '<h4 class="term-group-label" id="label-' . esc_attr( $group_id ) . '">' . esc_html( $term->name ) . '</h4>';
        echo '<div class="term-children" role="group" aria-labelledby="label-' . esc_attr( $group_id ) . '">';
        render_hierarchical_terms( $term->children, $name, $level + 1, $collapsible );
        echo '</div>';
        echo '</div>';
      }
    } else {
      // Child term - has checkbox
      $input_id = 'checkbox-' . sanitize_title( $name . '-' . $term->slug );
      echo '<label class="text-label term-child" for="' . esc_attr( $input_id ) . '">';
      echo '<input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $term->slug ) . '" id="' . esc_attr( $input_id ) . '">';
      echo esc_html( $term->name );
      echo '</label>';
    }
  }
}

// Function to render grade level range slider
function render_grade_level_slider( $grade_levels ) {
  // Create mapping for grade levels including special cases
  $grade_map = array();
  
  foreach ( $grade_levels as $grade ) {
    if ( ! empty( $grade->children ) ) {
      foreach ( $grade->children as $child_grade ) {
        $grade_map[] = array(
          'slug' => $child_grade->slug,
          'name' => $child_grade->name,
          'value' => get_grade_numeric_value( $child_grade->slug )
        );
      }
    } else {
      $grade_map[] = array(
        'slug' => $grade->slug,
        'name' => $grade->name,
        'value' => get_grade_numeric_value( $grade->slug )
      );
    }
  }
  
  // Sort by numeric value
  usort( $grade_map, function( $a, $b ) {
    return $a['value'] - $b['value'];
  });
  
  $min_value = min( array_column( $grade_map, 'value' ) );
  $max_value = max( array_column( $grade_map, 'value' ) );
  
  // Create JSON data for JavaScript
  $grade_map_json = json_encode( $grade_map );
  
  echo '<div class="grade-level-slider-container" data-grades="' . esc_attr( $grade_map_json ) . '">';
  
  echo '<div class="range-labels">';
  echo '<span class="range-label-min" id="grade-min-display">Pre-K</span>';
  echo '<span class="range-label-max" id="grade-max-display">Grade 12</span>';
  echo '</div>';

  echo '<div class="dual-range-slider">';
  echo '<div class="slider-track"></div>';
  echo '<input type="range" id="grade-min-range" class="range-min" ';
  echo 'min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" ';
  echo 'value="' . esc_attr( $min_value ) . '" step="1" ';
  echo 'aria-label="Minimum grade level">';
  echo '<input type="range" id="grade-max-range" class="range-max" ';
  echo 'min="' . esc_attr( $min_value ) . '" max="' . esc_attr( $max_value ) . '" ';
  echo 'value="' . esc_attr( $max_value ) . '" step="1" ';
  echo 'aria-label="Maximum grade level">';
  echo '</div>';
  
  
  echo '<div class="grade-range-display">';
  echo '<span id="grade-range-text">All Grade Levels</span>';
  echo '</div>';
  
  echo '<input type="hidden" name="filter-grade-level-min" id="grade-min-hidden" value="">';
  echo '<input type="hidden" name="filter-grade-level-max" id="grade-max-hidden" value="">';
  echo '</div>';
}

// Helper function to convert grade slugs to numeric values for sorting
function get_grade_numeric_value( $slug ) {
  switch ( strtolower( $slug ) ) {
    case 'pk':
      return -1;
    case 'k':
      return 0;
    default:
      // Handle numeric grades (1-12)
      if ( is_numeric( $slug ) ) {
        return intval( $slug );
      }
      return 999; // Unknown grades go to end
  }
}

// Subject Matter taxonomy - get all terms including hierarchy
$subjects_flat = get_terms( array(
  'taxonomy'   => 'subject-matter',
  'hide_empty' => true,
  'orderby'    => 'parent',
) );

$subjects = build_term_hierarchy( $subjects_flat );

// Resource Type taxonomy
$types_flat = get_terms( array(
  'taxonomy'   => 'resource-type',
  'hide_empty' => true,
  'orderby'    => 'parent',
) );

$types = build_term_hierarchy( $types_flat );

// Grade Level taxonomy
$grade_levels_flat = get_terms( array(
  'taxonomy'   => 'grade-level',
  'hide_empty' => true,
  'orderby'    => 'parent',
) );

$grade_levels = build_term_hierarchy( $grade_levels_flat );

// Academic Standards taxonomy
$standards_flat = get_terms( array(
  'taxonomy'   => 'academic-standard',
  'hide_empty' => true,
  'orderby'    => 'parent',
) );

$standards = build_term_hierarchy( $standards_flat );
?>

<section id="resource-filtergroup" class="resource-filtergroup" role="search" aria-labelledby="filter-heading" aria-expanded="false">
  <h2 id="filter-heading" class="text-label visually-hidden">Search & Filter</h2>
  <button class="toggle-filters filter-visibility-toggle" aria-expanded="false" title="Show or hide resource filters">
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
      <g>
        <path d="M28,9H11a1,1,0,0,1,0-2H28a1,1,0,0,1,0,2Z"/>
        <path d="M7,9H4A1,1,0,0,1,4,7H7A1,1,0,0,1,7,9Z"/>
        <path d="M21,17H4a1,1,0,0,1,0-2H21a1,1,0,0,1,0,2Z"/>
        <path d="M11,25H4a1,1,0,0,1,0-2h7a1,1,0,0,1,0,2Z"/>
        <path d="M9,11a3,3,0,1,1,3-3A3,3,0,0,1,9,11ZM9,7a1,1,0,1,0,1,1A1,1,0,0,0,9,7Z"/>
        <path d="M23,19a3,3,0,1,1,3-3A3,3,0,0,1,23,19Zm0-4a1,1,0,1,0,1,1A1,1,0,0,0,23,15Z"/>
        <path d="M13,27a3,3,0,1,1,3-3A3,3,0,0,1,13,27Zm0-4a1,1,0,1,0,1,1A1,1,0,0,0,13,23Z"/>
        <path d="M28,17H25a1,1,0,0,1,0-2h3a1,1,0,0,1,0,2Z"/>
        <path d="M28,25H15a1,1,0,0,1,0-2H28a1,1,0,0,1,0,2Z"/>
      </g>
    </svg>
  </button>

  <form action="" id="resource-filters" method="get" role="form" aria-label="Filter resources" data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>">
    
    <div class="search-field-group">
      <label for="resource-search" class="text-label">
        <span class="visually-hidden">Search Resources</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22.69 22.69">
          <g>
            <circle class="cls-1" cx="10.08" cy="10.08" r="9.08"/>
            <line class="cls-1" x1="16.33" y1="16.33" x2="21.98" y2="21.98"/>
          </g>
        </svg>
      </label>
      <input type="search" 
             id="resource-search" 
             name="search" 
             class="search-input" 
             placeholder="Enter keywords"
             aria-describedby="search-help">
      <span id="search-help" class="search-help-text visually-hidden">Search by title, description, or content</span>
    </div>
    
    <div class="selected-filters" role="status" aria-live="polite" aria-label="Currently selected filters">
      <h3 class="selected-filters-title visually-hidden">Active Filters</h3>
      <div class="selected-filters-list" id="selected-filters-container">
        <!-- JavaScript will populate this with selected filter tags -->
        <span class="no-filters-message">No filters selected</span>
      </div>
    </div>
    
    <div class="clear-filters-action">
      <button type="button" class="clear-filters" aria-label="Clear all selected filters">Clear Filters</button>
    </div>
    
    <fieldset class="filter-group">
      <legend class="visually-hidden">Subject Matter Filters</legend>
      <details aria-labelledby="subject-summary">
        <summary id="subject-summary" class="text-label" aria-expanded="false">Subject Matter</summary>
        <div class="filter-options" role="group" aria-labelledby="subject-summary">
          <?php render_hierarchical_terms( $subjects, 'filter-subject' ); ?>
        </div>
      </details>
    </fieldset>
    
    <fieldset class="filter-group">
      <legend class="visually-hidden">Resource Type Filters</legend>
      <details aria-labelledby="type-summary">
        <summary id="type-summary" class="text-label" aria-expanded="false">Resource Type</summary>
        <div class="filter-options" role="group" aria-labelledby="type-summary">
          <?php render_hierarchical_terms( $types, 'filter-type' ); ?>
        </div>
      </details>
    </fieldset>
    
    <fieldset class="filter-group">
      <legend class="visually-hidden">Grade Level Filters</legend>
      <details aria-labelledby="grade-summary">
        <summary id="grade-summary" class="text-label" aria-expanded="false">Grade Level</summary>
        <div class="filter-options" role="group" aria-labelledby="grade-summary">
          <?php render_grade_level_slider( $grade_levels ); ?>
        </div>
      </details>
    </fieldset>
    
    <fieldset class="filter-group">
      <legend class="visually-hidden">Academic Standard Filters</legend>
      <details aria-labelledby="standard-summary">
        <summary id="standard-summary" class="text-label" aria-expanded="false">Academic Standards</summary>
        <div class="filter-options" role="group" aria-labelledby="standard-summary">
          <?php render_hierarchical_terms( $standards, 'filter-standard', 0, true ); ?>
        </div>
      </details>
    </fieldset>
    
    <div class="filter-actions">
      <p id="no-results-message" hidden>No resources found matching your criteria.</p>
      <button type="button" id="no-results-reset" class="clear-filters" aria-label="Clear all selected filters" hidden>Clear Filters</button>
      <button id="show-results" type="submit" class="btn btn-primary">Show <?= $total_published ?> Results</button>
    </div>
    
  </form>
</section>
