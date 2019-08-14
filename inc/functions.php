<?php

function get_catalog_count($category = null, $search = null) {
  include 'connection.php';

  try {
    $sql = 'SELECT COUNT(*) FROM Media';

    if (!empty($search)) {
      $result = $db->prepare($sql . ' WHERE title LIKE ?');
      $result->bindValue(1, "%$search%", PDO::PARAM_STR);
    } elseif (!empty($category)) {
      $result = $db->prepare($sql . ' WHERE LOWER(category) = ?');
      $category = strtolower($category);
      $result->bindParam(1, $category, PDO::PARAM_STR);
    } else {
      $result = $db->prepare($sql);
    }
    $result->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  return $result->fetchColumn(0);
}

function full_catalog_array($limit = null, $offset = 0) {
  include 'connection.php';

  try {
    $sql = 'SELECT media_id, title, category, img
      FROM Media
      ORDER BY
        REPLACE(
          REPLACE(
            REPLACE(title, "The ", ""),
            "An ",
            ""),
          "A ",
          "")';
    if (is_integer($limit)) {
      $results = $db->prepare($sql . ' LIMIT ? OFFSET ?');
      $results->bindParam(1, $limit, PDO::PARAM_INT);
      $results->bindParam(2, $offset, PDO::PARAM_INT);
    } else {
      $results = $db->prepare($sql);
    }
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  return $results->fetchAll(PDO::FETCH_ASSOC);
}

function category_catalog_array($category, $limit = null, $offset = 0) {
  include 'connection.php';

  $category = strtolower($category);
  try {
    $sql = 'SELECT media_id, title, category, img
      FROM Media
      WHERE LOWER(category) = ?
      ORDER BY
        REPLACE(
          REPLACE(
            REPLACE(title, "The ", ""),
            "An ",
            ""),
          "A ",
          "")';
    if (is_integer($limit)) {
      $results = $db->prepare($sql . ' LIMIT ? OFFSET ?');
      $results->bindParam(1, $category, PDO::PARAM_STR);
      $results->bindParam(2, $limit, PDO::PARAM_INT);
      $results->bindParam(3, $offset, PDO::PARAM_INT);
    } else {
      $results = $db->prepare($sql);
      $results->bindParam(1, $category, PDO::PARAM_STR);
    }
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  return $results->fetchAll(PDO::FETCH_ASSOC);
}

function search_catalog_array($search, $limit = null, $offset = 0) {
  include 'connection.php';

  try {
    $sql = 'SELECT media_id, title, category, img
      FROM Media
      WHERE title LIKE ?
      ORDER BY
        REPLACE(
          REPLACE(
            REPLACE(title, "The ", ""),
            "An ",
            ""),
          "A ",
          "")';
    if (is_integer($limit)) {
      $results = $db->prepare($sql . ' LIMIT ? OFFSET ?');
      $results->bindValue(1, "%$search%", PDO::PARAM_STR);
      $results->bindParam(2, $limit, PDO::PARAM_INT);
      $results->bindParam(3, $offset, PDO::PARAM_INT);
    } else {
      $results = $db->prepare($sql);
      $results->bindValue(1, "%$search%", PDO::PARAM_STR);
    }
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  return $results->fetchAll(PDO::FETCH_ASSOC);
}

function random_catalog_array() {
  include 'connection.php';

  try {
    $results = $db->query(
      'SELECT media_id, title, category, img
      FROM Media
      ORDER BY RANDOM()
      LIMIT 4'
    );
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  return $results->fetchAll(PDO::FETCH_ASSOC);
}

function single_item_array($id) {
  include 'connection.php';

  try {
    $results = $db->prepare(
      'SELECT Media.media_id, title, category, img, format, year, genre, publisher, isbn
      FROM Media
      JOIN Genres ON Media.genre_id = Genres.genre_id
      LEFT OUTER JOIN Books ON Media.media_id = Books.media_id
      WHERE Media.media_id = ?'
    );
    $results->bindParam(1, $id, PDO::PARAM_INT);
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  $item = $results->fetch();
  
  if (empty($item)) return false;

  // Find all people involved in this media $id
  try {
    $results = $db->prepare(
      'SELECT fullname, role
      FROM Media_People
      JOIN People ON Media_People.people_id = People.people_id
      WHERE Media_People.media_id = ?'
    );
    $results->bindParam(1, $id, PDO::PARAM_INT);
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  foreach ($results as $row) {
    $item[$row['role']][] = $row['fullname'];
  }
  return $item;
}

function genre_array($category = null) {
  include 'connection.php';

  $category = strtolower($category);

  try {
    $sql = 'SELECT genre, category
      FROM Genres
      JOIN Genre_Categories
      ON Genres.genre_id = Genre_Categories.genre_id';
    if (!empty($categyr)) {
      $results = $db->prepare($sql . ' WHERE LOWER(category) = ? ORDER BY genre');
      $results->bindParam(1, $category, PDO::PARAM_STR);
    } else {
      $results = $db->prepare($sql . ' ORDER BY genre');
    }
    $results->execute();
  } catch (Exception $e) {
    echo 'Unable to retrieve results: ' . $e->getMessage();
    exit;
  }
  $genres = [];
  foreach ($results as $row) {
    $genres[$row['category']][] = $row['genre'];
  }
  return $genres;
}

function get_item_html($item) {
  return <<<HTML
    <li>
      <a href="details.php?id={$item['media_id']}">
        <img src="{$item['img']}" alt="{$item['title']}">
        <p>View Details</p>
      </a>
    </li>
HTML;
}

/*
 * $catalog is array of [ id => array ]
 * $category is a string or NULL
 */
function get_ids_in_category($catalog, $category) {
  // filter items matching the category
  $filtered_items = array_filter(
    $catalog,
    function($item) use ($category) {
      return $category === null ||
      strtolower($category) === strtolower($item['category']);
    });
  // sort an array of [ id => title(string) ]
  $sorted_items = array_map(
    function ($item) use ($category) {
      return remove_beginning_articles($item['title']);
    },
    $filtered_items);
  asort($sorted_items);
  // return array of ids
  return array_keys($sorted_items);
}

/*
 * Example: remove_first_word('The Ruins', 'The ') => 'Ruins'
 */
function remove_beginning_articles($str) {
  $articles = ['The ', 'A ', 'An '];
  foreach ($articles as $a)
    if (substr($str, 0, strlen($a)) === $a)
      $str = substr($str, strlen($a));
  return $str;
}

/*
 * Generate drop-down <option> line in 'suggest.php'
 */
function get_option_html($option, $current) {
  return "<option value='$option' "
    . (isset($current) && $current === $option ? 'selected' : '')
    . ">$option</option>";
}

?>
