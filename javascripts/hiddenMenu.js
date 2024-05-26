// const APIURL = localStorage.getItem("APIURL");
// const DEBUG = localStorage.getItem("DEBUG");
/****************************************************************************************
 * 
 * 未命名
 * LastUpdate 03/16/2024
 * 
 ****************************************************************************************/
/************************************************
 * ### 設置左邊導覽列的啟用狀態 ###
 * @param {string} actionElementId - 動作元素Id
 ************************************************/
function changeMenuStatus(clickBotton, placeholderElementId, actionElementId, widthPercent = "20%") {
    var actionElement = document.getElementById(actionElementId);
    var placeholderElement = document.getElementById(placeholderElementId);

    if (actionElement && placeholderElement && clickBotton) {
        // var cookieValue = getCookie(actionElementId);

        if (placeholderElement.style.width !== widthPercent) {
            // actionElement.style.display = 'block';
            // clickBotton.style.left = widthPercent;
            placeholderElement.style.width = widthPercent;
            actionElement.style.left = '0%';
            // placeholderElement.style.flexBasis = widthPercent;
            // if (cookieValue !== 'open') {
            //     setCookie(actionElementId, 'open', 365);
            // }
        } else {
            // clickBotton.style.left = '0%';
            placeholderElement.style.width = "0%";
            actionElement.style.left = '-'+widthPercent;
            // actionElement.style.display = 'none';
            // placeholderElement.style.flexBasis = '0%';
            // if (cookieValue !== 'closed') {
            //     setCookie(actionElementId, 'closed', 365);
            // }
        }
    }

}
/************************************************
 * ### 取得左邊導覽列狀態 ###
 * @param {string} actionElementId - 動作元素Id
 ************************************************/
// function loadMenuStatus(actionElementId) {
//     var cookieValue = getCookie(actionElementId);
//     if (cookieValue === 'open') {
//         actionElement.style.width = '20%';
//     } else if (cookieValue === 'closed') {
//         actionElement.style.width = '20%';
//     }
//     var actionElement = document.getElementById(actionElementId);

//     if (actionElement) {
//         var currentDisplayStyle = actionElement.style.width;
//         var cookieValue = getCookie(actionElementId);

//         if (currentDisplayStyle === 'none' || currentDisplayStyle === '') {

//             if (cookieValue !== 'open') {
//                 setCookie(actionElementId, 'open', 365);
//             }
//         } else {
//             actionElement.style.display = 'none';
//             if (cookieValue !== 'closed') {
//                 setCookie(actionElementId, 'closed', 365);
//             }
//         }
//     }

// }