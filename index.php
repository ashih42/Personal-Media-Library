<?php
include 'inc/data.php';
include 'inc/functions.php';

$page_title = 'Personal Media Library';
$section = null;

include 'inc/header.php';
?>

<div class="section catalog random">
  <div class="wrapper">
    <h2>May we suggest something?</h2>
    <ul class="items">
      <?php
      $random_ids = array_rand($catalog, 4);
      shuffle($random_ids);
      foreach ($random_ids as $id)
        echo get_item_html($id, $catalog[$id]);
      ?>
    </ul>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
