<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nexus
 */

wp_enqueue_script( 'topic-page', get_template_directory_uri() . '/js/single-topic.js', array('jquery'), '1.0.0', true );
get_header();
while ( have_posts() ) : the_post();?>
	<main id="primary" class="site-main single-topic">
		<div class="content-area">
			<?php
			get_template_part( 'template-parts/topic/topic-banner' );
			get_template_part( 'template-parts/topic/topic-about' );
			get_template_part( 'template-parts/topic/topic-activities' );
			?>
		</div>		
		<aside class="sidebar">
			<div class="actions resource-actions topic-actions">
				<?php get_template_part( 'template-parts/topic/topic-actions' ); ?>
				<?php get_template_part( 'template-parts/topic/topic-content-nav' ); ?>
			</div>
		</aside>
	</main><!-- #main -->
	<?php get_template_part( 'template-parts/page-blocks/related-topics' ); ?>

		 

<?php endwhile;
get_footer();
?>
