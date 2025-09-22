<?php
$id = isset( $args['id'] ) ? $args['id'] : get_the_ID();
$terms = get_the_terms( $id, 'subject-matter' );
$topics= [];
//print_r($terms);

if ( $terms && ! is_wp_error( $terms ) ) {
    // build an array of parent terms
    foreach ( $terms as $term ) {
        if ( $term->parent == 0 ) {
            $parent = $term;
            $parent->children = [];
            foreach ( $terms as $child ) {
                if ( $child->parent == $parent->term_id ) {
                    $parent->children[] = $child;
                }
            }
            $topics[] = $parent;
        }
    }


    // $topicString = 'Parent Topic without children, Parent Topic: Child Topic, Another Parent Topic without children, Parent Topic: Child Topic,';
    // build a string of topics and subtopics
    // Child topics should be appended to their parent topic with a colon separator, it is okay to have multiple child topics for a parent
    // Parent topics should be separated by a comma
    // A parent topic with children should not appear in the list by itself, it should only appear with one of its children
    // In the case of multiple child topics for a parent, the parent should be repeated for each child topic
    $topicString = '';
    foreach ( $topics as $topic ) {
        if ( count( $topic->children ) > 0 ) {
            foreach ( $topic->children as $child ) {
                $topicString .= $topic->name . ': ' . $child->name . ', ';
            }
        } else {
            $topicString .= $topic->name . ', ';
        }
    }
    // remove the trailing comma and space
    $topicString = rtrim( $topicString, ', ' );

}?>
<?php if ( isset( $topicString ) && ! empty( $topicString ) ): ?>
<div class="resource-topics">
  <span class="label">Subject Matter:</span>
  <?php echo $topicString; ?>
</div>
<?php endif; ?>