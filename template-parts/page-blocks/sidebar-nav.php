<?php
$navItems = [];
if( $blocks = get_field('page_blocks') ) {
  foreach( $blocks as $block ) {
    if ($block['acf_fc_layout'] == 'section_heading') :
      $navItems[] = [
        'title' => $block['title'],
        'id' => sanitize_title($block['title']),
      ];
    endif;
  }
}
?>
<aside class="sidebar-nav">
  <nav>
    <?php if ( count($navItems) ) : ?>
      <ul class="sidebar-nav-list">
        <?php foreach ( $navItems as $i => $item ) : ?>
          <li class="sidebar-nav-item <?php echo $i === 0 ? 'active' : ''; ?>">
            <a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </nav>
</aside>