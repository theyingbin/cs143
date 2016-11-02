<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
<div class="page-content">
  <h3>Add a Director to a Movie</h3>

  <?php
    $db = new mysqli('localhost', 'cs143', '', 'CS143');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $movies=$db->query("SELECT id, title, year FROM Movie ORDER BY title ASC") or die(mysqli_error($db));
    $directors=$db->query("SELECT id, first, last, dob FROM Director ORDER BY first ASC") or die(mysqli_error($db));
    $moviesDisplay="";
    $directorsDisplay="";
    while($row = $movies->fetch_array()) {
      $id = $row["id"];
      $title = $row["title"];
      $year = $row["year"];
      $moviesDisplay .= "<option value= " . $id . ">" . $title . " (" . $year . ")</option>"; 
    }

    while($row = $directors->fetch_array()) {
        $id = $row["id"];
        $first = $row["first"];
        $last = $row["last"];
        $dob = $row["dob"];
        $directorsDisplay .= "<option value= " . $id . ">" . $first . " " . $last . " (" . $dob . ")</option>"; 
      }

    $movies->free();
    $directors->free();
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
      <label for="director">Directors</label>
      <select name="director">
        <option selected disabled>Pick a Director</option>
        <?=$directorsDisplay?>
      </select>
    </div>
    <br>
    <button type="submit" name="submit" class="btn btn-default">Add Movie Director Relation!</button>
  </form>

  <?php
    $movieID=$_GET["movie"];
    $directorID=$_GET["director"];


    if ($movieID == '' && $directorID == '') {
      // Do Nothing - no query yet
    } else if ($movieID == '') {
      echo "Please select a movie.";
    } else if ($directorID == '') {
      echo "Please select a director.";
    } else { // Valid input
      $movieID = $db->real_escape_string($movieID);
      $directorID = $db->real_escape_string($directorID);

      $queryMD = $db->query("INSERT INTO MovieDirector (mid, did) VALUES ('$movieID', '$directorID')") or die(mysqli_error($db));

      $movie = $db->query("SELECT title FROM Movie WHERE id = '$movieID'") or die(mysqli_error($db));
      $director = $db->query("SELECT first, last FROM Director WHERE id = '$directorID'") or die(mysqli_error($db));

      $movieArr = $movie->fetch_array();
      $directorArr = $director->fetch_array();

      echo "(" . $movieArr["title"] . ", " . $directorArr["first"] . " " . $directorArr["last"] . ") Pair Added!";
    }

    $queryMD->free();
    $movie->free();
    $director->free();
    $db->close();
  ?>

</div>
</body>
</html>