<section class="topic-banner" id="overview">
  <div class="inner">
    <div class="infoblock">
      <?php get_template_part( 'template-parts/topic/topic-breadcrumbs' ); ?>
      <h1><?php the_title(); ?></h1>
      <div class="description"><?php the_excerpt(); ?></div>
      <div class="meta">
        <?php get_template_part( 'template-parts/resource/resource-grade-levels' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-topics' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-concepts' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-standards' ); ?>
        <?php get_template_part( 'template-parts/resource/resource-courses' ); ?>
      </div>
    </div>
    <div class="logo-divider"></div>
  </div>
</section>