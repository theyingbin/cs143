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
        <a href="#">Show Movie Information</a>
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