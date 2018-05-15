<?php
//-------------------------------------------------------
//函式: copy_folder()
//用途: 複製目錄
//日期: 2013年3月28日
//作者: jeff@max-life
//-------------------------------------------------------

    function copy_folder($spath,$tpath='.',$fso_enc='BIG5'){
    //---------------------------------------------------
    //函式: copy_folder()
    //用途: 複製目錄
    //---------------------------------------------------
    //$spath    來源路徑
    //$tpath    目的路徑,預設'.' 表示目前目錄下
    //$fso_enc  檔案系統語系編碼,預設'BIG5'
    //
    //本函式會從來源路徑複製所有內容,包含所有子資料夾與
    //檔案到目的路徑.
    //
    //成功傳回true
    //失敗傳回false,或錯誤訊息陣列
    //---------------------------------------------------

        //參數檢驗
        if(!isset($spath)||(trim($spath)=='')){
            return false;
        }
        if(!isset($tpath)||(trim($tpath)=='')){
            $tpath='.';
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        //編碼處理
        $arry_enc =array('UTF-8','BIG-5','GB2312');
        $page_enc =mb_internal_encoding();

        $spath_enc=mb_convert_encoding($spath,$fso_enc,$page_enc);
        $tpath_enc=mb_convert_encoding($tpath,$fso_enc,$page_enc);

        if(file_exists($spath_enc)){

            if($spath_enc==$tpath_enc){
                //echo '來源與目地相同'.'<br/>';
                return false;
            }

            if(!is_dir($spath_enc)){
                //echo '來源非目錄'.'<br/>';
                return false;
            }

            if(!file_exists($tpath_enc)){
            //目地不存在
                if(trim($tpath_enc)===''){
                    //echo '不指定目地,預設複製到目前目錄下'.'<br/>';
                }else{
                    //echo '目地不存在,準備建立目地資料夾'.'<br/>';
                    if(false===@mkdir($tpath_enc,$mode=0777,true)){
                        //echo '建立目的資料夾失敗'.'<br/>';
                        return false;
                    }
                }
            }else{
            //目地存在
                if(!is_dir($tpath_enc)){
                    //echo '目地非目錄'.'<br/>';
                    return false;
                }else{
                    //echo '目地存在'.'<br/>';
                }
            }
            //-------------------------------------------
            //取得來源目錄,檔案與資料夾陣列
            //-------------------------------------------
            //echo 'ok'.'<br/>';
            //echo "spath={$spath}"."<br/>";
            //echo "tpath={$tpath}"."<br/>";

            $arry_err      =array();    //錯誤資訊陣列
            $arry_dirs_err =array();    //目錄錯誤資訊陣列
            $arry_files_err=array();    //檔案錯誤資訊陣列

            $sarry_dirs    =array();    //來源目錄,資料夾陣列
            $sarry_files   =array();    //來源目錄,檔案陣列
            $tarry_dirs    =array();    //目的目錄,資料夾陣列
            $tarry_files   =array();    //目的目錄,檔案陣列

            if(fsolists_recursive($spath,$sarry_dirs,$sarry_files,$fso_enc)!==false){

                //來源檔案與資料夾陣列
                $sarry_dirs =array_reverse($sarry_dirs );
                $sarry_files=array_reverse($sarry_files);

                //echo "<pre>";
                //print_r($sarry_dirs );
                //print_r($sarry_files);
                //echo "</pre>";

                //目的檔案與資料夾陣列
                foreach($sarry_dirs as $inx=>$sdir){
                    $sdir=str_replace($spath_enc,$tpath_enc,$sdir);
                    $tarry_dirs[$inx]=$sdir;
                }
                foreach($sarry_files as $inx=>$sfile){
                    $sfile=str_replace($spath_enc,$tpath_enc,$sfile);
                    $tarry_files[$inx]=$sfile;
                }
                //echo "<pre>";
                //print_r($tarry_dirs );
                //print_r($tarry_files);
                //echo "</pre>";

                //建立目的資料夾
                foreach($tarry_dirs as $inx=>$tdir){
                    if(!file_exists($tdir)){
                        if(false===@mkdir($tdir,$mode=0777,true)){
                            $item=mb_convert_encoding($tdir,$page_enc,$fso_enc);
                            $err ="建立目的資料夾失敗 --> {$item}";
                            $arry_dirs_err[]=array(
                              'item'=>$item,
                              'err' =>$err
                            );
                        }
                    }
                }
                //複製檔案
                foreach($tarry_files as $inx=>$tfile){
                    if(false===@copy($sarry_files[$inx],$tfile)){
                        $item=mb_convert_encoding($tfile,$page_enc,$fso_enc);
                        $err ="複製目的檔案失敗 --> {$item}";
                        $arry_files_err[]=array(
                          'item'=>$item,
                          'err' =>$err
                        );
                    }
                }

                //判斷有無錯誤發生
                if(!empty($arry_dirs_err)||!empty($arry_files_err)){
                    $arry_err=array(
                        'dir' =>$arry_dirs_err,
                        'file'=>$arry_files_err
                    );
                    return $arry_err;
                }else{
                    return true;
                }

            }else{
                //echo '取得來源目錄,檔案與資料夾陣列'.'<br/>';
                return false;
            }
        }else{
            //echo '來源目錄不存在'.'<br/>';
            return false;
        }
    }
?>