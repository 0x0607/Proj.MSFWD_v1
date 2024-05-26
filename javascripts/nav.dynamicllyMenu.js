/************************************************
 * ### 開合導覽列清單 ###
 ************************************************/
function toggleNavMenu(targetElementId) {
    var targetElement = document.getElementById(targetElementId);
    if (targetElement.style.top === "" || targetElement.style.top === "90%") {
        targetElement.style.top = '60%';
        if (DEBUG == 'enable') console.log(`[js_toggleNavMenu] ${targetElementId} is set to expand.`);
    } else {
        targetElement.style.top = "";
        if (DEBUG == 'enable') console.log(`[js_toggleNavMenu] ${targetElementId} is set to collapse.`);
    }
}