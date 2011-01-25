<?php
/*
 *  Cloudy: a PHP Tag Cloud generator class
 *  Copyright (c) 2010 Mauro Pizzamiglio
 *
 *  contact : twitter @lplume
 * 
 *  This file is part of Cloudy
 * 
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */
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
  
  /**
   * Generate an array, the key will beh the word and the value its occurency.
   * This method applied a linear function to calculate
   * the occurencies.
   */
  public function generateFontSizeLinear() {
    $this->minOccurency = min($this->occurency);
    $this->maxOccurency = max($this->occurency);
    $deltaFreq = $this->maxOccurency - $this->minOccurency;
    $this->fontSize = array();
    foreach($this->occurency as $word => $freq) {
      $tmpSize = ceil(($this->maxFontSize * ($freq - $this->minOccurency)) / $deltaFreq);
      $tmpSize = ($tmpSize < $this->minFontSize) ? $this->minFontSize : $tmpSize;
      $this->fontSize[$word] = $tmpSize; 
    }
    return $this->fontSize;
  }

  /**
   * Generate an array, the key will beh the word and the value its occurency.
   * This method applied a logarithmic function to calculate
   * the occurencies.
   */
  public function generateFontSizeLog() {
    $this->minOccurency = min($this->occurency);
    $this->maxOccurency = max($this->occurency);
    $deltaFreq = log($this->maxOccurency - $this->minOccurency);
    $this->fontSize = array();
    foreach($this->occurency as $word => $freq) {
      $tmpSize = round(($this->maxFontSize * log($freq - $this->minOccurency + 1)) / $deltaFreq);
      $tmpSize = ($tmpSize < $this->minFontSize) ? $this->minFontSize : $tmpSize;
      $this->fontSize[$word] = $tmpSize; 
    }
    return $this->fontSize;
  }

  /**
   * Generate an array, the key will beh the word and the value its occurency.
   * This method applied a quadratic function to calculate
   * the occurencies.
   */
  public function generateFontSizePow() {
    $this->minOccurency = min($this->occurency);
    $this->maxOccurency = max($this->occurency);
    $deltaFreq = pow(($this->maxOccurency - $this->minOccurency), 2);
    $this->fontSize = array();
    foreach($this->occurency as $word => $freq) {
      $tmpSize = ceil(($this->maxFontSize * pow(($freq - $this->minOccurency), 2)) / $deltaFreq);
      $tmpSize = ($tmpSize < $this->minFontSize) ? $this->minFontSize : $tmpSize;
      $this->fontSize[$word] = $tmpSize; 
    }
    return $this->fontSize;
  }
  
  /**
   * Output a HTML representation
   * <div width 350px>
   *  <a dimension>word
   *  ...
   */
  public function toHTML() {
    $HTML = "<div style=\"width:350px\">\n";
    foreach ($this->fontSize as $word => $occurency) {
      $HTML .= "<a style='font-size: " . $occurency . "pt;' title='" . $occurency . " occurencies' onmouseover=\"this.style.color='red';\" onmouseout=\"this.style.color='black';\">".$word."</a>\n";
    }
    $HTML .= "</div>";
    return $HTML;
  }
  
  /**
   * Get the smallest font size
   * 
   * @return smallest font size
   */
  public function getMinFontSize() {
    return $this->minFontSize;
  }
  
  /**
   * Get the biggest font size
   * 
   * @return biggest font size
   */
  public function getMaxFontSize() {
    return $this->maxFontSize;
  }
  
  /**
   * Set the the minum font size
   * 
   * @param $minFontSize  new minimum font size to set to
   */
  public function setMinFontSize($minFontSize) {
    return $this->minFontSize = $minFontSize;
  }
  
  /**
   * Set the the maximum font size
   * 
   * @param $manFontSize  new maximum font size to set to
   */
  public function setMaxFontSize($maxFontSize) {
    return $this->minFontSize = $maxFontSize;
  }
  
  /**
   * Get the words within its occurency as an array
   * 
   * @return array key = word, value = frequency
   */
  public function getOccurency() {
    return $this->occurency;
  }
  
  /**
   * Get the words whitin occurency as a string
   * 
   * @return words and frequency as a string "key : frequency\n"
   */
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

  /**
   * Open a file and return its content
   * 
   * @params $filename  the filename (local or remote)
   * 
   * @return  the $filename contents
   */
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
?>