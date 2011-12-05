<?php

if (!defined('PDFREFERENCEVALUE'))
{
	define('PDFREFERENCEVALUE',true);

class PdfReferenceValue
{
	protected $id;
	protected $id1,$id2;
	
	public function __construct($id1,$id2) {
		$this->id1 = $id1;
		$this->id2 = $id2;
		$this->id = "$id1 $id2";
	}
	
	public function getValue(Pdf $pdf) {
		return $pdf->getObject($this->id)->getValue();
	}
	
	public function getObject(Pdf $pdf) {
		return $pdf->getObject($this->id);
	}
}

}

?>
