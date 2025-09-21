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
$results_count_string = $total_published === 1 ? 'Showing 1 of 1 resource' : "Showing {$posts_per_page} of {$total_published} resources";
?>

	<main id="primary" class="site-main page-resource-library">
		<div class="inner">
			<?php while ( have_posts() ) : the_post();?>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->
			<?php endwhile; ?>

			<section id="resource-info-bar" class="info-bar">
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
					<h2 id="resource-results-count" class="results-count" tabindex="0">
						<?=$results_count_string?>
					</h2>
				</div>
				<div class="filter-visibility-wrap">
					<div class="text-label">
						Search & Filter
					</div>
					<button class="toggle-filters filter-visibility-toggle" aria-expanded="false" title="Show or hide resource filters">
						<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
							<g>
								<path d="M28,9H11a1,1,0,0,1,0-2H28a1,1,0,0,1,0,2Z"/>
								<path d="M7,9H4A1,1,0,0,1,4,7H7A1,1,0,0,1,7,9Z"/>
								<path d="M21,17H4a1,1,0,0,1,0-2H21a1,1,0,0,1,0,2Z"/>
								<path d="M11,25H4a1,1,0,0,1,0-2h7a1,1,0,0,1,0,2Z"/>
								<path d="M9,11a3,3,0,1,1,3-3A3,3,0,0,1,9,11ZM9,7a1,1,0,1,0,1,1A1,1,0,0,0,9,7Z"/>
								<path d="M23,19a3,3,0,1,1,3-3A3,3,0,0,1,23,19Zm0-4a1,1,0,1,0,1,1A1,1,0,0,0,23,15Z"/>
								<path d="M13,27a3,3,0,1,1,3-3A3,3,0,0,1,13,27Zm0-4a1,1,0,1,0,1,1A1,1,0,0,0,13,23Z"/>
								<path d="M28,17H25a1,1,0,0,1,0-2h3a1,1,0,0,1,0,2Z"/>
								<path d="M28,25H15a1,1,0,0,1,0-2H28a1,1,0,0,1,0,2Z"/>
							</g>
						</svg>
					</button>
				</div>
			</section>

			<div class="resource-wrap">
				<section class="resource-container">
					<div id="resource-results-container" class="resource-cards">
						<?php foreach ( $resources as $article ) : ?>
							<?php get_template_part( 'template-parts/cards/resource-card-detail', null, array( 'article' => $article ) ); ?>
						<?php endforeach; ?>
					</div>
				</section>
				<?php get_template_part( 'template-parts/resource/resource-filtergroup', null, array( 'posts_per_page' => $posts_per_page, 'results_count_string' => $results_count_string, 'total_published' => $total_published ) ); ?>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
