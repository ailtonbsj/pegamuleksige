<?php

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

function getInnerHtml( $node ) {
    $innerHTML= '';
    if(!is_null(@$node->childNodes)){
    	$children = @$node->childNodes;
    	foreach ($children as $child) {
	        $innerHTML .= $child->ownerDocument->saveXML( $child );
	    }
    }
    return $innerHTML;
}

function getElementsByClass($classname, $doc){
    $finder = new DomXPath($doc);
    return $finder->query("//*[contains(@class, '$classname')]");
}

?>