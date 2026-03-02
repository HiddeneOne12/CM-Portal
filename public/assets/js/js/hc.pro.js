$(document).ready(function() {

    /**
     * DMC Card Form
     */
    $("#sform").on("submit", function(e) {
        e.preventDefault();
        var email = $('#email').val();
        var mobile = $('#mobile').val();
        if (email == '' && mobile == '') {
            $(".aj-msg").html('Mobile number or email address is required.');
            return false;
        }
        loadSpinner();
        var formData = {
            'email': email,
            'mobile': mobile
        };
        $.ajax({
            type: 'POST',
            url: siteUrl + '/digital-card?' + Date.now(),
            data: formData,
            dataType: 'json',
            encode: true
        }).done(function(data) {
            hideSpinner();
            if (data.hasError == 0) {
                $('.main-area').html(data.content);
            } else {
                $(".aj-msg").addClass("alert alert-danger");
                $(".aj-msg").html(data.errorMessage);
            }
        });
    });

    /**
     * VMN Form
     */
});