function checkpw($pwid, $pwcheckid, $pwhint) {
  $str = "";
  $value = document.getElementById($pwid).value;
  if ($value.length < 8) $str += "❎ 密碼長度低於八個字元\t\n";
  if (!$value.match(/[A-Z]/)) $str += "❎ 密碼缺少大寫字母\t\n";
  if (!$value.match(/[a-z]/)) $str += "❎ 密碼缺少小寫字母\t\n";
  if (!$value.match(/[0-9]/)) $str += "❎ 密碼缺少數字\t\n";
  if (!$value.match(/[!@#$%^&*()_\-+={[}\]:;"'<>,.?\/~`\\|]/))
    $str += "❎ 密碼缺少特殊字元\t\n";
  if (document.getElementById($pwcheckid).value != $value)
    $str += "⛔ 與再次輸入密碼不符合\t\n";
  if ($str == "") $str = "✅ 太棒了";
  changeinnerText($pwhint, $str);
}

async function hashPassword(password) {
  // 将密码转换为 Uint8Array
  const encoder = new TextEncoder();
  const data = encoder.encode(password);

  // 使用 SubtleCrypto 进行 SHA-256 哈希
  const hashBuffer = await crypto.subtle.digest("SHA-256", data);

  // 将哈希结果转换为十六进制字符串
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray
    .map((byte) => byte.toString(16).padStart(2, "0"))
    .join("");

  return hashHex;
}

//   const password = 'your_password';
//   hashPassword(password).then(hash => {
//     console.log(hash);
//   });
