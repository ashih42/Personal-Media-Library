<?php

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/src/Exception.php';

/* Check if form was submitted */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
  $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

  $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
  $category = trim(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));
  $format = trim(filter_input(INPUT_POST, 'format', FILTER_SANITIZE_STRING));
  $genre = trim(filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_STRING));
  $year = trim(filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT));
  $details = trim(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_SPECIAL_CHARS));

  if ($name === '' || $email === '' || $title === '' || $category === '')
    $error_message = 'Please fill in the required fields: Name, Email, Title, and Category.';

  if (!isset($error_message) && !PHPMailer::validateAddress($email))
    $error_message = "Invalid email address: $email";

  /* Check if a bot filled this invisible field */
  if (!isset($error_message) && $_POST['address'] !== '')
    $error_message = 'Are you a bot LOL!';

  /* Send email from Gmail account */
  if (!isset($error_message)) {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = 2;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = 'lurkinator@gmail.com';
    $mail->Password = 'oljjgacestxvbtdh';

    $mail->setFrom('lurkinator@gmail.com', $name);
    $mail->addReplyTo($email, $name);
    $mail->addAddress('andyshih51@gmail.com');
    
    $mail->Subject = "Library Suggestion from $name";
    $mail->Body = <<<END
Name: $name
Email: $email

Suggested Item:

Title: $title
Category: $category
Format: $format
Genre: $genre
Year: $year
Details: $details
END;
    
    if ($mail->send())
      exit (header('Location: suggest.php?status=thanks'));
    else
      $error_message = 'Mailer Error: ' . $mail->ErrorInfo;
  }
}

$page_title = 'Suggest a Media Item';
$section = 'suggest';

$genres = ['Books', 'Movies', 'Music'];

$format_books = ['Audio', 'Ebook', 'Hardback', 'Paperback'];
$format_movies = ['Blu-ray', 'DVD', 'Streaming', 'VHS'];
$format_music = ['Cassette', 'CD', 'MP3', 'Vinyl'];

$genre_books = ['Action', 'Adventure', 'Comedy', 'Fantasy', 'Historical', 'Historical Fiction', 'Horror', 'Magical Realism',
  'Mystery', 'Paranoid', 'Philosophical', 'Romance', 'Saga', 'Satire', 'Sci-Fi', 'Tech', 'Thriller', 'Urban'];
$genre_movies = ['Action', 'Adventure', 'Animation', 'Biography', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family', 'Fantasy',
  'Film-Noir', 'History', 'Horror', 'Musical', 'Mystery', 'Romance', 'Sci-Fi', 'Sports', 'Thriller', 'War', 'Western'];
$genre_music = ['Alternative', 'Blues', 'Classical', 'Country', 'Dance', 'Easy Listening', 'Electronic', 'Folk', 'Hip Hop/Rap',
  'Inspirational/Gospel', 'Jazz', 'Latin', 'New Age', 'Opera', 'Pop', 'R&B/Soul', 'Reggae', 'Rock'];

include 'inc/header.php';
include 'inc/functions.php';
?>

<div class="section page">
  <div class="wrapper">
    <h1>Suggest a Media Item</h1>
    <!-- Show 'Thank You' message -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'thanks') {?>
      <p>Thank you for the email! I&apos;ll check out your suggestion shortly!</p>
    <!-- Show input form -->
    <?php
    } else {
      if (isset($error_message))
        echo "<p class='message'>$error_message</p>";
      else
        echo '<p>If you think there is something missing, let me know! Complete the form to send me an email.</p>';
    ?>
      <form method="post" action="suggest.php">
        <table>
          <!-- Name -->
          <tr>
            <th><label for="name">Name (required)</label></th>
            <td><input type="text" id="name" name="name" value="<?= $name ?? '' ?>"></td>
          </tr>
          <!-- Email -->
          <tr>
            <th><label for="email">Email (required)</label></th>
            <td><input type="text" id="email" name="email" value="<?= $email ?? '' ?>"></td>
          </tr>
          <!-- Title -->
          <tr>
            <th><label for="title">Title (required)</label></th>
            <td><input type="text" id="title" name="title" value="<?= $title ?? '' ?>"></td>
          </tr>
          <!-- Category -->
          <tr>
            <th><label for="category">Category (required)</label></th>
            <td>
              <select id="category" name="category">
                <option value="">Select One</option>
                <?php
                foreach (['Books', 'Movies', 'Music'] as $option)
                  echo get_option_html($option, $category);
                ?>
              </select>
            </td>
          </tr>
          <!-- Format -->
          <tr>
            <th><label for="format">Format</label></th>
            <td>
              <select id="format" name="format">
                <option value="">Select One</option>
                <optgroup id="format_books" label="Books">
                  <?php
                  foreach ($format_books as $option)
                    echo get_option_html($option, $format);
                  ?>
                </optgroup>
                <optgroup id="format_movies" label="Movies">
                  <?php
                  foreach ($format_movies as $option)
                    echo get_option_html($option, $format);
                  ?>
                </optgroup>
                <optgroup id="format_music" label="Music">
                  <?php
                  foreach ($format_music as $option)
                    echo get_option_html($option, $format);
                  ?>
                </optgroup>
              </select>
            </td>
          </tr>
          <!-- Genre -->
          <tr>
            <th><label for="genre">Genre</label></th>
            <td>
              <select name="genre" id="genre">
                <option value="">Select One</option>
                <optgroup id="genre_books" label="Books">
                  <?php
                  foreach ($genre_books as $option)
                    echo get_option_html($option, $genre);
                  ?>
                </optgroup>
                <optgroup id="genre_movies" label="Movies">
                  <?php
                  foreach ($genre_movies as $option)
                    echo get_option_html($option, $genre);
                  ?>
                </optgroup>
                <optgroup id="genre_music" label="Music">
                  <?php
                  foreach ($genre_music as $option)
                    echo get_option_html($option, $genre);
                  ?>
                </optgroup>
              </select>
            </td>
          </tr>
          <!-- Year -->
          <tr>
            <th><label for="year">Year</label></th>
            <td><input type="text" id="year" name="year" value="<?= $year ?? '' ?>"></td>
          </tr>
          <!-- Details -->
          <tr>
            <th><label for="details">Additional Details</label></th>
            <td><textarea id="details" name="details"><?= $details ?? '' ?></textarea></td>
          </tr>
          <!-- Invisible field for bots -->
          <tr style="display:none">
            <th><label for="address">Address</label></th>
            <td>
              <input type="text" id="address" name="address">
              <p>Please leave this field blank.</p>
            </td>
          </tr>
        </table>
        <input type="submit" value="Send">
      </form>
      <!-- jQuery to show/hide relevant form options -->
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="js/suggest_showhide.js"></script>
    <?php } ?>
  </div>
</div>

<?php include 'inc/footer.php'; ?>
