<?php

class FilterHtmlEntities extends Filter
{
	protected $quoteStyle = ENT_COMPAT;
	protected $charset;
	protected $doubleQuote = true;
	
	public function __construct($quoteStyle=ENT_COMPAT, $charset=null, $doubleQuote=true) {
		$this->quoteStyle = $quoteStyle;
		$this->charset = ($charset !== null ? $charset : Php2Go::app()->getCharset());
		$this->doubleQuote = (bool)$doubleQuote;
	}
	
	public function filter($value) {
		return htmlentities($value, $this->quoteStyle, $this->charset, $this->doubleQuote);
	}
}