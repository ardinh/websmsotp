<?php unset($argv[0]); $content = implode(" ", $argv); preg_match_all('!\d+!', $content, $matches);

$num = $matches[0][0];
$ones = array( 
    1 => "SATU", 
    2 => "DUA", 
    3 => "TIGA", 
    4 => "EMPAT", 
    5 => "LIMA", 
    6 => "ENAM", 
    7 => "TUJUH", 
    8 => "DELAPAN", 
    9 => "SEMBILAN", 
); //limit t quadrillion 
$num = number_format($num,2,".",","); 
$num_arr = explode(".",$num); 
$wholenum = $num_arr[0]; 
$decnum = $num_arr[1]; 
$whole_arr = array_reverse(explode(",",$wholenum)); 
krsort($whole_arr); 
$rettxt = ""; 
foreach($whole_arr as $key => $i){ 
    if($i < 20){ 
        $rettxt .= $ones[$i]." "; 
    }elseif($i < 100){ 
        $rettxt .= $tens[substr($i,0,1)];
        $rettxt .= " ".$ones[substr($i,1,1)]; 
    }else{ 
        $rettxt .= $ones[substr($i,0,1)]." "; 
        $rettxt .= " ".$ones[substr($i,1,1)]; 
        $rettxt .= " ".$ones[substr($i,2,1)]; 
    } 
    if($key > 0){ 
        // $rettxt .= " ".$hundreds[$key]." "; 
    } 
} 
if($decnum > 0){ 
    $rettxt .= " and "; 
    if($decnum < 20){ 
        $rettxt .= $ones[$decnum]; 
    }elseif($decnum < 100){ 
        $rettxt .= $tens[substr($decnum,0,1)]; 
        $rettxt .= " ".$ones[substr($decnum,1,1)]; 
    } 
} 

$rettxt = str_replace($matches[0][0], "-".$rettxt."-", $content);
echo $rettxt; ?>