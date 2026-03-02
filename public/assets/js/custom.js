/*globals $*/

/*$(document).on("click", "#checkMark", function(e) {
	iosOverlay({
		text: "Success!",
		duration: 2e3,
		icon: "img/check.png"
	});
	return false;
});

$(document).on("click", "#cross", function(e) {
	iosOverlay({
		text: "Error!",
		duration: 2e3,
		icon: "img/cross.png"
	});
	return false;
});*/


function loadSpinner() {
    $('.overlay').css('display', 'block');
    var opts = {
        lines: 25 // The number of lines to draw
            ,
        length: 15 // The length of each line
            ,
        width: 28 // The line thickness
            ,
        radius: 17 // The radius of the inner circle
            ,
        scale: 1.75 // Scales overall size of the spinner
            ,
        corners: 0.3 // Corner roundness (0..1)
            ,
        color: '#FFF' // #rgb or #rrggbb or array of colors
            ,
        opacity: 0.2 // Opacity of the lines
            ,
        rotate: 51 // The rotation offset
            ,
        direction: 1 // 1: clockwise, -1: counterclockwise
            ,
        speed: 1 // Rounds per second
            ,
        trail: 60 // Afterglow percentage
            ,
        fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            ,
        zIndex: 2e9 // The z-index (defaults to 2000000000)
            ,
        className: 'spinner' // The CSS class to assign to the spinner
            ,
        top: '50%' // Top position relative to parent
            ,
        left: '50%' // Left position relative to parent
            ,
        shadow: false // Whether to render a shadow
            ,
        hwaccel: false // Whether to use hardware acceleration
            ,
        position: 'absolute' // Element positioning
    };

    var target = document.createElement("div");
    document.body.appendChild(target);
    var spinner = new Spinner(opts).spin(target);
    iosOverlay({
        text: "Processing...",
        /*duration: 2e3,*/
        spinner: spinner
    });
    return false;
}


/*lines: 25, // The number of lines to draw
		length: 20, // The length of each line
		width: 10, // The line thickness
		radius: 17, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		color: '#FFF', // #rgb or #rrggbb
		speed: 1, // Rounds per second
		trail: 60, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: 'auto', // Top position relative to parent in px
		left: 'auto' // Left position relative to parent in px*/


function hideSpinner() {
    $('.overlay').fadeOut(300);
    $('.ios-overlay-show').fadeOut(300);
    /*var overlay = iosOverlay({
    	onhide: this.hide
    	});
    	$('#loading').
    //overlay.destroy();
    //overlay.destroy();*/
}

function UpdateSpinner(txt) {
    var overlay = iosOverlay({});
    var hide_duration = (txt == 'Success') ? 300 : 300;
    overlay.update({
        icon: admin_url + "/assets/plugins/overlay/img/" + txt + ".png",
        text: txt
    });
    if (txt == 'Success') {
        $('.ios-overlay-show').css("background-color", "#00A65A");
    } else {
        $('.ios-overlay-show').css("background-color", " rgb(141, 13, 13)");
    }

    window.setTimeout(function() {
        hideSpinner();
    }, hide_duration);
}
