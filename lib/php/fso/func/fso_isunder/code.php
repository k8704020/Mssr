<?php
//-------------------------------------------------------
//函式: fso_isunder()
//用途: 是否在指定路徑下
//日期: 2011年12月6日
//作者: jeff@max-life
//-------------------------------------------------------

    function fso_isunder($path1,$path2,$fso_enc='BIG5'){
    //---------------------------------------------------
    //是否在指定路徑下
    //---------------------------------------------------
    //函式作用 : 判斷是否路徑$path2在$path1之下
    //函式回傳 : true/false
    //
    //$path1    路徑1
    //$path2    路徑2
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        //參數檢驗
        if((!isset($path1))||(trim($path1)=="")){
            return false;
        }
        if((!isset($path2))||(trim($path2)=="")){
            return false;
        }
        if((!isset($fso_enc))||(trim($fso_enc)=="")){
            $fso_enc='BIG5';
        }

        //編碼處理
        $arry_enc =array('UTF-8','BIG-5','GB2312');
        $page_enc =mb_internal_encoding();
        $path1_enc=mb_convert_encoding($path1,$fso_enc,$page_enc);
        $path2_enc=mb_convert_encoding($path2,$fso_enc,$page_enc);

        $path1_enc=realpath($path1_enc);
        $len1     =mb_strlen($path1_enc,$fso_enc);

        $path2_enc=realpath($path2_enc);
        $len2     =mb_strlen($path2_enc,$fso_enc);

        //判斷
        if(($path1_enc===false)||($path2_enc===false)){
            //路徑不存在
            return false;
        }

        if(mb_strtolower($path1_enc,$fso_enc)==mb_strtolower($path2_enc,$fso_enc)){
            //路徑相同
            return false;
        }

        if(mb_strtolower(mb_substr($path2_enc,0,$len1,$fso_enc),$fso_enc)==mb_strtolower($path1_enc,$fso_enc)){
            return true;
        }else{
            return false;
        }
    }
?>