<?php
require_once('curl.php');
mb_regex_encoding('UTF-8');

	$fp=fopen('word.list','r');
	$word_list=array();
	while($str=fgets($fp)){
		$word_list[]=trim($str);
	}

$callback=htmlspecialchars($_GET['callback'],ENT_QUOTES);
$number=$_GET['n'];
if($number>10){
	$number=3;
}
if(!is_numeric($number)){
	$number=3;
}

$q_ary=array();
$c=count($word_list);
	while(1){
	$s = rand(0,$c-1);
		if(!in_array($s,$q_ary)){
			$q_ary[]=$s;
			$words[]=array('q'=>$word_list[$s],'a'=>'');
			if(count($q_ary)==$number){
				break;
			}
		}
	}

	$cr = new CurlRequest;

	$SearchItemUrl='http://public.dejizo.jp/NetDicV09.asmx/SearchDicItemLite?Dic=EJdict&Scope=HEADWORD&Match=STARTWITH&Merge=AND&Prof=XHTML&PageSize=20&PageIndex=0&Match=EXACT&Word=';

//	foreach(array_chunk($word_list,10) as $words){

		$urls=array();
		foreach($words as $word){
			$urls[]=$SearchItemUrl.$word['q'];
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
			$words[$i]['a'] = (STRING)$obj->Body->div->div[0];
			//echo $words[$i]."\t".$trans."\n";
		/*
		if(mb_ereg('『(.+)』',$trans,$reg)){
			$trans=$reg[1];
			var_dump($trans);
		}else{
			var_dump("hoge");
		}
		 */
		}
//		usleep(1000000);
//	}

    
echo $callback."(".json_encode($words).")";


?>
