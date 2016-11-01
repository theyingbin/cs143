<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>CS143 Project 1C</title>

  <link href="css/project1c.css" rel="stylesheet">
</head>

<body>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li class="dropdown">
      <a href="#" class="dropbtn">Add New Content</a>
      <div class="dropdown-content">
        <a href="add_actor_director.php">Add Actor/Director</a>
        <a href="add_movie_information.php">Add Movie Information</a>
        <a href="#">Add Movie/Actor Relation</a>
        <a href="add_movie_director_relation.php">Add Movie/Director Relation</a>
      </div>
    </li>
    <li class="dropdown">
      <a href="#" class="dropbtn">Browsing Content</a>
      <div class="dropdown-content">
        <a href="#">Show Actor Information</a>
        <a href="show_movie_info.php">Show Movie Information</a>
      </div>
    </li>
    <li class="dropdown">
      <a href="#" class="dropbtn">Search Interface</a>
      <div class="dropdown-content">
        <a href="#">Search Actor/Movie</a>
      </div>
    </li>
  </ul>
<div class="page-content">
  <h3>Add a Comment to a Movie</h3>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $movies=$db->query("SELECT id, title, year FROM Movie ORDER BY title ASC") or die(mysqli_error($db));
    $moviesDisplay="";

    while($row = $movies->fetch_array()) {
      $id = $row["id"];
      $title = $row["title"];
      $year = $row["year"];
      $moviesDisplay .= "<option value= " . $id . ">" . $title . " (" . $year . ")</option>"; 
    }

    $movies->free();
  ?>

  <form method = "GET" action="#">
    <div class="form-group">
      <label for="movie">Movies</label>
      <select name="movie">
        <option selected disabled>Pick a Movie</option>
        <?=$moviesDisplay?>
      </select>
    </div>
    <br/>
    <div class="form-group">
      <label for="name">Your Name</label>
      <input type="text" name="name">
    </div>
    <br>
    <div class="form-group">
      <label for="rating">Rating</label>
      <select name="rating">
        <option selected disabled>Pick a Rating</option>
        <option value="5">5 / 5</option>
        <option value="4">4 / 5</option>
        <option value="3">3 / 5</option>
        <option value="2">2 / 5</option>
        <option value="1">1 / 5</option>
      </select>
    </div>
    <div class="form-group">
      <textarea name="comment" rows="5" placeholder="Enter Comment Here!"></textarea>
    </div>
    <button type="submit" name="submit" class="btn btn-default">Add Movie Comment!</button>
  </form>

  <?php
    $movie = $_GET["movie"];
    $name = trim($_GET["name"]);
    $rating = $_GET["rating"];
    $comment = trim($_GET["comment"]);

    if ($movie == '' && $name == '' && $rating == '' && $comment == '') {
      // Do Nothing - no query yet
    } else if ($movie == '') {
      echo "Please select a movie.";
    } else if ($rating == '') {
      echo "Please select a rating.";
    } else { // Valid input
      if ($name == '') {
        $name = "Anonymous";
      }
      $name = $db->real_escape_string($name);
      $comment = $db->real_escape_string($comment);

      $queryMC = $db->query("INSERT INTO Review (name, time, mid, rating, comment) VALUES ('$$name', now(), '$movie', '$rating', '$comment')") or die(mysqli_error($db));

      echo "Comment Added!"
      echo "<a href=\"show_movie_info.php?id=" . $movie . "\">Go Back to the Movie</a>";
    }

    $queryMC->free();
    $db->close();
  ?>

</div>
</body>
</html>