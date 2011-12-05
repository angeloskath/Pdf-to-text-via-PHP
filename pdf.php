<?php

if (!defined('PDF_CLASS'))
{
	define('PDF_CLASS',true);

include('pdfobject.php');

class Pdf
{

	protected $data;
	protected $objs;
	protected $fonts;

	public function __construct($file) {
		$this->data = file_get_contents($file,FILE_BINARY);
		$this->objs = array();
		$this->fonts = array();
		$this->parse();
	}
	
	protected function parse() {
		$x = $y = 0;
		$len = strlen($this->data);
		$x = strpos($this->data,'obj');
		while ($x!==FALSE)
		{
			$x = strrpos($this->data,"\n",$x-$len);
			$y = strpos($this->data,'endobj',$x);
			$obj = new PdfObject(substr($this->data,$x+1,$y+5-$x),$this);
			$this->objs[$obj->getID()] = $obj;
			$x = strpos($this->data,'obj',$y+6);	
		}
	}
	
	public function getFont($str) {
		if (isset($this->fonts[$str])) return $this->fonts[$str];
		foreach ($this->objs as $obj)
		{
			if ($obj->getDictionary()==null) continue;
			$k = $obj->getDictionary()->find($str);
			if ($k)
			{
				$this->fonts[$str] =  Font::Load($obj->getDictionary()->getRawValue($k));
				return $this->fonts[$str];
			}
		}
	}
	
	public function getObject($id) {
		return $this->objs[$id];
	}
	
	public function getStreams() {
		$strms = array();
		foreach ($this->objs as $obj)
		{
			if ($obj->getType()=='PdfStream') $strms[] = $obj;
		}
		return $strms;
	}
	
	public function debug() {
		///*
		foreach ($this->objs as $obj)
		{
			if ($obj->getDictionary()==null) continue;
			//if (($k=$obj->getDictionary()->find('uni'))!==false)
			//{
				//var_dump($obj->getDictionary()->getValue($k)->toString());
				var_dump($obj->getID(),$obj->getDictionary()->debug());
			//}
		}
		// */
		//var_dump(substr($this->objs['28 0']->getValue()->toString($this->objs['28 0']->getDictionary()),0,400));
	}
	
}

}

?>
