
<?php 

$xmlFile = 'hivatalok.xml';

if ( file_exists($xmlFile) ) {
	echo 'Az adatok forrása a ' . date("Y.m.d",filemtime($xmlFile)) . '-i <a href="https://tarhely.gov.hu/hivatalkereso/hivatal_lista.pdf">hivatal_lista.pdf</a>';
} else {
	echo "[A '$xmlFile' nevű fájl nem létezik, némi türelmet kérek, míg létrejön...]";
}
?>
