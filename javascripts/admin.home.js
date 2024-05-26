let now__timestamp = document.getElementById("now__timestamp");
let now__cstimestamp = document.getElementById("now__cstimestamp");
let now__time = document.getElementById("now__time");
if (now__timestamp && now__cstimestamp && now__time) {
    setInterval(function () {
        now__timestamp.innerText = Math.floor(new Date().getTime() / 1000);
        now__cstimestamp.innerText = convertTimestampToCSharpTick(parseInt(now__timestamp.innerText));
        now__time.innerText = formatDate(parseInt(now__timestamp.innerText));
    }, 1000);
}
