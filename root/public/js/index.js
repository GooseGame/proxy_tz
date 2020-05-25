
function getUserInput() {
    event.preventDefault();
    let input = document.getElementById("form").value;
    document.getElementById("loading").style.visibility = "visible";
    makeResponse(input);
}

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

function getCategoriesInfo() {
    event.preventDefault();
    let req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            fillCategories(this.responseText);
            req.abort();
        }
    };
    req.open("GET", '/index.php?getMaxCategories=10', true);
    req.setRequestHeader("Content-type","application/json");
    req.send();
}

function onclickCategory() {
    hide();
    category_id = event.srcElement.id;
    let req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            fillContent(this.responseText);
            req.abort();
        }
    };
    req.open("GET", '/index.php?id=' + category_id, true);
    req.setRequestHeader("Content-type","application/json");
    req.send();
}

function hide() {
    document.getElementById('CategoriesSidebar').style.display = "none";
    const elements = document.getElementsByClassName("sidebar");
    while (elements.length > 0) elements[0].remove();
}

function checkIp() {
    let req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            fillIpBlock(this.responseText);
            req.abort();
        }
    };
    req.open("GET", '/index.php?check=yep', true);
    req.setRequestHeader("Content-type","application/json");
    req.send();
}

function fillIpBlock(response) {
    let json = JSON.parse(response);
    if (json['message'] !== undefined) {
        alert(json['message']);
    }
    else {
        let categoryTemplate = '';
        let shopTemplate = '';
        if (json['category_id'] !== undefined) {
            categoryTemplate = `<p id="${json['category_id']}" class="secondaryColor" onclick="onclickCategory()">You saw this shop before: <u class="submitButton">${json['category_name']}</u></p>`;
        }
        if (json['shop_id'] !== undefined) {
            shopTemplate = `<p id="${json['shop_id']}" class="secondaryColor" onclick="makeResponse('${json['site']}')">You saw this category before: <u class="submitButton">${json['shop_name']}</u></p>`;
        }
        document.getElementById("answer").innerHTML += categoryTemplate;
        document.getElementById("answer").innerHTML += shopTemplate;
    }
}

function fillCategories(response) {
    let json = JSON.parse(response);
    if (json['message'] !== undefined) {
        alert(json['message']);
    }
    else {
        for (let i = 0; i < json.length; i++) {
            renderCategories(json[i]);
        }
    }
}

function getThemes() {
    document.getElementById('themes').style.display = "block";
    let req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let json = JSON.parse(this.responseText);
            alert(json);
            gen(json);
            changeCororPalette(json);
            req.abort();
        }
    };
    req.open("GET", '/index.php?themes=yep', true);
    req.setRequestHeader("Content-type","application/json");
    req.send();
}

function renderCategories(json) {
    const name = json['name'];
    const id = json['category_id'];
    let template = `<h5 id="${id}" class="hovered primaryColor sidebar" onclick="onclickCategory()">${name}</h5>`;
    document.getElementById("CategoriesSidebar").innerHTML += template;
}

function fillContent(response) {
    let json = JSON.parse(response);
    if (json['message'] !== undefined) {
        alert(json['message']);
    }
    else {
        for (let i = 0; i < json.length; i++) {
            render(json[i]);
        }
    }
}

function render(response) {
    const title = response['title'];
    const img = response['img_src'];
    let desc = response['desc'];
    const times = "used "+response['times']+" times";
    const date = "will expire in "+response['date'];
    //slice description if its too big
    if (desc.length > 100) {
        desc = desc.substr(0, 100)+"...";
    }

    let template;
    if (img.length > 5) {
        template = `<div class='item gray'> 
                        <img src="${img}" width="100" height="100" alt="" class='img left'"> 
                        <h3 class='title secondaryColor'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p>
                            <span class='small left'>${date}</span>
                            <span class='small right'>${times}</span>
                        </p> 
                    </div>`;
    }
    else {
        template = `<div class='item gray'> 
                        <h3 class='placeholder left primaryColor secondaryBGColor'>${img}</h3> 
                        <h3 class='title secondaryColor'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p>
                            <span class='small left'>${date}</span>
                            <span class='small right'>${times}</span>
                        </p> 
                    </div>`;
    }

    document.getElementById("answer").innerHTML += template;
}

function redirect() {
    document.getElementById('CategoriesSidebar').style.display = "block";
    getCategoriesInfo();
}

function changeCororPalette(response) {
    document.getElementById('themes').onclick = function() {
        let j = document.getElementById('themes').value;
        makeColoring(response, j);
        j++;
        document.getElementById('themes').value = j;
    }
}

function gen(response) {
    for (i=0; i<response.length; i++) {
        document.getElementById('body').innerHTML += `<style>  
                                                    .primary${i} {
                                                        color: ${response[i]['primary_color']};
                                                    }
                                                    .primaryBG${i} {
                                                        background-color: ${response[i]['primary_color']};
                                                    }
                                                    .secondary${i} {
                                                        color: ${response[i]['secondary_color']};
                                                    }
                                                    .secondaryBG${i} {
                                                        background-color: ${response[i]['secondary_color']};
                                                    }
                                                    .third${i} {
                                                        color: ${response[i]['third_color']};
                                                    }
                                                    .thirdBG${i} {
                                                        background-color: ${response[i]['third_color']};
                                                    }
                                                </style>`
    }
}

function makeColoring(response, j) {
    prim = document.getElementsByClassName('primaryColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`primary${j-1}`);
        prim[i].classList.add(`primary${j}`);
    }
    prim = document.getElementsByClassName('secondaryColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`secondary${j-1}`);
        prim[i].classList.add(`secondary${j}`);
    }
    prim = document.getElementsByClassName('thirdColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`third${j-1}`);
        prim[i].classList.add(`third${j}`);
    }
    prim = document.getElementsByClassName('primaryBGColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`primaryBG${j-1}`);
        prim[i].classList.add(`primaryBG${j}`);
    }
    prim = document.getElementsByClassName('secondaryBGColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`secondaryBG${j-1}`);
        prim[i].classList.add(`secondaryBG${j}`);
    }
    prim = document.getElementsByClassName('thirdBGColor');
    for (var i = 0; i < prim.length; i++) {
        prim[i].classList.remove(`thirdBG${j-1}`);
        prim[i].classList.add(`thirdBG${j}`);

    }
}