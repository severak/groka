<?php
// groonga minimal client
class groonga
{
	protected $_url;

	function __construct($url)
	{
		$this->_url = $url;
	}
	
	function cmd($command, $params=[], $data=null)
	{
		$curl = curl_init();
		if ($data==null) {
			curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $this->_url . "/d/" . $command . "?" . http_build_query($params),
			]);
		}	else {
			curl_setopt($curl, CURLOPT_URL, $this->_url . "/d/" . $command. "?" . http_build_query($params));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($curl, CURLOPT_POST, true );
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); 
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		}
		
		$resp = curl_exec($curl);
		curl_close($curl);
		
		$respJson = json_decode($resp, true);
		
		if (isset($respJson[1])) {
			//var_dump($respJson[0]);
			return $respJson[1];
		}
			
		return false;
	}
	
	function __call($command, $args)
	{
		return $this->cmd($command, $args[0] ?? [], $args[1] ?? null);
	}
}