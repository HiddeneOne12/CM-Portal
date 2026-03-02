var input = document.querySelector("#mobile");
errorMsg = document.querySelector("#error-msg"),
    validMsg = document.querySelector("#valid-msg");
var errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
var iti = window.intlTelInput(input, {
    hiddenInput: "full_phone",
    utilsScript: "assets/plugins/input/build/js/utils.js?1590403638581",
    //preferredCountries: ['ae'],
    onlyCountries: ['ae'],
    separateDialCode: true,
});

var reset = function() {
    input.classList.remove("error");
    errorMsg.innerHTML = "";
    errorMsg.classList.add("hide");
    validMsg.classList.add("hide");
};

var isOk = false;
input.addEventListener('blur', function() {
    reset();
    if (input.value.trim()) {
        if (iti.isValidNumber()) {
            validMsg.classList.remove("hide");
            isOk = true;
        } else {
            isOk = false;
            input.classList.add("error");
            var errorCode = iti.getValidationError();
            errorMsg.innerHTML = errorMap[errorCode];
            errorMsg.classList.remove("hide");
        }
    }
});

input.addEventListener('change', reset);
input.addEventListener('keyup', reset);
var invalidDomains = ["@gmail.", "@yahoo.", "@hotmail.", "@live.", "@aol.", "@outlook."];
/*
$('#lform').parsley().on('form:submit', function() {
    $('#em-error-msg').html();
    if ($('#company').val() != 'Atlantis The Palm') {
        var email = $('#email_address').val();
        if (!isEmailGood(email)) {
            $('#em-error-msg').html('Please enter your business email address.');
            return false;
        }
    }

    return isOk;
});
*/

$.listen('parsley:field:error', function() {
    var i = 0;
    $("#lform .form-control").each(function(k, e) {
        var field = $(e).data("err");
        $(e).next("ul").find("li:eq(" + i + ")").html(field + " field is required");
    });
});


function isEmailGood(email) {
    for (var i = 0; i < invalidDomains.length; i++) {
        var domain = invalidDomains[i];
        if (email.indexOf(domain) != -1) {
            return false;
        }
    }
    return true;
}