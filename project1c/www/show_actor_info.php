<?php include("navbar.php");?>
<html>
<body>
    <div class="page-content">
        <h1>Actor Information</h1>
    <?php
        $db = new mysqli('localhost', 'cs143', '', 'CS143');
        if($db->connect_errno > 0){
            die('Unable to connect to the database [' . $db->connect_error . ']');
        }

        $id = trim($_GET["id"]);

        if($id != ""){
            $actor = $db->query("SELECT first, last, sex, dob, dod FROM Actor WHERE id=$id") or die(mysqli_error());
            $row = $actor->fetch_row();
            echo "<div class='table-responsive'>
                        <table border=1 class='table table-bordered table-condensed table-hover'>
                            <thead> <tr><td align='center'><b>Name</b></td><td align='center'><b>Sex</b></td><td align='center'><b>Date of Birth</b></td><td align='center'><b>Date of Death</b></td></tr></thead>
                            <tbody><tr>";
            echo "<td align='center'>".$row[0]." ".$row[1]."</td>";
            echo "<td align='center'>".$row[2]."</td>";
            echo "<td align='center'>".$row[3]."</td>";
            if($row[4] == "" || $row[4] == "0000-00-00")
                echo "<td align='center'> N/A </td>";
            else
                echo "<td align='center'>".$row[4]."</td>";
            echo "</tr></tbody></table></div>";
            
            $actor_movies = $db->query("SELECT role, title, mid FROM MovieActor, Movie WHERE $id=aid AND mid=id") or die(mysqli_error());
            if ($actor_movies->num_rows > 0) {
                echo "<hr><h2>Actor's Movies and Role</h2>";
                echo "<div class='table-responsive'>
                    <table border=1 class='table table-bordered table-condensed table-hover'><thead> <tr><td align='center'><b>Role</b></td><td align='center'><b>Movie Title</b></td></thead></tr>
                    <tbody>";
                while($row = $actor_movies->fetch_array()){
                    echo "<tr><td align='center'>" . $row['role'] . "</td>";
                    echo "<td align='center'><a href='show_movie_info.php?id=" . $row['mid']. "'>" .$row['title'].'</a>'.'</td></tr>';
                }
                echo "</tbody></table></div><hr>";
            } else {
                echo "<h3>No Movie Information Available for " . $row[0] . " " . $row[1] . "</h3><hr>";
            }
            
            $actor->free();
            $actor_movies->free();
            $db->close();
        }
    ?>
        <p>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search Here!"></input>    
            <input type="submit" value="Search!" />
        </form>
        </p>

    </div>
</body>
</html>