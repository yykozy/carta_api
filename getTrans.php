<?php
require_once('curl.php');
mb_regex_encoding('UTF-8');

function getTranslation(){
	$cr = new CurlRequest;

	$SearchItemUrl='http://public.dejizo.jp/NetDicV09.asmx/SearchDicItemLite?Dic=EJdict&Scope=HEADWORD&Match=STARTWITH&Merge=AND&Prof=XHTML&PageSize=20&PageIndex=0&Match=EXACT&Word=';

	$fp=fopen('word.list','r');
	$urls=array();
	while($str=fgets($fp)){
		$urls[]=$SearchItemUrl.trim($str);
		if(count($urls)==10) break;
	}

	$res = $cr->getRequests($urls);
	$itemids=array();
	foreach($res as $i => $ret){
		$obj = new SimpleXMLElement($ret);
		$itemids[] = (STRING)$obj->TitleList[0]->DicItemTitle->ItemID[0];
	}

	$cr = new CurlRequest;

	$GetItemUrl='http://public.dejizo.jp/NetDicV09.asmx/GetDicItemLite?Dic=EJdict&Loc=&Prof=XHTML&Item=';

	$urls=array();
	foreach($itemids as $id){
		$urls[]=$GetItemUrl.$id;
	}

	$res = $cr->getRequests($urls);
	foreach($res as $i => $ret){
		$obj = new SimpleXMLElement($ret);
		//$itemid = (STRING)$obj->TitleList[0]->DicItemTitle->ItemID[0];
		$trans = (STRING)$obj->Body->div->div[0];
		echo $trans."\n";
		/*
		if(mb_ereg('『(.+)』',$trans,$reg)){
			$trans=$reg[1];
			var_dump($trans);
		}else{
			var_dump("hoge");
		}
		 */
	}
}
getTranslation();
