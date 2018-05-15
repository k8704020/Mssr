<?php
//-------------------------------------------------------
//函式: thumb_imagebysize()
//用途: 縮圖,指定高與寬
//日期: 2011年10月29日
//作者: jeff@max-life
//-------------------------------------------------------

    function thumb_imagebysize($bImg,$sImg,$thumb_width,$thumb_height,$type='same'){
    //---------------------------------------------------
    //縮圖,指定高與寬
    //---------------------------------------------------
    //$bImg         原圖
    //$sImg         縮圖
    //$thumb_width  縮圖寬度
    //$thumb_height 縮圖高度
    //$type         輸出格式,預設='same',表示輸出成原始格式
    //              允許輸出格式 png,jpg,gif
    //---------------------------------------------------

        //定義參數
        $imgtype=array(
            'jpg',
            'jpeg',
            'gif',
            'png'
        );
        $imgtype=array_map('strtolower',$imgtype);

        $convert_type=array(
            'same',
            'png',
            'jpg',
            'gif'
        );
        $convert_type=array_map('strtolower',$convert_type);

        //外掛函式檔
        if(false===@include_once('ThumbLib.inc.php')){
            //echo '無法外掛函式檔!'.'<br/>';
            return false;
        }

        //判斷參數
        if(!isset($bImg)||(trim($bImg)=='')||(!file_exists($bImg))||(!is_file($bImg))){
            //echo '原圖未指定或不存在!'.'<br/>';
            return false;
        }else{
            //判斷類型
            $info=pathinfo($bImg);
            $dirname    =$info['dirname'];
            $basename   =$info['basename'];
            $extension  =$info['extension'];
            $filename   =$info['filename'];

            if(!in_array(strtolower($extension),$imgtype)){
                //echo '原圖類型不符合允許類型!'.'<br/>';
                return false;
            }
        }

        if(!isset($sImg)||(trim($sImg)=='')){
            //echo '縮圖未指定!'.'<br/>';
            return false;
        }else{
            //判斷類型
            $info=pathinfo($sImg);
            $dirname    =$info['dirname'];
            $basename   =$info['basename'];
            $extension  =$info['extension'];
            $filename   =$info['filename'];

            if(!in_array(strtolower($extension),$imgtype)){
                //echo '縮圖類型不符合允許類型!'.'<br/>';
                return false;
            }else{
                $sImg_dir =$dirname;
                $sImg_name=$filename;
                $sImg_ext =$extension;
            }
        }

        if(!isset($thumb_width)||(trim($thumb_width)=='')||(!is_numeric($thumb_width))){
            //echo '縮圖寬度未指定,或非數值!'.'<br/>';
            return false;
        }

        if(!isset($thumb_height)||(trim($thumb_height)=='')||(!is_numeric($thumb_height))){
            //echo '縮圖高度未指定,或非數值!'.'<br/>';
            return false;
        }

        if(isset($type)&&(trim($type)!='')){
            $type=strtolower($type);
            if(!in_array($type,$convert_type)){
                //echo '輸出格式不在允許的類型!'.'<br/>';
                return false;
            }
        }

        //建立縮圖
        $thumb=PhpThumbFactory::create($bImg);
        $thumb->resize($thumb_width,$thumb_height);

        //縮圖檔名
        if($type!='same'){
            $sImg="{$sImg_dir}/{$sImg_name}.{$type}";
            //echo $sImg."<br/>";
        }

        //刪除既有縮圖
        if(file_exists($sImg)&&is_file($sImg)){
            if(false===@unlink($sImg)){
                //echo '無法刪除既有縮圖!'.'<br/>';
                return false;
            }
        }

        //儲存縮圖
        if($type!='same'){
            $thumb->save($sImg,$type);
        }else{
            $thumb->save($sImg);
        }

        return true;
    }
?>
