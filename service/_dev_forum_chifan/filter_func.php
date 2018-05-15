<?php


function filter($subject){

    $filter=array("去你媽","去你","你媽","黑人","榦","屌","fuck","Fuck","FUCK","FXXK","FUXK","FXCK","Fxxk","Fuxk","Fxck","幹你娘","幹","白癡","智障","ㄍㄢˋ","ㄍㄢ","娘","靠北","靠杯","靠ㄅ","ㄎㄅ","機掰","GY","gy","基掰","雞掰","雞ㄅ","ㄐㄅ","白癡","白吃","白痴","FUCK","賤","爛","屎","笨","FXUXK");
    //$rand = rand(0,32767);
    foreach ($filter as $k=>$v){
        $subject=str_replace($v,"*",$subject);
    }
 //    if (preg_match("/$rand/i", $subject)) {
 //    	 $subject="此內容已被移除";
	// } 

    return $subject;
}





?>