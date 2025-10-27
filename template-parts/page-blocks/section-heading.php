<?php 
$block = $args['block'];
$title = isset($block['title']) ? $block['title'] : null;
// Generate a slug from the title for use as an ID or anchor
$titleSlug = $title ? sanitize_title($title) : '';
$description = isset($block['description']) ? $block['description'] : null;
$image = isset($block['image']) ? $block['image'] : null;
?>
<section class="section-heading" id="<?php echo esc_attr($titleSlug); ?>">
  <div class="logo-divider"></div>
  <div class="inner">
    <div class="col text">
      <?php if ($title): ?>
        <h2 class="section-title"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>
  
      <?php if ($description): ?>
        <div class="section-description">
          <?php echo wp_kses_post($description); ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($image): ?>
    <div class="col img">
      <div class="section-image">
        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>