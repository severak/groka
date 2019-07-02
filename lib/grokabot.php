<?php
class grokabot
{
    public $userAgent = 'grokabot';
    public $status = -1;
    public $contentType = 'application/octet-stream';
    public $url = '';

	function get($url)
	{
		$this->status = -1;
		$this->contentType = 'application/octet-stream';
		$this->url = $url;

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_USERAGENT => $this->userAgent,
			CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
			CURLOPT_CAPATH => __DIR__ . '/cacert.pem'
		]);
		$resp = curl_exec($curl);

		$this->status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		$this->contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
		$this->url = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
		
		curl_close($curl);
		
		return $resp;
	}
	
	function analyze($html)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($html);
		
		if (!$dom) return false;
		
		$xml = simplexml_import_dom($dom);
		
		$info = [];
		
		$titles = $xml->xpath('/html/head/title');
		if (!isset($titles[0])) return false;
		
		$info['title'] = (string) $titles[0];
		
		foreach ($xml->xpath('/html/head/meta') as $meta) {
			if ($meta['property']=='og:description') {
				$info['description'] = (string )$meta['content'];
			}
 		}
		
		$articles = $xml->xpath('//article');
		
		if (count($articles)==1) {
			$info['text'] = dom_import_simplexml($articles[0])->nodeValue;
		} else {
			$info['text'] = dom_import_simplexml($xml->body)->nodeValue;
		}
		
		// todo: lépe
		if (empty($info['description'])) $info['description'] = $info['title'];
		
		return $info;
	}
	
}