let elements = document.querySelectorAll("input[type=submit]");

elements[0].value = "Next Page";

let page;

let isDoubleDigit = parseInt(window.location.pathname.split('/')[4].substring(9,10));

if (!isNaN(isDoubleDigit)) {
    page = parseInt(window.location.pathname.split('/')[4].substring(8, 10));
}
else {
    page = parseInt(window.location.pathname.split('/')[4].substring(8, 9));
}

if (page === 1) {
    let startTime =  Math.round(new Date().getTime() / 1000);

    localStorage.setItem("started", startTime.toString());
}



$("#questions").submit(function(e) {

    e.preventDefault();
    let submit = true;
    let i;

    let page_copy = page;

    page_copy -= 1;

    let noQs = 11;

    if (page_copy === 12) {
        noQs = 9;
    }

    for (i = 1; i < noQs; i++) {
        let fieldno = i + (page_copy * 10);
        let field = document.forms["questions"]["trial_".concat(fieldno.toString())];

        if (field.value.trim().length === 0) {
            if (field.type === "text") {
                field.style.background = "pink";
            }
            submit = false;
        }
        else {
            if (field.type === "text") {
                field.style.background = "white";
            }
        }
    }

    if (submit) {

        let i;

        let data = $(this).serializeArray();

        let noQs = 11;

        if (page === 13) {
            noQs = 9;
        }

        for (i = 1; i < noQs; i++) {
            let label = document.getElementById("label_".concat(i)).textContent;
            data.push({name: "label_".concat(i), value: label})
        }

        let participant = window.location.pathname.split('/')[4].substring(0, 6);


        data.push({name: "participant", value: participant});
        data.push({name: "page", value: page});

        let start = parseInt(localStorage.getItem("started"));

        data.push({name: "start", value: start});
        console.log(start);

        let request = $.ajax({
            type: "POST",
            url: "../Submit.php",
            data: data,
            dataType: 'text'
        });

        request.done(function (success) {

            if (page < 13) {
                window.location.href = participant.concat("_p").concat((page + 1).toString()).concat(".html");
            }
            else {
                window.location.href = '../debrief.html';
            }
        })
    }



});