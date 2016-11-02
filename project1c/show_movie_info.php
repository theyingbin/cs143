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
        <a href="search.php">Search Actor/Movie</a>
      </div>
    </li>
  </ul>
<div class="page-content">
  <h3>Movie Information</h3>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $id = $_GET["id"];

    if ($id == '') {
      echo "Invalid movie choice - Please select a valid movie. Thanks!";
    } else {
      $movie = $db->query("SELECT title, year, rating, company FROM Movie WHERE id=$id") or die(mysqli_error());
      $row = $movie->fetch_row();
      echo "<b>Title:</b> " . $row[0] . " (" . $row[1] . ")<br>";
      echo "<b>MPAA Rating:</b> " . $row[2] . "<br>";
      if ($row[3] == '') {
        echo "<b>Producer:</b> N/A <br>";
      } else {
        echo "<b>Producer:</b> " . $row[3] . "<br>";
      }
      $movie->free();

      echo "<b>Directors: </b>";
      $directors = $db->query("SELECT D.first, D.last FROM Director D, MovieDirector MD WHERE MD.mid=$id AND MD.did=D.id") or die(mysqli_error());
      $first = true;
      while ($row = $directors->fetch_array()) {
        if (!$first) {
          echo ", ";
        } else {
          $first = false;
        }
        echo $row['first'] . " " . $row['last'];
      }
      if ($first) {
        echo "N/A";
      }
      echo "<br>";
      $directors->free();

      echo "<b>Genres: </b>";
      $genres = $db->query("SELECT genre FROM MovieGenre WHERE $id=mid") or die(mysqli_error());
      $first = true;
      while ($row = $genres->fetch_array()) {
        if (!$first) {
          echo ", ";
        } else {
          $first = false;
        }
        echo $row['genre'];
      }
      if ($first) {
        echo "N/A";
      }
      echo "<br>";
      $genres->free();

      echo "<h3>Cast</h3>";
      $actors = $db->query("SELECT A.id, A.first, A.last, MA.role FROM Actor A, MovieActor MA WHERE $id=MA.mid AND MA.aid=A.id") or die(mysqli_error());
      while($row = $actors->fetch_assoc()) {
        echo "<a href=\"showActorInfo.php?id=" . $row['aid'] . "\">" . $row['first'] . " " . $row['last'] . "</a> - " . $row['role'] . "<br>";
      }
      echo "<br>";

      echo "<h3>User Reviews</h3>";
      $ratings = $db->query("SELECT AVG(rating), COUNT(rating) FROM Review WHERE mid=$id") or die(mysqli_error());
      $row = $ratings->fetch_array();
      echo "Average Review: ";
      if ($row[1] == 0) {
        echo "N/A<br>";
        echo "Be the first to <a href=\"add_movie_comment.php?id=" . $id . "\">submit a review!</a><br>";
      } else {
        echo $row[0] . " / 5<br>";
        echo $row[1] . "People have already reviewed - So why don't you! <a href=\"add_movie_comment.php?id=" . $id . "\">why don't you!</a><br>";
      }

      $reviews = $db->query("SELECT name, rating, time, comment FROM Review WHERE mid=$id ORDER BY time DESC") or die(mysqli_error());
      $reviewNum = $reviews->num_rows;
      while ($row = $reviews->fetch_array()) {
        echo "Review #" . $reviewNum . ") written by " . $row["name"] . "<br>";
        echo "Rating: " . $row["rating"] . "<br>";
        echo "Comment: " . $row4["comment"] . "<br>";
        echo "<br>";
        $reviewNum--;
      }

      $reviews->free();
    }

    $db->close();
  ?>
</div>
</body>
</html>