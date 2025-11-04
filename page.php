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
$showSidebar = get_field('show_sidebar_navigation');
?>

	<main id="primary" class="site-main page-default <?php echo $showSidebar ? 'with-sidebar' : 'no-sidebar'; ?>">

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

			<div class="page-wrap">
				<div class="page-blocks-container">
					<?php get_template_part( 'template-parts/page-blocks/block-controller' ); ?>
				</div>
	
				<?php if ($showSidebar) : ?>
					<div class="sidebar-container">
						<?php get_template_part( 'template-parts/page-blocks/sidebar-nav' ); ?>
					</div>
				<?php endif; ?>
			</div>


			<?php endwhile; ?>

	</main><!-- #main -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all section headings and navigation items
        const sections = document.querySelectorAll('.section-heading[id]');
        const navItems = document.querySelectorAll('.sidebar-nav-item a');
        
        if (sections.length === 0 || navItems.length === 0) return;
        
        // Create intersection observer
        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px', // Trigger when section is 20% from top
            threshold: 0
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Remove active class from all nav items
                    navItems.forEach(item => {
                        item.parentElement.classList.remove('active');
                    });
                    
                    // Add active class to corresponding nav item
                    const targetId = entry.target.getAttribute('id');
                    const activeNavItem = document.querySelector(`.sidebar-nav-item a[href="#${targetId}"]`);
                    if (activeNavItem) {
                        activeNavItem.parentElement.classList.add('active');
                    }
                }
            });
        }, observerOptions);
        
        // Observe all sections
        sections.forEach(section => {
            observer.observe(section);
        });
        
        // Handle click events for smooth scrolling
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update active state immediately
                    navItems.forEach(navItem => {
                        navItem.parentElement.classList.remove('active');
                    });
                    this.parentElement.classList.add('active');
                }
            });
        });
    });
</script>

<?php
get_footer();
