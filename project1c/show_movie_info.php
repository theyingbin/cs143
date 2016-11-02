<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
<div class="page-content">
  <h3>Movie Information</h3>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $id = $_GET["id"];

    if ($id == '') {
    ?>
      <label for="search">Search for Movie Information:</label>
        <form class="form-group" action="search.php" method ="GET">
          <input type="text" id="search" placeholder="Search for Movie Information" name="search"><br>
          <input type="submit" value="Search!" class="btn btn-default"><br>
        </form>
   <?php } else {
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
        echo "<a href=\"show_actor_info.php?id=" . $row['aid'] . "\">" . $row['first'] . " " . $row['last'] . "</a> - " . $row['role'] . "<br>";
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
        echo $row[1] . " People have already reviewed - <a href=\"add_movie_comment.php?id=" . $id . "\">So why don't you!</a><br><br>";
      }

      $reviews = $db->query("SELECT name, rating, time, comment FROM Review WHERE mid=$id ORDER BY time DESC") or die(mysqli_error());
      $reviewNum = $reviews->num_rows;
      while ($row = $reviews->fetch_array()) {
        echo "Review #" . $reviewNum . " - written by " . $row["name"] . "<br>";
        echo "Rating: " . $row["rating"] . "<br>";
        echo "Comment: " . $row["comment"] . "<br>";
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