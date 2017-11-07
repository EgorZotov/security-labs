<?
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}

if(!function_exists('hash_equals')) {
  function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
  }
}

function decryptToFile(){
		$fileCrypt = dirname(dirname(__FILE__))."\secret-files\crypt.txt";
		$fileDecrypt = dirname(dirname(__FILE__))."\secret-files\userdata.json";
		$cryptKeys = dirname(dirname(__FILE__))."\secret-files\cryptData.json";
		$cryptoData = json_decode(file_get_contents($cryptKeys),true);
		$decriptedContent = openssl_decrypt(file_get_contents($fileCrypt),$cryptoData['cipher'],$cryptoData['key'], $options=0,$cryptoData['iv']); 
		file_put_contents($fileDecrypt, $decriptedContent);
}

function encryptToFile($data){
	$fileCrypt = dirname(dirname(__FILE__))."\secret-files\crypt.txt";
	$cryptKeys = dirname(dirname(__FILE__))."\secret-files\cryptData.json";
	//$cryptoData = json_decode(file_get_contents($cryptKeys),true);
	/*AES-256-ECB*/
	$cipher = 'AES-256-CBC';
	$key = hash('md4',rand());
	$ivlen = openssl_cipher_iv_length($cipher);
	$iv = openssl_random_pseudo_bytes($ivlen);
	$cryptoData = array('cipher'=>$cipher,'key'=>$key,'iv'=>$iv);
	$cryptoData = utf8ize($cryptoData);
	$encryptedData = openssl_encrypt($data, $cryptoData['cipher'], $cryptoData['key'], $options=0, $cryptoData['iv']);
	file_put_contents($fileCrypt, $encryptedData);
	file_put_contents($cryptKeys, json_encode($cryptoData));
	decryptToFile();
}