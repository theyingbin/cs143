<html>
<head>
<meta charset="utf-8">
<title>CS143 Project 1B</title>
</head>
<body>
    <h2>Web Query Interface</h2>
    Type an SQL query in the following box: 
    Example: <tt>SELECT * FROM Actor WHERE id=10;</tt>
    <p>
        <form action="query.php" method="GET">
            <textarea name="query" cols="60" rows="8"></textarea><br />
            <input type="submit" value="Submit" />
        </form>
    </p>
<?php
$db = new mysqli('localhost', 'cs143', '', 'CS143');
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
$user_query = $_GET["query"];
if(strlen($user_query) !== 0){
    $result = $db->query($user_query);
    if(!$result){
        $err_msg = $db->error;
        print("Query failed: $err_msg <br />");
        exit(1);
    }
    $fields = $result->fetch_fields();
    
    echo '<table border="1" cellspacing="1" cellpadding="2"><tbody>';
    
    $titles = 0;

    while($row = $result->fetch_assoc()) {
        $attr = array_keys($row);
        if($titles == 0){
            echo '<tr align="center">';
            for($k = 0; $k < count($attr); $k++){
                echo '<td><b>'.$attr[$k].'</b></td>';
            }
            echo '</tr>';
            $titles = 1;
        }
        echo '<tr align="center">';
        for($i = 0; $i < count($attr); $i++){
            if(is_null($row[$attr[$i]])){
                echo "<td>N/A</td>";
            }
            else{
                echo "<td>".$row[$attr[$i]]."</td>";
            }
        }
        echo '</tr>';
    }

    echo '</tbody></table>';

    if($titles == 0){
        echo '<p>Nothing matched this query</p>';
    }

    $result->free();
}
$db->close();
?>
</body>
</html>
 
 