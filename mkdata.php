<?php
$fp=fopen('trans.list.bak','r');
while($str=fgets($fp)){
	$list - explode("\t",trim($str));
	if(!isset($list[1]) continue;
	$words[]=array('q'=>$list[0],'a'=>$list[1]);

}
