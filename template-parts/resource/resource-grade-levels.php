<?php 
$id = isset( $args['id'] ) ? $args['id'] : get_the_ID();
$gradeRange = get_field('grade_range', $id); 
?>
<?php if ($gradeRange): ?>
  <div class="grade-levels">
    <span class="label">Grade Levels:</span> 
    <span class="minimum-grade"><?php echo $gradeRange['minimum_grade']->name; ?></span>
    <span class="divider"> - </span>
    <span class="maximum-grade"><?php echo $gradeRange['maximum_grade']->name; ?></span>
  </div>
<?php endif; ?>