<?php
//-------------------------------------------------------
//函式: mk_dir()
//用途: 建立目錄
//日期: 2011年12月6日
//作者: jeff@max-life
//-------------------------------------------------------

    function mk_dir($path,$mode=0777,$recursive=true,$fso_enc='BIG5'){
    //---------------------------------------------------
    //建立目錄
    //---------------------------------------------------
    //$path     路徑
    //
    //$mode     權限,預設 0777
    //          組成:
    //              擁有者  擁有者所在群組  everyone
    //
    //          權限:
    //              1   執行(execute)
    //              2   寫入(writeable)
    //              4   讀取(readable)
    //
    //$recursive  是否回遞路徑,預設 true
    //$fso_enc    檔案系統語系編碼,預設 BIG5
    //---------------------------------------------------

        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($mode)||(trim($mode)=='')){
            $mode=0777;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $page_enc=mb_internal_encoding();
        $path_enc=mb_convert_encoding($path,$fso_enc,$page_enc);

        if(!file_exists($path_enc)){
            if(false!==@mkdir($path_enc,$mode,$recursive)){
                //print_r '建立 ok'.'<br/>';
                @chmod($path_enc,$mode);
                return true;
            }else{
                //echo '建立 fail'.'<br/>';
                return false;
            }
        }else{
            //echo '目錄已存在'.'<br/>';
            return false;
        }
    }
?>
