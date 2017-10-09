$(document).ready(function() {
    function toggleForm(formSelector) {
        $('form').fadeOut('slow', function() {
            $(formSelector).fadeIn('slow');
        });
    }
    $('.login-form').submit(function(e) {
    	e.preventDefault();
    	var data  = $(this).serializeArray();
    	$.post('/api/login.php',$(this).serialize(),function(res) {
    		console.log(res);
    	});
    });
    /*$('.login-form').submit(function() {

    });
    $('.login-form').submit(function() {

    });
    $('.login-form').submit(function() {

    });*/
});