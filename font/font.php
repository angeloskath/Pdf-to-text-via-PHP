<?php

if (!defined('FONT'))
{
	define('FONT',true);

class Font
{
	protected $map = array();
	
	protected function __construct() {
		for ($ch=0;$ch<10;$ch++)
		{
			for ($ch2=0;$ch2<10;$ch2++)
			{
				$this->map[$ch.$ch2] = chr(intval(base_convert($ch.$ch2,16,10)));
			}
			for ($ch2=ord('A');$ch2<ord('G');$ch2++)
			{
				$key = $ch.chr($ch2);
				$this->map[$key] = chr(intval(base_convert($key,16,10)));
				$this->map[strtolower($key)] = $this->map[$key];
			}
		}
		for ($ch=ord('A');$ch<ord('G');$ch++)
		{
			for ($ch2=0;$ch2<10;$ch2++)
			{
				$this->map[chr($ch).$ch2] = chr(intval(base_convert(chr($ch).$ch2,16,10)));
			}
			for ($ch2=ord('A');$ch2<ord('G');$ch2++)
			{
				$key = chr($ch).chr($ch2);
				$this->map[$key] = chr(intval(base_convert($key,16,10)));
				$this->map[strtolower($key)] = $this->map[$key];
			}
		}
	}
	
	public function map($c) {
		if (!isset($this->map[$c]))
			return $c;
		else
			return $this->map[$c];
	}
	
	protected static $defaultFont;
	public static function Font() {
		if (self::$defaultFont==null)
		{
			self::$defaultFont = new Font();
		}
		return self::$defaultFont;
	}
	public static function Load(PdfObject $font) {
		if ($font->getDictionary()->find('touni'))
		{
			return new UnicodeFont($font);
		}
	}
	
}

include ('unicodefont.php');

}

?>
