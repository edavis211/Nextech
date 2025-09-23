<?php
$related_topics = get_field('related_topics');
if( $related_topics && is_array( $related_topics ) && count( $related_topics ) > 0 ): ?>
<section class="related-resources related-topics">
  <div class="inner">
    <h2>More Topics Like This</h2>
    <div class="resource-cards">
      <?php foreach( $related_topics as $topic ): ?>
        <?php $article = $topic['topic']; ?>
        <?php if( isset($article) && !empty($article) && isset($article->ID) ): ?>
          <?php get_template_part( 'template-parts/cards/resource-card', null, array( 'article' => $article ) ); ?>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>