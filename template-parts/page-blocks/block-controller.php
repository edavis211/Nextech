<?php
/**
 * Block Controller
 *
 * @package Nexus
 */
if( $blocks = get_field('page_blocks') ) {
  foreach( $blocks as $block ) {
    if ($block['acf_fc_layout'] == 'text_block') :
      get_template_part( 'template-parts/page-blocks/text-block', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'accordion') :
      get_template_part( 'template-parts/page-blocks/accordion', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'two_column_image_blocks') :
      get_template_part( 'template-parts/page-blocks/two_column_image_blocks', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'tabs_block') :
      get_template_part( 'template-parts/page-blocks/tabs-block', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'video_block') :
      get_template_part( 'template-parts/page-blocks/video-block', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'icon_block') :
      get_template_part( 'template-parts/page-blocks/icon-block', null, ['block' => $block] );
    elseif ($block['acf_fc_layout'] == 'featured_image_block') :
      get_template_part( 'template-parts/page-blocks/featured-image-block', null, ['block' => $block] );
    endif;
  }
}