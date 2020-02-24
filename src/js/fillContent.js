function fillContent(response) {
    let json = JSON.parse(response);
    for (let i = 0; i < json.length; i++) {
        render(json[i]);
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
                        <h3 class='title'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p>
                            <span class='small left'>${date}</span>
                            <span class='small right'>${times}</span>
                        </p> 
                    </div>`;
    }
    else {
        template = `<div class='item gray'> 
                        <h3 class='placeholder left'>${img}</h3> 
                        <h3 class='title'>${title}</h3> 
                        <p class='desc'>${desc}</p> 
                        <p>
                            <span class='small left'>${date}</span>
                            <span class='small right'>${times}</span>
                        </p> 
                    </div>`;
    }



    document.getElementById("answer").innerHTML += template;
}