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

	<main id="primary" class="site-main front-page">

		<?php while ( have_posts() ) : the_post();?>

			<section class="banner">
				<div class="inner wysiwyg">
					<?php the_content(); ?>
				</div>
			</section>
			<?php get_template_part( 'template-parts/homepage/section', 'quick-links' ); ?>
			<?php get_template_part( 'template-parts/homepage/section', 'search' ); ?>


		<?php endwhile; ?>

	</main><!-- #main -->

<?php
get_footer();
