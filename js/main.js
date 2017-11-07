/*$(window).bind('beforeunload', function(eventObject) {
    var returnValue = undefined;
    $.post('/api/handler.php', {action:'test'}, function(res) {
        console.log(res);
    });
    eventObject.returnValue = returnValue;
    return returnValue;
}); */

$(document).ready(function() {


    function showAlert(text) {
        $('.alert-box__content').text(text);
        $('.alert-box').animate({ right: 0 }).delay(5000).animate({ right: -290 });
    }

    function toggleForm(formSelector) {
        //if (formSelector == "admin-form") {
        /*$.post('/api/handler.php', { action: 'users' }, function(res) {
                    res = JSON.parse(res);
                    if (res.status && res.status == "success") {
                        res.users.forEach(function(user, key) {
                            var restricted = user.password_restrict ? true : false;
                            var admin = user.admin ? true : false;
                            var blocked = user.blocked ? true : false;
                            $('#user-selector').append('<option data-restricted="'+restricted+'" data-blocked="'+blocked+'" data-admin="'+admin+'" value="' + user.username + '">' + user.username + '</option>');
                        });
                        $('form').fadeOut('700').promise().done(function() {
                            $('.' + formSelector).fadeIn('1500');
                        });
                    } else {
                        showAlert('Нет Пользователей');
                    }
                });
            } else {*/
        $('form').fadeOut('700').promise().done(function() {
            $('.' + formSelector).fadeIn('1500');
        });

        /*}*/
    }

    function closeProgram() {
        $('form').fadeOut('700').promise().done(function() {
            $.post('api/handler.php', { action: 'clear' }, function(res) {
                console.log(res);
                res = JSON.parse(res);
                if (res.status && res.status == 'success') {
                    $('.start').fadeIn('1500');
                }
            });
        });
    }


    /*function startProgram() {
        console.log('работает');
        $('.start').fadeOut('700').promise().done(function() {
            $.post('api/handler.php', { action: 'start' }, function(res) {
                console.log(res);
            });
        });
    }*/
    $('.start').submit(function(e) {
        e.preventDefault();
        $.post('api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status == "ok") {
                $('.start').fadeOut('700').promise().done(function() {
                    $('.login-form').fadeIn('1500');
                });
            } else {
                showAlert('Неверный пароль');
            }
        });
    });
    //$('.start__button').bind('click', startProgram);
    $('.window-header__close').bind('click', closeProgram);

    $('.login-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                if (res.role == 'admin') {
                    if (res.users.length) {
                        $('#user-selector').empty();
                        res.users.forEach(function(user, key) {
                            var restricted = user.password_restrict ? true : false;
                            var blocked = user.block ? true : false;
                            $('#user-selector').append('<option data-restricted="' + restricted + '" data-blocked="' + blocked + '" value="' + user.username + '">' + user.username + '</option>');
                        });
                        var first_restircted = res.users[0].password_restrict ? true : false;
                        var first_blocked = res.users[0].block ? true : false;
                        $('#password_restrict').prop('checked', first_restircted);
                        $('#block').prop('checked', first_blocked);
                    }
                    $('#admin-route').removeClass('hidden');
                    toggleForm('password-form');
                } else if (res.role == 'new-user') {
                    $('#confirm-username').val(res.user);
                    toggleForm('confirm-form');
                } else {
                    $('#admin-route').addClass('hidden');
                    toggleForm('password-form');
                }
            } else {
                switch (res.reason) {
                    case 'blocked':
                        showAlert('Пользователь заблокирован');
                        break;
                    case 'password_restricted':
                        showAlert('Введено ограничение');
                        toggleForm('password-form');
                        break;
                    case 'wrong_password':
                        var tries = parseInt($('.login-form').data('tries'));
                        $('.login-form').data('tries', ++tries);
                        showAlert('Неудачных попыток : ' + tries);
                        $('.login-form').data('tries', tries);
                        if (tries == 3) {
                            $('.login-form').data('tries', tries);
                            closeProgram();
                        }
                        break;
                    default:
                        showAlert('Пользователь не найден');
                        break;
                }
            }
        });
    });

    $('#user-selector').change(function() {
        var $currentOption = $(this).find('option[value=' + $(this).val() + ']');
        console.log($currentOption);
        var blocked = $currentOption.data('blocked');
        var restricted = $currentOption.data('restricted');
        $('#password_restrict').prop('checked', restricted);
        $('#block').prop('checked', blocked);
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
            } else if(res.status = "no_match" && !res.role){
                showAlert('Неверный старый пароль');
            } else if(res.status = "no_match" && res.role == 'wrong_pattern'){
                showAlert('Только Цифры,Знаки препинания,Буквы');
            }
        });
    });

    $('.new-user').submit(function(e) {
        e.preventDefault();
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                showAlert('Новый пользователь создан');
                $('#user-selector').append('<option data-restricted="false" data-blocked="false" value="' + res.user.username + '">' + res.user.username + '</option>');
                toggleForm('admin-form');
            }
        });
    });
    $('.admin-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        console.log(data);
        $.post('/api/handler.php', $(this).serialize(), function(res) {
            console.log(res);
            res = JSON.parse(res);
            if (res.status && res.status == 'success') {
                var $currentOption = $('#user-selector').find('option[value=' + $('#user-selector').val() + ']');
                $currentOption.data('blocked', $('#block').prop('checked'));
                $currentOption.data('restricted', $('#password_restrict').prop('checked'));
                showAlert('Права изменены');
            }
        });
    });
    $('.confirm-form').submit(function(e) {
        e.preventDefault();
        var password = $(this).find('input[name=password]').val();
        var repeat_password = $(this).find('input[name=repeat-password]').val();
        console.log(password);
        console.log(repeat_password);
        if (password == repeat_password) {
            $.post('/api/handler.php', $(this).serialize(), function(res) {
                res = JSON.parse(res);
                if (res.status && res.status == 'success') {
                    showAlert('Пароль выставлен');
                    toggleForm('login-form');
                }
            });
        } else {
            showAlert('Пароли не совпадают');
        }
    });
    $('.form-button.link').bind('click', function() {
        var route = $(this).data('route');
        toggleForm(route);
    });

    $('.form-button.back').click(function(e) {
        e.preventDefault();
        var back = $(this).data('back')
        if (back >= 'close')
            toggleForm(back);
    });
});