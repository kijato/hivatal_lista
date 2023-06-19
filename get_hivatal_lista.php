
<?php 

$tempFile = date("Y.m.d").'.tmp'; 
$pdfFile = 'hivatal_lista.pdf';
$xmlFile = 'hivatalok.xml';

file_put_contents($tempFile, date("y:m:d h:i:s"), LOCK_EX);

if ( file_exists($xmlFile) && date("Y.m.d")===date("Y.m.d",filemtime($xmlFile)) ) {
	//echo "The current file is available.";
} else {
	if ( $pdfRawData = file_get_contents('https://tarhely.gov.hu/hivatalkereso/'.$pdfFile, true) ) {
		//echo "File downloaded successfully";
		$start = strpos($pdfRawData, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>');
		//$start = strpos($pdfRawData, '<Hivatalok>');
		$stop = strpos($pdfRawData, '</Hivatalok>');
		file_put_contents($xmlFile, substr($pdfRawData,$start,($stop-$start)+strlen('</Hivatalok>')), LOCK_EX);
	} else {
		//echo "File downloading failed.";
	}
}

unlink($tempFile);

?>

