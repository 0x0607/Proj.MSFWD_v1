/************************************************
 * ### 將元素之Value轉換sha256並輸出到hash ###
 * @param {HTMLFormElement} form - 表單
 * @returns {string} 轉換後的結果
 ************************************************/
async function sha256(element) {
    if (!element.value) return "";
    const encoder = new TextEncoder();
    const data = encoder.encode(element.value);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');

    if (true) element.hash = hashHex;
    else element.value = hashHex;
}

function clearInput(element) {
    element.value = '';
}
