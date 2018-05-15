<?php
//-------------------------------------------------------
//函式: load_filetemplate()
//用途: 載入樣板
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function load_filetemplate($file,$fso_enc,$replace_arry=array(),$return_type='array'){
    //---------------------------------------------------
    //載入樣板
    //---------------------------------------------------
    //$file         檔案
    //$fso_enc      檔案系統語系編碼
    //$replace_arry 內容取代陣列    預設:空陣列,表示不取代
    //$return_type  回傳類型
    //              'array'         傳回陣列,預設
    //              'string'        傳回字串
    //---------------------------------------------------

        //參數驗證
        if(!isset($file)||(trim($file)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            return false;
        }
        if(!isset($replace_arry)||(empty($replace_arry))){
            $replace_arry=array();
        }
        if(!isset($return_type)||(trim($return_type)=='')){
            $return_type='array';
        }

        //編碼處理
        $file     =trim($file);
        $enc_array=array('UTF-8','BIG-5','GB2312');
        $str_enc  =mb_detect_encoding($file,$enc_array);
        $file_enc =mb_convert_encoding($file,$fso_enc,$str_enc);

        //讀檔
        if(!file_exists($file_enc)){
            //檔案不存在
            //echo '檔案不存在'.'<br/>';
            return false;
        }
        if(!is_file($file_enc)){
            //不是檔案
            //echo '不是檔案'.'<br/>';
            return false;
        }
        if(($cont=@file_get_contents($file_enc))===false){
            //讀檔錯誤
            //echo '讀檔錯誤'.'<br/>';
            return false;
        }

        if(!empty($replace_arry)){
            $cont=str_replace(array_keys($replace_arry),array_values($replace_arry),$cont);
        }

        switch(strtolower($return_type)){
            case 'array':
                $cont=mb_split("\r\n",$cont);
                return $cont;
                break;
            case 'string':
                return $cont;
                break;
            default:
                $cont=mb_split("\r\n",$cont);
                return $cont;
                break;
        }
    }
?>
