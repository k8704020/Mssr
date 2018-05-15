<?php
//-------------------------------------------------------
//函式: rm_dir()
//用途: 刪除目錄
//日期: 2011年12月6日
//作者: jeff@max-life
//-------------------------------------------------------

    function rm_dir($path,$fso_enc='BIG5'){
    //---------------------------------------------------
    //用途: 刪除目錄
    //日期: 2011年12月6日
    //---------------------------------------------------
    //$path     路徑(相對)
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //
    //本函式會刪除指定目錄,包含所有子資料夾與檔案內容
    //成功傳回true
    //失敗傳回false,或錯誤訊息陣列
    //---------------------------------------------------

        //參數檢驗
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        //編碼處理
        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $page_enc=mb_internal_encoding();
        $path_enc=mb_convert_encoding($path,$fso_enc,$page_enc);

        if(file_exists($path_enc)){
            if(!is_dir($path_enc)){
                //echo '非目錄'.'<br/>';
                return false;
            }else{
                //echo 'ok'.'<br/>';

                //取回檔案與目錄陣列
                $arry_dirs     =array();    //檔案陣列
                $arry_files    =array();    //目錄陣列
                $arry_dirs_err =array();    //檔案錯誤陣列
                $arry_files_err=array();    //目錄錯誤陣列
                $arry_err      =array();    //錯誤陣列

                if(fsolists_recursive($path,$arry_dirs,$arry_files,$fso_enc)!==false){
                    $arry_dirs =array_reverse($arry_dirs );
                    $arry_files=array_reverse($arry_files);

                    //echo "<pre>";
                    //print_r($arry_dirs );
                    //print_r($arry_files);
                    //echo "</pre>";

                    //-----------------------------------
                    //驗證,與篩除
                    //-----------------------------------

                    //刪除檔案部分
                    foreach($arry_files as $inx=>$file){
                        $file_tmp=mb_convert_encoding($file,$page_enc,$fso_enc);
                        $ch=fso_isunder($path,$file_tmp,$fso_enc)?'true':'false';
                        //echo "{$file_tmp}-->{$ch}"."<br/>";

                        if((bool)$ch===true){
                            if(false===@unlink($file)){
                                $arry_files_err[]=array(
                                  'item'=>$file_tmp,
                                  'err' =>'無法刪除檔案!'
                                );
                            }
                        }else{
                            $arry_files_err[]=array(
                              'item'=>$file_tmp,
                              'err' =>"刪除檔案放棄,檔案不在指定路徑下-->{$path}!"
                            );
                        }
                    }

                    //刪除目錄部分
                    foreach($arry_dirs as $inx=>$dir){
                        $dir_tmp=mb_convert_encoding($dir,$page_enc,$fso_enc);
                        $ch=fso_isunder($path,$dir_tmp,$fso_enc)?'true':'false';
                        //echo "{$dir_tmp}-->{$ch}"."<br/>";

                        if((bool)$ch===true){
                            if(false===@rmdir($dir)){
                                $arry_dirs_err[]=array(
                                  'item'=>$dir_tmp,
                                  'err' =>'無法刪除目錄!'
                                );
                            }
                        }else{
                            $arry_dirs_err[]=array(
                              'item'=>$dir_tmp,
                              'err' =>"刪除目錄放棄,目錄不在指定路徑下-->{$path}!"
                            );
                        }
                    }

                    //刪除根目錄
                    if(false===@rmdir($path_enc)){
                        $arry_dirs_err[]=array(
                          'item'=>$path,
                          'err' =>'無法刪除目錄!'
                        );
                    }

                    if(!empty($arry_dirs_err)||!empty($arry_files_err)){
                        $arry_err=array(
                            'dir' =>$arry_dirs_err,
                            'file'=>$arry_files_err
                        );
                        return $arry_err;
                    }else{
                        //echo 'ok'.'<br/>';
                        return true;
                    }

                }else{
                    //echo '錯誤發生'.'<br/>';
                    return false;
                }
            }
        }else{
            //echo '目錄不存在'.'<br/>';
            return false;
        }
    }
?>