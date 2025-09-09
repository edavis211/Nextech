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

get_header();
?>

	<main id="primary" class="site-main single-resource">

		<?php while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/resource/resource-banner' );
			get_template_part( 'template-parts/resource/resource-materials' );
			get_template_part( 'template-parts/resource/resource-accordion' );
			get_template_part( 'template-parts/resource/resource-contributors' );
			get_template_part( 'template-parts/page-blocks/related-resources' );

		endwhile; ?>

	</main><!-- #main -->

<?php
get_footer();
