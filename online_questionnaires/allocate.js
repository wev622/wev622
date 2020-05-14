$("#consent").submit(function(e) {

    e.preventDefault();

    let i;

    for (i = 1; i < 6; i++) {
        if (!(document.getElementById(i.toString()).checked)) {
            alert("Please check all boxes if you wish to continue to the experiment");
            return;
        }
    }

    let request = $.ajax({
        type: "GET",
        url: "allocate.php",
        dataType: 'text'
    });

    request.done(function(questionnaire) {
        if (questionnaire !== 'none') {
            window.location.href = questionnaire.concat('/').concat(questionnaire).concat('_p1.html');
        }
        else {
            alert("There are no questionnaires left for you to take");
        }
    });

});