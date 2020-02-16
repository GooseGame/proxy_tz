function getUserInput() {
    event.preventDefault();
    let input = document.getElementById("form").value;
    document.getElementById("loading").style.visibility = "visible";
    makeResponse(input);
}
