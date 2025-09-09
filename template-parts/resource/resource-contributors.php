<?php 
$contributors = get_field('contributors'); if( $contributors ): 
?>
<section class="resource-contributors">
  <div class="inner">
    <div class="message"><?=$contributors['message']; ?></div>
    <a href="<?=$contributors['learn_more']; ?>" class="link">
      Learn More
    </a>
  </div>
</section>
<?php endif; ?>