<?php
$LOG_STATUS_CODE["api.updateProduct"] = "1|OK";
$LOG_REASON["api.updateProduct"] = "";
/****************************************************************************************
 * 
 * 變更商品狀態
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
if (isset($_SESSION['mid']) && isset($_POST['action']) && false) {
    header("Content-type: application/json");
    $command = "";
    // if (!$permissions->checkMemberPermissions(WEBSITE_ID, $_SESSION['mid'], 32)) {
    //     echo json_encode([]);
    //     exit;
    // }
    switch (($_POST['action'])) {
        case "change_status":
            if (!isset($_POST['product_id'])) exit;
            $result = $productManage->updateProductInformation(WEBSITE_ID, $_POST['product_id'], ["status" => $_POST['product_status']]);
            $command = "CHANGE_PRODUCT";
            break;
        case "add_product":
            if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_quantity'])) {
                if (preg_match('/^[.0-9]+$/', $_POST['product_price']) || preg_match('/^[0-9]+$/', $_POST['product_quantity'])) {
                    $arr=[
                        "name" => ($_POST['product_name'] != '') ? htmlspecialchars($_POST['product_name']) : "未命名商品",
                        "price" => floatval($_POST['product_price']),
                        "quantity" => intval($_POST['product_quantity'])
                    ];
                    $result = $productManage->addProduct(WEBSITE_ID, $sf->getId(), $arr);
                    $command = "ADD_PRODUCT";
                }
            }
            break;
        case "update_product":
            if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_quantity'])) {
                if (preg_match('/^[.0-9]+$/', $_POST['product_price']) || preg_match('/^[0-9]+$/', $_POST['product_quantity'])) {
                    $arr=[
                        "name" => ($_POST['product_name'] != '') ? htmlspecialchars($_POST['product_name']) : "未命名商品",
                        "price" => floatval($_POST['product_price']),
                        "quantity" => intval($_POST['product_quantity'])
                    ];
                    $result = $productManage->addProduct(WEBSITE_ID, $_POST['product_id'], $arr);
                    $command = "CHANGE_PRODUCT";
                }
            }
            break;
    }
    echo json_encode([]);
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, $command, 1);
} else {
    include_once "../404/index.php";
}
// $_FILES['IMAGE_UPLOAD']['error'] === UPLOAD_ERR_OK
