<?php
// use function PHPSTORM_META\type;
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 針對商品設置專用
 * 
 * Note: 
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$productResult = false;
$respondJson = ["status" => 200, "data" => "0|NO"];
switch ($ACTION) {
    case "PRODUCT_ADD":
        if (!isset($DATA['pid'])) $DATA['pid'] = $sf->getId();

        $updateProductData = [];
        if (!empty($DATA['name'])) $updateProductData['name'] = $DATA['name'];
        if (!empty($DATA['description'])) $updateProductData['description'] = $DATA['description'];
        // if (!empty($DATA['types'])) $updateProductData['types'] = $DATA['types'];
        // if (!empty($DATA['specification'])) $updateProductData['specification'] = $DATA['specification'];
        // if (!empty($DATA['color'])) $updateProductData['color'] = $DATA['color'];
        if (!empty($DATA['price'])) $updateProductData['price'] = intval($DATA['price']);
        if (!empty($DATA['quantity'])) $updateProductData['quantity'] = intval($DATA['quantity']);
        if (!empty($DATA['status'])) $updateProductData['status'] = intval($DATA['status']);
        if (isset($DATA['productImages'])) $updateProductData['images'] = $DATA['productImages'];

        if (!empty($updateProductData)) $productResult = $productManage->addProduct($DATA['pid'], $updateProductData);
        if ($productResult) $LOG_REASON["api"] = "Product create success.";
        break;
    case "PRODUCT_CHANGE_INFORMATION":
        if (isset($DATA['pid'])) {
            $updateProductData = [];
            if (!empty($DATA['name'])) $updateProductData['name'] = $DATA['name'];
            if (!empty($DATA['description'])) $updateProductData['description'] = $DATA['description'];
            // if (!empty($DATA['types'])) $updateProductData['types'] = $DATA['types'];
            // if (!empty($DATA['specification'])) $updateProductData['specification'] = $DATA['specification'];
            // if (!empty($DATA['color'])) $updateProductData['color'] = $DATA['color'];
            if (!empty($DATA['price'])) $updateProductData['price'] = intval($DATA['price']);
            if (!empty($DATA['quantity'])) $updateProductData['quantity'] = intval($DATA['quantity']);
            // if (!empty($DATA['status'])) $updateProductData['status'] = intval($DATA['status']);
            if (isset($DATA['productImages'])) $updateProductData['images'] = $DATA['productImages'];
            if (isset($DATA['delete_uploadfile'])) $updateProductData['deletedimage'] = $DATA['delete_uploadfile'];

            if (!empty($updateProductData)) $productResult = $productManage->updateProductInformation($DATA['pid'], $updateProductData);
            if ($productResult) $LOG_REASON["api"] = "Product update success.";
        }
        break;
    case "PRODUCT_CHANGE_STATUS":
        if (isset($DATA['pid'])) {
            $productResult = $productManage->changeProductStatus($DATA['pid']);
            if ($productResult) $LOG_REASON["api"] = "Product update success.";
        }
        break;
    case "PRODUCT_DELETE":
        if (isset($DATA['pid'])) {
            $productResult = $productManage->deleteProduct($DATA['pid']);
            if ($productResult) $LOG_REASON["api"] = "Product delete success.";
        }
        break;
}
/****************************************************************************************/
if ($productResult) {
    $respondJson["data"] = "1|OK";
} else {
    $respondJson['data'] = "Invalid request parameters.";
    $LOG_REASON["api"] = "Invalid request parameters.";
}
