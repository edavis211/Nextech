<?php
$links = [
  ['url' => '#', 'text' => 'Worksheets'],
  ['url' => '#', 'text' => 'Lesson Plans'],
  ['url' => '#', 'text' => 'Videos'],
  ['url' => '#', 'text' => 'Websites'],
  ['url' => '#', 'text' => 'Books'],
  ['url' => '#', 'text' => 'Slides'],
  ['url' => '#', 'text' => 'Posters'],
  ['url' => '#', 'text' => 'Games'],
  ['url' => '#', 'text' => 'Apps'],
];
?>

<section class="quick-links">
  <div class="inner">
    <div class="card" data-theme="orange">
      <h2>Browse the Library</h2>
      <p>Access our complete library of educational materials from individual resources to comprehensive lesson plans.</p>
      <div class="links">
        <?php foreach ( $links as $link ) : ?>
          <a href="<?php echo esc_url( $link['url'] ); ?>" class="button"><?php echo esc_html( $link['text'] ); ?></a>
        <?php endforeach; ?>
      </div>
      <a href="" class="show-all">
        <span>See all resources</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.3 16.63">
          <g>
            <polyline class="cls-1" points="14.3 1 20.3 8.32 14.3 15.63"/>
            <line class="cls-1" x1="1" y1="8.32" x2="19.3" y2="8.32"/>
          </g>
        </svg>
      </a>
    </div>
    <div class="card" data-theme="teal">
      <h2>Explore Topics</h2>
      <p>Explore curated materials organized by computer science subject matter and learning areas.</p>
      <div class="links">
        <?php foreach ( $links as $link ) : ?>
          <a href="<?php echo esc_url( $link['url'] ); ?>" class="button"><?php echo esc_html( $link['text'] ); ?></a>
        <?php endforeach; ?>
      </div>
      <a href="" class="show-all">
        <span>Browse by subject</span>
        <svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.3 16.63">
          <g>
            <polyline class="cls-1" points="14.3 1 20.3 8.32 14.3 15.63"/>
            <line class="cls-1" x1="1" y1="8.32" x2="19.3" y2="8.32"/>
          </g>
        </svg>
      </a>
    </div>
    <div class="card" data-theme="yellow">
      <h2>Find your course</h2>
      <p>Find structured resources aligned with specific computer science courses and curricula.</p>
      <div class="links">
        <?php foreach ( $links as $link ) : ?>
          <a href="<?php echo esc_url( $link['url'] ); ?>" class="button"><?php echo esc_html( $link['text'] ); ?></a>
        <?php endforeach; ?>
      </div>
      <a href="" class="show-all">
        <span>Browse by course</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.3 16.63">
          <g>
            <polyline points="14.3 1 20.3 8.32 14.3 15.63"/>
            <line x1="1" y1="8.32" x2="19.3" y2="8.32"/>
          </g>
        </svg>
      </a>
    </div>
  </div>
</section>