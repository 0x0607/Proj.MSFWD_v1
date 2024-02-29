/****************************************************************************************
 * 
 * 通用JavaScript庫 By MaizuRoad
 * LastUpdate 02/24/2024
 * 
 ****************************************************************************************/

/************************************************
 * ### 處理表單提交後的回傳資訊 ###
 * @param {HTMLFormElement} form - 表單元素
 * @param {string} apiUrl - API 位址
 * @param {string | Function} successCallback - 成功時的回調函數或跳轉的 URL
 * @param {string | Function} errorCallback - 錯誤時的回調函數或顯示錯誤訊息的元素 ID
 ************************************************/
function done(form, apiUrl, successCallback = '', errorCallback = '') {
    if (typeof errorCallback !== 'function') {
        var errorOutput = document.getElementById(errorCallback);
        errorOutput.textContent = 'wait...';
    }
    /************************************************
     * ### 將表單內元素轉換為JSON格式 ###
     * @param {HTMLFormElement} form - 表單
     * @returns {string} 轉換後的結果
     ************************************************/
    function json_encodeData() {
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
            if (element.name) {
                result[element.name] = convertElementValue(element);
            }
        }
        return JSON.stringify(result);
    }
    /************************************************
     * ### 將POST至API ###
     * @returns {Promise} 完成後返還的值
     ************************************************/
    function postData() {
        var formData = new FormData(form);
        formData.append("data", json_encodeData());
        if (true) console.log(json_encodeData());
        return new Promise((resolve, reject) => {
            $.ajax({
                url: apiUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (result) {
                    resolve(result);
                },
                error: function (err) {
                    reject(err);
                },
            });
        });
    }
    /************************************************
     * ### 最後輸出 ###
     ************************************************/
    postData()
        .then(response => {
            if (response.data === '1|OK') {
                if (typeof successCallback === 'function') {
                    successCallback();
                } else {
                    window.location.href = successCallback;
                }
            } else {
                if (typeof errorCallback === 'function') {
                    errorCallback();
                } else {
                    errorOutput.textContent = response.data;
                    // console.error(response.data); 
                }
            }
        })
        .catch(error => {
            // console.log('Error:', error);
        });

    return false;
}
