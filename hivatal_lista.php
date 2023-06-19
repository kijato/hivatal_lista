
<?php 
/*
exec() - Execute an external program
shell_exec () - Execute command via shell and return the complete output as a string
system() - Execute an external program and display the output
passthru() - Execute an external program and display raw output
*/

function execInBackground($cmd) {
    if (strtoupper(substr(php_uname('s'), 0, 3)) == "WIN"){
        pclose(popen("start /B ". $cmd, "r"));
    }
    else {
        exec($cmd . " > /dev/null &");
    }
}

$tempFile = date("Y.m.d").'.tmp';
$xmlFile = 'hivatalok.xml';

if ( !file_exists($xmlFile) || date("Y.m.d")!=date("Y.m.d",filemtime($xmlFile)) ) {
	if (file_exists($tempFile)) {
		print("A mai frissítés folymatban van. 1-2 perc türelmet kérek, melynek elteltével a keresést ismételd meg.");
	} else {
		//execInBackground('php get_hivatal_lista.php');
		//exec('pdfdetach.exe -saveall hivatal_lista.pdf' . " > /dev/null &");
		//exec('pdfdetach.exe -saveall hivatal_lista.pdf');
		exec('php get_hivatal_lista.php');
		print("A napi frissítés megkezdődött. Ez általában legfeljebb percig tart, ennek elteltével a keresést ismételd meg!");
	}
	return;
}


if ( isset($_GET["query"]) ) {
	$query = htmlspecialchars($_GET["query"]);
} else {
	die("Nincs keresőkifejezés...!");
}


$hivatalok = array();
$fejlec = array();

if (file_exists($xmlFile)) {

	$oXml = new XMLReader();

	// https://stackoverflow.com/questions/20188293/how-to-validate-xml-file-w-o-dtd
	libxml_use_internal_errors(TRUE);

	try {
		// Open XML file
		$oXml->open($xmlFile);

		$oXml->setParserProperty(XMLReader::VALIDATE, true);
		//echo $oXml->isValid() ? "valid PDF<br>" : "NOT valid PDF<br>";
		//echo $data = $oXml->readInnerXml();
		
		$hivatal = array();
		while ($oXml->read()) {
			// https://www.php.net/manual/en/class.xmlreader.php#xmlreader.props.nodetype
			switch ($oXml->nodeType) {
				case XMLReader::ELEMENT:
					$key = $oXml->name;
					if ( $key == 'Hivatalok' )
						break;
					if ( $key == 'Hivatal' ) { 
						array_push( $hivatalok, $hivatal );
						$hivatal = array();
						break;
					}
					if ( array_key_exists($key,$fejlec) ) {
						$fejlec[$key]++;
					} else {
						$fejlec[$key]=0;
					}
					break;
				case XMLReader::TEXT:
					// echo $key.': '.$oXml->value.";";
					$hivatal[$key] = $oXml->value;
					break;
				case XMLReader::END_ELEMENT:
					// echo "\n";
					break;
			}
		}
		$oXml->close();
		//var_dump($fejlec);
		//var_dump($hivatalok);
		
	} catch (Exception $e) {
		echo $e->getMessage(). ' | Try open file: '.$xmlFile;
	}
} else {
	echo "Hiányzik a 'hivatalok.xml' fájl...<br>";
}


// Fejléc:
echo "<table class=myTable><tr>";
foreach ($fejlec as $key => $value) {
	echo '<th>'.$key.'';
}
// Sorok:
$catch = false;
foreach ( $hivatalok as $hivatal ) {
	// Megfelel az adott sor a feltételnek...?
	$values = array_values($hivatal);
	foreach ( $values as $value ) {
		if( preg_match("/".$query."/i", $value) ) {
			$catch = true;
			break;
		} else {
			$catch = false;
		}
	}
	if ( $catch ) {
		echo '<tr>';
		foreach ($fejlec as $key => $counter) {
			echo '<td>';
			echo array_key_exists($key,$hivatal) ? $hivatal[$key] : '';
		}
		echo "\n";
	}
}

	
?>

