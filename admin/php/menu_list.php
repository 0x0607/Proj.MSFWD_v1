<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$file->setPath(WEBSITE_FILE_DIR);
$tempFileData = $file->readFile('nav_config.json');
if (!empty($tempFileData)) {
    $tempDecodeData = json_decode($tempFileData, true);
    if ($tempDecodeData !== null && json_last_error() === JSON_ERROR_NONE) {
        $smarty->assign("navbar_information", $tempDecodeData);
    }
} else $WEB_NAV = '';
