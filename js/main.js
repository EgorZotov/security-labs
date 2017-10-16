$(document).ready(function() {
    function showAlert(text) {
        $('.alert-box__content').text(text);
        $('.alert-box').animate({ right: 0 }).delay(5000).animate({ right: -290 });
    }

    function toggleForm(formSelector) {
        $('form').fadeOut('700').promise().done(function() {
            if (formSelector == "admin-form") {
                $.post('/api/handler.php', { action: 'users' }, function(res) {
                    var users = JSON.parse(res);
                    users.forEach(function(user, key) {
                        $('#user-selector').append('<option name value="' + user + '">' + user + '</option>');
                    });
                    $('.' + formSelector).fadeIn('1500');
                });
            } else {
                $('.' + formSelector).fadeIn('1500');
            }
        });
    }
    $('.login-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                (res.role == 'admin') ? toggleForm('admin-form'): toggleForm('password-form');
            } else {
               switch(res.reason){
                case 'blocked':
                    showAlert('Пользователь заблокирован');
                break;
                case 'password_restricted':
                    showAlert('Введено ограничение');
                break;
                default:
                    showAlert('Пользователь не найден');
                    break;
               }
            }
        });
    });
    $('.password-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            res = JSON.parse(res);
            console.log(res);
            if (res.status && res.status == 'success') {
                showAlert('Пароль изменён');
            }
        });
    });
    $('.admin-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        console.log(data);
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                showAlert('Права изменены');
            }
        });
    });
    $('.form-button.link').bind('click', function() {
        var route = $(this).data('route');
        toggleForm(route);
    });
    /*$('.login-form').submit(function() {

    });
    $('.login-form').submit(function() {

    });
    $('.login-form').submit(function() {

    });*/
});