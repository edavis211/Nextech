<?php
/**
 * The template for displaying flexible content pages
 * Template Name: Flexible Content Template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Nexus
 */

get_header();
?>

	<main id="primary" class="site-main" data-template="flex">

		<?php while ( have_posts() ) : the_post();?>

			<?php get_template_part( 'template-parts/blocks/flex/banner', 'flex' ); ?>

      <?php 
        // ACF Flexible Blocks
        $blocks = get_field('blocks');

        if($blocks) {
          foreach($blocks as $block) {
            $blockType = $block['acf_fc_layout'];
            get_template_part( 'template-parts/blocks/flex/' . $blockType, null, array('block' => $block) );
          }
        }
      ?>


		<?php endwhile; ?>
	
	</main><!-- #main -->


<?php
get_sidebar();
get_footer();
