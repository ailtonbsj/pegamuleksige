<html>
<head>
<!-- Bootstrap Core CSS -->
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="jquery/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<style>
	table {
		font-size: 12px;
	}
</style>
</head>
<body>
<div id="wrapper">
<div class="col-lg-12">
<?php

header("Content-type: text/html; charset=utf-8");
$ano = $_POST['ano'];
$codcity = $_POST['cidade'];

include('navigate.php');

function getEscolas($m, $re){
	//Init Curl
	$html = post('http://www.fnde.gov.br/pddeinfo/index.php/pddeinfo/escola/consultarinep', "uf=CE&municipio=$m&redeensino=$re&pesquisar=Pesquisar");
	$doc = new DOMDocument();
	$doc->loadHTML($html);
	$listagem = getElementsByClass('listagem', $doc)->item(0);
	$trs = $listagem->getElementsByTagName('tr');

	$array = [];
	foreach ($trs as $i => $tr) {
	$tds = $tr->getElementsByTagName('td');
		$td0 = $tds->item(0);
		if(!is_null($td0)){
			$inep = $td0->textContent;
			$nome = getInnerHtml($tds->item(1));
			array_push($array, array('nome' => $nome, 'inep' => $inep));
	    }
	}
	return $array;
}

function estaduais($m){
	return getEscolas($m, 1);
}

function municipais($m){
	return getEscolas($m, 2);
}

//get escolas
$ch = curl_init();
$est = estaduais($codcity);
$mun = municipais($codcity);
curl_close($ch);

//Init Curl
$ch = curl_init();
$cokie = tempnam ("/tmp", "CURLCOOKIE");

//Authetication
//Home
$p1 =  post("http://sige.seduc.ce.gov.br/login.asp",
	 "nrAcesso=1&professor=N");
usleep(500);
//Login
$p2 = post("http://sige.seduc.ce.gov.br/login.asp",
	"professor=N&site=&nr_Codigo=1&nm_Login=".$_POST['login']."&nm_Senha=".$_POST['pass']."&x=48&y=14");
usleep(500);

echo "<table class='table table-striped table-bordered table-hover'>";
echo "<thead><tr>";
echo "<th>Mat Sige</th><th>Censo</th><th>Nome</th><th>Data</th><th>Origem</th>";
$totalEscola = count($est);
foreach ($est as $i => $v) {
	echo "<th>". $v['nome'] ."</th>";
}
echo "</tr></thead><tbody>";

foreach ($mun as $k => $es) {
	$inep = $es['inep'];
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
	$trs = @$table->getElementsByTagName('tr');
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

		
		echo "<td>" . $es['nome'] . "</td>";
		for($ii = 0; $ii < $totalEscola; $ii++) echo "<td>&nbsp;</td>";
		//echo $td->ownerDocument->saveHTML($td);
		//$td = $tds->item(0);
		//echo $td->ownerDocument->saveHTML($td);
		echo "</tr>";
	}
}



	//////////END CADA ESCOLA///////////////
}

echo '</tbody></table>';

//Finish Curl
curl_close($ch);
?>
</div>
</div>
</body>
</html>
