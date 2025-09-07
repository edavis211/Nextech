<?php
/**
 * TinyMCE Customization
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
} 
function custom_tinymce_settings( $init ) {
    // Add formatting options to the TinyMCE editor
    // Add text color format as span classes, blue, green, and orange
    $init['style_formats'] = json_encode(array(
        array(
            'title' => 'Text Color',
            'items' => array(
                array(
                    'title' => 'Blue',
                    'inline' => 'span',
                    'classes' => 'text-blue',
                ),
                array(
                    'title' => 'Teal',
                    'inline' => 'span',
                    'classes' => 'text-teal',
                ),
                array(
                    'title' => 'Orange',
                    'inline' => 'span',
                    'classes' => 'text-orange',
                ),
            ),
        ),
        array(
            'title'=> 'Text Styles',
            'items' => array(
                array(
                    'title' => 'Label',
                    'inline' => 'span',
                    'classes' => 'text-label',
                ),
                array(
                    'title' => 'Small',
                    'inline' => 'span',
                    'classes' => 'text-small',
                ),
            ),
        ),
        array(
            'title' => 'Paragraph Styles',
            'items' => array(
                array(
                    'title' => 'Large',
                    'block' => 'p',
                    'classes' => 'text-large',
                ),
            ),
        ),
        array(
            'title' => 'Link Styles',
            'items' => array(
                array(
                    'title' => 'Button Orange',
                    'selector' => 'a',
                    'classes' => 'button-orange',
                ),
            ),
        )
    ));
    
    // Enable the style formats dropdown in the toolbar
    if (!isset($init['toolbar1'])) {
        $init['toolbar1'] = 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,separator,styleselect';
    } else {
        // Add styleselect to existing toolbar if not already present
        if (strpos($init['toolbar1'], 'styleselect') === false) {
            $init['toolbar1'] .= ',styleselect';
        }
    }
    
    return $init;
}
add_filter( 'tiny_mce_before_init', 'custom_tinymce_settings' );

// Add styleselect to the TinyMCE buttons
function add_styleselect_to_tinymce( $buttons ) {
    if ( ! in_array( 'styleselect', $buttons ) ) {
        array_unshift( $buttons, 'styleselect' );
    }
    return $buttons;
}
add_filter( 'mce_buttons_2', 'add_styleselect_to_tinymce' );