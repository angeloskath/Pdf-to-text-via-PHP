<?php

if (!defined('PDFVALUEPARSER'))
{
	define('PDFVALUEPARSER',true);

include ('pdfdictionary.php');
include ('pdfreferencevalue.php');
include ('pdfstream.php');

class PdfValueParser
{
	public static function ParseValue(&$str,&$i, Pdf $pdf,PdfObject $o = null) {
		$value = null;
		$len = strlen($str);
		if ($i>=$len) return null;
		switch ($str[$i])
		{
			case '(':
				$value = self::ParseString($str,$i);
				break;
			case '/':
				$value = self::ParseName($str,$i);
				break;
			case '[':
				$value = self::ParseArray($str,$i);
				break;
			case '<':
				if ($str[$i+1]=='<')
				{
					$value = self::ParseDic($str,$i, $pdf);
				}
				break;
			case 'n':
				if ($str[$i+1]=='u' && $str[$i+2]=='l' && $str[$i+3]=='l')
				{
					$value = null;
					break;
				}
			case 'f':
				if ($str[$i+1]=='a' && $str[$i+2]=='l' && $str[$i+3]=='s' && $str[$i+4]=='e')
				{
					$value = false;
					break;
				}
			case 't':
				if ($str[$i+1]=='r' && $str[$i+2]=='u' && $str[$i+3]=='e')
				{
					$value = true;
					break;
				}
			default:
				$value = self::ParseOther($str,$i,$pdf,$o);
		}
		return $value;
	}
	
	public static function ParseName(&$str,&$i) {
		$val = $str[$i++];
		$len = strlen($str);
		while ($i<$len && ctype_graph($str[$i]) && $str[$i]!='/' && $str[$i]!='[')
		{
			$val .= $str[$i];
			$i++;
		}
		return $val;
	}
	public static function ParseString(&$str, &$i) {
		$val = '';
		switch ($str[$i])
		{
			case '(':
				$i++;
				$paropened = 0;
				$break = false;
				while (1)
				{
					$c = $str[$i];
					if ($c==')')
					{
						$paropened --;
						if ($paropened<0)
						{
							return $val;
						}
					}
					else if ($c=='(')
					{
						$paropened++;
					}
					else if ($c=='\\')
					{
						$nc = $str[$i+1];
						if ($nc=='(' || $nc==')')
						{
							$val .= $nc;
							$i+=2;
							continue;
						}
					}
					$val .= $c;
					$i++;
				}
				break;
		}
		return $val;
	}
	public static function ParseDic(&$str, &$i,Pdf $pdf) {
		$cnt=0;
		$x_good = $y_good = 0;
		$x = strpos($str,'<<',$i);
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
		$y = strpos($str,'>>',$i);
		while ($y!==FALSE && $cnt>0)
		{
			$cnt--;
			$y_good = $y;
			$y = strpos($str,'>>',$y+1);
		}
		$x+=2;
		$i+=$y_good-$x_good;
		return new PdfDictionary(substr($str,$x_good,$y_good-$x_good),$pdf);
	}
	public static function ParseArray(&$str,&$i) {
		return null;
	}
	public static function ParseOther(&$str,&$i,Pdf $pdf,PdfObject $o=null) {
		$val_raw = '';
		$x = strpos($str,'/',$i);
		if ($x===FALSE)
		{
			$x = strpos($str,'>',$i);
			if ($x===FALSE)
			{
				$x = strlen($str);
			}
		}
		$val_raw = trim(substr($str,$i,$x-$i));
		if (is_numeric($val_raw))
		{
			$val = 0 + $val_raw;
			$vs = (string)$val;
			$i += strlen($vs);
			return $val;
		}
		else
		{
			if ($val_raw=="endobj") return null;
			
			$matches = array();
			if (preg_match('/^(\d+) (\d+) R$/',$val_raw,$matches))
			{
				$i += strlen($matches[0]);
				return new PdfReferenceValue($matches[1],$matches[2]);
			}
			
			if ($val_raw=='') return null;
			
			if (($x = strpos($str,'stream'))!==FALSE)
			{
				$y = strpos($str,'endstream');
				// +7 for 'stream\n'
				$x+=7;
				return new PdfStream(substr($str,$x,$y-$x),$pdf,$o);
			}
			
			//var_dump($val_raw,$str);
		}
		return null;
	}
	
	public static function getType(&$val) {
		if (is_object($val)) return get_class($val);
		else return gettype($val);
	}

}

}

?>
