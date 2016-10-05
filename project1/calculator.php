<html>
<head><title>Calculator</title></head>
<body>

<h1>Calculator</h1>
Type an expression in the following box (e.g., 10.5+20*3/25).
<p>
    <form method="GET">
        <input type="text" name="expr">
        <input type="submit" value="Calculate">
    </form>
</p>

<ul>
    <li>Only numbers and +,-,* and / operators are allowed in the expression.
    <li>The evaluation follows the standard operator precedence.
    <li>The calculator does not support parenthesis.
    <li>The calculator handles invalid input "gracefully". It does not output PHP error messages.
</ul>

Here are some(but not limit to) reasonable test cases:
<ol>
    <li> A basic arithmetic operation:  3+4*5=23 </li>
    <li> An expression with floating point or negative sign : -3.2+2*4-1/3 = 4.46666666667, 3*-2.1*2 = -12.6 </li>
    <li> Some typos inside operation (e.g. alphabetic letter): Invalid input expression 2d4+1 </li>
</ol>

<?php 
$equation = $_GET["expr"];
$valid = preg_match("/\s*([-]?[0-9]+((\.)?[0-9]+)?)\s*(([+\-\/\*]\s*)[-]?[0-9]+((\.)?[0-9]+)?\s*)*/", $equation, $matches);
$only_spaces = strlen(trim($equation)) == 0;
$divide_by_zero = preg_match('/\/\s*[0]/', $equation);
$preceding_zero = preg_match('/([^0-9][0][0-9]+|^([0][0-9]+))/', $equation);
$multiple_matches = $equation !== $matches[0];

if ($only_spaces) {
    // Do Nothing
} elseif($multiple_matches){
    echo "<h2>Result</h2>";
    echo "Invalid Expression!";
} elseif($preceding_zero){
    echo "<h2>Result</h2>";
    echo "Invalid Expression!";
} elseif ($divide_by_zero) {
    echo "<h2>Result</h2>";
    echo "Division by zero error!";
} elseif ($valid) {
    try{
        $tmp = str_replace('--', '- -', $equation);
        eval("\$result = $tmp;");
        echo "<h2>Result</h2>";
        echo $equation . " = " . $result;
    }
    catch(Exception $e){
        echo "<h2>Result</h2>";
        echo "Invalid Expression!";
    }
    
} else {
    echo "<h2>Result</h2>";
    echo "Invalid Expression!";
}
?>

</body>
</html>