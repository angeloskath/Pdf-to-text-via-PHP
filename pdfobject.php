<?php

if (!defined('PDF_OBJECT'))
{
	define('PDF_OBJECT',true);

include('pdfdictionary.php');

class PdfObject
{
	protected $pdf;
	
	protected $raw_data;
	
	protected $id;
	protected $type;
	protected $value;
	protected $dic;
	
	public function __construct($data,Pdf $pdf) {
		$this->pdf = $pdf;
		$this->raw_data = $data;
		$this->parseID();
		$this->dic = PdfDictionary::CreateFromObjString($this->pdf, $data);
		$this->parseData();
	}
	
	public function debug() {
		return substr($this->raw_data,0,100);
	}
	
	protected function parseID() {
		$this->id = substr($this->raw_data,0,strpos($this->raw_data,'obj')-1);
	}
	protected function parseData() {
		if ($this->dic != null)
		{
			$x = strrpos($this->raw_data,PdfDictionary::DIC_END);
			if ($x===FALSE) return;
			$x += PdfDictionary::DIC_END_LEN;
		}
		else
		{
			$x = strpos($this->raw_data,'obj');
			if ($x===FALSE) return;
			// +3 for 'obj'
			$x += 3;
		}
		while (!ctype_graph($this->raw_data[$x]))
		{
			$x++;
		}

		$this->value = PdfValueParser::ParseValue(substr($this->raw_data,0,strrpos($this->raw_data,"\nendobj")),$x,$this->pdf,$this);
		$this->type = PdfValueParser::getType($this->value);
	}
	public function getID() {
		return $this->id;
	}
	
	public  function getType() {
		return $this->type;
	}
	public function getValue() {
		return $this->value;
	}
	public function getDictionary() {
		return $this->dic;
	}
	public function getFont($str) {
		return $this->pdf->getFont($str);
	}

}
	
}

?>
