<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/*************************************************************
 *
 * 引入Classes Part.2
 * 
 *************************************************************/
    //foreach (glob(__DIR__ . '/class.*.php') as $filename) require_once $filename;
    require_once 'classes/function.utils.php';
    require_once 'classes/class.connectDatabase.php';
    require_once 'classes/class.snowflake.php';
    require_once 'classes/class.websiteManage.php';
    require_once 'classes/class.memberManage.php';
    require_once 'classes/class.pageManage.php';
    // require_once 'classes/class.componentPage.php';
    // require_once 'classes/class.pageRouter.php';
    require_once 'classes/class.permissions.php';
    // require_once 'classes/class.log.php';
    require_once 'classes/class.file.php';
    require_once 'classes/class.productManage.php';
    // require_once 'classes/class.cartManage.php';
    require_once 'classes/class.colorManage.php';
    require_once 'classes/class.msgBoard.php';
?>