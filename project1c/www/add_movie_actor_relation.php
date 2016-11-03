<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
  <div class="page-content">
    <h1>Add an Actor to a Movie</h1>
    <?php
      $db = new mysqli('localhost', 'cs143', '', 'CS143');
      if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
      }

      $movies = $db->query("SELECT id, title, year FROM Movie ORDER BY title ASC") or die(mysqli_error($db));
      $actors = $db->query("SELECT id, first, last, dob FROM Actor ORDER BY first ASC") or die(mysqli_error($db));
      $moviesDisplay="";
      $actorsDisplay="";
      
      while($row = $movies->fetch_array()) {
        $id = $row["id"];
        $title = $row["title"];
        $year = $row["year"];
        $moviesDisplay .= "<option value= " . $id . ">" . $title . " (" . $year . ")</option>"; 
      }

      while($row = $actors->fetch_array()){
        $id = $row["id"];
        $first = $row["first"];
        $last = $row["last"];
        $dob = $row["dob"];
        $actorsDisplay .= "<option value= " . $id . ">" . $first . " " . $last . " (" . $dob . ")</option>";
      }

      $movies->free();
      $actors->free();
    ?>
    <form method = "GET" action="#">
      <div class="form-group">
        <label for="movie">Movie:</label>
        <select name="movie">
          <option selected disabled>Pick a Movie</option>
          <?=$moviesDisplay?>
        </select>
      </div>
      <div class="form-group">
        <label for="actor">Actor:</label>
        <select name="actor">
          <option selected disabled>Pick a Actor</option>
          <?=$actorsDisplay?>
        </select>
      </div>
      <div class="form-group">
        <label for="role">Role:</label>
        <input type='text' name='role' class="form-control" >
        <br>
        <input type='submit' class="btn btn-default" value='Add Movie Actor Relation!'>
      </div>
    </form>
    <?php
      $movieID=$_GET["movie"];
      $actorID=$_GET["actor"];
      $role = $_GET["role"];

      if($movieID == "" && $actorID == "" && $role == "" ){
        // Do nothing
      } else if($movieID == ""){
        echo "<h4>Please select a movie.</h4>";
      } else if($actorID == ""){
        echo "<h4>Please select an actor.</h4>";
      } else if($role == ""){
        echo "<h4>Actor must have a role.</h4>";
      } else{
        $movieID = $db->real_escape_string($movieID);
        $actorID = $db->real_escape_string($actorID);
        $role = $db->real_escape_string($role);

        $insert_query = $db->query("INSERT INTO MovieActor (mid, aid, role) VALUES ('$movieID', '$actorID', '$role')") or die(mysqli_error($db));

        $movie = $db->query("SELECT title FROM Movie WHERE id = '$movieID'") or die(mysqli_error($db));
        $actor = $db->query("SELECT first, last FROM Actor WHERE id = '$actorID'") or die(mysqli_error($db));

        $movieArr = $movie->fetch_array();
        $actorArr = $actor->fetch_array();

        echo "<h4>Added (" . $movieArr["title"] . ", " . $actorArr["first"] . " " . $actorArr["last"] . ") Relation!</h4>";

        $insert_query->free();
        $movie->free();
        $actor->free();
      }
      $db->close();

    ?>
  </div>

</body>
</html>