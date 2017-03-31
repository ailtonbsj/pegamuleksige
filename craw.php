<?php

header("Content-type: text/html; charset=utf-8");
$ano = $_POST['ano'];

function post($url, $data){
	//echo '<hr>';
	global $cokie, $ch;
	//Initialize
	//Set UserAgent
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0");
	//Use Cookies
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cokie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cokie);
	//Method
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	//Debug with proxy
	//curl_setopt($ch, CURLOPT_PROXY, "http://20.20.0.1:8080");
	//curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
	//curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "aluno:aluno");
	//END Debug with proxy
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	$html = curl_exec($ch);
	return $html;
}

function get($url){
	//echo '<hr>';
	global $cokie, $ch;
	//Initialize
	//Set UserAgent
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0");
	//Use Cookies
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cokie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cokie);
	//Method
	curl_setopt($ch, CURLOPT_POST, 0);
	//Debug with proxy
	//curl_setopt($ch, CURLOPT_PROXY, "http://20.20.0.1:8080");
	//curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
	//curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "aluno:aluno");
	//END Debug with proxy
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	$html = curl_exec($ch);
	return $html;
}

function simpleGet($url){
	//echo('<hr>');
	$chr = curl_init();
	curl_setopt($chr, CURLOPT_USERAGENT,"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0) Gecko/20100101 Firefox/50.0");
	curl_setopt($chr, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($chr, CURLOPT_URL, $url);
	//Debug with proxy
	///curl_setopt($chr, CURLOPT_PROXY, "http://20.20.0.1:8080");
	//curl_setopt($chr, CURLOPT_PROXYPORT, 8080);
	//curl_setopt ($chr, CURLOPT_PROXYUSERPWD, "aluno:aluno");
	//END Debug with proxy
	$html = curl_exec($chr);
	curl_close($chr);
	return $html;
}

//function rm($str){
//	return str_replace('script', '', $str);
//}

///////////////////////////////////////

//get escolas
$sch_est = [];
$est = fopen("estaduais.csv", "r") or die("Unable to open file!");
while(!feof($est)) {
	$linn = fgets($est);
  $tk = explode(',',$linn);
  $cidade = $tk[0];
  $inep = $tk[1];
  $escola = str_replace("\n", '', $tk[2]);
  if(@$sch_est[$cidade] == NULL) $sch_est[$cidade] = [];
  array_push($sch_est[$cidade], $escola);
}
fclose($est);

$sch_mu = [];
$city = $_POST['cidade'];
$mu = fopen("municipais.csv", "r") or die("Unable to open file!");
while(!feof($mu)) {
  $tk = explode(',',fgets($mu));
  $cidade = $tk[0];
  $inep = $tk[1];
  $escola = $tk[2];
  if($cidade == $city) array_push($sch_mu, array('nome' => $escola, 'inep' => $inep));
}
fclose($mu);

//Init Curl
$ch = curl_init();
$cokie = tempnam ("/tmp", "CURLCOOKIE");


//Authetication
//Home
$p1 =  post("http://sige.seduc.ce.gov.br/login.asp",
	 "nrAcesso=1&professor=N");
//echo rm($p1);
usleep(500);
//Login
$p2 = post("http://sige.seduc.ce.gov.br/login.asp",
	"professor=N&site=&nr_Codigo=1&nm_Login=".$_POST['login']."&nm_Senha=".$_POST['pass']."&x=48&y=14");
//echo rm($p2);
usleep(500);


echo "<table border='1'>";
echo "<tr>";
echo "<td>Mat Sige</td><td>Censo</td><td>Nome</td><td>Data</td><td>Origem</td>";
$totalEscola = count($sch_est[$city]);
for($i = 0; $i < $totalEscola; $i++) echo "<td>". $sch_est[$city][$i] ."</td>";
echo "</tr>";


for($iEscMu = 0; $iEscMu < count($sch_mu); $iEscMu++){
	$inep = $sch_mu[$iEscMu]['inep'];
	//////// CADA ESCOLA ///////////////////




//Get Unidade de Trabalho
$codUni = "";

$utr = simpleGet("http://sige.seduc.ce.gov.br/Consulta/EscolaOfertaAno/PesquisaItem.asp?codigo=".$inep."&campo=Origem&nr_anoletivo=$ano");

$utr = str_replace(';', '\n', $utr);
$utr = str_replace('{', '\n', $utr);
$utr = str_replace(' ', '', $utr);
//echo rm($utr);
$utr = explode('\n', $utr);
foreach ($utr as $u) {
	$tk = explode('=', $u);
	if($tk[0] == "document.getElementById('cd_Unidade_Trabalho_Origem').value"){
		$codUni = str_replace("'", '', $tk[1]);
	}
}
//Get Oferta
$ofraw = get("http://sige.seduc.ce.gov.br/Academico/Relatorios/Aluno/pesquisaOfertaItens.asp?cd_Escola=" .$inep. "&ci_OfertaItem=&nr_anoletivo=$ano");
//echo rm($ofraw);
$ofert = [];
$jscmd = explode(';', $ofraw);
$jsl = count($jscmd);
foreach ($jscmd as $key => $js) {
	if($key < 2) continue;
	elseif($key > ($jsl-2)) break;
	$js = str_replace('\'', '', $js);
	$js1 = @explode('(', $js)[1];
	$js2 = @explode(')', $js1)[0];
	$js3 = @explode(',', $js2);
	$lb = preg_replace('/[^A-Za-z0-9\-]/', '', $js3[0]);
	$vl = $js3[1];
	if('9anoEnsinoFundamentalRegularManh' == $lb) $ofert[0] = $vl;
	elseif('9anoEnsinoFundamentalRegularTarde' == $lb) $ofert[1] = $vl;
}

$ano = $_POST['ano'];
//Get 9 ano Manha e Tarde
foreach ($ofert as $ofe) {
	//Relatorios
	//echo '<hr>RELATORIOS';
	$html = post("http://sige.seduc.ce.gov.br/Academico/Relatorios/Aluno/Alunos.asp",
	"num_escOrigem=$inep&
cd_Unidade_Trabalho_Origem=$codUni&
nr_AnoLetivo=$ano&
ci_ofertaitem=$ofe&");
	
	//echo rm($html).'END REL <hr>';
	usleep(500);
	$doc = new DOMDocument();
	@$doc->loadHTML($html);
	$tables = $doc->getElementsByTagName('table');
	$table = $tables->item(5);
	$trs = $table->getElementsByTagName('tr');
	for($i=2; $i < ($trs->length-2); $i++){
		echo "<tr>";
		$tr = $trs->item($i);
		//echo $tr->ownerDocument->saveHTML($tr);
		$tds = $tr->getElementsByTagName('td');
		$td = $tds->item(0);
		echo "<td>" . $td->nodeValue . "</td>";
		$td = $tds->item(1);
		echo "<td>" . $td->nodeValue . "</td>";
		$td = $tds->item(2);
		echo "<td>" . $td->nodeValue . "</td>";
		$td = $tds->item(3);
		echo "<td>" . $td->nodeValue . "</td>";

		
		echo "<td>" . $sch_mu[$iEscMu]['nome'] . "</td>";
		for($ii = 0; $ii < $totalEscola; $ii++) echo "<td>&nbsp;</td>";
		//echo $td->ownerDocument->saveHTML($td);
		//$td = $tds->item(0);
		//echo $td->ownerDocument->saveHTML($td);
		echo "</tr>";
	}
}



	//////////END CADA ESCOLA///////////////
}

echo '</table>';

//Finish Curl
curl_close($ch);
?>
