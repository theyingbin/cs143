<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
<div class="page-content">
  <h1>Add a Comment to a Movie</h1>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $idFromURL = $_GET["id"];

    $movies=$db->query("SELECT id, title, year FROM Movie ORDER BY title ASC") or die(mysqli_error($db));
    $moviesDisplay="";

    while($row = $movies->fetch_array()) {
      $id = $row["id"];
      $title = $row["title"];
      $year = $row["year"];
      if ($idFromURL == $id) {
        $moviesDisplay .= "<option value= " . $id . " selected>" . $title . " (" . $year . ")</option>"; 
      } else {
        $moviesDisplay .= "<option value= " . $id . ">" . $title . " (" . $year . ")</option>"; 
      }
    }

    $movies->free();
  ?>

  <form method = "GET" action="#">
    <div class="form-group">
      <label for="movie">Movies:</label>
      <select name="movie">
        <?=$moviesDisplay?>
      </select>
    </div>
    <div class="form-group">
      <label for="name">Your Name:</label>
      <input type="text" name="name">
    </div>
    <div class="form-group">
      <label for="rating">Rating:</label>
      <select name="rating">
        <option selected disabled>Pick a Rating</option>
        <option value="5">5/5</option>
        <option value="4">4/5</option>
        <option value="3">3/5</option>
        <option value="2">2/5</option>
        <option value="1">1/5</option>
      </select>
    </div>
    <div class="form-group">
      <textarea name="comment" rows="5" cols="100" placeholder="Enter Your Movie Comment Here!"></textarea>
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
      echo "<h4>Please select a movie.</h4>";
    } else if ($rating == '') {
      echo "<h4>Please select a rating.</h4>";
    } else { // Valid input
      if ($name == '') {
        $name = "Anonymous";
      }
      $name = $db->real_escape_string($name);
      $comment = $db->real_escape_string($comment);

      $queryMC = $db->query("INSERT INTO Review (name, time, mid, rating, comment) VALUES ('$name', now(), '$movie', '$rating', '$comment')") or die(mysqli_error($db));

      echo "<h4>Added Comment!</h4>";
      echo "<h5><a href=\"show_movie_info.php?id=" . $movie . "\">Go Back to the Movie</a><h5>";
    }

    $queryMC->free();
    $db->close();
  ?>

</div>
</body>
</html>