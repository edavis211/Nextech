<?php 
$contributors = get_field('contributors'); if( $contributors ): 
?>
<section class="resource-contributors">
  <div class="inner">
    <div class="message"><?=$contributors['message']; ?></div>
    <?php if( !empty($contributors['learn_more']) ): ?>
    <a href="<?=$contributors['learn_more']['url']; ?>" class="link" target="<?=$contributors['learn_more']['target'] ? $contributors['learn_more']['target'] : '_self'; ?>">
      Learn More
    </a>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>