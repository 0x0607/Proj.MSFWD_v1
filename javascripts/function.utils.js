const APIURL = localStorage.getItem("APIURL");
const DEBUG = localStorage.getItem("DEBUG");
const HTTP_REFERER = localStorage.getItem("HTTP_REFERER");
const authorInformation = ".\n\
#==============================================================#\n\
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░\n\
░██╗░░░██╗░░░████╗░░████████╗████████╗██╗░░░██╗██████╗░██╗░░░██╗\n\
████╗░████╗░██╔═██╗░╚══██╔══╝╚════██╔╝██║░░░██║██╔══██╗██║░░░██║\n\
██╔═██╔═██║██╔╝░░██╗░░░██║░░░░░░██╔═╝░██║░░░██║██████╔╝██║░░░██║\n\
██║░██║░██║████████║░░░██║░░░░██╔═╝░░░██║░░░██║██╔══██╗██║░░░██║\n\
██║░╚═╝░██║██╔═══██║████████╗████████╗╚██████╔╝██║░░██║╚██████╔╝\n\
╚═╝░░░░░╚═╝╚═╝░░░╚═╝╚═══════╝╚═══════╝░╚═════╝░╚═╝░░╚═╝░╚═════╝░\n\
░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░\n\
#==============================================================#\n\
By Maizuru5272(MaizuRoad)\n\n";
/****************************************************************************************
 * 
 * [注意] 相依性，這裡必須先於其他js被載入
 * 通用JavaScript庫 By MaizuRoad
 * LastUpdate 03/17/2024
 * 
 ****************************************************************************************/
// console.log("%c" + "注意!!", 'font-size: 64px; color: #ff0000;');
// console.log("%c" + "除非你知道你在做什麼，不然十分建議您關閉本視窗.", 'font-size: 20px;');
console.log("%c" + "\n十分建議您關閉本視窗，除非你知道你在做什麼.", 'color: #ff0000; font-weight: 900; font-size: 18px');
if (DEBUG == 'enable') {
    console.log(authorInformation + "[注意] 調適模式已被啟用.");
    console.log("[提示] 強制POST資料至系統 ↓\npostData({action: 'TEST', data: 'testData' });")
    console.log("[提示] 觸發通知 ↓\nnotify('TEST測試訊息');")
    // notify("調適模式已被啟用.", 'prom');
}
var notificationQueue = [], notifyIsAnimating = false;
/************************************************
 * ### 跳出通知 ###
 * @param {string} message - 訊息
 * @param {string} type - 類型(info、warn、succ)
 * @param {int} lifeSec - 持續秒數(默認依據字元數來判斷)
 ************************************************/
function notify(message = '', type = 'info', lifeSec = 0) {
    var notification = document.getElementById('notification');
    var notification_icon = document.getElementById('notification_icon');
    var notification_message = document.getElementById('notification_message');
    var notification_bar = document.getElementById('notification_bar');
    var icon = "";
    var minLifeSec = 0.5;
    var maxLifeSec = 3.5;
    if (!lifeSec) {
        var isEnglish = !message.match(/[\u4e00-\u9fa5]/);
        if (isEnglish) lifeSec = Math.max(Math.ceil(message.length / 10), minLifeSec);
        else lifeSec = Math.max(Math.ceil(message.length / 6), minLifeSec);
    }
    if (lifeSec < minLifeSec) lifeSec = minLifeSec;
    if (lifeSec > maxLifeSec) lifeSec = maxLifeSec;
    if (notifyIsAnimating) {
        // 最多佇列8則訊息
        if (notificationQueue.length <= 8) {
            // 隨著訊息越多，後面的訊息會讀取越快
            // let nextLifeTime = Math.max(lifeSec - (notificationQueue.length - 1) * 0.5, 0.4);
            nextLifeTime = lifeSec;
            notificationQueue.push({ message: message, type: type, lifeSec: nextLifeTime })
        }
    }
    else {
        notifyIsAnimating = true;
        switch (type) {
            case "succ":
                icon = "circle-check";
                notification.style.backgroundColor = "var(--succ-alpha60p)";
                notification_bar.style.background = "linear-gradient(to right,var(--succ-alpha20p),var(--succ))";
                break;
            case "warn":
                icon = "circle-exclamation";
                notification.style.backgroundColor = "var(--warn-alpha60p)";
                notification_bar.style.background = "linear-gradient(to right,var(--warn-alpha20p),var(--warn))";
                break;
            case "prom":
                icon = "circle-exclamation";
                notification.style.backgroundColor = "var(--prom-alpha60p)";
                notification_bar.style.background = "linear-gradient(to right,var(--prom-alpha20p),var(--prom))";
                break;
            default:
                icon = "circle-info";
                notification.style.backgroundColor = "var(--primary-bg-alpha60p)";
                notification_bar.style.background = "linear-gradient(to right,var(--primary-bg-alpha20p),var(--primary-bg))";
                break;
        }

        // 設置顯示訊息
        notification_message.textContent = message;
        // 設置圖標
        notification_icon.innerHTML = `<span class="fa-solid fa-${icon}"></span>`;
        // 顯示
        notification.style.animation = `notification_visible ${lifeSec}s linear`;
        notification_bar.style.animation = `notifi_bar ${lifeSec}s linear`;

        // 如果滑鼠碰觸到暫停
        notification.addEventListener('mouseenter', function () {
            notification.style.animationPlayState = "paused";
            notification_bar.style.animationPlayState = "paused";
        });
        notification.addEventListener('mouseleave', function () {
            notification.style.animationPlayState = "running";
            notification_bar.style.animationPlayState = "running";
        });

        // 結束
        notification.addEventListener('animationend', function () {
            notification.style.animation = "";
            notification_bar.style.animation = "";
            notification_icon.innerHTML = "";
            notification_message.textContent = "";
            notifyIsAnimating = false;
            setTimeout(function () {
                if (notificationQueue.length > 0) {
                    var nextNotification = notificationQueue.shift();
                    notify(nextNotification.message, nextNotification.type, nextNotification.lifeSec);
                }
            }, 1);
        });
    }
    return false;
}
/************************************************
 * ### 將POST至API ###
 * @param {JSON} jsonData - json 資料 (預設測試模式)
 * @param {HTMLFormElement} form 表單資料 (無則免填)
 * @returns {Promise} 完成後返還的值
 ************************************************/
function postData(jsonData = { action: "TEST", data: "testData" }, form = '') {
    if (form != '') var formData = new FormData(form);
    else var formData = new FormData();
    formData.append("data", JSON.stringify(jsonData));
    if (DEBUG == 'enable') {
        console.log(`[js_postData] Posting to ${APIURL}.`);
        console.log(jsonData);
    }
    return new Promise((resolve, reject) => {
        $.ajax({
            url: APIURL,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (DEBUG == 'enable') console.log('[js_postData] Posting succeeded.');
                resolve(result);
            },
            error: function (err) {
                if (DEBUG == 'enable') console.log('[js_postData] Posting failed.');
                reject(err);
            },
        });
    });
}
/************************************************
 * ### 處理表單提交後的回傳資訊 ###
 * @param {HTMLFormElement} form - 表單元素
 * @param {string | Function} successCallback - 成功時的回調函數或跳轉的 URL (refresh/success)
 * @param {string | Function} errorCallback - 錯誤時的回調函數或顯示錯誤訊息的元素 ID
 * @param {bool} clearForm - 是否清除表單
 ************************************************/
function done(form, successCallback = '', errorCallback = '', clearForm = false) {
    var delayTime = 1200;
    if (DEBUG == 'enable') console.log('[js_done] Start working...');
    notify("wait..", "prom", 0.5);
    /************************************************
     * ### 將表單內元素轉換為JSON格式 ###
     * @param {HTMLFormElement} form - 表單
     * @returns {string} 轉換後的結果
     ************************************************/
    function json_encodeFormData() {
        var result = {};
        function convertElementValue(element) {
            switch (element.type) {
                case "password":
                    return element.hash ? element.hash : element.value;
                case "checkbox":
                    return element.checked;
                case "radio":
                    return element.checked ? element.value : null;
                case "select-one":
                    return element.options[element.selectedIndex].value;
                case "select-multiple":
                    return Array.from(element.selectedOptions, option => option.value);
                default:
                    return element.value;
            }
        }
        for (var i = 0; i < form.elements.length; i++) {
            var element = form.elements[i];
            if (element.name) result[element.name] = convertElementValue(element);
        }
        if (DEBUG == 'enable') {
            console.log('[js_done] Convert elements...');
            // console.log(result);
        }
        return result;
        // return JSON.stringify(result);
    }
    /************************************************
     * ### 最後輸出 ###
     ************************************************/
    postData(json_encodeFormData(), form)
        .then(response => {
            if (DEBUG == 'enable') {
                console.log('[js_done] Parse the returned data...');
                console.log(response);
            }
            if (response.data === '1|OK') {
                if (DEBUG == 'enable') console.log('[js_done] 1|OK');
                notify("SUCCESS !!", "succ", 0.8);
                setTimeout(function () {
                    if (clearForm) form.reset();
                    if (successCallback) {
                        switch (successCallback) {
                            case 're':
                            case 'refresh':
                                route('', 'location');
                                break;
                            case 'pass':
                            case 'succ':
                            case 'success':
                                // alert('SUCCESS !!');
                                break;
                            default:
                                route(successCallback, 'location');
                                break;
                        }
                    }
                }, delayTime);
            } else {
                if (DEBUG == 'enable') console.log('[js_done] 0|OTHER');
                if (typeof errorCallback == 'function') errorCallback();
                else notify(response.data, "warn");
            }
        })
        .catch(error => {
            if (DEBUG == 'enable') console.log(`[js_done] Error\n`, error);
            // console.log('Error:', error);
        });
    return false;
}
/************************************************
 * ### 送出圖片前預覽圖片 ###
 * @param {string} inputfile - 上傳檔案inputId(this)
 * @param {string} previewImage - 圖片預覽的Id
 ************************************************/
function updatePreview(inputfile, previewImage) {
    // var input = document.getElementById(inputfile);
    var preview = document.getElementById(previewImage);
    // document.getElementById(`delete_${input.name}`).checked = false;
    // console.log(`delete_${inputfile.name} set disable.`);

    if (inputfile.files && inputfile.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(inputfile.files[0]);
        // notify("Upload file success", "succ");
        notify("上傳檔案成功", "succ", 1);
    } else notify("Upload file fail", "prom");
}
/************************************************
 * ### 清空上傳檔案 ###
 * @param {string} inputId - 上傳檔案inputId
 * @param {string} previewImage - 圖片預覽的Id
 ************************************************/
function clearFileInput(inputId, previewImage) {
    var input = document.getElementById(inputId);
    var newInput = document.createElement('input');
    var preview = document.getElementById(previewImage);
    document.getElementById(`delete_${input.name}`).checked = true;
    // console.log(`delete_${input.name} set enable.`);

    newInput.type = 'file';
    newInput.name = input.name;
    newInput.id = input.id;
    newInput.style.display = 'none';
    newInput.accept = '.png,.jpg,.jpeg';
    newInput.onchange = input.onchange;

    var parent = input.parentNode;
    parent.insertBefore(newInput, input);
    parent.removeChild(input);
    preview.src = '';
    // updatePreview(newInput, inputId);
    // notify("Clear file success", "succ");
    notify("清空檔案成功", "succ", 0.8);
    return false;
}
/************************************************
 * ### 設置餅乾 ###
 * @param {string} name - 餅乾名稱
 * @param {any} value - 餅乾值
 * @param {int} days - 餅乾存在時長
 ************************************************/
function setCookie(name, value, days) {
    var expires = "";

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "expires=" + date.toUTCString() + ";";
    }
    document.cookie = `${name}=${value}; ${expires} path=/`;
    if (DEBUG == 'enable') console.log(`[js_setCookie] Set $_COOKIES['${name}'].`)
    return getCookie(name);
}
/************************************************
 * ### 刪除一個一個餅乾 ###
 * @param {string} name - 餅乾名稱
 ************************************************/
function delCookie(name) {
    var date = new Date();
    date.setTime(date.getTime() + (-114514));
    var expires = date.toUTCString();
    document.cookie = `${name}=''; expires=${expires}; path=/`
    if (DEBUG == 'enable') console.log(`[js_delCookie] Deleted $_COOKIES['${name}'].`)
    return !Boolean(getCookie(name));
}
/************************************************
 * ### 取得餅乾 ###
 * @param {string} name - 餅乾名稱
 ************************************************/
function getCookie(name) {
    var nameEQ = name + "=";
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(nameEQ) === 0) {
            // if (DEBUG == 'enable') console.log(`[js_getCookie] Get $_COOKIES['${name}']`)
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}
/************************************************
 * ### 取得餅乾 JSON 口味 ###
 * @param {string} name - 餅乾名稱
 ************************************************/
function getCookieToJson(name) {
    var cookieValue = getCookie(name);
    if (!cookieValue) return null;
    try {
        var jsonData = JSON.parse(cookieValue);
        return jsonData;
    } catch (error) {
        console.error("[js_parseCookieToJson]:", error);
        return null;
    }
}
/************************************************
 * ### 跳轉頁面 ###
 * @param {string} herf - 目標網址
 * @param {string} method - 跳轉模式(location、new_window)
 ************************************************/
function route(herf = '', method = 'location') {
    var regex = /^(\/|http|\?)/;
    var bridge = ''

    if (!regex.test(herf) && herf != '') bridge = '?route='
    if (method != 'location') {
        if (DEBUG == 'enable') console.log(`[js_route] Opening window ${bridge}${herf}...`)
        window.open(`${bridge}${herf}`, `${herf}`, config = 'toolbar=no,status=no,location=no')
    }
    else {
        if (DEBUG == 'enable') console.log(`[js_route] Redirecting to ${bridge}${herf}...`)
        window.location.href = `${bridge}${herf}`
    }
    return false
}

/************************************************
 * ### 複製資料 ###
 * @param {string} value - 複製值
 ************************************************/
function copyContent(value) {
    var textarea = document.createElement('textarea');
    textarea.value = value;
    document.body.appendChild(textarea);
    textarea.select();
    navigator.clipboard.writeText(textarea.value)
        .then(function () {
            if (DEBUG == 'enable') console.log(`[js_copyContent] Text copied to clipboard.`);
            // notification(`Copy successful! "${value}"`,"succ");
            if (value.length < 64) notify(`複製成功!! "${value}"`, "succ");
            else notify(`複製成功!! `, "succ");
        })
        .catch(function (err) {
            if (DEBUG == 'enable') console.log(`[js_copyContent] Error copying text to clipboard.`);
            notify("Copy failed ;(", "prom");
        });
    // 刪除<textarea>元素
    document.body.removeChild(textarea);
    return false;
}
/************************************************
 * ### 時間轉換器 將Unix時間戳轉換成時間戳 ###
 * @param {int} time - 一個一個C#Tick時間戳
 ************************************************/
function convertCSharpTickToTimestamp(time) {
    let timezoneOffset = 28800;
    return (time / 10000000 - 62135596800) - timezoneOffset;
}
/************************************************
 * ### 時間轉換器 將時間戳轉換成Unix時間戳 ###
 * @param {int} timestamp - 一個一個timestamp時間戳
 ************************************************/
function convertTimestampToCSharpTick(timestamp) {
    let timezoneOffset = 28800;
    return (timestamp + timezoneOffset + 62135596800) * 10000000;
}
/************************************************
 * ### 格式化時間 ###
 * @param {int} timestamp - 一個一個timestamp時間戳
 ************************************************/
function formatDate(timestamp) {
    let date = new Date(parseInt(timestamp) * 1000);
    let formattedDate = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
    let formattedTime = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
    return `${formattedDate} ${formattedTime}`;
}
/************************************************
 * ### 設置動作 ###
 * @param {string} formId - 表單ID
 * @param {string} action - 賦予的action
 ************************************************/
function setAction(formId, action="TEST") {
    var form = document.getElementById(formId);
    var existingInput = form.querySelector("input[name='action']");
    if (!existingInput) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "action";
        input.value = action;
        input.style = "display:none; content:'1145141919810';";
        form.appendChild(input);
        if (DEBUG == 'enable') console.log(`[js_setAction] Set action with ${formId}.`);
    }
}