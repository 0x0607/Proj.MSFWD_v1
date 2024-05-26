<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;

$webData = [];
if ($_SESSION['member']['mid'] == 589605057335390208){
    $webData = $websiteManage->getAllWebsiteInformation();
}
$smarty->assign("webdata",$webData);