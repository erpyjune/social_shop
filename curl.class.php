<?php
class EPCurl {

	var $agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36';

	///////////////////////////////////////////////////////////
	public function requestGetDataFromUrl($url, $method = 'GET')
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
	public function requestPostDataFromUrl($url, $data_string, $request_headers) {
		$ch = curl_init();                                                                      
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true); // POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers); 
		$result = curl_exec($ch);
		$response = curl_getinfo($ch);
		curl_close ($ch);

		$code = $response['http_code'];
		echo ">> HTTP RES CODE : $code"."\n";
		return $result;
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
		curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
		$result = curl_exec($ch);
	}

} // class.

?>
