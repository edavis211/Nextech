<?php 
/**
 * Resource Materials Template Part
 * 
 * Displays materials associated with a resource including PDFs, websites, videos, and Google Docs.
 * Each material has contextual actions (preview, download, copy link, etc.)
 */

$materials = get_field('materials');

if ( ! $materials ) {
    return;
}

/**
 * Get material configuration based on type
 * 
 * @param string $type Material type (pdf, website, video, google_slides, google_doc, google_sheet)
 * @return array Configuration array with icon and actions
 */
function get_material_config( $type ) {
    $configs = [
        'pdf' => [
            'icon' => 'pdf',
            'actions' => [
                'primary' => [ 'text' => 'Preview', 'icon' => 'preview-link' ],
                'secondary' => [ 'text' => 'Download PDF', 'icon' => 'download' ]
            ]
        ],
        'website' => [
            'icon' => 'website',
            'actions' => [
                'primary' => [ 'text' => 'Copy Link', 'icon' => 'copy-link' ],
                'secondary' => [ 'text' => 'Open Link', 'icon' => 'external-link' ]
            ]
        ],
        'video' => [
            'icon' => 'website', // Using website icon as fallback
            'actions' => [
                'primary' => [ 'text' => 'Copy Link', 'icon' => 'copy-link' ],
                'secondary' => [ 'text' => 'Watch Video', 'icon' => 'external-link' ]
            ]
        ],
        'google_slides' => [
            'icon' => 'website',
            'actions' => [
                'primary' => [ 'text' => 'Copy Link', 'icon' => 'copy-link' ],
                'secondary' => [ 'text' => 'Open Slides', 'icon' => 'external-link' ]
            ]
        ],
        'google_doc' => [
            'icon' => 'website',
            'actions' => [
                'primary' => [ 'text' => 'Copy Link', 'icon' => 'copy-link' ],
                'secondary' => [ 'text' => 'Open Document', 'icon' => 'external-link' ]
            ]
        ],
        'google_sheet' => [
            'icon' => 'website',
            'actions' => [
                'primary' => [ 'text' => 'Copy Link', 'icon' => 'copy-link' ],
                'secondary' => [ 'text' => 'Open Sheet', 'icon' => 'external-link' ]
            ]
        ]
    ];

    return $configs[ $type ] ?? $configs['website']; // Default to website config
}

/**
 * Build action array for material
 * 
 * @param array $material Material data
 * @param array $action_config Action configuration
 * @param string $action_type 'primary' or 'secondary'
 * @return array Action array with url, text, icon, and target
 */
function build_material_action( $material, $action_config, $action_type ) {
    $type = $material['material_type'];
    $is_pdf = ( $type === 'pdf' );
    $url = $is_pdf ? $material['file']['url'] ?? '#' : $material['link'] ?? '#';
    
    $action = [
        'text' => $action_config['text'],
        'url' => $url,
        'icon' => $action_config['icon'],
        'target' => '_blank'
    ];

    // Special handling for copy link actions
    if ( strpos( $action_config['icon'], 'copy' ) !== false ) {
        $action['data_copy_url'] = $url;
    }

    // Add download attribute for PDF download actions
    if ( $is_pdf && strpos( $action_config['icon'], 'download' ) !== false ) {
        $action['download'] = true;
    }

    return $action;
}

/**
 * Render material icon
 * 
 * @param string $icon_type Icon type to render
 */
function render_material_icon( $icon_type ) {
    $icon_path = "template-parts/icons/{$icon_type}";
    if ( locate_template( $icon_path . '.php' ) ) {
        get_template_part( $icon_path );
    }
}

/**
 * Render action button
 * 
 * @param array $action Action data array
 */
function render_action_button( $action ) {
    $target_attr = ! empty( $action['target'] ) ? 'target="' . esc_attr( $action['target'] ) . '"' : '';
    $copy_attr = ! empty( $action['data_copy_url'] ) ? 'data-copy-url="' . esc_attr( $action['data_copy_url'] ) . '"' : '';
    $download_attr = ! empty( $action['download'] ) ? 'download' : '';
    ?>
    <a href="<?php echo esc_url( $action['url'] ); ?>" 
       class="button" 
       <?php echo $target_attr; ?> 
       <?php echo $copy_attr; ?>
       <?php echo $download_attr; ?>>
        <?php if ( ! empty( $action['icon'] ) ) : ?>
            <span class="icon icon-<?php echo esc_attr( $action['icon'] ); ?>">
                <?php render_material_icon( $action['icon'] ); ?>
            </span>
        <?php endif; ?>
        <?php echo esc_html( $action['text'] ); ?>
    </a>
    <?php
}

?>
<section class="resource-materials">
    <div class="inner">
        <h2>Materials</h2>
        <ul class="materials-list">
            <?php foreach ( $materials as $material ) : 
                $title = $material['title'] ?? '';
                $type = $material['material_type'] ?? 'website';
                $note = $material['note'] ?? '';
                
                // Skip if no title or invalid data
                if ( empty( $title ) ) {
                    continue;
                }
                
                // Get configuration for this material type
                $config = get_material_config( $type );
                
                // Build actions
                $action1 = build_material_action( $material, $config['actions']['primary'], 'primary' );
                $action2 = build_material_action( $material, $config['actions']['secondary'], 'secondary' );
                
                // Skip if no valid URL for actions
                if ( $action1['url'] === '#' && $action2['url'] === '#' ) {
                    continue;
                }
            ?>
            <li class="material-item material-<?php echo esc_attr( $type ); ?>">
                <span class="material-icon">
                    <?php render_material_icon( $config['icon'] ); ?>
                </span>
                
                <div class="text-wrap">
                  <h3><?php echo esc_html( $title ); ?></h3>
                  <?php if ( ! empty( $note ) ) : ?>
                    <p class="material-note"><?php echo esc_html( $note ); ?></p>
                  <?php endif; ?>
                </div>
                
                <div class="material-actions">
                    <?php 
                    render_action_button( $action1 );
                    render_action_button( $action2 );
                    ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>