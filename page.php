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

	<main id="primary" class="site-main page-default">

		<?php while ( have_posts() ) : the_post(); ?>
		<?php $featuredImg = get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>
		
			<header class="entry-header">
				<div class="inner">
					<div class="col">					
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<div class="page-description">
							<?php the_content();?>
						</div>
					</div>
					<?php if ( $featuredImg ) : ?>
						<div class="col image-col">
							<img src="<?php echo esc_url( $featuredImg ); ?>" alt="<?php the_title_attribute(); ?>" />
						</div>
					<?php endif; ?>
				</div>
			</header>

			<div class="page-blocks-container">
				<?php get_template_part( 'template-parts/page-blocks/block-controller' ); ?>
			</div>

			<?php endwhile; ?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
