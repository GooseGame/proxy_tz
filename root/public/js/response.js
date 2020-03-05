function makeResponse(input) {
    let req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            fillContent(this.responseText);
            document.getElementById("loading").style.visibility = "hidden";
            req.abort();
        }
    };
    req.open("GET", '/index.php?site=' + input, true);
    req.setRequestHeader("Content-type","application/json");
    req.send();
}

