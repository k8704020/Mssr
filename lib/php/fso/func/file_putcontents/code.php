<?php
//-------------------------------------------------------
//函式: file_putcontents()
//用途: 寫檔
//日期: 2011年12月4日
//作者: jeff@max-life
//-------------------------------------------------------

    function file_putcontents($file,$cont,$append=false,$fso_enc='BIG5'){
    //---------------------------------------------------
    //寫檔
    //---------------------------------------------------
    //$file     檔案(相對路徑|絕對路徑)
    //$cont     內容(字串|陣列)
    //$append   是否附加內容,預設 false
    //$fso_enc  檔案系統語系編碼('UTF-8'|'BIG5'|'GB2312')
    //          'BIG5'-->預設
    //---------------------------------------------------

        //參數檢驗
        if(!isset($file)||(trim($file)=='')){
            return false;
        }
        if(!isset($cont)){
            if(is_array($cont) && empty($cont)){
                return false;
            }elseif(trim($cont)==''){
                return false;
            }
        }
        if(!isset($append)||(trim($append)=='')){
            $append=false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        //處理
        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $fso_enc ='BIG5';
        $page_enc=mb_internal_encoding();
        $file_enc=mb_convert_encoding($file,$fso_enc,$page_enc);

        if((bool)$append===true){
            if(false===(@file_put_contents($file_enc,$cont,FILE_APPEND))){
                //echo "附加內容輸出失敗"."<br/>";
                return false;
            }else{
                //echo "附加內容輸出成功"."<br/>";
                return true;
            }
        }else{
            if(false===(@file_put_contents($file_enc,$cont))){
                //echo "輸出失敗"."<br/>";
                return false;
            }else{
                //echo "輸出成功"."<br/>";
                return true;
            }
        }
    }
?>