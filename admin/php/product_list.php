<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
$INCLUDE_CSS[] = "/css/admin.productlist.css";
$orderby = 'name';
$limit = 10;
$lastPage = 0;
if (isset($_GET['orderby'])) {
    $validOrderBy = ['name', 'price', 'quantity'];
    $orderby = in_array(trim($_GET['orderby']), $validOrderBy) ? trim($_GET['orderby']) : 'name';
}
if (isset($_GET['limit'])) {
    $limit = intval(trim($_GET['limit'])) ? intval(trim($_GET['limit'])) : $limit;
}

$productCount = $productManage->getAllPostCount();
if ($productCount) {
    $lastPage = ceil($productCount / $limit);
    $PAGE_NUM = max(1, min($PAGE_NUM, $lastPage));
}
$products = $productManage->getProductInformation(null, ($PAGE_NUM - 1) * $limit, $limit, "{$orderby} {$PAGE_ORDER}");

foreach ($products as $productKey => $value) {
    $HASH_RAWDATA["pid"] = $products[$productKey]['id'];
    // $productKey['hashdata'] = json_encode(["action" => "PRODUCT_CHANGE_STATUS", "hash" => encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']), "iv" => $DATA['iv']]);
    $products[$productKey]['hashdata'] = encryptData($HASH_RAWDATA, WEBSITE_HASH_KEY, $DATA['iv']);
}
// echo json_encode($products);
if ($MEMBER_PERMISSIONS & 1 << 6 || $MEMBER_PERMISSIONS & 1) $smarty->assign("deleteProduct", true);
if ($lastPage > 1) $smarty->assign("lastPage", $lastPage);
$smarty->assign("products", $products);
$smarty->assign("orderby", $orderby);
$smarty->assign("limit", $limit);
