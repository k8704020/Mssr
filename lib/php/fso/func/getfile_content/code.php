<?php
//-------------------------------------------------------
//函式: getfile_content()
//用途: 載入檔案
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function getfile_content($file,$fso_enc,$allow_types=array('txt'),$return_type='array'){
    //---------------------------------------------------
    //載入檔案
    //---------------------------------------------------
    //$file         檔案(含路徑)
    //$fso_enc      檔案系統語系編碼
    //$allow_types  允許類型    預設: array('txt')
    //$return_type  回傳類型
    //              'array'     傳回陣列,預設
    //              'string'    傳回字串
    //---------------------------------------------------

        //參數驗整
        if(!isset($file)||(trim($file)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            return false;
        }
        if(!isset($allow_types)||(empty($allow_types))){
            $allow_types=array('txt');
        }
        if(!isset($return_type)||(trim($return_type)=='')){
            $return_type='array';
        }

        //編碼處理
        $file       =trim($file);
        $file_ext   =explode('.',basename($file));
        $file_ext   =strtolower($file_ext[1]);

        $enc_array  =array('UTF-8','BIG-5','GB2312');
        $str_enc    =mb_detect_encoding($file,$enc_array);
        $file_enc   =mb_convert_encoding($file,$fso_enc,$str_enc);
        $allow_types=array_map('strtolower',$allow_types);

        if(1==2){//除錯用
            echo "file    ={$file    }"."<br/>";
            echo "file_ext={$file_ext}"."<br/>";
            echo "str_enc ={$str_enc }"."<br/>";
            echo "file_enc={$file_enc}"."<br/>";
            echo "<pre>";
            print_r($allow_types);
            echo "</pre>";
        }

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
        if(!in_array($file_ext,$allow_types)){
            //非允許類型
            //echo '非允許類型'.'<br/>';
            return false;
        }
        if(($cont=@file_get_contents($file_enc))===false){
            //讀檔錯誤
            //echo '讀檔錯誤'.'<br/>';
            return false;
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