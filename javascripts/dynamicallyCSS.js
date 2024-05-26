// const APIURL = localStorage.getItem("APIURL");
// const DEBUG = localStorage.getItem("DEBUG");
/****************************************************************************************
 * 
 * 未命名
 * LastUpdate 03/16/2024
 * 
 ****************************************************************************************/
function userChangeColor() {
    let themeColorSelect = document.getElementById('theme_color');
    themeColorSelect.selectedIndex = 0;
    changeColor();
}

function changeColor() {
    let advSetting = document.getElementById('enableAdvancedSetting');
    let btnlockSetting = document.getElementById('enablelockButtonColorSetting');
    let elementsColors = {
        'promptboxTitle': 'secondary',
        'promptboxContentText': 'primaryFont',
        'promptboxAccentContentText': 'secondaryFont',
        'promptboxHyperlink': 'primaryFont',
        'promptboxHyperlinkHover': 'primary',
        'promptboxNav': 'secondary',
        'promptboxContentButton': 'buttonFont',
        'promptboxContentButtonHover': 'buttonFontHover',
        'promptboxContentAccentButton': 'secondaryButtonFont',
        'promptboxContentAccentButtonHover': 'secondaryButtonFontHover'
    };

    let elementsBackgrounds = {
        'browserContent': 'primaryBackground',
        'promptboxTitle': 'primary',
        'promptboxContent': 'secondaryBackground',
        'promptboxNav': 'navBackground',
        'promptboxContentButton': 'button',
        'promptboxContentButtonHover': 'buttonHover',
        'promptboxContentAccentButton': 'secondaryButton',
        'promptboxContentAccentButtonHover': 'secondaryButtonHover'
    };
    followColor(advSetting.checked, btnlockSetting.checked);

    for (let elementId in elementsColors) {
        let colorValueId = elementsColors[elementId];
        let element = document.getElementById(elementId);
        let colorValueElement = document.getElementById(colorValueId);
        if (element && colorValueElement) {
            element.style.color = colorValueElement.value;
        }
    }

    for (let elementId in elementsBackgrounds) {
        let backgroundValueId = elementsBackgrounds[elementId];
        let element = document.getElementById(elementId);
        let backgroundValueElement = document.getElementById(backgroundValueId);
        if (element && backgroundValueElement) {
            element.style.backgroundColor = backgroundValueElement.value;
        }
    }
}

function followColor(adv, btnlock) {
    let advFollowColors = {
        'navBackground': 'secondaryBackground',
        'button': 'primary',
        'buttonHover': 'secondary',
        'secondaryButton': 'secondary',
        'secondaryButtonHover': 'primary',
    };
    let btnFollowColors = {
        'buttonFont': 'buttonHover',
        'buttonFontHover': 'button',
        'secondaryButtonFont': 'secondaryButtonHover',
        'secondaryButtonFontHover': 'secondaryButton'
    }
    if (!adv) {
        for (let elementId in advFollowColors) {
            let followElementId = advFollowColors[elementId];
            let element = document.getElementById(elementId);
            let followElement = document.getElementById(followElementId);
            if (element && followElement) {
                element.value = followElement.value;
            }
        }
    }
    if (!adv | btnlock) {
        for (let elementId in btnFollowColors) {
            let followElementId = btnFollowColors[elementId];
            let element = document.getElementById(elementId);
            let followElement = document.getElementById(followElementId);
            if (element && followElement) {
                element.value = followElement.value;
            }
        }
    }


}

function changeThemeColor(theme) {
    let advSetting = document.getElementById('enableAdvancedSetting');
    let lockbtnSetting = document.getElementById('enablelockButtonColorSetting');
    let colorList = {
        'primary': '#2879F6',
        'primaryFont': '#FFFFFF',
        'primaryBackground': '#212121',
        'secondary': '#FFFFFF',
        'secondaryFont': '#2879F6',
        'secondaryBackground': '#171717',
        'navBackground': '#373737',
        'button': '#2879F6',
        'buttonHover': '#FFFFFF',
        'buttonFont': '#FFFFFF',
        'buttonFontHover': '#2879F6',
        'secondaryButton': '#FFFFFF',
        'secondaryButtonHover': '#2879F6',
        'secondaryButtonFont': '#2879F6',
        'secondaryButtonFontHover': '#FFFFFF',
    }
    advSetting.checked = true;
    lockbtnSetting.checked = false;
    enableAdvSetting(advSetting.checked);
    lockButtonColorSetting(lockbtnSetting.checked);
    switch (theme) {
        case "snkms":
            colorList = {
                'primary': '#105393',
                'primaryFont': '#000000',
                'primaryBackground': '#E3E3E3',
                'secondary': '#FFFFFF',
                'secondaryFont': '#105393',
                'secondaryBackground': '#FFFFFF',
                'navBackground': '#105393',
                'button': '#FFFFFF',
                'buttonHover': '#C5D6E5',
                'buttonFont': '#105393',
                'buttonFontHover': '#FFFFFF',
                'secondaryButton': '#105393',
                'secondaryButtonHover': '#048AA4',
                'secondaryButtonFont': '#FFFFFF',
                'secondaryButtonFontHover': '#FFFFFF'
            };
            break;
        case "red":
            colorList = {
                ...colorList,
                'primary': '#965454',
                'secondaryFont': '#D10009',
                'button': '#965454',
                'buttonFontHover': '#965454',
                'secondaryButtonHover': '#965454',
                'secondaryButtonFont': '#965454'
            };
            break;
        case "phub":
            colorList = {
                'primary': '#F7971D',
                'primaryFont': '#FFFFFF',
                'primaryBackground': '#171717',
                'secondary': '#FFFFFF',
                'secondaryFont': '#F7971D',
                'secondaryBackground': '#212121',
                'navBackground': '#212121',
                'button': '#F7971D',
                'buttonHover': '#FFFFFF',
                'buttonFont': '#FFFFFF',
                'buttonFontHover': '#F7971D',
                'secondaryButton': '#FFFFFF',
                'secondaryButtonHover': '#F7971D',
                'secondaryButtonFont': '#F7971D',
                'secondaryButtonFontHover': '#FFFFFF',
            };
            break;
        case "steam":
            colorList = {
                'primary': '#3B6E8C',
                'primaryFont': '#C3C3C3',
                'primaryBackground': '#171D25',
                'secondary': '#FFFFFF',
                'secondaryFont': '#1A9FFF',
                'secondaryBackground': '#1B2C41',
                'navBackground': '#171A21',
                'button': '#3B6390',
                'buttonHover': '#67C1F5',
                'buttonFont': '#67C1F5',
                'buttonFontHover': '#FFFFFF',
                'secondaryButton': '#6fA720',
                'secondaryButtonHover': '#8BD328',
                'secondaryButtonFont': '#FFFFFF',
                'secondaryButtonFontHover': '#FFFFFF',
            };
            break;
        //171D25
        // 1A9FFF
        // C3C3C3
        // 1B2C41
        // 3B6390
    }
    for (let inputId in colorList) {
        let color = colorList[inputId];
        let element = document.getElementById(inputId);
        element.value = color;
    }
    changeColor()
}

function enableAdvSetting(enable) {
    let advancedSetting = document.getElementById('advancedSetting');

    if (enable) advancedSetting.style.display = '';
    else advancedSetting.style.display = 'none';
    changeColor();
}

function lockButtonColorSetting(enable) {
    let buttonFontSetting = document.getElementById('buttonFontSetting');
    let secondaryButtonFontSetting = document.getElementById('secondaryButtonFontSetting');
    if (!enable) {
        buttonFontSetting.style.display = '';
        secondaryButtonFontSetting.style.display = '';
    }
    else {
        buttonFontSetting.style.display = 'none';
        secondaryButtonFontSetting.style.display = 'none';
    }
    changeColor();
}
// function changeSecondary(changeStyleValue){
//     let promptboxAccentContentText = document.getElementById('promptboxAccentContentText');
//     let promptboxContentButton = document.getElementById('promptboxAccentContentText');

//     promptboxAccentContentText.style.color = changeStyleValue;
//     promptboxContentButton.style.backgroundColor = changeStyleValue;
// }