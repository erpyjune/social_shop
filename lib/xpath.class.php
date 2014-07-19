<?php

Class XPathManager {
	private $url;
	private $dom;
	private $xPath;
	public function XPathManager($strUrl, $opt) {
		if ($opt == 1) {
		$this--->url = $strUrl;
		// 객체 생성
		$this->dom = new DOMDocument();
		$this->dom->loadHTMLFile($strUrl);
		$this->xPath = new DOMXPath($this->dom);
	}
	public function getObjectList($strQuery) {
		$result = $this->xPath->query($strQuery);
		return $result;
	}
}

?>

