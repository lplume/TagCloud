<?php

class TagCloud {
	private $text;
	private $occurency;
	private $fontSize;
	private $minFontSize, $maxFontSize;
	private $maxOccurency, $minOccurency;

	public function __construct($text, $minFontSize = 12, $maxFontSize = 240) {
		$this->text = $this->sanitize($text);
		$temp = explode(';', $this->text);
		$this->occurency = array();
		foreach($temp as $t) {
			if($t == null) continue;
			if(array_key_exists($t, $this->occurency))
				$this->occurency[$t]++;
			else
				$this->occurency[$t] = 1;
		}
		$this->minFontSize = $minFontSize;
		$this->maxFontSize = $maxFontSize;
		$this->minOccurency = $this->maxOccurency = 0;
	}
	
	public function generateFontSizeLinear() {
		$this->minOccurency = min($this->occurency);
		$this->maxOccurency = max($this->occurency);
		$deltaFreq = $this->maxOccurency - $this->minOccurency;
		$this->fontSize = array();
		foreach($this->occurency as $word => $freq) {
			$this->fontSize[$word] = ceil(($this->maxFontSize * ($freq - $min)) / $deltaFreq); 
		}
		print_r($this->fontSize);
	}

	public function generateFontSizeLog() {
		$this->minOccurency = min($this->occurency);
		$this->maxOccurency = max($this->occurency);
		$deltaFreq = log($this->maxOccurency - $this->minOccurency);
		$this->fontSize = array();
		foreach($this->occurency as $word => $freq) {
			$this->fontSize[$word] = round(($this->maxFontSize * log($freq - $min + 1)) / $deltaFreq); 
		}
		print_r($this->fontSize);
	}

	public function generateFontSizePow() {
		$this->minOccurency = min($this->occurency);
		$this->maxOccurency = max($this->occurency);
		$deltaFreq = pow(($this->maxOccurency - $this->minOccurency), 2);
		$this->fontSize = array();
		foreach($this->occurency as $word => $freq) {
			$this->fontSize[$word] = ceil(($this->maxFontSize * pow(($freq - $min), 2)) / $deltaFreq); 
		}
		print_r($this->fontSize);
	}
	
	public function getMinFontSize() {
		return $this->minFontSize;
	}
	
	public function getMaxFontSize() {
		return $this->maxFontSize;
	}
	
	public function setMinFontSize($minFontSize) {
		return $this->minFontSize = $minFontSize;
	}
	
	public function setMaxFontSize($maxFontSize) {
		return $this->minFontSize = $maxFontSize;
	}
	
	public function getOccurency() {
		return $this->occurency;
	}
	
	public function occurenciesToString() {
		$occurencyString = "";
		foreach($this->occurency as $key => $value) {
			$occurencyString .= "$key : $value\n";
		}
		return $occurencyString;
	}

	private function sanitize($line) {
		$sanitized = "";
		$temp = trim($line);
		$temp = strtolower($temp);
		$sanitized = preg_replace("/[\W]+\s?/", ';', $temp);
		return $sanitized;
	}

	private function getOccurencies() {
		return $this->occurency;
	}

	static function openFile($filename) {
		$text = "";
		$resource = @fopen($filename, 'r');
		if(!$resource) {
			echo "cannot open $filename for reading.\n";
			exit;
		}
		while(!feof($resource)) {
			$buffer = fgetss($resource);
			$text .= $buffer;
		}
		return $text;
	}
}

$text = TagCloud::openFile("http://www.json.org/json.js");
$tc = new TagCloud($text);
$tc->generateFontSizeLog();
