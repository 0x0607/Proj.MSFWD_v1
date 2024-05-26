<?php

/****************************************************************************************
 * 
 * 顏色處理器
 * @param array $themeColor 顏色資料
 * 
 ****************************************************************************************/
class colorManage
{
    /************************************************
     * ### 將RGB陣列轉換成HEX ###
     * @param array $color 顏色陣列  
     ************************************************/
    private static function rgbaToHex($color): string
    {
        if (count($color) > 4) return "";
        // $transparent = isset($color[3]) ? $transparent = $color[3] : 1;
        if (!isset($color[3])) $color[3] = 1;
        // if ($transparent > 1 || $transparent < 0) return "";

        $hex = "#";
        // for ($i = 0; $i < 3; $i++) {
        for ($i = 0; $i < 4; $i++) {
            if ($color[$i] > 255) $color[$i] = 255;
            if ($color[$i] < 0) $color[$i] = 0;
            // $hex .= str_pad(dechex(round($color[$i] * $transparent)), 2, "0", STR_PAD_LEFT);
            $hex .= str_pad(dechex(round($color[$i])), 2, "0", STR_PAD_LEFT);
        }
        return $hex;
    }
    /************************************************
     * ### 將HEX轉換成RGB陣列 ###
     * @param string $color 顏色HEX值
     * @param string $mothed 輸出格式 `array`,`string`
     ************************************************/
    private static function hexToRgba($color, $mothed = "string")
    {
        $color = str_replace("#", "", $color);
        if (!preg_match('/^[0-9A-Fa-f]{6,8}$/', $color)) return [];
        // 透明度處理
        if (strlen($color) === 6) $color .= 'FF';
        elseif (strlen($color) !== 8) return [];
        $colorArray = [];
        for ($i = 0; $i < 4; $i++) $colorArray[] = hexdec(substr($color, $i * 2, 2));
        if (strtolower($mothed) === 'string') return "rgba(" . implode(",", $colorArray) . ")";
        else return $colorArray;
    }
    /************************************************
     * ### 新增透明度 ###
     * @param string $color 顏色HEX值
     * @param float $transparent 透明度 百分比 (0-1)
     ************************************************/
    public static function addTransparent($color, $transparent = 0): string
    {
        $result = '';
        $alpha = round($transparent * 255);
        $alphaHex = str_pad(dechex($alpha), 2, '0', STR_PAD_LEFT);
        $result = $color . $alphaHex;
        return $result;
    }
    /************************************************
     * ### 調整亮度 ###
     * @param string $color 顏色HEX值
     * @param float $percentage 亮度 百分比 (0-1)
     ************************************************/
    public static function changeBrightness($color, $percentage): string
    {
        $rgba = self::hexToRgba($color, 'array');

        if (empty($rgba)) return "#FFFFFF00";

        $red = $rgba[0];
        $green = $rgba[1];
        $blue = $rgba[2];
        $alpha = $rgba[3];

        $red = min(255, max(0, $red * (1 + $percentage)));
        $green = min(255, max(0, $green * (1 + $percentage)));
        $blue = min(255, max(0, $blue * (1 + $percentage)));

        $newHexColor = self::rgbaToHex([$red, $green, $blue, $alpha]);

        return empty($newHexColor) ? "#00000000" : $newHexColor;
    }
    /************************************************
     * ### 隨機產生顏色 ###
     * @param bool $useDefultColorList 是否使用系統配置的顏色表
     * #2475b2 藍色
     * #b22424 紅色
     * #e45c10 橙色
     * #edc214 黃色
     * #1faa00 綠色
     * #7b23a7 紫色
     ************************************************/
    public static function randomColor($useDefultColorList = true): string
    {
        if ($useDefultColorList) {
            $colorList = ['#3F769E', '#CC8200', '#9A71B5', '#EA6C6C'];
            // $result = $colorList[array_rand($colorList)];
            shuffle($colorList);
            $result = $colorList[0];
        } else {
            // 目前不提供除了隨機顏色表以外的顏色，因為很醜
            $result = "#000000";
        }
        return $result;
    }
    /************************************************
     * ### 顏色轉換器 ###
     * @param string|int $color 顏色
     * @param string $mothed 輸出格式 `array`,`string`
     ************************************************/
    public static function colorConvert($color, $mothed = "string")
    {
        if (empty($color)) return "transparent";
        if (is_array($color)) $result = self::rgbaToHex($color);
        else if (is_string($color)) $result = self::hexToRgba($color, $mothed);
        else $result = "";
        return $result;
    }
    /************************************************
     * ### 顏色深淺判斷器 ###
     * @param string $color 顏色HEX值  
     * 人眼對於顏色敏感度請參考  
     * https://en.wikipedia.org/wiki/RGB_color_model
     ************************************************/
    public static function isDark($color): bool
    {
        $rgb = self::hexToRgba($color, "array");
        if (empty($rgb) || $color == "transparent") return false;

        $red = $rgb[0];
        $green = $rgb[1];
        $blue = $rgb[2];
        $brightness = ($red * 299 + $green * 587 + $blue * 114) / 1000;

        return $brightness < 128;
    }
    /************************************************
     * ### 格式化CSS ###
     * @param array $colors 顏色陣列
     ************************************************/
    public static function parseColor($colors = []): string
    {
        $result = ":root\x20{\x20\n";
        foreach ($colors as $colorId => $color) {
            if (preg_match('/^[A-Za-z0-9\-]+$/', $colorId)) {
                if (preg_match('/^#[0-9A-Fa-f]{6,8}$/', $color) || $color == "transparent") $result .= "\x20\x20\x20\x20--{$colorId}:\x20{$color};\n";
                else $result .= "\x20\x20\x20\x20--{$colorId}: ;\n";
            }
        }
        $lastUpdateTime = date('Y-m-d H:i:s');
        $result .= "}\n\n/*\x20LastUpdate:\x20{$lastUpdateTime}\x20*/";
        return $result;
    }
    /************************************************
     * ### 將CSS檔轉換為陣列 ###
     * @param array $colors CSS檔
     * @param bool $ignoreEmptySettings 是否忽略空值
     ************************************************/
    public static function parseColorToArray($colors = "", $ignoreEmptySettings = true)
    {
        $result = [];
        // 匹配--顏色名稱: 顏色;的字符
        if($ignoreEmptySettings) preg_match_all('/--[a-zA-Z0-9\-]+:\s*(\#[0-9A-Fa-f]{3,8}|transparent);/', $colors, $matches);
        else preg_match_all('/--[a-zA-Z0-9\-]+:\s*(.*?);/', $colors, $matches);
        // $filteredKeys = preg_grep('/alpha[0-9]{2}p/', $matches[0], PREG_GREP_INVERT);
        $filteredKeys = preg_grep('/alpha/', $matches[0], PREG_GREP_INVERT);
        foreach ($filteredKeys as $index => $declaration) {
            $parts = explode(':', $declaration);
            $name = trim(str_replace('--', '', $parts[0]));
            $value = trim(str_replace(';', '', $parts[1]));
            $result[$name] = $value;
        }
        return $result;
    }
}
