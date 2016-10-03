<html>
<head><title>Calculator</title></head>
<body>

<h1>Calculator</h1>
(Ver 1.4.1 10/3/2016 by Ying Bin Wu)<br />
Type an expression in the following box (e.g., 10.5+20*3/25).
<p>
    <form method="GET">
        <input type="text" name="expr">
        <input type="subject" value="Calculate">
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