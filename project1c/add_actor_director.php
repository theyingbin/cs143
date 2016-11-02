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
  $is_actor=$_GET["identity"]=='Actor';
  $first_name=$_GET["fname"];
  $last_name=$_GET["lname"];
  $gender=$_GET["sex"];
  $dob_temp=$_GET["dateb"];
  $dod_temp=$_GET["dated"];

  if(isset($_GET['submit'])){
    echo "helloworld<br>";
    if(strlen($first_name) == 0 || strlen($last_name) == 0 || strlen($dob_temp) == 0){
      echo "<h4>Invalid Input. A field was left empty</h4>";
      exit(1);
    }
    $dob=DateTime::createFromFormat('Y-m-d', $dob_temp);
    $dod=DateTime::createFromFormat('Y-m-d', $dod_temp);
    if($dob==False){
      die('<h2>invalid date format</h2>');
    }
    if(strlen($dod_temp) && $dod==False){
      die('<h2>invalid date format</h2>');
    }
    print_r($dob);
  }
?>
  </div>
</body>
</html>