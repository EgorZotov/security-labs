$(document).ready(function() {
    function showAlert(text) {
        $('.alert-box__content').text(text);
        $('.alert-box').animate({ right: 0 }).delay(5000).animate({ right: -290 });
    }

    function toggleForm(formSelector) {
            if (formSelector == "admin-form") {
                $.post('/api/handler.php', { action: 'users' }, function(res) {
                    res = JSON.parse(res);
                    if (res.status && res.status == "success") {
                        res.users.forEach(function(user, key) {
                            $('#user-selector').append('<option name value="' + user + '">' + user + '</option>');
                        });
                        $('form').fadeOut('700').promise().done(function() {
                        	$('.' + formSelector).fadeIn('1500');
                        });
                    } else {
                    	showAlert('Нет Пользователей');
                    }
                });
            } else {
            	$('form').fadeOut('700').promise().done(function() {
            		$('.' + formSelector).fadeIn('1500');
            	});
            
            }
    }

    function closeProgram() {
        $('.modal-window').fadeOut('700').promise().done(function() {
            $.post('/api/handler.php', { action: 'clear' }, function(res) {
                res = JSON.parse(res);
                console.log(res);
                if (res.status && res.status == 'success') {
                    $('.start__program').fadeIn('1500');
                }
            });
        });
    }

    function startProgram() {
        console.log('работает');
        $('.start').fadeOut('700').promise().done(function() {
            $('.modal-window').fadeIn('1500');
        });
    }
    $('.form-button.back').click(function(e) {
    	e.preventDefault();
    	console.log($(this).closest('form').attr('class'));
    });
    $('.start__button').bind('click', startProgram);
    $('.window-header__close').bind('click', closeProgram);

    $('.login-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                (res.role == 'admin') ? toggleForm('admin-form'): toggleForm('password-form');
            } else {
                switch (res.reason) {
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
    $('.registration-form').submit(function(e) {
        e.preventDefault();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            res = JSON.parse(res);
            console.log(res);
            if (res.status && res.status == 'success') {
                showAlert('Успешная регистрация');
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