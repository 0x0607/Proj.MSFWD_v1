<?php

/*************************************************************
 * 
 * 檔案處理系統
 * @param obj $db 資料庫
 * @param string $folderPath 檔案上傳之目錄  
 * 注意目錄不存在將會自動建立
 *************************************************************/
class files
{
    private static $fileReadLineLimitBit = 8192;
    private static $pointer = 0;
    // private static $lastFile = "";
    private $folderPath = "temp";
    private $rootPath;
    private $permission = 0775;
    private $maxSize = 1 << 27;
    private $compressionLevel = 5;

    public function __construct($rootPath = "/f/")
    {
        $rootPath = rtrim($rootPath, '/') . '/';
        $this->rootPath = $_SERVER['DOCUMENT_ROOT'] . $rootPath;
        // $this->rootPath = $rootPath;
    }
    /************************************************
     * ### 設置目錄位址 ###
     * @param string $folderPath 檔案上傳之目錄  
     * 注意目錄不存在將會自動建立
     ************************************************/
    public function setPath($folderPath): bool
    {
        if (empty($folderPath)) $folderPath = "temp";
        $folderPath = rtrim($folderPath, '/') . '/';
        $this->folderPath = "{$this->rootPath}{$folderPath}";
        if (!is_dir($this->folderPath)) {
            if (!@mkdir($this->folderPath, $this->permission, true)) {
                return false;
            }
        }
        // 表頭指針復位
        self::$pointer = 0;
        return true;
    }
    /************************************************
     * ### 清除開頭文字 ###
     * @param string $string 需清除的字串
     * @param string $prefix 清除的字串
     ************************************************/
    private function removePrefix($string, $prefix = "..")
    {
        // if (substr($string, 0, strlen($prefix)) === $prefix) $res = substr($string, strlen($prefix));
        return str_replace([$prefix, $_SERVER['DOCUMENT_ROOT']], '', $string);
    }
    /************************************************
     * ### 掃瞄目錄符合條件的檔案 ###
     * @param string $regular 符合正則條件的檔案  
     * 注意 這適用於小檔讀取
     ************************************************/
    public function getMatchingFiles($regular = "/.*?/"): array
    {
        $result = [];
        $filesPath = scandir($this->folderPath);
        $matches = preg_grep($regular, $filesPath);
        // foreach ($matches as $f) $result[] = $this->folderPath . $f;
        foreach ($matches as $f) $result[] = $f;
        return $result;
    }
    /************************************************
     * ### 確認檔案是否存在 ###
     * @param string $filename 檔案名稱
     ************************************************/
    public function checkFile($filename)
    {
        $targetFile = "{$this->folderPath}{$filename}";
        return file_exists($targetFile);
    }
    /************************************************
     * ### 讀取檔案 ###
     * @param string $filename 檔案名稱
     * @param int $offsetLine 偏移行數
     * @param int $limitLine 限制讀取行數
     ************************************************/
    public function readFile($filename, $offsetLine = 0, $limitLine = 0)
    {
        $result = "";
        $targetFile = "{$this->folderPath}{$filename}";

        if (!file_exists($targetFile)) return $result;
        // 如果設置位移行則指針復位
        if ($offsetLine) self::$pointer = 0;
        // 如果讀取了另一個檔案則指針復位
        // if(self::$lastFile != $targetFile){
        //     self::$pointer = 0;
        //     self::$lastFile = $targetFile;
        // }
        $file = fopen($targetFile, "r");
        if ($file !== false) {
            fseek($file, self::$pointer);
            for ($i = 0; $i < $offsetLine; $i++) {
                if (fgets($file) === false) break;
            }
            $count = 0;
            while (($line = fgets($file)) !== false) {
                $result .= $line;
                $count++;
                if ($limitLine > 0 && $count >= $limitLine) break;
            }
            if (!feof($file)) self::$pointer = ftell($file);
            else self::$pointer = 0;

            fclose($file);
        }
        return $result;
    }
    /************************************************
     * ### 寫入檔案 ###
     * @param string $folderPath 檔案上傳之目錄  
     ************************************************/
    public function writeFile($content = "", $fileName = null, $method = 'w')
    {
        if (empty($fileName)) $fileName . time() . ".txt";
        if (is_array($content)) $content = json_encode($content);
        if ($method != 'w' && $method != 'a')  $method = 'a';
        $targetFile = "{$this->folderPath}{$fileName}";
        $file = fopen($targetFile, $method);
        if (fwrite($file, $content) === FALSE) return false;
        fclose($file);
        return $this->removePrefix($targetFile);
    }
    /************************************************
     * ### 刪除檔案 ###
     * @param string $filePath 刪除檔案路徑  
     ************************************************/
    public function deleteFile($fileName)
    {
        $result = false;
        // if (empty($this->folderPath)) return $result;
        $targetFile = "{$this->folderPath}{$fileName}";
        if (is_file($targetFile)){
            $result = unlink($targetFile);
        }
        
        return $result;
    }
    /************************************************
     * ### 取得目錄位址 ###
     ************************************************/
    public function getPath()
    {
        return $this->folderPath;
    }
    /************************************************
     * ### 將jpg轉png ###
     * 記得啟用 php.ini extension=gd  
     * @param $source 來源檔案
     * @parm $destination 目標檔案
     ************************************************/
    private function convertJpgToPng($source, $destination)
    {
        $image = imagecreatefromjpeg($source);
        $result = imagepng($image, $destination);
        imagedestroy($image);
        return $result;
    }
    /************************************************
     * ### 壓縮png ###
     * 記得啟用 php.ini extension=gd  
     * @param $source 來源檔案
     * @parm $destination 目標檔案
     ************************************************/
    private function compressPng($source, $destination = null): bool
    {
        if (is_null($destination)) $destination = $source;
        $image = imagecreatefrompng($source);
        $result = imagepng($image, $destination, $this->compressionLevel);
        imagedestroy($image);
        return $result;
    }
    /************************************************
     * ### 上傳檔案 ###
     * 記得啟用 php.ini extension=gd  
     * @param $_FILE $file 檔案  
     * @param string $fileRename 檔案重新命名（留空則依照檔案本名）
     ************************************************/
    public function upload($file, $fileRename = null): array
    {
        $result = [];
        if (is_array($file['name'])) {
            $files = [];
            foreach ($file['error'] as $key => $fileError) {
                // 判斷是否超出大小或上傳失敗
                if ($fileError === UPLOAD_ERR_OK && $file['size'][$key] < $this->maxSize && $file['size'][$key] > 0) {
                    // 判斷檔名是否安全
                    // if (mb_strlen($file['name'][$key], 'UTF-8') > 0 && mb_strlen($file['name'][$key], 'UTF-8') <= 64 && preg_match('/^[\p{L}0-9_-]+$/u', $file['name'][$key])) {
                    $files[$key] = [
                        "name" => $file['name'][$key],
                        "tmp_name" => $file['tmp_name'][$key]
                    ];
                    // }
                } else {
                    $files[$key] = [
                        "name" => '',
                        "tmp_name" => ''
                    ];
                }
                // 超過十個檔案則跳出
                if ($key > 10) break;
            }
            if (!empty($files)) {
                foreach ($files as $key => $f) {
                    // 讀取檔案並規劃名稱
                    $sourceName = $f['name'];
                    $tempName = $f['tmp_name'];
                    if (!empty($sourceName) && !empty($tempName)) {
                        // 如果為空則設檔案原本名稱
                        if (is_null($fileRename)) $fileRename = $sourceName;
                        // 設置新檔名 加上編號
                        $fileExtension = pathinfo($sourceName, PATHINFO_EXTENSION);
                        $fileNewname = "{$fileRename}_{$key}.{$fileExtension}";
                        // 移動檔案
                        $targetPath = "{$this->folderPath}{$fileNewname}";
                        if (move_uploaded_file($tempName, $targetPath)) {
                            if (strtolower($fileExtension) == "jpg" || strtolower($fileExtension) == "jpeg") {
                                $fileExtension = "png";
                                $pngTargetPath = "{$this->folderPath}{$fileRename}_{$key}.{$fileExtension}";
                                if ($this->convertJpgToPng($targetPath, $pngTargetPath)) {
                                    if ($this->deleteFile($targetPath)) $targetPath = $pngTargetPath;
                                }
                            }
                            $targetPath = $this->removePrefix($targetPath) . "?t=" . time();
                            // if (strtolower($fileExtension) == "png") $this->compressPng($targetPath);
                        }
                    } else {
                        $targetPath = "";
                    }
                    $allTargetPath[] = $targetPath;
                    // $result = json_encode($allTargetPath);
                }
                $result = $allTargetPath;
            }
        } else {
            // 判斷是否超出大小或上傳失敗
            if ($file['error'] === UPLOAD_ERR_OK && $file['size'] < $this->maxSize && $file['size'] > 0) {
                // 判斷檔名是否安全
                // if (mb_strlen($file['name'], 'UTF-8') > 0 && mb_strlen($file['name'], 'UTF-8') <= 64 && preg_match('/^[\p{L}0-9_-]+$/u', $file['name'])) {
                // 如果為空則設檔案原本名稱
                if (is_null($fileRename)) $fileRename = $file['name'];
                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileNewname = "{$fileRename}.{$fileExtension}";
                // 移動檔案
                $targetPath = "{$this->folderPath}{$fileNewname}";
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    if (strtolower($fileExtension) == "jpg" || strtolower($fileExtension) == "jpeg") {
                        $fileExtension = "png";
                        $pngTargetPath = "{$this->folderPath}{$fileRename}.{$fileExtension}";
                        if ($this->convertJpgToPng($targetPath, $pngTargetPath)) {
                            if ($this->deleteFile($targetPath)) $targetPath = $pngTargetPath;
                        }
                    }
                    // if (strtolower($fileExtension) == "png") $this->compressPng($targetPath);
                    $result = [$this->removePrefix($targetPath) . "?t=" . time()];
                }
                // }
            }
        }
        return $result;
    }
}
