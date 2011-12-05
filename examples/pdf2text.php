<?php

include ('../pdf.php');

if ($argc<2)
{
	echo "\n\tphp -f pdf2text.php filename.pdf\n\n";
	die;
}
$pdf = new Pdf($argv[1]);

foreach ($pdf->getStreams() as $obj)
{
	echo $obj->getValue()->toString();
}

?>
