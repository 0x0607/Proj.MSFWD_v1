/****************************************************************************************
 * 
 * 元件調整
 * LastUpdate 04/18/2024
 * By SN-Koarashi、MaizuRoad
 * 
 ****************************************************************************************/
var pidElement = document.getElementById("pageEdit");
// var interface = document.getElementById("interface");
var interface = document.getElementById("interface_succ");
var pageViewDiv = document.getElementById("pagePreView");
var pageViewIframe = document.getElementById("pagePreViewIframe");

// if(!pidElement) notify("新頁面建立成功!!","succ");

// 載入先清除所有快取
delCookie("last_page_components_pos");
// 設為無法操作
pageViewIframe.contentWindow.document.addEventListener("keydown", function(event) {
    event.preventDefault();
});

// function remPidCache() {
//     if (interface) interface.style.visibility = "hidden";
//     delCookie("last_page_components_pos");
//     notify("已清空所有變更..", "succ");
// }

function savePidCache() {
    var pid = pidElement ? pidElement.getAttribute("data-pid") : null;
    var err_message = pid ? null : "沒有設置pageId，將無法存檔...";
    if (err_message) notify(err_message, "warn");
    else {
        if (interface) interface.style.visibility = "hidden";
        postData({
            action: 'PAGE_CHANGE_COMPONENT_POS',
            data: { "pid": pid, "pos": getCookieToJson('last_page_components_pos') }
        }).then(response => {
            if (response.data === '1|OK') {
                delCookie("last_page_components_pos");
                notify("已儲存所有異動..", "succ");
                if (pageViewDiv.style.display != "none") pageViewIframe.contentWindow.location.reload(true);
            } else notify(response.data, "warn");
        })
    }
}


function preViewPage(route) {
    // pageViewDiv.style.visibility = "visible";
    if (!pageViewIframe.src) {
        pageViewDiv.style.display = "block";
        pageViewIframe.src = `/?route=${route}`
    }
    // if (pageViewIframe.src) pageViewIframe.contentWindow.location.reload(true);
    // else pageViewIframe.src = `/?route=${route}`;
}


$(function () {
    var source = null;
    var last = null;
    var k = 0;

    function setPidCookie(dragArr3D) {
        // var pid = pidElement ? pidElement.getAttribute("data-pid") : null;
        // var message = pid ? null : "沒有設置 pid，將無法存檔...";
        // var cookieName = pid ? `p_${pid}_components_pos` : "last_page_components_pos";
        var cookieName = "last_page_components_pos";
    
        // if(message) notify(message, "warn");
        if (interface) interface.style.visibility = "visible";
        setCookie(cookieName, JSON.stringify(dragArr3D));
    }

    function saveDragPosition(cookieName) {
        let drag_arr = [];
        let drag_arr_3d = [];
        $('.drag_wrapper .drag_content[draggable="true"]').each(function () {
            let positionY = $(this).closest('.flex_td').index();
            let positionX = $(this).index();
            let dataId = $(this).attr('data-id');
            let dataCid = $(this).attr('data-cid');
            drag_arr.push({ positionX: positionX, positionY: positionY, id: dataId, cid: dataCid });

            // 將數據轉換為三維數組表示
            if (!drag_arr_3d[positionY]) {
                drag_arr_3d[positionY] = [];
            }
            drag_arr_3d[positionY][positionX] = { id: dataId, cid: dataCid };
        });
        if (DEBUG == 'enable') console.log('sortList', JSON.stringify(drag_arr_3d));
        // 設置存檔點
        setPidCookie(drag_arr_3d);
    }

    function bindDragEvents(element) {
        $(element).on('dragstart', function (e) {
            source = this;
            e.stopPropagation();
        });

        $(element).on('dragover', function (e) {
            if (this === source) return;

            if (e.offsetY < this.offsetHeight / 2) {
                $(this).addClass('insertBefore');
                $(this).removeClass('insertAfter');
            } else {
                $(this).addClass('insertAfter');
                $(this).removeClass('insertBefore');
            }

            e.preventDefault();
            e.stopPropagation();
        });

        $(element).on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $(element).on('dragleave', function (e) {
            $(this).removeClass('insertAfter');
            $(this).removeClass('insertBefore');
            e.preventDefault();
            e.stopPropagation();
        });

        $(element).on('dragend', function (e) {
            source = null;
            e.preventDefault();
            e.stopPropagation();
        });

        $(element).on('drop', function (e) {
            let after = $(this).hasClass('insertAfter');
            let before = $(this).hasClass('insertBefore');
            if (after || before) {
                if (after) {
                    $(source).insertAfter(this);
                } else {
                    $(source).insertBefore(this);
                }
            }

            $(this).removeClass('insertAfter');
            $(this).removeClass('insertBefore');

            // let drag_arr = [];
            // let drag_arr_3d = [];
            // $('.drag_wrapper .drag_content[draggable="true"]').each(function () {
            //     let positionY = $(this).closest('.flex_td').index();
            //     let positionX = $(this).index();
            //     let dataId = $(this).attr('data-id');
            //     let dataCid = $(this).attr('data-cid');
            //     drag_arr.push({ positionX: positionX, positionY: positionY, id: dataId, cid: dataCid });

            //     // 將數據轉換為三維數組表示
            //     if (!drag_arr_3d[positionY]) {
            //         drag_arr_3d[positionY] = [];
            //     }
            //     drag_arr_3d[positionY][positionX] = { id: dataId, cid: dataCid };
            // });

            // if (DEBUG == 'enable') console.log('sortList', JSON.stringify(drag_arr_3d));
            // // 設置存檔點
            // setPidCookie(drag_arr_3d);
            saveDragPosition();
            e.preventDefault();
            e.stopPropagation();
        });
    }

    $('.drag_wrapper .drag_content[draggable="true"]').each(function () {
        k++;
        bindDragEvents(this);

        if (k === $('.drag_wrapper .drag_content[draggable="true"]').length)
            last = this;
    });

    $('.drag_wrapper').on('click', '.drag_content .delete', function () {
        saveDragPosition();
        // 找到父級 drag_content 元素
        var $dragContent = $(this).closest('.drag_content');

        // 獲取要刪除的元件的 data-id
        var dataId = $dragContent.attr('data-id');

        getCookie("last_page_components_pos");
        var cookieValue = getCookie("last_page_components_pos");
        if (cookieValue) {
            var cookieData = JSON.parse(cookieValue);
            if (Array.isArray(cookieData)) {
                // 從數據陣列 drag_arr_3d 中刪除對應的數據
                for (var i = 0; i < cookieData.length; i++) {
                    var index = cookieData[i].indexOf(dataId);
                    if (index !== -1) {
                        cookieData[i].splice(index, 1);
                        break;
                    }
                }
                $dragContent.remove();
                setPidCookie(cookieData);
            }
        } else {
            notify("刪除元件時發生錯誤...", "warn")
            if (DEBUG == 'enable') console.log("last_page_components_pos catch error ;((");
        }
    });


    $('.drag_content_add button').on('click', function () {
        // 找到點擊按鈕所在的 drag_wrapper
        var dragWrapper = $(this).closest('.drag_wrapper');

        // 向後端發送請求獲取新元件的數據
        postData({ action: 'SNOWFLAKE' }).then(response => {
            // 獲取新元件的數據
            newDataId = response.data;
            var newComponent = `
            <div class="drag_content" data-id="${newDataId}" data-cid="1" draggable="true">
                <div style="width: 80%;">
                    <span class="fa-solid fa-star"></span>
                    <span>New Component<span style="color: var(--accent-ft);">(無)<span></span>
                </div>
                <div>
                    <div class="flex_td" style="align-items: center;">
                        <button class="edit" onclick="notify('測試')">
                            <span class="fa-solid fa-pen"></span>
                            <span class="tabletsHidden">編輯</span>
                        </button>
                    </div>
                    <div class="flex_td">
                        <button class="delete">
                            <span class="fa-solid fa-trash-can"></span>
                            <span class="tabletsHidden">刪除</span>
                        </button>
                    </div>
                </div>
            </div>
            `;

            // 在 drag_wrapper 最上方添加新的元件
            dragWrapper.prepend(newComponent);

            // 為新添加的元素綁定拖放事件
            var addedElement = dragWrapper.find('.drag_content[data-id="' + newDataId + '"]').first();
            bindDragEvents(addedElement);
            saveDragPosition();
            notify("元建建立成功", "succ", 0.8);
        });
    });
});

window.addEventListener('beforeunload', function (e) {
    var page_cache = getCookie("last_page_components_pos");
    if (page_cache) e.preventDefault();
    delCookie("last_page_components_pos");
});