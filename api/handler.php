<?
session_start();
include_once 'encrypt.php';



switch($_POST['action']){
	case 'test':
		$file = dirname(dirname(__FILE__))."\secret-files\jest.txt";
		$result = file_put_contents($file, 'Отработал');
		break;
	case 'start':
		/*$cipher = 'AES-256-CBC';*/
		/*AES-256-ECB*/
		/*$key = hash('md4',7771);
		$ivlen = openssl_cipher_iv_length($cipher);
    	$iv = openssl_random_pseudo_bytes($ivlen);
    	$cryptoData = array('cipher'=>$cipher,'key'=>$key,'iv'=>$iv);
    	$cryptoData = utf8ize($cryptoData);
    	$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
    	$cryptData = dirname(dirname(__FILE__))."\secret-files\cryptData.json";
    	$fileCrypt = dirname(dirname(__FILE__))."\secret-files\crypt.txt";
    	$fileDecrypt = dirname(dirname(__FILE__))."\secret-files\decrypt.json";
    	$file_content = file_get_contents($file);
    	$file_crypt = openssl_encrypt($file_content, $cryptoData['cipher'], $cryptoData['key'], $options=0, $cryptoData['iv']);
    	$file_decrypt = openssl_decrypt($file_crypt, $cryptoData['cipher'], $cryptoData['key'], $options=0, $cryptoData['iv']); 
		file_put_contents($fileCrypt, $file_crypt);
		file_put_contents($fileDecrypt, $file_decrypt);
		file_put_contents($cryptData, json_encode($cryptoData));*/
		if($_POST['password'] == 'admin'){
			decryptToFile();
			$respond['status'] = 'ok';
		} else {
			$respond['status'] = 'error';
		}
		echo json_encode($respond);
		break; 
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
		$hashedPassword = hash('md4',$_POST['password']);
		$logged_in = false;
		$isNew = false;
		$respond = array();
		if(!empty($data)){
			foreach ($data as $db_user) {
				if($_POST['username']=='admin' && hash_equals($db_user['password'] , $hashedPassword) && $db_user['isAdmin'] == true) {
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
				} else if($db_user['username'] == $_POST['username']){
					if(hash_equals($db_user['password'],$hashedPassword)){
						if($db_user['password_restrict'] || $db_user['block']){
							if($db_user['password_restrict']){
								if(preg_match('/[A-Za-z]/',$_POST['password']) && preg_match('/[0-9]/',$_POST['password']) && preg_match('/p{P}/',$_POST['password'])) {
									$logged_in = true;	
								} else {
									$respond['reason'] = 'password_restricted';
									$_SESSION['username'] = $db_user['username'];
								}
								if($db_user['block']){
									$respond['reason'] = 'blocked';
								}
							}
						} else {
							$logged_in = true;
							$_SESSION['username'] = $db_user['username'];
						}
					} else {
						$respond['reason'] = 'wrong_password';
					}
				} 
				if($logged_in || $isNew) {
					$respond['status'] = 'success';
				} else {
					$respond['status'] = 'error';
				}
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
		$hashedPassword = hash('md4',$_POST['old-password']);
		foreach ($data as $db_user) {
			if($db_user->username == $_SESSION['username'] && hash_equals($db_user->password,$hashedPassword)){
				if($db_user->password_restrict && !preg_match('/[A-Za-z]/',$_POST['password']) && !preg_match('/[0-9]/',$_POST['password']) && !preg_match('/p{P}/',$_POST['password'])){
					$respond['role'] = 'wrong_pattern';
				} else { 
					$db_user->password = hash('md4',$_POST['new-password']);
					$valid = true;
				}
			} 
		}
		if($valid){
			$respond['status'] = 'success';
			$result = encryptToFile(json_encode($data));
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
					} else {
						$db_user['block'] = false;
					}
					if($_POST['password_restrict']=='on'){
						$db_user['password_restrict'] = true;	
					} else {
						$db_user['password_restrict'] = false;
					}
					$valid = true;
				}
			}
			if($valid){
				$respond['status'] = 'success';
				encryptToFile(json_encode($data));
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
		unlink($file);
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
		//file_put_contents($file, json_encode($data));
		encryptToFile(json_encode($data));
		echo json_encode($respond);
		break;
	case 'confirm':
		$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$file_content = file_get_contents($file);
		$data = json_decode($file_content,true);
		$finded = false;
		foreach ($data as &$db_user) {
			if($db_user['username'] == $_POST['username'] && $db_user['isNew'] == true ) {
				$db_user['password'] = hash('md4', $_POST['password']);
				$db_user['isNew'] = false;
				$finded = true;
			}
		}
		if($finded){
			encryptToFile(json_encode($data));
			//file_put_contents($file, json_encode($data));
			$respond['status'] = 'success';
		} else {
			$respond['status'] = 'error';
			$respond['reason'] = 'not_found';
		}
		echo json_encode($respond);
		break;
 }
// закрываем