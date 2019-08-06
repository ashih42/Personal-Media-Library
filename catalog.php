<?php
include 'inc/data.php';
include 'inc/functions.php';

$page_title = 'Full Catalog';
$section = null;

if (isset($_GET['cat'])) {
  switch ($_GET['cat']) {
    case 'books':
      $page_title = 'Books';
      $section = 'books';
      break;
    case 'movies':
      $page_title = 'Movies';
      $section = 'movies';
      break;
    case 'music':
      $page_title = 'Music';
      $section = 'music';
      break;
  }
}

include 'inc/header.php';
?>

<div class="section catalog page">
  <div class="wrapper">
    <h1>
      <?php
      if ($section !== null)
        echo '<a href="catalog.php">Full Catalog</a> &gt; ';
      echo $page_title;
      ?>
    </h1>
    <ul class='items'>
      <?php
      $ids = get_ids_in_category($catalog, $section);
      foreach ($ids as $id)
        echo get_item_html($id, $catalog[$id]);
      ?>
    </ul>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
