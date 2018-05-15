<?php
//-------------------------------------------------------
//函式: mk_dir_ftp()
//用途: 建立ftp目錄
//日期: 2015年03月31日
//作者: peter@mssr@team
//-------------------------------------------------------

    ////檔案系統語系編碼,本機檔案系統BIG5,線上主機檔案系統UTF-8
    //$fso_enc='UTF-8';
    //
    ////頁面編碼
    //$page_enc=mb_internal_encoding();
    //
    ////FTP資訊
    //$arry_ftp1_info=array(
    //    trim('host      ')=>trim('140.115.135.230'),
    //    trim('port      ')=>trim('21'             ),
    //    trim('account   ')=>trim('webadmin'       ),
    //    trim('password  ')=>trim('n15-c03-u19'    )
    //);
    //
    //$ftp_root ="public_html/mssr/info/user";
    //$ftp_path ="{$ftp_root}/5030/book/mbl001xxx/draw";
    //
    ////檢核資料夾
    //$_arrys_path=array(
    //    "{$ftp_root}"                    =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
    //    "{$ftp_root}/5030"               =>mb_convert_encoding("{$ftp_root}/5030",$fso_enc,$page_enc),
    //    "{$ftp_root}/5030/book"          =>mb_convert_encoding("{$ftp_root}/5030/book",$fso_enc,$page_enc),
    //    "{$ftp_root}/5030/book/mbl001xxx"=>mb_convert_encoding("{$ftp_root}/5030/book/mbl001xxx",$fso_enc,$page_enc),
    //
    //    "{$ftp_path}"                    =>mb_convert_encoding("{$ftp_path}",$fso_enc,$page_enc),
    //    "{$ftp_path}/bimg"               =>mb_convert_encoding("{$ftp_path}/bimg",$fso_enc,$page_enc),
    //    "{$ftp_path}/simg"               =>mb_convert_encoding("{$ftp_path}/simg",$fso_enc,$page_enc)
    //);
    //foreach($_arrys_path as $_path=>$_path_enc){
    //    //連接 | 登入 FTP
    //    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
    //    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
    //    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
    //        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
    //    }
    //
    //}
    ////關閉連線
    //ftp_close($ftp_conn);


    function mk_dir_ftp($conn,$path,$mode=0777,$fso_enc='BIG5'){
    //---------------------------------------------------
    //建立ftp目錄
    //---------------------------------------------------
    //$conn     ftp連線物件
    //
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
    //$fso_enc    檔案系統語系編碼,預設 BIG5
    //---------------------------------------------------

        if(!isset($conn)){
            return false;
        }
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

        //echo $path_enc.'<br/>';
        if(false===@ftp_chdir($conn,$path_enc)){
            //echo '目錄不存在'.'<br/>';
            if(false!==@ftp_mkdir($conn,$path_enc)){
                //echo '建立 ok'.'<br/>';
                @ftp_chmod($conn,$mode,$path_enc);
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
