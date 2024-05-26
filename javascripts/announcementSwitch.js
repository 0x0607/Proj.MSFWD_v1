// const APIURL = localStorage.getItem("APIURL");
// const DEBUG = localStorage.getItem("DEBUG");
/****************************************************************************************
 * 
 * 未命名
 * LastUpdate 03/16/2024
 * 
 ****************************************************************************************/
function announcementSwitch(evt, tabName) {
    $('.announcementContentText').hide();
    $('#' + tabName).show();
}