<?php
$related_resources = get_field('related_resources');
if( $related_resources && is_array( $related_resources ) && count( $related_resources ) > 0 ): ?>
<section class="related-resources">
  <div class="inner">
    <h2>Related Resources</h2>
    <div class="resource-cards">
      <?php foreach( $related_resources as $resource ): ?>
        <?php $article = $resource['resource']; ?>
        <?php if( isset($article) && !empty($article) && isset($article->ID) ): ?>
          <?php get_template_part( 'template-parts/cards/resource-card', null, array( 'article' => $article ) ); ?>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>