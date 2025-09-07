<?php
$block = $args['block'];
?>
<section class="icon-block">
  <div class="inner">
    <div class="heading">
      <h2><?php echo esc_html($block['heading']); ?></h2>
    </div>
    <div class="icon-list-container">
      <?php if ( isset( $block['icon_links'] ) && is_array( $block['icon_links'] ) ) : ?>
        <ul>
          <?php foreach ( $block['icon_links'] as $icon ) : ?>
            <li class="icon-item">
              <a href="<?php echo esc_url( $icon['link']['url'] ); ?>">
                <div class="icon" style="background-image: url('<?php echo esc_url( $icon['icon']['url'] ); ?>');"></div>
                <p><?php echo esc_html( $icon['title'] ); ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</section>