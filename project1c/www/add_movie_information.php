<?php include("navbar.php");?>
<!DOCTYPE html>
<html>
<body>
  <div class="page-content">
    <h1>Add new Movie Information</h1>
    <form method = "GET" action="#">
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control" placeholder="Enter a Title" name="title"/>
      </div>
      <div class="form-group">
        <label for="company">Company</label>
        <input type="text" class="form-control" placeholder="Enter a Company" name="company"/>
      </div>
      <div class="form-group">
        <label for="year">Year</label>
        <input type="text" class="form-control" placeholder="Ex: 2008" name="year"/>
      </div>
      <div class="form-group">
        <label for="rating">MPAA Rating</label>
      	<select name="rating">
    	  <option value="G">G</option>
    	  <option value="PG">PG</option>
    	  <option value="PG-13">PG-13</option>
    	  <option value="R">R</option>
    	  <option value="NC-17">NC-17</option>
  	  	</select>
      </div>
      <div class="form-group">
        <label for="genre">Genre:</label>
        	<div class="checkbox">
				<label><input type="checkbox" name="genre[]" value="Action">Action</input></label>
				<label><input type="checkbox" name="genre[]" value="Adult">Adult</input></label>
				<label><input type="checkbox" name="genre[]" value="Adventure">Adventure</input></label>
				<label><input type="checkbox" name="genre[]" value="Animation">Animation</input></label>
				<label><input type="checkbox" name="genre[]" value="Comedy">Comedy</input></label>
				<label><input type="checkbox" name="genre[]" value="Crime">Crime</input></label>
				<label><input type="checkbox" name="genre[]" value="Documentary">Documentary</input></label>
				<label><input type="checkbox" name="genre[]" value="Drama">Drama</input></label>
				<label><input type="checkbox" name="genre[]" value="Family">Family</input></label>
			</div>
			<div class="checkbox">
				<label><input type="checkbox" name="genre[]" value="Fantasy">Fantasy</input></label>
				<label><input type="checkbox" name="genre[]" value="Horror">Horror</input></label>
				<label><input type="checkbox" name="genre[]" value="Musical">Musical</input></label>
				<label><input type="checkbox" name="genre[]" value="Mystery">Mystery</input></label>
				<label><input type="checkbox" name="genre[]" value="Romance">Romance</input></label>
				<label><input type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi</input></label>
				<label><input type="checkbox" name="genre[]" value="Short">Short</input></label>
				<label><input type="checkbox" name="genre[]" value="Thriller">Thriller</input></label>
				<label><input type="checkbox" name="genre[]" value="War">War</input></label>
				<label><input type="checkbox" name="genre[]" value="Western">Western</input></label>
			</div>
      </div>
      <button type="submit" name="submit" class="btn btn-default">Add Movie Information!</button>
    </form>
<?php
	$db = new mysqli('localhost', 'cs143', '', 'CS143');
	if($db->connect_errno > 0){
    	die('Unable to connect to database [' . $db->connect_error . ']');
	}

  $title=trim($_GET["title"]);
  $company=trim($_GET["company"]);
  $year=$_GET["year"];
  $rating=$_GET["rating"];
  $genre=$_GET["genre"];

	if ($title == '' && $company == '' && $year == '' && $rating == '' && count($genre) == 0) {
		// Do Nothing - no query yet
	} else if ($title == '') {
		echo "<h4>Please enter a valid title.</h4>";
	} else if ($company == '') {
		echo "<h4>Please enter a valid company.</h4>";
	} else if ($year == '' || $year <= 1500 || $year >= 2100) {
		echo "<h4>Please enter a valid year.</h4>";	
	} else { // Valid input
		$maxIDs = $db->query("SELECT MAX(id) FROM MaxMovieID") or die(mysqli_error($db));

		$maxIDArr = mysqli_fetch_array($maxIDs);
		$curMaxID = $maxIDArr[0];
		$newMaxID = $curMaxID + 1;

		$company = $db->real_escape_string($company);
		$title = $db->real_escape_string($title);

		$MIquery = $db->query("INSERT INTO Movie (id, title, year, rating, company) VALUES ('$newMaxID', '$title', '$year', '$rating', '$company')") or die(mysqli_error($db));

		$db->query("UPDATE MaxMovieID SET id=$newMaxID WHERE id=$curMaxID") or die(mysqli_error($db));

		for ($i = 0; $i < count($genre); $i++) {
			$Gquery = $db->query("INSERT INTO MovieGenre (mid, genre) VALUES ('$newMaxID', '$genre[$i]')") or die(mysqli_error($db));
		}

		echo "<h4>Added " . $title . "!</h4>";
	}
  $maxIDs->free();
  $MIquery->free();
  $Gquery->free();
  $db->close();
?>
  </div>
</body>
</html>