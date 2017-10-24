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
		if(!empty($fileData)){
			$respond['status'] = 'success';
			$respond['users'] = $fileData;
			echo json_encode($respond);
		} else {
		  $respond['status'] = 'no_users';
		  echo json_encode($respond);
		}
		break;
	case 'login':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = json_decode($file_content,true);
		$logged_in = false;
		$isNew = false;
		$respond = array();
		if(!empty($data)){
			foreach ($data as $db_user) {
				if($_POST['username']=='admin' && $db_user['password'] == $_POST['password'] && $db_user['isAdmin'] == true) {
					$respond['status'] = 'success';
					$respond['role'] = 'admin';
					$_SESSION['username'] = $db_user['username'];
					array_shift($data);
					$respond['users'] = $data;
					$logged_in = true;
				} else if($db_user['username'] == $_POST['username'] && $db_user['isNew'] == true) {
					$respond['role'] = 'new-user';
					$respond['user'] = $db_user['username'];
					$isNew = true;
				} else if($db_user['username'] == $_POST['username'] && $db_user['password'] == $_POST['password']){
					if($db_user['password_restrict'] || $db_user['block']){
						if($db_user['password_restrict']){
							if(preg_match('/[A-Za-z]/',$db_user['password']) && preg_match('/[0-9]/',$db_user['password']) && preg_match('/p{P}/',$db_user['password'])) {
								$logged_in = true;	
							} else {
								$respond['reason'] = 'password_restricted';
							}
							if($db_user['block']){
								$respond['reason'] = 'blocked';
							}
						}
					} else {
						$logged_in = true;
						$_SESSION['username'] = $db_user['username'];
					}  
				}
			} 
			if($logged_in || $isNew) {
				$respond['status'] = 'success';
			} else {
				$respond['status'] = 'error';
			}
		} else {
			$respond['status'] = 'no_users';
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
		if(!empty($data)){
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
		} else {
			$respond['status'] = 'no_users';
		}
		echo json_encode($respond);
		break;
	case 'clear' :
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$result = file_put_contents($file, '');
		$respond['status'] = 'success';
		echo json_encode($respond);
		break;
	case 'newuser':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = json_decode($file_content,true);
		$newUser = array('username'=>$_POST['username'],'isNew'=>true);
		array_push($data,$newUser);
		$respond['status'] = 'success';
		$respond['user'] = $newUser;
		file_put_contents($file, json_encode($data));
		echo json_encode($respond);
		break;
	case 'confirm':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = json_decode($file_content,true);
		$finded = false;
		foreach ($data as &$db_user) {
			if($db_user['username'] == $_POST['username'] && $db_user['isNew'] == true ) {
				$db_user['password'] = $_POST['password'];
				$db_user['isNew'] = false;
				$finded = true;
			}
		}
		if($finded){
			file_put_contents($file, json_encode($data));
			$respond['status'] = 'success';
		} else {
			$respond['status'] = 'error';
			$respond['reason'] = 'not_found';
		}
		echo json_encode($respond);
		break;
 }
// закрываем