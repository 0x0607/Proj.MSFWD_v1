

function selectUser(value){
    let searchUser = document.getElementById("searchUser");
    searchUser.value = value;
    notify(`已選取"${value}"`,"succ");
}

function searchDBUser(value){
    if(value){
        postData({action: 'SEARCH_USER', searchAccount: value }).then(response => {
            let resultUser = document.getElementById("searchResult");
            resultUser.innerHTML = ''; // 清空先前的結果
    
            response.data.forEach(element => {
                // 附加每個結果
                resultUser.innerHTML += `<div class="flex_tr allergic" onclick="selectUser('${element.account}')"><span>${element.nickname}</span><span style='color:var(--accent-ft)'>&nbsp;(@${element.account})</span></div>`;
            });
        });
    }
}