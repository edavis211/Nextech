<?php 
$block = $args['block'];
$heading = false;
if ( isset( $block['heading'] ) && ! empty( $block['heading'] ) ) {
	$heading = $block['heading'];
}
?>
<section class="two_column_image_blocks">
  <div class="container">
    <div class="header">
      <div class="col">
        <h2><?php echo esc_html( $heading['heading'] ); ?></h2>
      </div>
      <?php if ( isset( $heading['description'] ) && ! empty( $heading['description'] ) ) : ?>
        <div class="col">
          <p><?php echo esc_html( $heading['description'] ); ?></p>
        </div>
      <?php endif; ?>
    </div>
    <div class="blocks">
      <?php if ( isset( $block['blocks'] ) && is_array( $block['blocks'] ) ) : ?>
        <?php foreach ( $block['blocks'] as $inner_block ) : ?>
          <div class="block">
            <div class="textcol">
              <?=$inner_block['text_content'];?>
            </div>
            <div class="imgcol">
              <img src="<?php echo esc_url( $inner_block['image']['url'] ); ?>" alt="<?php echo esc_attr( $inner_block['image']['alt'] ); ?>" />
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>