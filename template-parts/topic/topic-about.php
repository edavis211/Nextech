<?php 
$aboutBlock = get_field('about_block');
if ( !$aboutBlock ) {
  return;
}
?>
<section id="about">
  <h2><?php echo esc_html($aboutBlock['heading']); ?></h2>
  <div class="about-content"><?php echo $aboutBlock['content']; ?></div>
  <div class="logo-divider"></div>
</section>
