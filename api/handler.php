<?
/*
hash_hmac //Генерация хэша
*/
session_start();
switch($_POST['action']){
	case 'save':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = (!empty($file_content)?json_decode($file_content):array());
		$exists = false;
		unset($_POST['action']);
		foreach ($data as $db_user) {
			if($db_user->username==$_POST['username']){
				$exists = true;
			}
		}
		if($exists){
			$respond = array(); 
			$respond['status'] = 'error';
			$respond['reason'] = 'exists';
		} else {
			array_push($data,$_POST);
			$result = file_put_contents($file, json_encode($data));
			$respond = array(); 
			if ($result){
				$respond['status'] = 'success';
			} else {
				$respond['status'] = 'error';
				$respond['reason'] = 'save_error';
			}
		}
		echo json_encode($respond); 
		break;
	case 'users':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$fileData = json_decode(file_get_contents($file),true);
		echo json_encode(array_column($fileData,'username'));
		break;
	case 'login':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		if($_POST['username']=='admin' && $_POST['password']=='admin' ){
			$respond = array();
			$respond['status'] = 'success';
			$respond['role'] = 'admin'; 
		} else {
			$file_content = file_get_contents($file);
			$logged_in = false;
			$data = json_decode($file_content);
			$respond = array();
			foreach ($data as $db_user) {
				if($db_user->username == $_POST['username'] && $db_user->password == $_POST['password']){
					if($db_user->password_restrict || $db_user->block){
						if($db_user->password_restrict){
							if(preg_match('/[A-Za-z]/',$db_user->password) && preg_match('/[0-9]/',$db_user->password) && preg_match('/p{P}/',$db_user->password)) {
								$logged_in = true;	
							} else {
								$respond['reason'] = 'password_restricted';
							}
							if($db_user->block){
								$respond['reason'] = 'blocked';
							}
						} else {
							$logged_in = true;
							$_SESSION['username'] = $db_user->username;
						}  
					}
				}
			} 
			if($logged_in) {
				$respond['status'] = 'success';
			} else {
				$respond['status'] = 'error';
			}
		}
		echo json_encode($respond); 
		break;	
	case 'password-change':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$valid = false; 
		$data = json_decode($file_content);
		foreach ($data as $db_user) {
			if($db_user->username == $_SESSION['username'] && $db_user->password == $_POST['old-password']){ 
				$db_user->password = $_POST['new-password'];
				$valid = true;
			}
		} 
		if($valid){
			$respond['status'] = 'success';
			$result = file_put_contents($file, json_encode($data));
		} else {
			$respond['status'] = 'no_match';
		}
		echo json_encode($respond);
		break;	
	case 'admin-permission':

		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = json_decode($file_content,true);
		$valid = false;
		foreach ($data as &$db_user) {
			if($db_user['username'] == $_POST['username']) { 
				if($_POST['block'] =='on'){
					$db_user['block'] = true;
				}
				if($_POST['password_restrict']=='on'){
					$db_user['password_restrict'] = true;
				}
				$valid = true;
			}
		}
		if($valid){
			$respond['status'] = 'success';
			$result = file_put_contents($file, json_encode($data));
		} else {
			$respond['status'] = 'no_match';
		}
		echo json_encode($respond);
		break;
 }
// закрываем