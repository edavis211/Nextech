<section class="resource-banner">
  <div class="inner">
    <div class="infoblock">
      <?php get_template_part( 'template-parts/resource/resource-breadcrumbs' ); ?>
      <h1><?php the_title(); ?></h1>
      <div class="description"><?php the_content(); ?></div>
      <div class="meta">
        <?php get_template_part( 'template-parts/resource/resource-grade-levels' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-topics' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-concepts' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-standards' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-courses' ); ?>
      </div>
    </div>
    <div class="actions resource-actions">
      <?php get_template_part( 'template-parts/resource/resource-actions' ); ?>
    </div>
    <div class="featured-img">
      <?php the_post_thumbnail( 'full' ); ?>
    </div>
  </div>
</section>