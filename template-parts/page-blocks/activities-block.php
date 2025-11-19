<?php 
$activitiesBlock = isset( $args['block'] ) ? $args['block'] : null; 
?>
<section class="activities-block" id="activities">
  <?php if ( isset( $activitiesBlock['heading'] ) && $activitiesBlock['heading'] ) : ?>
    <h2><?php echo esc_html($activitiesBlock['heading']); ?></h2>
  <?php endif; ?>
  <?php if ( isset( $activitiesBlock['content'] ) && $activitiesBlock['content'] ) : ?>
    <div class="activities-content"><?php echo $activitiesBlock['content']; ?></div>
  <?php endif; ?>
  <?php if (isset($activitiesBlock['activities']) && is_array($activitiesBlock['activities']) && count($activitiesBlock['activities'])) : ?>
    <div class="activities-grid">
        <?php foreach ( $activitiesBlock['activities'] as $activity ) : ?>
          <h3><?php echo esc_html($activity['activity_title']); ?></h3>
          <?php if( isset($activity['activity_detail']) && $activity['activity_detail'] ) : ?>
            <div class="activity-detail"><?php echo $activity['activity_detail']; ?></div>
          <?php endif; ?>
          <?php if (isset($activity['resources']) && is_array($activity['resources']) && count($activity['resources'])) : ?>
            <div class="activity-group">
              <article class="activity-resource-wrap">
                <?php foreach ( $activity['resources'] as $resource ) : ?>
                  <div class="activity-resource">
                    <?php get_template_part( 'template-parts/cards/resource-card-detail', null, array( 'article' => $resource['resource'] ) ); ?>
                    <div class="activity-resource-description"><?php echo $resource['description']; ?></div>
                  </div>
                <?php endforeach; ?>
              </article>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
