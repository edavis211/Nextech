<?php
$id = isset( $args['id'] ) ? $args['id'] : get_the_ID();
$terms = get_the_terms( $id, 'resource-type' );
$types = [];
?>
<?php if ( $terms && ! is_wp_error( $terms ) ): ?>
  <div class="resource-types">
    <?php foreach ( $terms as $term ): ?>
      <div class="resource-type-button" data-term-id="<?php echo esc_attr( $term->term_id ); ?>" data-term-slug="<?php echo esc_attr( $term->slug ); ?>">
        <?php echo esc_html( $term->name ); ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
