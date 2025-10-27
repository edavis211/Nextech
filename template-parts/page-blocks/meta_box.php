<?php 
$block = isset( $args['block'] ) ? $args['block'] : null; 
$gradeRange = $block && isset( $block['grade_range'] ) ? $block['grade_range'] : null;
$subjectMatter = $block && isset( $block['subject_matter'] ) ? $block['subject_matter'] : null;
$acamdemicStandards = $block && isset( $block['academic_standards'] ) ? $block['academic_standards'] : null;
$courses = $block && isset( $block['courses'] ) ? $block['courses'] : null;
?>
<section class="meta-box">
  <div class="inner meta">
    <?php if (is_array($gradeRange) && isset($gradeRange['minimum_grade'], $gradeRange['maximum_grade'])): ?>
      <div class="grade-levels">
        <span class="label">Grade Levels:</span> 
        <span class="minimum-grade"><?php echo $gradeRange['minimum_grade']->name; ?></span>
        <span class="divider"> - </span>
        <span class="maximum-grade"><?php echo $gradeRange['maximum_grade']->name; ?></span>
      </div>
    <?php endif; ?>

    <?php if (is_array($subjectMatter) && !empty($subjectMatter)): ?>
      <div class="subject-matter">
        <span class="label">Subject Matter:</span> 
        <span class="subjects">
          <?php 
            $subjects = array_map(function($subject) {
              return esc_html($subject->name);
            }, $subjectMatter);
            echo implode(', ', $subjects);
          ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if (is_array($acamdemicStandards) && !empty($acamdemicStandards)): ?>
      <?php
        // Separate concepts (parent terms) and standards (child terms)
        $concepts = [];
        $standards = [];
        
        foreach ($acamdemicStandards as $term) {
          if ($term->parent == 0) {
            // Parent terms are concepts
            $concepts[] = $term->name;
          } else {
            // Child terms are standards
            $standards[] = $term->name;
          }
        }
        
        $conceptString = implode(', ', $concepts);
        $standardString = implode(', ', $standards);
      ?>
      
      <?php if (!empty($conceptString)): ?>
        <div class="resource-concepts">
          <span class="label">Concepts:</span>
          <?php echo esc_html($conceptString); ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($standardString)): ?>
        <div class="resource-standards">
          <span class="label">Standards:</span>
          <?php echo esc_html($standardString); ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if (is_array($courses) && !empty($courses)): ?>
      <div class="resource-courses">
        <span class="label">Course:</span> 
        <span class="courses">
          <?php 
            $courseNames = array_map(function($course) {
              return esc_html($course->name);
            }, $courses);
            echo implode(', ', $courseNames);
          ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</section>