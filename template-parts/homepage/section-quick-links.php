<?php
$themes = ['orange', 'teal', 'yellow'];
$linkBlock = get_field( 'link_block' );
?>

<section class="quick-links">
  <div class="inner">
    <?php if ( $linkBlock ) : ?>
      <?php foreach ( $linkBlock as $i =>$block ) : ?>
        <div class="card" data-theme="<?=$themes[$i] ?>">
          <h2><?php echo esc_html( $block['title'] ); ?></h2>
          <p><?php echo esc_html( $block['description'] ); ?></p>
          <div class="links">
            <?php foreach ( $block['quick_links'] as $quick_link ) : ?>
              <a href="<?php echo esc_url( $quick_link['link']['url'] ); ?>" class="button"><?php echo esc_html( $quick_link['link']['title'] ); ?></a>
            <?php endforeach; ?>
          </div>
          <a href="<?php echo esc_url( $block['primary_link']['url'] ); ?>" class="show-all">
            <span><?php echo esc_html( $block['primary_link']['title'] ); ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.3 16.63">
              <g>
                <polyline points="14.3 1 20.3 8.32 14.3 15.63"/>
                <line x1="1" y1="8.32" x2="19.3" y2="8.32"/>
              </g>
            </svg>
          </a>


        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>