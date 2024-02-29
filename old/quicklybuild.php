<?php
require_once '../sys.global.php';
include_once 'new_page_data.php';
// 589605057335390208
echo "<title>測試用-快速建站" . ($sf->getId()) . "</title>";
if (!isset($_GET['test'])) exit;
/****************************************************************************************/
$wid = 1344589822513115136;
// if (isset($_GET['domain'])) {
// 	// if ($_SESSION['mid'] != 589605057335390208) return false;
//     header("Content-type: application/json");
//     $result = true;
//     $websiteInformation = [
//         "website" => [
//             "domain" => "rui.snkms.org",
//             "name" => "rui_site",
//             "displayname" => "屁眼派對"
//         ],
//         "newebpay" => [
//             "store_return_url" => "https://rui.snkms.org/projects/steam",
//             "store_client_back_url" => "https://rui.snkms.org/projects/steam",
//             "store_notify_url" => "https://rui.snkms.org/projects/steam/api/done.php",
//         ]
//     ];
//     $initRoles = [
//         [
//             "id" => $wid,
//             "wid" => $wid,
//             "name" => "everyone",
//             "displayname" => "所有人",
//             "parent_id" => $wid,
//             "permissions" => 0,
//             "status" => 2
//         ],
//         [
//             "id" => $sf->getId(),
//             "wid" => $wid,
//             "name" => "root",
//             "displayname" => "超級管理員",
//             "parent_id" => $wid,
//             "permissions" => 0x1FFC,
//             "status" => 2
//         ],
//         [
//             "id" => $sf->getId(),
//             "wid" => $wid,
//             "name" => "admin",
//             "displayname" => "管理員",
//             "parent_id" => $wid,
//             "permissions" => 0x2000,
//             "status" => 2
//         ]
//     ];
//     $result &= $websiteManage->initWebsite($wid, $websiteInformation["website"]["domain"]);
//     $result &= $websiteManage->updateWebsiteInformation($wid, $websiteInformation);
//     foreach ($initRoles as $role) {
//         $result &= $permissions->addRoles($wid, $role['id']);
//         $result &= $permissions->updateRoleInformation($wid, $role['id'], $role);
//     }
//     $result &= $permissions->changeMemberRoles($wid,$initRoles[1]['id'],589605057335390208);
//     echo json_encode([$result]);
// } else {
// 	echo "請輸入網站domainName變數\$_GET['web']";
// }

$sql = "SELECT `domain` FROM `s_website` WHERE `id` = ?";
$result = $db->prepare($sql, [$wid]);
$domainName = empty($result) ? '未知網站' : $result[0]['domain'];

$createPagesSql = "INSERT INTO `w_pages` (`id`, `wid`, `name`, `displayname`, `description`, `status`) VALUES ";
$createPageComponent = "INSERT INTO `w_page_component` (`id`, `pid`, `cid`, `displayname`, `position`, `params`, `permissions`, `status`) VALUES ";
foreach ($NEW_PAGE_INFORMATION as $pKey => $info) {
	$pid =  $sf->getId();
	$NEW_PAGE_INFORMATION[$pKey]['pid'] = $pid;
	foreach ($info['pageComponents'] as $cKey => $pageComponents) {
		if (!empty($pageComponents)) {
			$cid = $sf->getId();
			$createPageComponentSql[] =	$createPageComponent . "({$cid}, {$pid}, {$pageComponents['cid']}, '{$domainName}的{$pageComponents['displayname']}',  {$pageComponents['position']}, '{$pageComponents['params']}', {$pageComponents['permission']}, 1); ";
		}
	}
	$createPagesSql .=	"({$pid}, {$wid}, '{$info['name']}', '{$info['displayname']}',  '{$info['description']}', {$info['status']})" . ($pKey < count($NEW_PAGE_INFORMATION) - 1 ? "," : ";") . " ";
}
echo '<span style="display:none;">' . $createPagesSql . '</span>';
$db->single($createPagesSql);
echo '<span style="display:none;">';
foreach ($createPageComponentSql as $sql) {
	echo $sql . '<br/>';
	$db->single($sql);
}
echo '</span>';
