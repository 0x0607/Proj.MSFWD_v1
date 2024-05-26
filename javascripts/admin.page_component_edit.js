/****************************************************************************************
 * 
 * 元件調整
 * LastUpdate 04/18/2024
 * By SN-Koarashi、MaizuRoad
 * 
 ****************************************************************************************/;
var pageViewDiv = document.getElementById("pagePreView");
var pageViewIframe = document.getElementById("pagePreViewIframe");

// 載入先清除所有快取
delCookie("last_page_components_pos");
// 設為無法操作
pageViewIframe.contentWindow.document.addEventListener("keydown", function(event) {
    event.preventDefault();
});

function preViewPage(route) {
    // pageViewDiv.style.visibility = "visible";
    if (!pageViewIframe.src) pageViewIframe.src = `/?route=${route}`
    else pageViewIframe.contentWindow.location.reload(true);
    // if (pageViewIframe.src) pageViewIframe.contentWindow.location.reload(true);
    // else pageViewIframe.src = `/?route=${route}`;
}