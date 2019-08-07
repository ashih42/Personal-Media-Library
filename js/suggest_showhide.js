/*
 * Show/hide drop-down optgroups based on selected category in 'suggest.php'
 */

$(document).ready(() => {
  toggleFields();

  $('#category').change(() => {
    toggleFields();
    $('#format').val('');
    $('#genre').val('');
  });
});

function toggleFields() {
  if ($('#category').val() === 'Books') {
    $('#format_books').show();
    $('#genre_books').show();
  } else {
    $('#format_books').hide();
    $('#genre_books').hide();
  }
  if ($('#category').val() === 'Movies') {
    $('#format_movies').show();
    $('#genre_movies').show();
  } else {
    $('#format_movies').hide();
    $('#genre_movies').hide();
  }
  if ($('#category').val() === 'Music') {
    $('#format_music').show();
    $('#genre_music').show();
  } else {
    $('#format_music').hide();
    $('#genre_music').hide();
  }
}
