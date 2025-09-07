<?php 
$block = $args['block'];
$background_image = false;
if ( isset( $block['background_image'] ) && ! empty( $block['background_image'] ) ) {
  $background_image = $block['background_image']['url'];
}
$video_url = false;
if ( isset( $block['video_url'] ) && ! empty( $block['video_url'] ) ) {
  $video_url = $block['video_url']['url'];
}
$video_poster = false;
if ( isset( $block['video_poster'] ) && ! empty( $block['video_poster'] ) ) {
  $video_poster = $block['video_poster']['url'];
}
$video_title = false;
if ( isset( $block['video_title'] ) && ! empty( $block['video_title'] ) ) {
  $video_title = $block['video_title'];
}
$link = false;
if ( isset( $block['link'] ) && ! empty( $block['link'] ) ) {
  $link = $block['link']['url'];
}
?>

<section class="video-block" <?php if($background_image) { ?>style="background-image: url('<?php echo esc_url( $background_image ); ?>');"<?php } ?>>
  <div class="inner">
    <div class="video-wrapper" <?php if($video_poster) { ?>style="background-image: url('<?php echo esc_url( $video_poster ); ?>');"<?php } ?>>
      <?php if($video_url) { ?>
        <a data-fancybox href="<?php echo esc_url( $video_url ); ?>" class="play-button">
          <svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44.94 50.09">
            <g>
              <path d="M0,4.01v42.07c0,3.28,3.73,5.16,6.37,3.22l36.95-22.74c2.27-1.67,2.14-5.1-.25-6.59L6.12.62C3.46-1.05,0,.86,0,4.01Z" fill="#2e3333"/>
            </g>
          </svg>
        </a>
      <?php } ?>
    </div>
    <div class="infowrap">
      <?php if($video_title) { ?>
        <h2 class="video-title"><?php echo esc_html( $video_title ); ?></h2>
      <?php } ?>
      <?php if($link) { ?>
        <a href="<?php echo esc_url( $link ); ?>" class="learn-more-button">
          <span><?=$block['link']['title']?></span>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.3 16.63">
            <g>
              <polyline points="14.3 1 20.3 8.32 14.3 15.63"/>
              <line x1="1" y1="8.32" x2="19.3" y2="8.32"/>
            </g>
          </svg>
        </a>
      <?php } ?>
    </div>
  </div>
</section>