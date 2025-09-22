<?php 
$article = $args['article'];
$post_type = get_post_type( $article );
$resource_type = get_the_terms( $article->ID, 'resource-type' );
$primary_type = ( $resource_type && ! is_wp_error( $resource_type ) ) ? $resource_type[0] : null;
$group_type = get_field( 'group_type'); // ACF field used on 'topic' post type only
if ( $primary_type ) {
  // if primary is not a parent, find the top-level parent
  if ( $primary_type->parent != 0 ) {
    $ancestors = get_ancestors( $primary_type->term_id, 'resource-type' );
    $top_ancestor_id = end( $ancestors );
    $top_ancestor = get_term( $top_ancestor_id, 'resource-type' );
    $type_name = $top_ancestor->name;
    $type_slug = $top_ancestor->slug;
  } else {
    $type_name = $primary_type->name;
    $type_slug = $primary_type->slug;
  }
} else {
  $type_slug = 'default';
  $type_name = 'Resource';
}

$resource_type_children = null;
// loop through all terms ignoring all parents 
if ( $resource_type && ! is_wp_error( $resource_type ) ) {
  foreach ( $resource_type as $type ) {
    if ( $type->parent != 0 ) {
      $resource_type_children[] = $type;
    }
  }
}
?>
<article 
  class="resource-card resource-card-detail" 
  aria-labelledby="resource-title-<?php echo esc_attr( $article->ID ); ?>" 
  data-resource-type="<?php echo esc_attr( $type_slug ); ?>"
  data-group-type="<?php echo esc_attr( $group_type ); ?>"
  data-post-type="<?php echo esc_attr( $post_type ); ?>"
>
  <a href="<?php echo get_permalink( $article->ID ); ?>">
    <div class="resource-types">
      <span class="resource-type"><?php echo esc_html( $type_name ); ?></span>
    </div>
    <h3><?php echo get_the_title( $article->ID ); ?></h3>
    <div class="resource-excerpt">
      <?php echo get_the_excerpt( $article->ID ); ?>
    </div>
    <?php if ( $resource_type_children ): ?>
      <div class="resource-type-children">
        <?php foreach ( $resource_type_children as $child ): ?>
          <span class="resource-type-child"><?php echo esc_html( $child->name ); ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php get_template_part( 'template-parts/resource/resource-grade-levels', null, array( 'id' => $article->ID ) ); ?>
    <?php get_template_part( 'template-parts/resource/resource-topics', null, array( 'id' => $article->ID ) ); ?>
  </a>
</article>
