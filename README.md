## 基於Smarty的模組化全端網頁架站工具
> 本專案還有很多功能正在開發，請勿直接下載使用

```md
# LastUpdate 20240515
-------------------------------------------------------------------------------
Language                     files          blank        comment           code
-------------------------------------------------------------------------------
PHP                            287           1661          14022          23169
HTML                            39             78            335           1923
CSS                             20            351            452           1864
JavaScript                      11             73            279            908
SQL                              3             56             47            718
Smarty                           1             24              0            151
Markdown                         3              3              0            110
JSON                             8              0              0             92
INI                              1              4              0             38
Bourne Shell                     2              3              8             26
SVG                              4              0              1             12
Text                             2              0              0              4
-------------------------------------------------------------------------------
SUM:                           381           2253          15144          29015
-------------------------------------------------------------------------------
```
### 完成項目
- 基於二進位之權限判定系統
- 身分組父繼承功能
- 帳戶註冊/登入/登出/修改功能
- 網站頁面判斷暨頁面元件處理功能
- 頁面調整與元件拖曳更動功能
- 元件自訂義參數功能
- 元件二維坐標軸判斷功能
- 基於指針標頭之讀檔系統
- CSS轉陣列功能
- 雪花演算法ID生成器
- 公告及公告分類系統
- 田所浩二彩蛋功能（迫真
- 商品上架/下架/變更處理系統
- 藍新金流API串接(接收與發送跟發貨)
- 訂單報表圖表生成工具
- SteamAPI第三方登入串接
- 簽到功能

### 系統環境  
- [RockyOS](https://rockylinux.org/zh_TW/news/rocky-linux-8-6-ga-release/)/8.6  
- [Apache](https://httpd.apache.org/)/2.4.53  
- [PHP](https://www.php.net/)/8.0.20  
- ~~[composer](https://getcomposer.org/)/2.5.4~~  
- ~~[codeigniter](https://codeigniter.tw/)/4.1.5~~  

**安裝Apache**  
```shell
sudo dnf install httpd -y
```
**安裝Composer**  
```shell
sudo dnf install composer -y

cd /var/www/html/  
composer install  
```
**目錄**
```diff
$ tree -L 2
.
├── 404                 // 404 Not found 頁面
├── api                 // API 
│   └── NewebPay        // 藍新金流
│   └── upload          // 檔案上傳
├── assets              // 素材資源
│   └── images          // 圖片素材
│   └── uploads         // 上傳檔案
├── cache               // 快取
├── classes             // 類別庫
├── configs             // 設定值
│   └── config.ini      // 一些基礎設定值(重要)
├── css                 // Css樣式
│   └── style_default   // 默認頁面設計
├── inc.global.php      // 全域配置
├── index.php           // 主頁
├── javascripts         // Js腳本
├── libs                // 函式庫
│   └── smarty          // Smarty函式庫
├── LICENSE             // MIT LICENSE
├── php                 // 頁面物件
├── plugins             // Smarty Plugin
├── sqlscripts          // 資料庫腳本
├── src                 // 不顯示在前後臺的背景執行php
├── templates           // 前端設計
│   └── style_default   // 頁面物件設計
└── templates_c         // 前端快取
```
- - -
_當前版本快照_  
![](assets/Template2.png)
- - -
> ### 使用資源  
> * _lib/[Smarty 4.3.0](https://www.smarty.net)_  
> * _javaScript/[Bootstrap v4.0.0](https://getbootstrap.com)_  
> * _javaScript/[jquery-3.2.1](https://jquery.com)_  
> ~~* _javaScript/[Popper 3](https://github.com/vusion/popper.js)_~~  
> * _css/[Bootstrap v4.0.0](https://getbootstrap.com)_  
