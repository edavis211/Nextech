<?php
$breadcrumbs = [
  [
    'title' => 'Resource Library',
    'url' => home_url('/resource-library/')
  ]
];
$terms = get_the_terms( get_the_ID(), 'resource-type' );
//echo print_r($terms);
$root_term = false;
if ( $terms && ! is_wp_error( $terms ) ) {
  // Get the first term with a parent of 0 (top-level term)
  foreach ( $terms as $term ) {
    if ( $term->parent == 0 ) {
      $root_term = $term;
      break;
    }
  }
  if ( $root_term ) {
    $breadcrumbs[] = [
      'title' => $root_term->name,
      'url' => get_term_link( $root_term )
    ];
  }

  // Get the first with a parent id of the root term id (second-level term)
  foreach ( $terms as $term ) {
    if ( $term->parent == $root_term->term_id ) {
      $breadcrumbs[] = [
        'title' => $term->name,
        'url' => get_term_link( $term )
      ];
      break;
    }
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
