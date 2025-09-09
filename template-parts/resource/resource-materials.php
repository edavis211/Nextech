<?php 
$materials = get_field('materials');
/*
Array ( [0] => Array ( [title] => Coding Book Covers Module on Code.org [material_type] => website [link] => https://nextech.org/ [file] => [note] => ) [1] => Array ( [title] => Planning Your Book Cover Handout [material_type] => pdf [link] => [file] => Array ( [ID] => 104 [id] => 104 [title] => WBL Menu of Experiences _8_1_2025 [filename] => WBL-Menu-of-Experiences-_8_1_2025.pdf [filesize] => 2138548 [url] => https://nexus.local/wp-content/uploads/2025/09/WBL-Menu-of-Experiences-_8_1_2025.pdf [link] => https://nexus.local/resource/random-ai-report-book-test/wbl-menu-of-experiences-_8_1_2025/ [alt] => [author] => 4 [description] => [caption] => [name] => wbl-menu-of-experiences-_8_1_2025 [status] => inherit [uploaded_to] => 103 [date] => 2025-09-05 17:54:48 [modified] => 2025-09-05 17:54:48 [menu_order] => 0 [mime_type] => application/pdf [type] => application [subtype] => pdf [icon] => https://nexus.local/wp-includes/images/media/document.png ) [note] => ) )
*/
?>
<section class="resource-materials">
  <div class="inner">
    <h2>Materials</h2>
    <ul class="materials-list">
      <?php foreach($materials as $material): 
        $title = $material['title'];
        $type = $material['material_type'];
        $link = $material['link'];
        $file = $material['file'];
        $note = $material['note'];


        if( $type == 'website' && $link ):
          $url = $link;
          $target = '_blank';
          $action1 = [
            'text' => 'Copy Link',
            'url' => $link,
            'icon' => 'copy',
          ];
          $action2 = [
            'text' => 'Open Link',
            'url' => $link,
            'icon' => 'external-link',
            'target' => '_blank',
          ];
        elseif( $type == 'pdf' && $file ):
          $url = $file['url'];
          $target = '_blank';
          $action1 = [
            'text' => 'Preview',
            'url' => $file['url'],
            'icon' => 'preview',
            'target' => '_blank',
          ];
          $action2 = [
            'text' => 'Download PDF',
            'url' => $file['url'],
            'icon' => 'download',
            'target' => '_blank',
          ];
        else:
          $url = '#';
          $target = '';
        endif;
      ?>
      <li class="material-item material-<?php echo esc_html($type); ?>">
        <span class="material-icon">
          <?php if( $type == 'website' ) : ?>
            <?php echo get_template_part('template-parts/icons/website'); ?>
          <?php elseif( $type == 'pdf' ) : ?>
            <?php echo get_template_part('template-parts/icons/pdf'); ?>
          <?php else : ?>
            <?php echo ''; ?>
          <?php endif; ?>
        </span>
        <h3><?php echo esc_html($title); ?></h3>
        <div class="material-actions">
          <a href="<?php echo esc_url($action1['url']); ?>" class="button" <?php if( isset($action1['target']) ) echo 'target="'.esc_attr($action1['target']).'"'; ?>>
            <?php if( isset($action1['icon']) ) : ?>
              <span class="icon icon-<?php echo esc_attr($action1['icon']); ?>"></span>
            <?php endif; ?>
            <?php echo esc_html($action1['text']); ?>
          </a>
          <a href="<?php echo esc_url($action2['url']); ?>" class="button" <?php if( isset($action2['target']) ) echo 'target="'.esc_attr($action2['target']).'"'; ?>>
            <?php if( isset($action2['icon']) ) : ?>
              <span class="icon icon-<?php echo esc_attr($action2['icon']); ?>"></span>
            <?php endif; ?>
            <?php echo esc_html($action2['text']); ?>
          </a>
        </div>
        <?php if( $note ): ?>
          <p class="material-note"><?php echo esc_html($note); ?></p>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>