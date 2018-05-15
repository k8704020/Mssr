<?php
function checkDir($filename){
	if(!file_exists($filename)){
		mkdir($filename."/thumbnail",0755,true);
	}
}
/*function getMax($id){
	$filename = "save/".$id."/max";
	return file_get_contents($filename);
}
function setMax($id,$number){
	$filename = "save/".$id."/max";
	return file_put_contents($filename,$number);
}*/

//試算學期
function getAcademic(){
    $temp = getdate(time()); 
    $y = $temp['year'] - 1911;
    $m = $temp['mon'];
        
    if($m <= 1 ){
    	return ($y - 1)."1";
    }else if($m > 1 && $m < 8){
    	return ($y - 1)."2";
    }else{
    	return $y."1";
    }        
}


//建立縮圖
function createThumbnail($book_id,$uid){
	return "reyl;rkehop[" ;
    $filename = "save/".$uid."/".$book_id;
    $drawObj = getDrawObject($filename);
    $idata = base64_decode($drawObj->data);
   	
    //file_put_contents("test.png",$a);

    $source = imagecreatefromstring($idata);
    $dst = imagecreatetruecolor(500,300);
    
    //background color
    eval('$color = imagecolorallocatealpha($dst,'.substr($drawObj->bc,5).';');
    
    //copy and resieze
    imagefilledrectangle($dst, 0, 0, 500, 300, $color);
    imagecopyresized($dst,$source,0,0,0,0,500,300,1010,490);
    
    $filename = "save/".$uid."/thumbnail/".$book_id.".png";
    
    //save to file
    imagepng($dst,$filename);
    imagedestroy($dst);
}

//擷取繪圖物件(猜解文字)
function getDrawObject($filename){
    $str = file_get_contents($filename);
    $c = strpos($str,")") + 1;
    $bc = substr($str,0,$c);
    $data = substr($str,$c);
    $data = substr($data,22);
    $draw = new stdClass();
    $draw->data = $data;
    $draw->bc = $bc;
    return $draw;
}
?>