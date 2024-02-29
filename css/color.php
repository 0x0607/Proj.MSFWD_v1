<?php
require_once '../sys.global.php';
header('content-type:text/css; charset:UTF-8;');
// $colorPicker->adjustColor("background",0,"");
$result=$colorPicker->parseColor();
echo $result;
/*************************************************************
 * 
 * 新超級顏色選擇器 :D
 * 
 *************************************************************/
?>
.material-icons,
.material-icons:hover {
    background-color: transparent;
}
/* 由新超級顏色選擇器生成 By MaizuRoad */