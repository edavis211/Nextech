<?php $accordion = get_field('accordion'); if( $accordion ): ?>
<section class="resource-accordion">
  <div class="inner">
    <?php foreach( $accordion as $item ): ?>
      <details class="accordion-item">
        <summary>
          <h2><?=$item['title']; ?></h2>
          <div class="indicator"></div>
        </summary>
        <div class="accordion-content">
          <div class="content">
            <?php echo wp_kses_post( $item['content'] ); ?>
          </div>
          <?php if( $item['image'] ): ?>
            <div class="image">
              <img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['image']['alt'] ); ?>" />
            </div>
          <?php endif; ?>
        </div>
      </details>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>