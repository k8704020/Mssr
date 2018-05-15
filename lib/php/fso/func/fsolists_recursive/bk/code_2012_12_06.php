<?php
//-------------------------------------------------------
//函式: fsolists_recursive()
//用途: 檔案目錄清單,回遞所有結構
//日期: 2011年12月6日
//作者: jeff@max-life
//-------------------------------------------------------

    function fsolists_recursive($path,&$arry_dirs,&$arry_files,$fso_enc='BIG5'){
    //---------------------------------------------------
    //檔案目錄清單,回遞所有結構
    //---------------------------------------------------
    //$path         路徑(相對)
    //$arry_dirs    目錄結果陣列
    //$arry_files   檔案結果陣列
    //$fso_enc      檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        //參數檢驗
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($arry_dirs)||(!is_array($arry_dirs))){
            return false;
        }
        if(!isset($arry_files)||(!is_array($arry_files))){
            return false;
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

                $tmp =scandir($path_enc);
                $files=array(); //本階層下檔案陣列
                $dirs =array(); //本階層下目錄陣列

                foreach($tmp as $inx=>$item){
                    $full_path="$path_enc/$item";

                    if(is_file($full_path)){
                        $arry_files[]=$full_path;
                        $files[]=$full_path;
                    }elseif(is_dir($full_path)&&(!mb_eregi("[\.]{1,2}$",$full_path))){
                        $arry_dirs[] =$full_path;
                        $dirs[]=$full_path;
                    }
                }

                foreach($dirs as $inx=>$dir){
                    $path=mb_convert_encoding($dir,$page_enc,$fso_enc);
                    fsolists_recursive($path,&$arry_dirs,&$arry_files,$fso_enc);
                }
            }
        }else{
            //echo '目錄不存在'.'<br/>';
            return false;
        }
    }
?>