<?php

include 'inc/functions.php';

$page_title = 'Full Catalog';
$section = null;
$search = null;
$items_per_page = 8;

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

if (isset($_GET['s']))
  $search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING);

if (isset($_GET['pg']))
  $current_page = filter_input(INPUT_GET, 'pg', FILTER_SANITIZE_NUMBER_INT);
if (empty($current_page))
  $current_page = 1;

$total_items = get_catalog_count($section, $search);
$total_pages = 1;
$offset = 0;

if ($total_items > 0) {
  $total_pages = ceil($total_items / $items_per_page);

  if (!empty($section))
    $limit_results = "cat=$section";
  else if (!empty($search))
    $limit_results = 's=' . urlencode(htmlspecialchars($search));
  else
    $limit_results = '';

  // Redirect to last page
  if ($current_page > $total_pages) {
    header("Location: catalog.php?$limit_results&pg=$total_pages");
  }

  // Redirect to first page
  if ($current_page < 1) {
    header("Location: catalog.php?$limit_results&pg=1");
  }

  // determine the offset for the current page
  // e.g. on page 3 with 8 items per page, offset is 16
  $offset = ($current_page - 1) * $items_per_page;

  // Pagination HTML
  $pagination = '<div class="pagination">';
  $pagination .= 'Pages:';
  for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $current_page)
      $pagination .= " <span>$i</span>";
    else
      $pagination .= " <a href='catalog.php?$limit_results&pg=$i'>$i</a>";
  }
  $pagination .= '</div>';
}

if (!empty($search))
  $catalog = search_catalog_array($search, $items_per_page, $offset);
elseif (!empty($section))
  $catalog = category_catalog_array($section, $items_per_page, $offset);
else
  $catalog = full_catalog_array($items_per_page, $offset);

include 'inc/header.php';
?>

<div class="section catalog page">
  <div class="wrapper">
    <?php
    echo '<h1>';
    if ($search != null) {
      echo 'Search Results for: "' . htmlspecialchars($search) . '"';
    } else {
      if ($section !== null)
        echo '<a href="catalog.php">Full Catalog</a> &gt; ';
      echo $page_title;
    }
    echo '</h1>';
    if ($total_items < 1) {
      echo '<p>No items were found matching the search term.</p>';
      echo '<p>Please search again or browse the <a href="catalog.php">Full Catalog</a>.</p>';
    } else {
      echo $pagination;
      echo '<ul class="items">';
      foreach ($catalog as $item)
        echo get_item_html($item);
      echo '</ul>';
      echo $pagination;
    }
    ?>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
