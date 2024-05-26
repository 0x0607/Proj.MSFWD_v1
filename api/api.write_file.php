<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) exit;
/****************************************************************************************
 * 
 * 寫檔案系統
 * 
 * Note: 
 * int WEBSITE_ID : 網站編號
 * int $_SESSION['mid'] : 會員編號
 * array $DATA : 數據
 * 
 ****************************************************************************************/
$respondJson = ["status" => 200, "data" => "0|NO"];

switch ($ACTION) {
    case "WEBSITE_CHANGE_THEME":
        $customColor = [
            // 背景顏色
            "sunbaby-bg" => "#212121",
            // 導覽列顏色
            // "nav-ft" => "#FFFFFF",
            "nav-bg" => "#373737",
            "nav-btn-ft" => "#FFFFFF",
            "nav-btn-bg" => "transparent",
            "nav-btn-hover-ft" => "#2879F6",
            "nav-btn-hover-bg" => "transparent",
            "nav-icon-ft" => "#FFFFFF",
            "nav-icon-bg" => "transparent",
            "nav-dropdown-bg" => "#",
            // 超連結顏色
            "link-ft" => "#FFFFFF",
            "link-hover-ft" => "#FFFFFF",
            // 碼顏色
            "code-ft" => "#2879F6",
            "code-hover-ft" => "#2879F6",
            // 系統資訊顏色(文字、底色通用)
            "warn" => "#FF6363",
            "prom" => "#FEC142",
            "succ" => "#33CD4B",
            "warn-hover" => "#CC3131",
            "prom-hover" => "#F49C2A",
            "succ-hover" => "#2CA043",
            // 基本顏色文字
            // "primary" => "#FFFFFF",
            // "accent" => "#2879F6",
            // "secondary" => "#000000",
            "primary-ft" => "#FFFFFF",
            "accent-ft" => "#2879F6",
            "secondary-ft" => "#",
            // 基本背景顏色
            "primary-bg" => "#2879F6",
            "accent-bg" => "#",
            "secondary-bg" => "#",
            // switch-slider顏色
            "switch-slider" => "#FFFAF4",
            "switch-slider-checked-bg" => "#4A7056",
            "switch-slider-checked-hover-bg" => "#96A48B",
            "switch-slider-bg" => "#965454",
            "switch-slider-hover-bg" => "#A27E7E",
            // button文字顏色
            "primary-btn-ft" => "#FFFFFF",
            "primary-btn-hover-ft" => "#2879F6",
            "accent-btn-ft" => "#FFFFFF",
            "accent-btn-hover-ft" => "#1B4F9E",
            "secondary-btn-ft" => "#",
            "secondary-btn-hover-ft" => "#",
            // button背景顏色
            "primary-btn-bg" => "#2879F6",
            "primary-btn-hover-bg" => "#FFFFFF",
            "accent-btn-bg" => "#1B4F9E",
            "accent-btn-hover-bg" => "#FFFFFF",
            "secondary-btn-bg" => "#",
            "secondary-btn-hover-bg" => "#",
            // 登出按鈕文字顏色
            "logout-btn-ft" => "#FFFFFF",
            "logout-btn-hover-ft" => "#965454",
            // 登出按鈕背景顏色
            "logout-btn-bg" => "#965454",
            "logout-btn-hover-bg" => "#FFFFFF",
            // 用戶設置按鈕文字顏色
            // "user-setting-btn-ft" => "#",
            // "user-setting-btn-hover-ft" => "#",
            // 用戶設置按鈕背景顏色
            // "user-setting-btn-bg" => "#",
            // "user-setting-btn-hover-bg" => "#",
            // input文字顏色
            // "input-ft" => "#000000",
            // "input-focus-ft" => "#2879F6",
            // input背景顏色
            // "input-bg" => "#FFFFFF",
            // "input-focus-bg" => "#FFFAF4",
            // scrollbar顏色
            "scrollbar-track" => "#FFFAF4",
            "scrollbar-thumb" => "#888888",
            "scrollbar-thumb-hover" => "#555555",
        ];
        // 透明度 10% - 90%
        $transpList = ["1A", "33", "4D", "66", "80", "99", "B3", "CC", "E6"];
        if (isset($DATA["sunbaby-bg"])) $customColor["sunbaby-bg"] = $DATA["sunbaby-bg"];
            $bgisDark = $colorPicker->isDark($customColor["sunbaby-bg"]);
            foreach ($customColor as $type => $value) {
                if (isset($DATA[$type])) {
                    $customColor[$type] = $DATA[$type];
                    unset($DATA[$type]);
                }
            }
            if ($bgisDark) $transpBg = "#0F0F0F";
            else $transpBg = "#8E8E8E";

            // 陰影
            $customColor["shadow"] = "{$transpBg}33";
            // 完全透明，要它幹嘛，我也不知道反正先留著
            $customColor["alpha"] = "transparent";

            if ($customColor["nav-dropdown-bg"] == "#") $customColor["nav-dropdown-bg"] = "{$transpBg}99";
            
            // 由10%至90%添加元素
            foreach ($transpList as $key => $transp) {
                $percentage = ($key + 1) * 10;
                $customColor["alpha-{$percentage}p"] = $transpBg . $transp;
                $customColor["primary-bg-alpha{$percentage}p"] = $customColor["primary-bg"] . $transp;
                $customColor["accent-bg-alpha{$percentage}p"] = $customColor["accent-bg"] . $transp;
                $customColor["secondary-bg-alpha{$percentage}p"] = $customColor["accent-bg"] . $transp;
                $customColor["succ-alpha{$percentage}p"] = $customColor['succ']. $transp;
                $customColor["warn-alpha{$percentage}p"] = $customColor['warn']. $transp;
                $customColor["prom-alpha{$percentage}p"] = $customColor['prom']. $transp;
            }

            // else if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) $customColor[$type] = $customColor[$value];
            $file->setPath(CONFIG_PATH['web'] . "/" . WEBSITE['id']);
            if ($file->writeFile($colorPicker->parseColor($customColor),'color.css')) $DATA['theme_colors'] = $customColor;
        
        break;
}

/****************************************************************************************/
    // if ($imageResult) $LOG_REASON["api"] = "Success.";
    // else $LOG_REASON["api"] = "Invalid file updload.";
    // if (!$imageResult) $LOG_REASON["api"] = "Invalid file updload.";