<?php unset($argv[0]); $content = implode(" ", $argv); preg_match_all('!\d+!', $content, $matches);

$a = "( ".$matches[0][0]." )";
$a = str_replace($matches[0][0], $a, $content);
$b = date("ymdhim");
$a = $a." ".$b;
echo $a; ?>