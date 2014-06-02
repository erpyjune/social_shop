<?php
class EPCurl {

	var $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';

	///////////////////////////////////////////////////////////
	public function getUrl($url, $method = 'GET')
	{
		$ch = curl_init();
		switch(strtoupper($method))
		{
			case 'GET':
				curl_setopt($ch, CURLOPT_URL, $url);
				break;
			case 'POST':
				$info = parse_url($url);
				$url = $info['scheme'] . '://' . $info['host'] . $info['path'];
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $info['query']);
				break;
			default:
				return false;
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}


	///////////////////////////////////////////////////////////
	public function requestPost($init_url, $data_string) {
		$ch = curl_init('http://somedomain.com/test.php');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))); 
		$result = curl_exec($ch);
	}

	///////////////////////////////////////////////////////////
	public function requestPost2($url, $data_arr) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_arr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$result = curl_exec($ch);
	}

} // class.

?>
