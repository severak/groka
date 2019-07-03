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
		libxml_use_internal_errors(false);
		
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

	function add($url, groonga $g)
    {
        $oldUrlToRemove = false;

        // TODO - there is something mysterious - some redirects returns false on curl_exec
        // See http://vmezerach.svita.cz/autor vs something other
        $html = $this->get($url);

        if (in_array($this->status, [301, 302])) {
            $newUrl = $this->url;
            if (str_replace('https://', 'http://', $newUrl)==$url) {
                // this is HTTP to HTTPS upgrade, we should follow it
                $html = $this->get($newUrl);
                $oldUrlToRemove = $url;
                // echo 'INFO: HTTP upgrade' . PHP_EOL;
            } else {
                $html = $this->get($newUrl);
                // echo 'INFO: redir to ' . $newUrl . PHP_EOL;
            }
            $url = $newUrl;
        }

        if (!$html) return 'cannot download ' . $url;

        if ($this->status!=200) {
            return 'status '.$this->status;
        }

        $mimeType = strtok($this->contentType, ';');
        if ($mimeType=='text/html') {
            $info = $this->analyze($html);
            if (!isset($info['title'])) return 'cannot find title';

            $info['_key'] = $url;
            $info['title'] = cleantext($info['title']);
            $info['description'] = cleantext($info['description']);
            $info['text'] = cleantext($info['text']);
            if (!$info) return 'cannot analyze HTML';
        } elseif ($mimeType=='text/plain') {
            $info['_key'] = $url;
            $info['title'] = basename(parse_url($url, PHP_URL_PATH));
            $info['description'] = $info['title']; // TODO - something better, like first non blank line
            $info['text'] = $html;
        } else {
            return 'unsupported mime type ' . $mimeType;
        }

        if ($g->load(['table'=>'groka'], $info)) {

            if ($oldUrlToRemove) {
                $g->delete(['table'=>'groka', 'key'=>$url]);
            }

            return true;
        }

        return 'cannot save';
    }
	
}