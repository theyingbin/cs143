<?php include("navbar.php");?>
<html>
<body>
    <div class="page-content">
        <h3>Actor Information</h3>
    <?php
        $db = new mysqli('localhost', 'cs143', '', 'CS143');
        if($db->connect_errno > 0){
            die('Unable to connect to the database [' . $db->connect_error . ']');
        }

        $id = $_GET["id"];

        if($id != ""){
            $actor = $db->query("SELECT first, last, sex, dob, dod FROM Actor WHERE id=$id") or die(mysqli_error());
            $row = $actor->fetch_row();
            echo "<hr><h4><b>Actor Information is:</b></h4>
                    <div class='table-responsive'>
                        <table border=1 class='table table-bordered table-condensed table-hover'>
                            <thead> <tr><td>Name</td><td>Sex</td><td>Date of Birth</td><td>Date of Death</td></tr></thead>
                            <tbody><tr>";
            echo "<td>".$row[0]." ".$row[1]."</td>";
            echo "<td>".$row[2]."</td>";
            echo "<td>".$row[3]."</td>";
            if($row[4] == "")
                echo "<td> N/A </td>";
            else
                echo "<td>".$row[4]."</td>";
            echo "</tr></tbody></table></div><hr>";
            echo "<hr><h4><b>Actor's Movies and Role</b></h4>";
            echo "<div class='table-responsive'>
                    <table border=1 class='table table-bordered table-condensed table-hover'><thead> <tr><td>Role</td><td>Movie Title</td></thead></tr>
                    <tbody>";
            $actor_movies = $db->query("SELECT role, title, mid FROM MovieActor, Movie WHERE $id=aid AND mid=id") or die(mysqli_error());
            while($row = $actor_movies->fetch_array()){
                echo '<tr><td>"'.$row['role'].'"</td>';
                echo '<td><a href="show_movie_info.php?identidier='.$row['mid'].'">'.$row['title'].'</a>'.'</td></tr>';
            }
            echo "</tbody></table></div><hr>";

        }
    ?>
        <p>
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search for Actor Information"></input>    
            <input type="submit" value="Search!" />
        </form>
        </p>

    </div>
</body>
</html>