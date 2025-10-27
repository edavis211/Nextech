<?php 
$block = $args['block'];
$resources = isset( $block['resources'] ) ? $block['resources'] : array();
?>
<section class="resource-grid">
  <div class="resource-wrap">
    <section class="resource-container">
      <div id="resource-results-container" class="resource-cards">
        <?php foreach ( $resources as $article ) : ?>
          <?php get_template_part( 'template-parts/cards/resource-card-detail', null, array( 'article' => $article['resource'] ) ); ?>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</section>