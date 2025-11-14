<?php 
$article = $args['article'];
if ( ! isset( $article )  || empty( $article ) ) {
  return;
}
$post_type = get_post_type( $article );

$resource_type = get_the_terms( $article->ID, 'resource-type' );
$type_string = $post_type; // Default label
$type_slug = $post_type; // Default slug for data attribute
$resource_type_parent_slug = ''; // New attribute for resource-type-parent

// Get primary resource type and its parent for new data attribute
if ( is_array( $resource_type ) ) {
  // Try to get the primary term from Yoast SEO
  $primary_term_id = get_post_meta( $article->ID, '_yoast_wpseo_primary_resource-type', true );
  $primary_term = null;
  
  if ( $primary_term_id ) {
    // Find the primary term in our terms array
    foreach ( $resource_type as $term ) {
      if ( $term->term_id == $primary_term_id ) {
        $primary_term = $term;
        break;
      }
    }
  }
  
  // If no primary term found, use the first term as fallback
  if ( ! $primary_term ) {
    $primary_term = $resource_type[0];
  }
  
  // Determine the parent slug for the new data attribute
  if ( $primary_term->parent != 0 ) {
    // Term has a parent, get the parent's slug
    $parent_term = get_term( $primary_term->parent, 'resource-type' );
    if ( $parent_term && ! is_wp_error( $parent_term ) ) {
      $resource_type_parent_slug = $parent_term->slug;
    }
  } else {
    // Term is already a parent/top-level term, use its own slug
    $resource_type_parent_slug = $primary_term->slug;
  }
}

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
  class="resource-card-detail" 
  aria-labelledby="resource-title-<?php echo esc_attr( $article->ID ); ?>" 
  data-resource-type="<?php echo esc_attr( $type_slug ); ?>"
  data-resource-type-parent="<?php echo esc_attr( $resource_type_parent_slug ); ?>"
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
