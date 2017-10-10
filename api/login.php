<?
if($_POST['action'] == "save"){
	$file = dirname(dirname(__FILE__))."\secret-files\userdata.json";
	$file_content = file_get_contents($file);
	$data = (!empty($file_content)?json_decode($file_content):array());
	unset($_POST['action']);
	array_push($data,$_POST);
	$result = file_put_contents($file, json_encode($data));
	$respond = array(); 
	if ($result){
		$respond['status'] = 'success';
	} else {
		$respond['status'] = 'error';
	}
	echo json_encode($respond); 
}
 
// закрываем