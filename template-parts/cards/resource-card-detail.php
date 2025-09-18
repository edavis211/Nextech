<?php 
$article = $args['article']; 
?>
<article class="resource-card resource-card-detail" aria-labelledby="resource-title-<?php echo esc_attr( $article->ID ); ?>">
  <a href="<?php echo get_permalink( $article->ID ); ?>">
    <?php get_template_part( 'template-parts/resource/resource-types', null, array( 'id' => $article->ID ) ); ?>
    <h3><?php echo get_the_title( $article->ID ); ?></h3>
    <div class="resource-excerpt">
      <?php echo get_the_excerpt( $article->ID ); ?>
    </div>
    <?php get_template_part( 'template-parts/resource/resource-grade-levels', null, array( 'id' => $article->ID ) ); ?>
    <?php get_template_part( 'template-parts/resource/resource-topics', null, array( 'id' => $article->ID ) ); ?>
  </a>
</article>
