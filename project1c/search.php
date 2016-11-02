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
      <a href="search.php" class="dropbtn">Search Interface</a>
      <div class="dropdown-content">
        <a href="search.php">Search Actor/Movie</a>
      </div>
    </li>
  </ul>
  <div class="page-content">
    <h3>Search Movies and Actors</h3>
    <p>
      <form action="search.php" method="GET">
        <input type="text" name="search" placeholder="Search Here!"></input>    
        <input type="submit" value="Search!" />
      </form>
    </p>
<?php
  $db = new mysqli('localhost', 'cs143', '', 'CS143');
  if($db->connect_errno > 0){
      die('Unable to connect to database [' . $db->connect_error . ']');
  }
  $search = $_GET["search"];
  $search = $db->real_escape_string($search);
  $words = explode(' ', $search);
  if (trim($search) == '') {
    // Do Nothing because no search query
  } else {
    echo "<h3>Matching Actors</h3>";
    $query = "SELECT id, last, first, dob FROM Actor WHERE (first LIKE '%$words[0]%' OR last LIKE '%$words[0]%') ORDER BY first ASC";
    for($i = 1; $i < count($words); $i++) {
      $word = $words[$i];
      $query = $query . "AND (first LIKE '%$word%' OR last LIKE '%$word%')";
    }

    $actors = $db->query($query) or die(mysqli_error());

    while ($row = $actors->fetch_assoc()) {
      echo "<a href=\"show_actor_info.php?id=" . $row["id"] . "\">" . $row["first"] . " " . $row["last"] . " (" . $row["dob"] . ")</a><br>";
    }

    $actors->free();

    echo "<h3>Matching Movies</h3>";
    $query = "SELECT id, title, year FROM Movie WHERE (title LIKE '%$words[0]%') ORDER BY title ASC";
    for($i = 1; $i < count($words); $i++) {
      $word = $words[$i];
      $query = $query . "AND (title LIKE '%$word%')";
    }

    $movies = $db->query($query) or die(mysqli_error());

    while ($row = $movies->fetch_assoc()) {
      echo "<a href=\"show_movie_info.php?id=" . $row["id"] . "\">" . $row["title"] . " (" . $row["year"] . ")</a><br>";
    }
    $movies->free();
  }
  $db->free();
?>
</div>
</body>
</html>