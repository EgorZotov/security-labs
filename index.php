<?
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Secure Labs</title>
    <meta charset="utf-8">
</head>

<body>
    <div class="page">
        <div class="modal-window">
            <div class="window-header">
                <span class="window-header__text">Вход в систему</span>
                <div class="window-header__close">
                </div>
            </div>
            <div class="window-content">
                <form class = "start">
                    <div class="input-wrap">
                        <input type='hidden' name = 'action' value = 'start'>
                        <label class="form-label">Введите пароль для расшифровывания:</label>
                        <input class="form-input form-input_normal" type="password" name="password"/>
                    </div>
                    <button class = "start__button">Начать работу с программой</button>
                </form>
                <form class="login-form" data-tries = "0">
                    <input type="hidden" name="action" value="login">
                    <div class="input-wrap">
                        <label class="form-label">Введите имя:</label>
                        <input class="form-input form-input_normal" type="text" name="username" />
                    </div>
                    <div class="input-wrap">
                        <label class="form-label">Введите пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="password" />
                    </div>
                    <div class="buttons-wrap row">
                        <button class="form-button">Ок</button>
                        <button type="button" class="form-button back" data-back = "close">Отмена</button>
                        <button type="button" class="form-button link" data-route="registration-form">Регистрация</button>
                    </div>
                </form>
                <form class="registration-form">
                    <input type="hidden" name="action" value="save">
                    <div class="input-wrap">
                        <label class="form-label">Введите имя:</label>
                        <input class="form-input form-input_normal" type="text" name="username" />
                    </div>
                    <div class="input-wrap">
                        <label class="form-label">Введите пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="password" />
                    </div>
                    <div class="buttons-wrap row">
                        <button class="form-button">Ок</button>
                        <button type="button" class="form-button back" data-back = "login-form" >Отмена</button>
                    </div>
                </form>
                <form class="password-form">
                    <input type="hidden" name="action" value="password-change">
                    <div class="input-wrap">
                        <label class="form-label">Введите старый пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="old-password" />
                    </div>
                    <div class="input-wrap">
                        <label class="form-label">Введите новый пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="new-password" />
                    </div>
                    <div class="buttons-wrap row">
                        <button class="form-button">Ок</button>
                        <button id="admin-route" type="button" class="form-button link hidden" data-route="admin-form">Администрирование</button>
                        <button type="button" class="form-button back" data-back = "login-form">Отмена</button>
                    </div>
                </form>
                <form class="admin-form">
                	<input type="hidden" name="action" value="admin-permission">
                    <div class="input-wrap row">
                        <label class="form-label">Имя пользователя:</label>
                        <select id = "user-selector" class="form-input form-input_normal" name="username">
                        </select>
                        <!--<input class="form-input form-input_normal" type="text" name="username" />-->
                    </div>
                    <div class="input-wrap row">
                        <label class="form-label checkbox">Блокрировка</label>
                        <input type="checkbox" id = "block" name="block" class="form-input checkbox" />
                    </div>
                    <div class="input-wrap row">
                        <label class="form-label checkbox">Парольное ограничение</label>
                        <input type="checkbox" id= "password_restrict" name="password_restrict" class="form-input checkbox" />
                    </div>
                    <div class="buttons-wrap row">
                        <button type = 'button' class="form-button back" data-back = "password-form">Отмена</button>
                        <button class="form-button">Ок</button>
                        <button type="button" class="form-button link" data-route="new-user">Новый пользователь</button>
                    </div>
                </form>
                <form class="confirm-form">
                    <input type="hidden" name="action" value="confirm">
                    <input id = "confirm-username" type="hidden" name="username">
                    <div class="input-wrap">
                        <label class="form-label">Установите пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="password" />
                    </div>
                    <div class="input-wrap">
                        <label class="form-label">Повторите пароль:</label>
                        <input class="form-input form-input_wide" type="password" name="repeat-password" />
                    </div>
                    <div class="buttons-wrap row">
                        <button class="form-button">Ок</button>
                        <button type="button" class="form-button back" data-back = "login-form">Отмена</button>
                    </div>
                </form>
                <form class = "new-user">
                    <input type="hidden" name="action" value = "newuser">
                    <div class="input-wrap">
                        <label class="form-label">Имя нового пользователя</label>
                        <input class="form-input form-input_wide" type="text" name="username" />
                    </div>
                    <div class="buttons-wrap row">
                        <button class="form-button">Ок</button>
                        <button type="button" class="form-button back" data-back = "login-form">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
        <a target='_blank' href = "/about.html" class = "about-programm">О программе</a>
        <div class="alert-box">
        	<div class="alert-box__content"></div>
        	<i class = 'alert-box__icon'></i>
        </div>
    </div>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>