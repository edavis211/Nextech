<?php
  $terms = get_the_terms( get_the_ID(), 'academic-standard' );
  $concepts = [];
  if ( $terms && ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term ) {
      if($term->parent != 0) continue; // only parent terms
      $concepts[] = $term->name;
    }
    $conceptString = implode( ', ', $concepts );
  }
  ?>
  <?php if ( ! empty( $conceptString ) ): ?>
  <div class="resource-concepts">
    <span class="label">Concepts:</span>
    <?php echo esc_html( $conceptString ); ?>
  </div>
  <?php endif; ?>