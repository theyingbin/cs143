<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>CS143 Project 1c</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/project1c.css" rel="stylesheet">
</head>

<body>
  <div id="ui-dropdown">
    <ul class="proj-navbar">
      <li><a href="index.php">Home</a></li>
      <li class="proj-dropdown">
        <a href="#" class="proj-dropbtn">Add New Content</a>
        <div class="proj-dropdown-content">
          <a href="add_actor_director.php">Add Actor/Director</a>
          <a href="add_movie_information.php">Add Movie Information</a>
          <a href="add_movie_actor_relation.php">Add Movie/Actor Relation</a>
          <a href="add_movie_director_relation.php">Add Movie/Director Relation</a>
        </div>
      </li>
      <li class="proj-dropdown">
        <a href="#" class="proj-dropbtn">Browsing Content</a>
        <div class="proj-dropdown-content">
          <a href="show_actor_info.php">Show Actor Information</a>
          <a href="show_movie_info.php">Show Movie Information</a>
        </div>
      </li>
      <li class="proj-dropdown">
        <a href="search.php" class="proj-dropbtn">Search Interface</a>
        <div class="proj-dropdown-content">
          <a href="search.php">Search Actor/Movie</a>
        </div>
      </li>
    </ul>
  </div>
</body>
</html>