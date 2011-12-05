<?php

if (!defined('PDFDICTIONARY'))
{
	define('PDFDICTIONARY',true);

include ('pdfvalueparser.php');

class PdfDictionary
{
	const DIC_END = '>>';
	const DIC_END_LEN = 2;
	
	protected $pdf;
	
	protected $dic_raw;
	protected $dic =  array();
	
	public function __construct($data, Pdf $pdf) {
		$this->pdf = $pdf;
		$this->dic_raw = $data;
		$this->parse();
	}
	
	public function debug() {
		return $this->dic;
	}
	
	public function getDicSize() {
		// +4 for << >>
		return strlen($this->dic_raw)+4;
	}
	
	public function find($keypart) {
		$kp = strtolower($keypart);
		foreach ($this->dic as $k=>$v) {
			if (strpos(strtolower($k),$kp)!==FALSE) return $k;
		}
		return false;
	}
	public function getValue($key) {
		if (!isset($this->dic[$key])) return null;
		if (is_object($this->dic[$key])) return $this->dic[$key]->getValue($this->pdf);
		else return $this->dic[$key];
	}
	
	public function getRawValue($key) {
		if (!isset($this->dic[$key])) return null;
		if (is_object($this->dic[$key])) return $this->dic[$key]->getObject($this->pdf);
		else return $this->dic[$key];
	}
	
	protected function parse() {
		$len = strlen($this->dic_raw);
		$key = $value = null;
		for ($i=0;$i<$len;)
		{
			if ($this->dic_raw[$i]!='/')
			{
				$i++;
				continue;
			}
			else
			{
				$key = PdfValueParser::ParseName($this->dic_raw,$i);
				while ($i<$len && !ctype_graph($this->dic_raw[$i]) && $this->dic_raw[$i]!='/')
				{
					$i++;
				}
				$value = PdfValueParser::ParseValue($this->dic_raw,$i,$this->pdf);
				if ($key!=null)
				{
					$this->dic[$key] = $value;
				}
			}
		}
	}

	public static function CreateFromObjString(Pdf $pdf,$str) {
		$cnt=0;
		$x_good = $y_good = 0;
		$x = strpos($str,'<<');
		if ($x===FALSE)
		{
			return null;
		}
		$x_good = $x+2;
		while ($x!==FALSE)
		{
			$cnt++;
			$x = strpos($str,'<<',$x+1);
		}
		$y = strpos($str,'>>');
		while ($y!==FALSE && $cnt>0)
		{
			$cnt--;
			$y_good = $y;
			$y = strpos($str,'>>',$y+1);
		}
		return new PdfDictionary(substr($str,$x_good,$y_good-$x_good),$pdf);
	}

}

}

?>
