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
    <p><small>Note: tables and fields are case sensitive. All tables in Project 1B are availale.</small></p>
 
<?php
$db = new mysqli('localhost', 'cs143', '', 'TEST');
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
$user_query = $_GET["query"];
if(strlen($user_query) !== 0){
    $result = $db->query($user_query);
    echo $user_query."<br>";
    $fields = $result->fetch_fields();
    //print_r($fields);
    echo "<br>";
    for($i = 0; $i < count($fields); $i++){
        print("HELLO WORLD\n");
        print_r($fields[$i]->name);
        if($i !== count($fields) - 1){
            print(", ");
        }
    }
    echo "<br>";
    while($row = $result->fetch_assoc()) {
        $attr = array_keys($row);
        for($i = 0; $i < count($attr); $i++){
            if(is_null($row[$attr[$i]])){
                print("NULL");
            }
            else{
                print($row[$attr[$i]]);
            }
            if($i !== count($attr) - 1){
                print(", ");
            }
        }
        echo "<br>";
    }
    $result->free();
}
$db->close();
?>
 
</body>
</html>
 
 