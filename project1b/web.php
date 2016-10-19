<html>
<head><title>CS143 Project 1B</title></head>
<body>
    <h2>Web Query Interface</h2>
    Type an SQL query in the following box: 
    Example: <tt>SELECT * FROM Actor WHERE id=10;</tt>
    <p>
        <form method="GET">
            <textarea name="query" cols="60" rows="8"></textarea><br />
            <input type="submit" value="Submit" />
        </form>
    </p>
<?php
$db = new mysqli('localhost', 'cs143', '', 'TEST');
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
    
    echo '<table border="1" cellspacing="1" cellpadding="2"><tbody><tr align="center">';

    for($i = 0; $i < count($fields); $i++){
        echo '<td><b>'.$fields[$i]->name.'</b></td>';
    }
    echo '</tr>';
    
    while($row = $result->fetch_assoc()) {
        $attr = array_keys($row);
        echo '<tr align="center">';
        for($i = 0; $i < count($attr); $i++){
            if(is_null($row[$attr[$i]])){
                echo "<td>NULL</td>";
            }
            else{
                echo "<td>".$row[$attr[$i]]."</td>";
            }
        }
        echo '</tr>';
    }
    $result->free();
    echo '</tbody></table>';
}
$db->close();
?>
</body>
</html>
 
 