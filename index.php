`<?php

require_once "php/CheckMethods.php";


$dataB = "2019-2-21";
$dataF = "2019-2-27";



if (checkDateBeginAndEnd($dataB, $dataF)){
    echo "B & E ok";
}else
    echo "B & E Nok";
    