<?php
include 'inc/data.php';
include 'inc/functions.php';

/*
 * Check 'id' is provided and is valid.
 * Otherwise, redirect to 'catalog.php'
 */
if (isset($_GET['id']) && isset($catalog[$_GET['id']]))
  $item = $catalog[$_GET['id']];
else
  exit (header('Location: catalog.php'));

$page_title = $item['title'];
$section = null;

include 'inc/header.php';
?>

<div class="section page">
  <div class="wrapper">
    <div class="breadcrumbs">
      <a href="catalog.php">Full Catalog</a>
      &gt; <a href="catalog.php?cat=<?= strtolower($item['category']) ?>"><?= $item['category'] ?></a>
      &gt; <?= $item['title'] ?>
    </div>
    <div class="media-picture">
      <span>
        <img src="<?= $item['img'] ?>" alt="<?= $item['title'] ?>">
      </span>
    </div>
    <div class="media-details">
      <h1><?= $item['title'] ?></h1>
      <table>
        <tr>
          <th>Category</th>
          <td><?= $item['category'] ?></td>
        </tr>
        <tr>
          <th>Genre</th>
          <td><?= $item['genre'] ?></td>
        </tr>
        <tr>
          <th>Format</th>
          <td><?= $item['format'] ?></td>
        </tr>
        <tr>
          <th>Year</th>
          <td><?= $item['year'] ?></td>
        </tr>
        <?php if (strtolower($item['category']) === 'books') { ?>
        <tr>
          <th>Authors</th>
          <td><?= implode(', ', $item['authors']) ?></td>
        </tr>
        <tr>
          <th>Publisher</th>
          <td><?= $item['publisher'] ?></td>
        </tr>
        <tr>
          <th>ISBN</th>
          <td><?= $item['isbn'] ?></td>
        </tr>
        <?php } elseif (strtolower($item['category']) === 'movies') { ?>
        <tr>
          <th>Director</th>
          <td><?= $item['director'] ?></td>
        </tr>
        <tr>
          <th>Writers</th>
          <td><?= implode(', ', $item['writers']) ?></td>
        </tr>
        <tr>
          <th>Stars</th>
          <td><?= implode(', ', $item['stars']) ?></td>
        </tr>
        <?php } elseif (strtolower($item['category']) === 'music') { ?>
        <tr>
          <th>Artist</th>
          <td><?= $item['artist'] ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
