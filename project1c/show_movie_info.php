<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
<div class="page-content">
  <h1>Movie Information</h1>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $id = $_GET["id"];

    if ($id != '') {
      $movie = $db->query("SELECT title, year, rating, company FROM Movie WHERE id=$id") or die(mysqli_error());
      $row = $movie->fetch_row();

      echo "<div class='table-responsive'>
                  <table border=1 class='table table-bordered table-condensed table-hover'>
                      <thead> <tr><td align='center'>Title</td><td align='center'>Year</td><td align='center'>MPAA Rating</td><td align='center'>Producer</td><td align='center'>Directors</td><td align='center'>Genres</td></tr></thead>
                      <tbody><tr>";
      echo "<td align='center'>" . $row[0] . "</td>";
      echo "<td align='center'>" . $row[1] . "</td>";
      echo "<td align='center'>" . $row[2] . "</td>";
      if($row[3] == "")
          echo "<td align='center'> N/A </td>";
      else
          echo "<td align='center'>" . $row[3] . "</td>";
      
      $directors = $db->query("SELECT D.first, D.last FROM Director D, MovieDirector MD WHERE MD.mid=$id AND MD.did=D.id") or die(mysqli_error());
      $first = true;
      echo "<td align='center'>";
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
      echo "</td>";

      $genres = $db->query("SELECT genre FROM MovieGenre WHERE $id=mid") or die(mysqli_error());
      $first = true;
      echo "<td align='center'>";
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
      echo "</td align='center'>";
      echo "</tr></tbody></table></div><hr>";

      $movie->free();
      $directors->free();
      $genres->free();


      $actors = $db->query("SELECT A.id, A.first, A.last, MA.role FROM Actor A, MovieActor MA WHERE $id=MA.mid AND MA.aid=A.id") or die(mysqli_error());

      if ($actors->num_rows > 0) {
        echo "<h2>Cast Information</h2>";

        echo "<div class='table-responsive'>
                  <table border=1 class='table table-bordered table-condensed table-hover'>
                      <thead> <tr><td>Cast</td><td>Role</td></tr></thead>
                      <tbody>";

      
        while($row = $actors->fetch_assoc()) {
          echo "<tr><td align='center'><a href=\"show_actor_info.php?id=" . $row['aid'] . "\">" . $row['first'] . " " . $row['last'] . "</a></td> <td align='center'>" . $row['role'] . "</td></tr>";
        }
        echo "</tbody></table></div><hr>";
      } else {
        echo "<h2>No Cast Information Available</h2>";
      }
      
      $ratings = $db->query("SELECT AVG(rating), COUNT(rating) FROM Review WHERE mid=$id") or die(mysqli_error());
      
      echo "<h2>User Reviews</h2>";

      $row = $ratings->fetch_array();

      echo "<div class='table-responsive'>
                <table border=1 class='table table-bordered table-condensed table-hover'>
                    <thead> <tr><td align='center'>Average Review</td></tr> </thead> 
                    <tbody><tr><td align='center'>";

      if ($row[1] == 0) {
        echo "N/A</td></tr></tbody></table></div>";
        echo "Be the first to <a href=\"add_movie_comment.php?id=" . $id . "\">submit a review!</a><br>";
      } else {
        echo $row[0] . " / 5</td></tr></tbody></table></div><br>";
        echo "<b>" . $row[1] . " People</b> have already reviewed - <a href=\"add_movie_comment.php?id=" . $id . "\">So why don't you!</a><br><br>";
      }

      $reviews = $db->query("SELECT name, rating, time, comment FROM Review WHERE mid=$id ORDER BY time DESC") or die(mysqli_error());
      
      if ($reviews->num_rows > 0) {
        echo "<div class='table-responsive'>
                  <table border=1 class='table table-bordered table-condensed table-hover'>
                      <thead> <tr><td align='center'>Review #</td><td align='center'>Author</td>
                        <td align='center'>Rating</td><td align='center'>Comment</td></tr></thead>
                      <tbody>";

        $reviewNum = $reviews->num_rows;
        while ($row = $reviews->fetch_array()) {
          echo "<tr><td align='center'>" . $reviewNum . "</td><td align='center'>" . $row["name"] . "</td>";
          echo "<td align='center'>" . $row["rating"] . "</td>";
          echo "<td align='center'>" . $row["comment"] . "</td>";
          echo "</tr>";
          $reviewNum--;
        }
        echo "</tbody></table></div><hr>";
      }

      $reviews->free();
    }
    ?>
    <p>
      <form action="search.php" method="GET">
        <input type="text" name="search" placeholder="Search Here!"></input>    
        <input type="submit" value="Search!" />
      </form>
    </p>
    <?php

    $db->close();
  ?>
</div>
</body>
</html>