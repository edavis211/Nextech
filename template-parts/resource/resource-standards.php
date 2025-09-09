<?php
  $terms = get_the_terms( get_the_ID(), 'academic-standard' );
  $concepts = [];
  if ( $terms && ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term ) {
      if($term->parent == 0) continue; // exclude parent terms
      $concepts[] = $term->name;
    }
    $conceptString = implode( ', ', $concepts );
  }
  ?>
  <?php if ( ! empty( $conceptString ) ): ?>
  <div class="resource-standards">
    <span class="label">Standards:</span>
    <?php echo esc_html( $conceptString ); ?>
  </div>
  <?php endif; ?>