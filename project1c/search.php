<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
  <div class="page-content">
    <h1>Search Movies and Actors</h1>
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
    $query = "SELECT id, last, first, dob FROM Actor WHERE (first LIKE '%$words[0]%' OR last LIKE '%$words[0]%')";
    for($i = 1; $i < count($words); $i++) {
      $word = $words[$i];
      $query .= "AND (first LIKE '%$word%' OR last LIKE '%$word%')";
    }
    $query .= "ORDER BY first ASC";

    $actors = $db->query($query) or die(mysqli_error());

    if ($actors->num_rows > 0) {
      echo "<h2>Matching Actors</h2>";
      echo "<div class='table-responsive'>
                        <table border=1 class='table table-bordered table-condensed table-hover'>
                            <thead> <tr><td align='center'>Name</td><td align='center'>Date of Birth</td></tr></thead>
                            <tbody>";

      while ($row = $actors->fetch_assoc()) {
        echo "<tr><td align='center'><a href=\"show_actor_info.php?id=" . $row["id"] . "\">" . $row["first"] . " " . $row["last"] . "</a></td><td align='center'><a href=\"show_actor_info.php?id=" . $row["id"] . "\">" . $row["dob"] . "</a></td></tr>";
      }
      echo "</tbody></table></div><hr>";
    } else {
      echo "<h2><b>No Matching Actors</b></h2><hr>";
    }
    
    $actors->free();

    $query = "SELECT id, title, year FROM Movie WHERE (title LIKE '%$words[0]%')";
    for($i = 1; $i < count($words); $i++) {
      $word = $words[$i];
      $query .= "AND (title LIKE '%$word%')";
    }
    $query .= "ORDER BY title ASC";

    $movies = $db->query($query) or die(mysqli_error());
    if ($movies->num_rows > 0) {
      echo "<h2>Matching Movies</h2>";
      echo "<div class='table-responsive'>
                        <table border=1 class='table table-bordered table-condensed table-hover'>
                            <thead> <tr><td align='center'>Title</td><td align='center'>Year</td></tr></thead>
                            <tbody>";
      while ($row = $movies->fetch_assoc()) {
        echo "<tr><td align='center'><a href=\"show_movie_info.php?id=" . $row["id"] . "\">" . $row["title"] . "</a></td><td align='center'><a href=\"show_movie_info.php?id=" . $row["id"] . "\">" . $row["year"] . "</a></td></tr>";
      }
      echo "</tbody></table></div>";
    } else {
      echo "<h2><b>No Matching Movies</b></h2>";
    }
    $movies->free();
  }
  $db->free();
?>
  
</div>
</body>
</html>