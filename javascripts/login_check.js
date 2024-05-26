// const APIURL = localStorage.getItem("APIURL");
// const DEBUG = localStorage.getItem("DEBUG");
/****************************************************************************************
 * 
 * 未命名
 * LastUpdate 03/16/2024
 * 
 ****************************************************************************************/
/************************************************
 * ### 將元素之Value轉換sha256並輸出到hash ###
 * @param {HTMLFormElement} form - 表單
 * @returns {string} 轉換後的結果
 ************************************************/
async function sha256(element) {
    if (element.value.length < 8 || !element.value) {
        if (element.hash) {
            if (DEBUG == 'enable') console.log(`[js_sha256] Clear element.${element.name}.hashPassword`);
            element.hash = '';
        }
        return "";
    }
    const encoder = new TextEncoder();
    const data = encoder.encode(element.value);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(byte => byte.toString(16).padStart(2, '0')).join('');
    if (DEBUG == 'enable') console.log(`[js_sha256] HashPassword: ${hashHex}`);
    element.hash = hashHex;
    // else element.value = hashHex;
}

function clearInput(element) {
    if (DEBUG == 'enable') console.log(`[js_clearInput] Clear element.${element.name}.value`);
    element.value = '';
    if (element.hash) {
        if (DEBUG == 'enable') console.log(`[js_clearInput] Clear element.${element.name}.value`);
        element.hash = '';
    }
}
