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
        <a href="#">Add Movie/Director Relation</a>
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
    <h3>Add new Movie Information</h3>
    <form method = "GET" action="#">
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" placeholder="Input a Title" name="title"/>
      </div>
      <div class="form-group">
        <label for="company">Company</label>
        <input type="text" class="form-control" placeholder="Input a Company" name="company"/>
      </div>
      <div class="form-group">
        <label for="year">Year</label>
        <input type="text" class="form-control" placeholder="Input a Year (ex. 2008)" name="year"/>
      </div>
      <div>
        <label for="rating">MPAA Rating</label>
      	<select name="rating">
    	  <option value="G">G</option>
    	  <option value="PG">PG</option>
    	  <option value="PG-13">PG-13</option>
    	  <option value="R">R</option>
    	  <option value="NC-17">NC-17</option>
  	  	</select>
      </div>
      <div>
        <label for="genre">Genre</label>
      	<select name="genre">
    	  <option value="Action">Action</option>
    	  <option value="Adventure">Adventure</option>
    	  <option value="Animation">Animation</option>
    	  <option value="Biography">Biography</option>
    	  <option value="Comedy">Comedy</option>
    	  <option value="Crime">Crime</option>
    	  <option value="Documentary">Documentary</option>
    	  <option value="Drama">Drama</option>
    	  <option value="Family">Family</option>
    	  <option value="Fantasy">Fantasy</option>
    	  <option value="Film-Noir">Film-Noir</option>
    	  <option value="History">History</option>
    	  <option value="Horror">Horror</option>
    	  <option value="Music">Music</option>
    	  <option value="Musical">Musical</option>
    	  <option value="Mystery">Mystery</option>
    	  <option value="Romance">Romance</option>
    	  <option value="Sci-Fi">Sci-Fi</option>
    	  <option value="Sport">Sport</option>
    	  <option value="Thriller">Thriller</option>
    	  <option value="War">War</option>
    	  <option value="Western">Western</option>
  	  	</select>
      </div>
      <button type="submit" name="submit" class="btn btn-default">Add!</button>
    </form>
<?php
  $title=$_GET["title"];
  $company=$_GET["company"];
  $year=$_GET["year"];
  $rating=$_GET["rating"];
  $genre=$_GET["genre"];

  if(isset($_GET['submit'])){
    echo "helloworld<br>";
    if(strlen($title) == 0 || strlen($company) == 0 || strlen($year) == 0){
      echo "<h4>Invalid Input. A field was left empty</h4>";
      exit(1);
    }
    print_r($title);
    print_r($company);
    print_r($year);
    print_r($rating);
    print_r($genre);
  }
?>
  </div>
</body>
</html>