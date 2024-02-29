<?php

/*************************************************************
 * 
 * 通用Function
 * 
 *************************************************************/

/************************************************
 * ### 產生CRC32 ###
 * @param string $account 帳號
 ************************************************/
function generateCRC32($input = null): string
{
    return hash('crc32', microtime(true) . $input);
}
/************************************************
 * ### 檢查密碼格式是否符合複雜性密碼原則 ###
 * @param string $password 密碼
 ************************************************/
function checkPassword($password): bool
{
    if ($password === '') return false;
    // if (strlen($password) < 8) return false;
    // if (!preg_match('/[A-Z]/', $password)) return false;
    // if (!preg_match('/[a-z]/', $password)) return false;
    // if (!preg_match('/[0-9]/', $password)) return false;
    // if (!preg_match('/[!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/~`|\\\]/', $password)) return false;
    return true;
}
/************************************************
 * ### 檢查帳戶格式 ###
 * @param string $account 帳號
 ************************************************/
function checkAccount($account): bool
{
    if (strlen($account) < 3) return false;
    if (!preg_match('/^[a-zA-Z0-9_\-@$.]+$/', $account)) return false;
    return true;
}
/************************************************
 * ### 產生加密隨機金鑰 ###
 * @param string $length 數據長度
 ************************************************/
function generateRandomString($length = 32): string
{
    $characters = '2345679abcdefghjkmnpqrtwxyABCDEFGHJKMNPQRTWXY';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/************************************************
 * ### 產生Key及Initialization Vector ###
 * @return array [ ["key"], ["iv"] ]
 ************************************************/
function generateHashKey(): array
{
    $encryptionKey = generateRandomString(32);
    $iv = generateRandomString(16);
    return ["key" => $encryptionKey, "iv" => $iv];
}
/************************************************
 * ### 將資料以Key及Initialization Vector加密 ###
 * @param array $rawData 原始資料
 * @param string $key 金鑰
 * @param string $iv 初始變量
 ************************************************/
function encryptData($rawData, $key, $iv, $cipherAlgo = 'aes-256-cbc'): string
{
    if (gettype($rawData) != "array") return "";
    $data = json_encode($rawData, true);
    return trim(bin2hex(openssl_encrypt($data, $cipherAlgo, $key, OPENSSL_RAW_DATA, $iv)));
}
/************************************************
 * ### 將資料以Key及Initialization Vector解密 ###
 * @param string $rawData 原始資料
 * @param string $key 金鑰
 * @param string $iv 初始變量
 ************************************************/
function decryptData($rawData, $key, $iv, $cipherAlgo = 'AES-256-CBC'): array
{
    if (gettype($rawData) != "string") return "";
    $data = openssl_decrypt(hex2bin($rawData), $cipherAlgo, $key, OPENSSL_RAW_DATA, $iv);
    if ($data === false) return [];
    return json_decode($data, true);
}
/************************************************
 * ### 回傳JSON格式資訊 ###  
 * *Content-type: application/json*  
 * @param array $message 回傳JSON資訊`["status" => 狀態, "data" => 資訊]`  
 * @param bool $exitDirectly 是否執行後直接中斷
 ************************************************/
function httpRespondJson($message = ["status" => 200, "data" => ""], $exitDirectly = true): string
{
    header("Content-type: application/json");
    http_response_code($message['status']);
    if ($exitDirectly) exit(json_encode($message));
    return json_encode($message);
}
/************************************************
 * ### 傳送站內API資訊 ###
 * @param string $data 資料
 * @param string $target 目標
 ************************************************/
function apiRespondJsonData($data, $target): string
{
    $contextOptions = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content" => http_build_query(["data" => $data])
        ]
    ];
    $result = file_get_contents($target, false, stream_context_create($contextOptions));
    return $result;
}
/************************************************
 * ### 敏感字過濾器 ###
 * @param string $input 資料
 * @param array $banword 敏感字
 ************************************************/
function sensitiveWordFilter($str, $banword = []): bool
{
    if (empty($banword)) return false;
    $pattern = '/' . implode('|', array_map('preg_quote', $banword)) . '/i';
    return preg_match($pattern, strtolower($str)) === 1;
}
/************************************************
 * ### 時間轉換器 將Unix時間戳轉換成時間戳 ###
 * @param int $time 一個C#Tick時間戳
 * @param int $timezoneOffset 時區差
 ************************************************/
function convertCSharpTickToTimestamp($time, $timezoneOffset = 28800)
{
    return ($time - 621355968000000000) / 10000000 - $timezoneOffset;
}
/************************************************
 * ### 時間轉換器 將時間戳轉換成Unix時間戳 ###
 * @param int $time 一個timestamp時間戳
 * @param int $timezoneOffset 時區差
 ************************************************/
function convertTimestampToCSharpTick($time, $timezoneOffset = 28800)
{
    return ($time + $timezoneOffset + 62135596800) * 10000000;
    // number_format($res, 0, '', '')
}
