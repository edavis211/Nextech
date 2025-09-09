<?php
  $terms = get_the_terms( get_the_ID(), 'course' );
  $courses = [];
  if ( $terms && ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term ) {
      $courses[] = $term->name;
    }
    $courseString = implode( ', ', $courses );
  }
  ?>
  <?php if ( ! empty( $courseString ) ): ?>
  <div class="resource-courses">
    <span class="label">Course:</span>
    <?php echo esc_html( $courseString ); ?>
  </div>
  <?php endif; ?>