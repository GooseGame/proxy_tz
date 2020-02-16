function fillContent(response) {
    let json = JSON.parse(response);
    for (let i = 0; i < json.length; i++) {
        render(json[i]);
    }
}

function render(response) {
    const title = response['title'];
    const img = response['img'];
    let desc = response['desc'];
    const times = "used "+response['times']+" times";
    //slice description if its too big
    if (desc.length > 100) {
        desc = desc.substr(0, 100)+"...";
    }

    let template;
    if (img.length > 5) {
        template = `<div class='item gray'> 
                        <img src="${img}" width="100" height="100" alt="" class='img'"> 
                        <h3 class='title'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p class='times'>${times}</h3> 
                    </div>`;
    }
    else {
        template = `<div class='item gray'> 
                        <h3 class='placeholder'>${img}</h3> 
                        <h3 class='title'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p class='times'>${times}</h3> 
                    </div>`;
    }



    document.getElementById("answer").innerHTML += template;
}