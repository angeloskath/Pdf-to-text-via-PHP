<?php

if (!defined('PDFSTRINGPARSER'))
{
	define('PDFSTRINGPARSER',true);

include ('font/font.php');

class PdfStringParser
{
	protected $parent;
	
	protected $str;
	protected $len;
	protected $ispar;
	protected $istext;
	
	protected $linechange = "\n";
	protected $currentFont;
	
	public function __construct(PdfObject $o) {
		$this->parent = $o;
	}
	
	public function init($str) {
		$this->str = $str;
		$this->len = strlen($str);
		$this->ispar = null;
		$this->istext= null;
		$this->currentFont = Font::Font();
	}
	
	public function Parse(PdfDictionary $dic) {
		if ($this->isPar())
		{
			return $this->parsePar();
		}
		else
		{
			return $this->parseHex($dic);
		}
	}
	
	public function isText() {
		if ($this->istext!==null) return $this->istext;
		$cnt = 0;
		$y = 0;
		while (($y = strpos($this->str,'BT',$y))!==FALSE)
		{
			$cnt++;
			$y++;
		}
		$this->istext = ($cnt / $this->len) > 0.001;
		return $this->istext;
		
	}
	public function isPar() {
		if ($this->ispar===null)
			$this->ispar = (strpos($this->str,"(")!==FALSE);
		return $this->ispar;
	}
	
	protected function parsePar() {
		$par = 0;
		$res = '';
		$x=0;
		while ($x<$this->len)
		{
			if ($this->str[$x]=='(')
			{
				$par++;
				$x++;
				continue;
			}
			else if ($this->str[$x]==')')
			{
				$par--;
			}
			else if ($this->str[$x]=='\\')
			{
				if ($this->str[$x+1]=='(' || $this->str[$x+1]==')' )
				{
					$res .= $this->str[$x+1];
					$x+=2;
					continue;
				}
			}
			if ($par>0)
			{
				$res .= $this->str[$x];
			}
			else
			{
				if ($this->str[$x]=='T')
				{
					switch ($this->str[$x+1])
					{
						case 'j':
						case 'J':
							$res .= $this->linechange;
							break;
					}
				}
			}
			$x++;
		}
		return $res;
	}
	
	protected function parseHex($dic) {
		$hexon = false;
		$res = '';
		$hex = '';
		$x=0;
		while ($x<$this->len)
		{
			if ($this->str[$x]=='<')
			{
				$hexon = true;
				$x++;
				continue;
			}
			else if ($this->str[$x]=='>')
			{
				$hexon = false;
				for ($i=0;$i<strlen($hex);$i+=2)
				{
					$res .= $this->currentFont->map(substr($hex,$i,2));
				}
				$hex = '';
				$x++;
				continue;
			}
			
			if  ($hexon)
			{
				$hex .= $this->str[$x];
			}
			else
			{
				if ($this->str[$x]=='T')
				{
					switch ($this->str[$x+1])
					{
						case 'j':
						case 'J':
							$res .= $this->linechange;
							break;
					}
				}
				else if ($this->str[$x]=='/')
				{
					$x++;
					if ($this->str[$x]=='F' || $this->str[$x]=='f' )
					{
						$num = '';
						$x++;
						while (ctype_digit($this->str[$x]))
						{
							$num = $this->str[$x];
							$x++;
						}
						$this->currentFont = $this->parent->getFont("/F$num");
					}
				}
			}
			$x++;
		}
		
		//var_dump($dic->debug());
		if ($dic->find('/ToUnicode'))
		{
			return $this->toUnicode($dic,$res);
		}
		else
		{
			return $res;
		}
	}
	
	protected function toUnicode() {
		
	}
	
}

}

?>
