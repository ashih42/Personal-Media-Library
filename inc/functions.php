<?php

function get_item_html($id, $item) {
  return <<<HTML
    <li>
      <a href="details.php?id=$id">
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
    . (isset($current) && $current === $option ? 'selected' : '') .
    ">$option</option>";
}

?>
