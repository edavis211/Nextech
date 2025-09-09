<?php
/**
 * The template for the Resource Library page
 *
 * template name: Resource Library
 */

get_header();
$resources = get_posts( array(
	'post_type'      => 'resource',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>

	<main id="primary" class="site-main page-resource-library">

		<?php while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', 'page' );
		endwhile; ?>

		<section class="resource-grid">
			<div class="inner">
				<div class="resource-cards">
					<?php foreach ( $resources as $article ) : ?>
						<?php get_template_part( 'template-parts/cards/resource-card', null, array( 'article' => $article ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
