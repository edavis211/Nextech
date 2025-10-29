<?php
$resourceLibraryRootURL = home_url('/resource-library/');
$breadcrumbs = [
  [
    'title' => 'Resource Library',
    'url' => $resourceLibraryRootURL
  ]
];
$terms = get_the_terms( get_the_ID(), 'resource-type' );

if ( $terms && ! is_wp_error( $terms ) ) {
  $parent_term = false;
  $child_term = false;
  
  // First, identify parent and child terms
  foreach ( $terms as $term ) {
    if ( $term->parent == 0 ) {
      // This is a top-level term (potential parent)
      $parent_term = $term;
    } else {
      // This is a child term
      $child_term = $term;
    }
  }
  
  // If we have a parent term, check if it has children
  if ( $parent_term ) {
    $parent_has_children = count(get_terms([
      'taxonomy' => 'resource-type',
      'parent' => $parent_term->term_id,
      'hide_empty' => false,
    ])) > 0;
    
    // Only add parent term to breadcrumbs if it has children
    if ( $parent_has_children ) {
      // Get all children of the parent term for the filter URL
      $child_terms = get_terms( [
        'taxonomy' => 'resource-type',
        'parent' => $parent_term->term_id,
        'hide_empty' => false,
      ] );
      $child_filters = [];
      foreach ( $child_terms as $child_term_obj ) {
        $child_filters[] = $child_term_obj->slug;
      }
      $childFiltersString = implode( ',', $child_filters );

      $breadcrumbs[] = [
        'title' => $parent_term->name,
        'url' => $resourceLibraryRootURL . '?resource_type=' . $childFiltersString
      ];
    }
  }
  
  // Add child term to breadcrumbs if it exists
  if ( $child_term ) {
    $breadcrumbs[] = [
      'title' => $child_term->name,
      'url' => $resourceLibraryRootURL . '?resource_type=' . $child_term->slug
    ];
  } elseif ( $parent_term && !$parent_has_children ) {
    // If there's only a parent term with no children, treat it as the final term
    $breadcrumbs[] = [
      'title' => $parent_term->name,
      'url' => $resourceLibraryRootURL . '?resource_type=' . $parent_term->slug
    ];
  }
}
?>
<nav aria-label="Breadcrumb" class="breadcrumbs">
  <?php if ( count($breadcrumbs) ) : ?>
    <ol class="breadcrumb-list">
      <?php foreach ( $breadcrumbs as $crumb ) : ?>
        <li class="breadcrumb-item">
          <a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['title'] ); ?></a>
        </li>
      <?php endforeach; ?>
      <li class="breadcrumb-item current" aria-current="page">
        <?php the_title(); ?>
      </li>
    </ol>
  <?php endif; ?>
</nav>
