<?php

if (!defined('PDFSTREAM'))
{
	define('PDFSTREAM',true);


include('pdfstringparser.php');

class PdfStream
{
	protected $pdf;
	protected $str_data;
	
	protected $stringParser;
	protected $parent;
	
	public function __construct($stream_data,Pdf $pdf,PdfObject $parent) {
		$this->pdf = $pdf;
		$this->str_data = $stream_data;
		$this->stringParser = new PdfStringParser($parent);
		
		$this->parent = $parent;
	}
	
	public function toString($raw=false) {
		$dic = $this->parent->getDictionary();
		$len = $dic->getValue('/Length');
		$this->str_data = substr($this->str_data,0,$len);
		
		$filter = $dic->getValue('/Filter');
		
		switch ($filter)
		{
			case '/FlateDecode':
				$v = gzuncompress($this->str_data);
				$this->stringParser->init($v);
				if ($this->stringParser->isText())
					return $this->stringParser->Parse($dic);
				if ($raw)
					return $v;
				//else
				//	return $v;
				break;
		}
	}
}

}

?>
