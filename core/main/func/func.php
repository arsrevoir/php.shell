<?php 

function length($arr) {
    return count($arr) - 1;
}

function arr_to_string($arr) {
    $string = '';
    $i = 0;

    foreach($arr as $elem) {
        if($i == length($arr)) {
            $string .= $elem;
        } else {
            $string .= $elem . ', ';
        }

        $i++;
    }

    return $string;
}

function name_generate($min, $max) {
    $sym = ['A','a','B','b','C','c','D','d','E','e','F','f','G','g','H','h','I','i','J','j','K','k','L','l','M','m','N','n','O','o','P','p','Q','q','R','r','S','s','T','t','U','u','V','v','W','w','X','x','Y','y','Z','z','1','2','3','4','5','6','7','8','9','0'];
    $finarr = [];
    $sym_num = floor(rand($min , $max));
    for($i = 0; $i < $sym_num;$i++) {
        $random = floor(rand(0,sizeof($sym) - 1));
        $finarr[$i] = $sym[$random];
    }   
    $str = implode('' , $finarr);
    return $str;
}

function regexp_validate($pattern, $string) { 
    if(preg_match($pattern, $string)) {
        return true;
    } else {
        return false;
    }
}

function unset_POST($post) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        unset($post);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>