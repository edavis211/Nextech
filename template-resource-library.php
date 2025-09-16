<?php
/**
 * The template for the Resource Library page
 *
 * template name: Resource Library
 */
get_header();

// Get posts per page setting from filter group
$posts_per_page = 12; // Should match the setting in resource-filtergroup.php

$resources = get_posts( array(
	'post_type'      => 'resource',
	'posts_per_page' => $posts_per_page, // Use configurable setting instead of -1
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
) );

// Get total count for proper pagination
$total_resources = wp_count_posts('resource');
$total_published = $total_resources->publish;
?>

	<main id="primary" class="site-main page-resource-library">
		<div class="inner">
			<?php while ( have_posts() ) : the_post();?>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->
			<?php endwhile; ?>

			<div class="resource-wrap">
				<section class="resource-container">
					<div class="resource-results">
						<form action="" class="sort-choices">
							<label for="sort-by" class="text-label">Sort by:</label>
							<select name="sort-by" id="sort-by" aria-label="Sort resources by" class="text-label">
								<option value="newest">Newest</option>
								<option value="oldest">Oldest</option>
								<option value="a-z">A-Z</option>
								<option value="z-a">Z-A</option>
							</select>
						</form>
						<div class="results-count">
							<?php
							$count = count( $resources );
							if ( $count === 1 ) {
								echo 'Showing 1 of ' . $total_published . ' resources';
							} else {
								echo 'Showing ' . $count . ' of ' . $total_published . ' resources';
							}
							?>
						</div>
					</div>
					<div id="resource-results-container" class="resource-cards">
						<?php foreach ( $resources as $article ) : ?>
							<?php get_template_part( 'template-parts/cards/resource-card', null, array( 'article' => $article ) ); ?>
						<?php endforeach; ?>
					</div>
				</section>
				<?php get_template_part( 'template-parts/resource/resource-filtergroup', null, array( 'posts_per_page' => $posts_per_page ) ); ?>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
