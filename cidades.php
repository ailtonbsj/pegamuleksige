<?php

include('navigate.php');

header("Content-type: text/html; charset=utf-8");

$ch = curl_init();

//Init Curl
$html = post('http://www.fnde.gov.br/pddeinfo/index.php/pddeinfo/escola/consultarinep', 'uf=CE&municipio=&redeensino=');

$doc = new DOMDocument();
$doc->loadHTML($html);
$select = $doc->getElementById('municipio');

echo getInnerHtml($select);

curl_close($ch);

?>
