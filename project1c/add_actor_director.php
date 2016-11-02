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
    <h3>Add new Actor/Director</h3>
    <form method = "GET" action="#">
      <label class="radio-inline">
        <input type="radio" checked="checked" name="identity" value="Actor"/>Actor
      </label>
      <label class="radio-inline">
        <input type="radio" name="identity" value="Director"/>Director
      </label>
      <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" class="form-control" placeholder="Text input" name="fname"/>
      </div>
      <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" class="form-control" placeholder="Text input" name="lname"/>
      </div>
      <label class="radio-inline">
        <input type="radio" name="sex" checked="checked" value="male">Male
      </label>
      <label class="radio-inline">
        <input type="radio" name="sex" value="female">Female
      </label>
      <div class="form-group">
        <label for="DOB">Date of Birth</label>
        <input type="text" class="form-control" placeholder="Text input" name="dateb">ie: 1997-05-05<br>
      </div>
      <div class="form-group">
        <label for="DOD">Date of Die</label>
        <input type="text" class="form-control" placeholder="Text input" name="dated">(leave blank if alive now)<br>
      </div>
      <button type="submit" name="submit" class="btn btn-default">Add!</button>
    </form>
<?php
  $db = new mysqli('localhost', 'cs143', '', 'CS143');
  if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
  }
  $is_actor=$_GET["identity"] == 'Actor';
  $first_name=trim($_GET["fname"]);
  $last_name=trim($_GET["lname"]);
  $gender=trim($_GET["sex"]);
  $dob=trim($_GET["dateb"]);
  $dod=trim($_GET["dated"]);
  $dob_temp=DateTime::createFromFormat('Y-m-d', $dob);
  $dod_temp=$dod == "" ? NULL:DateTime::createFromFormat('Y-m-d', $dod);

  if($first_name == "" && $last_name == "" && $dob == "" && $dod == ""){
    // Do Nothing
  }
  else if($first_name == "" || $last_name == "" || $dob == ""){
    echo "Invalid Input. A field was left empty";
  }
  else if($dob_temp == False){
    echo 'invalid date of birth format';
  }
  else if($dod != "" && $dod_temp == False){
    echo 'invalid date of death format';
  }
  else if($dod != "" && $dod_temp != False && $dob_temp > $dod_temp){
    echo 'Cannot die before being born';
  }
  else{
    $maxIDs = $db->query("SELECT MAX(id) FROM MaxPersonID") or die(mysqli_error($db));
    $maxID_ary = mysqli_fetch_array($maxIDs);
    $oldMaxID = $maxID_ary[0];
    $newMaxID = $oldMaxID + 1;
    $first_name = $db->real_escape_string($first_name);
    $last_name = $db->real_escape_string($last_name);

    $insert_query = NULL;
    $update_query = NULL;

    if($is_actor){
      $insert_query = $db->query("INSERT INTO Actor (id, last, first, sex, dob, dod) VALUES ($newMaxID, '$last_name', '$first_name', '$gender', '$dob', '$dod')") or die(mysqli_error($db));
    }
    else{
      $insert_query = $db->query("INSERT INTO Director (id, last, first, dob, dod) VALUES ('$newMaxID', '$last_name', '$first_name', '$dob', '$dod')") or die(mysqli_error($db));
    }
    $update_query = $db->query("UPDATE MaxPersonID SET id=$newMaxID WHERE id=$oldMaxID") or die(mysqli_error($db));
    
    echo "Added";

    $insert_query->free();
    $update_query->free();
  }
  $db->close();
?>
  </div>
</body>
</html>