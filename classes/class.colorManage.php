<?php

/****************************************************************************************
 * 
 * 顏色處理器
 * @param array $themeColor 顏色資料
 * 
 ****************************************************************************************/
class colorManage
{
    private $themeColor;
    // private $colorNamespace=[
    //     "primary",
    //     "accent",
    //     "secondary",
    //     "alternate"
    // ];

    public function __construct($themeColor, $colorNamespace = [])
    {
        $this->themeColor = $themeColor;
        // if(!empty($namespace))$this->colorNamespace = $colorNamespace;
    }
    /************************************************
     * ### 將RGB陣列轉換成HEX ###
     * @param array $color 顏色陣列  
     ************************************************/
    private function rgbaToHex($color): string
    {
        if (count($color) > 4) return "";
        $transparent = isset($color[3]) ? $transparent = $color[3] : 1;
        if ($transparent > 1 || $transparent < 0) return "";

        $hex = "#";
        for ($i = 0; $i < 3; $i++) {
            if ($color[$i] > 255) $color[$i] = 255;
            if ($color[$i] < 0) $color[$i] = 0;
            $hex .= str_pad(dechex(round($color[$i] * $transparent)), 2, "0", STR_PAD_LEFT);
        }
        return $hex;
    }
    /************************************************
     * ### 將HEX轉換成RGB陣列 ###
     * @param string $color 顏色HEX值
     * @param string $mothed 輸出格式 `array`,`string`
     ************************************************/
    private function hexToRgba($color, $mothed = "string")
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
     * ### 隨機產生顏色 ###
     * @param bool $useDefultColorList 是否使用系統配置的顏色表
     * #2475b2 藍色
     * #b22424 紅色
     * #e45c10 橙色
     * #edc214 黃色
     * #1faa00 綠色
     * #7b23a7 紫色
     ************************************************/
    public function randomColor($useDefultColorList = true): string
    {
        if ($useDefultColorList) {
            $colorList = ['#3F769E', '#CC8200', '#9A71B5', '#EA6C6C'];
            // $result = $colorList[array_rand($colorList)];
            shuffle($colorList);
            $result = $colorList[0];
        } else {
            // 目前不提供除了隨機顏色表以外的顏色
            $result = "#000000";
        }
        return $result;
    }
    /************************************************
     * ### 顏色轉換器 ###
     * @param string|int $color 顏色
     ************************************************/
    public function colorConvert($color)
    {
        if (empty($color)) return "transparent";
        if (is_array($color)) $result = $this->rgbaToHex($color);
        else if (is_string($color)) $result = $this->hexToRgba($color);
        else $result = "";
        return $result;
    }
    /************************************************
     * ### HTML回傳Style ###
     * @param array $mothed 格式: `background`,`font`
     * @param array $color 顏色(需與mothed對應)
     ************************************************/
    public function parseColor(): string
    {
        $theme = $this->themeColor;
        $result = "";
        foreach ($theme as $styleType => $styles) {
            foreach ($styles as $label => $color) {
                if (strtolower($color) == "auto") {
                    if (preg_match('/\S*:hover$/', strtolower($label))) {
                        $color = isset($theme[$styleType][".accentColor"]) ? $theme[$styleType][".accentColor"] : "transparent";
                    } else {
                        $color = isset($theme[$styleType][".primaryColor"]) ? $theme[$styleType][".primaryColor"] : "transparent";
                    }
                }
                $result .= "$label {{$styleType}: {$color};}\n\n";
            }
        }

        return $result;
    }
    // public function adjustColor($mothed=[], $colors=[]) : string
    // {
    //     $result = "";
    //     foreach($mothed as $key => $label){
    //         switch($label){
    //             case "background":
    //                 $result .= "background-color: {$colors[$key]};";
    //                 break;
    //             case "font":
    //             default:
    //                 $result .= "color: {$colors[$key]};";
    //                 break;
    //         }
    //     }
    //     return $result;
    // }
}
