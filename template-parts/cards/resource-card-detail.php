<?php 
$article = $args['article'];
$post_type = get_post_type( $article );

$resource_type = get_the_terms( $article->ID, 'resource-type' );
$type_string = $post_type; // Default label
$type_slug = $post_type; // Default slug for data attribute
// loop through all resource types ignoring all parents 
// insert commas between terms
if ( is_array( $resource_type ) ){
  foreach ( $resource_type as $type ) {
    if ( $type->parent != 0 ) {
      $type_string = $type->name;
      $type_slug = $type->slug;
      break;
    }
  }
}

$group_type = get_field( 'group_type'); // ACF field used on 'topic' post type only
if( $post_type === 'topic' && !empty($group_type) ) {
  $type_string = $group_type;
  $type_slug = sanitize_title( $group_type );
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
      <span class="resource-type"><?php echo esc_html( $type_string ); ?></span>
    </div>
    <h3><?php echo get_the_title( $article->ID ); ?></h3>
    <div class="resource-excerpt">
      <?php echo get_the_excerpt( $article->ID ); ?>
    </div>

    <?php get_template_part( 'template-parts/resource/resource-grade-levels', null, array( 'id' => $article->ID ) ); ?>
    <?php get_template_part( 'template-parts/resource/resource-topics', null, array( 'id' => $article->ID ) ); ?>
  </a>
</article>
