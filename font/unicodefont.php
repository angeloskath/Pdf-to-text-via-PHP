<?php

if (!defined('UNICODEFONT'))
{
	define('UNICODEFONT',true);


class UnicodeFont extends Font
{
	
	protected $obj;
	
	protected function __construct(PdfObject $obj) {
		parent::__construct();
		
		$this->obj = $obj;
		
		if ($this->obj->getDictionary()!==null && $this->obj->getDictionary()->find('/tounicode')!==FALSE)
		{
			$this->parseMap();
		}
	}
	
	protected function parseMap() {
		$cmap = $this->obj->getDictionary()->getValue('/ToUnicode')->toString(true);
		if ($cmap==null) return;
		
		$x = strpos($cmap,'endcodespacerange');
		if ($x===false) return;
		$cmaplen = strlen($cmap);
		$key = $value = null;
		while ($x < $cmaplen)
		{
			if ($cmap[$x]=='<')
			{
				$x++;
				$tkey = '';
				while ($cmap[$x]!='>')
				{
					$tkey .= $cmap[$x];
					$x++;
				}
				
				while ($cmap[$x]!='<') $x++;
				
				$x++;
				$tval = '';
				while ($cmap[$x]!='>')
				{
					$tval .= $cmap[$x];
					$x++;
				}
				
				$val = '';
				for ($i=0;$i<strlen($tval);$i+=2)
				{
					$val .= sprintf( "%c" , intval(base_convert(substr($tval,$i,2),16,10)) );
				}
				
				$val = iconv('UTF-16BE','UTF-8',$val);
				
				$this->map[$tkey] = $val;
			}
			$x++;
		}
	}
	
}

}

?>
