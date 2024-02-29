<?php
$key = "";
$iv = "";
$data1 = "";
//去除 padding 副程式
function strippadding($string)
{
    $slast = ord(substr($string, -1));
    $slastc = chr($slast);
    $pcheck = substr($string, -$slast);
    if (preg_match("/$slastc{" . $slast . "}/", $string)) {
        $string = substr($string, 0, strlen($string) - $slast);
        return $string;
    } else {
        return false;
    }
}
function create_aes_decrypt($parameter = "", $key = "", $iv = "")
{
    return strippadding(
        openssl_decrypt(
            hex2bin($parameter),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        )
    );
}

//主程式
$rawData = create_aes_decrypt($data1, $key, $iv);
echo "解密後資料=[<font color=darkblue><gg
id='outtt'>" . $rawData . "</gg></font>]<br><br>";

// json_decode
$data = json_decode($rawData, true);
echo "json_decode後資料=[<font color=darkblue><gg
id='outtt'>" . $data . "</gg></font>]<br><br>";

// urldecode
parse_str(urldecode($rawData), $data);
echo "urldecode後資料=[<font color=darkblue><gg
id='outtt'>";
print_r($data);
echo "</gg></font>]<br>";