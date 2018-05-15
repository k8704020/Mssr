<?php
//-------------------------------------------------------
//函式: dir_size()
//用途: 取得目錄容量,回遞所有結構
//日期: 2013年3月28日
//作者: jeff@max-life
//-------------------------------------------------------

    function dir_size($path,&$size=0,$fso_enc='BIG5'){
    //---------------------------------------------------
    //取得目錄容量,回遞所有結構
    //---------------------------------------------------
    //$path     路徑
    //$size     容量,單位 : bytes
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        //參數檢驗
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($size)||(trim($size)=='')){
            $size=0;
        }

        //編碼處理
        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $page_enc=mb_internal_encoding();
        $path_enc=mb_convert_encoding($path,$fso_enc,$page_enc);

        if(file_exists($path_enc)){
            if(!is_dir($path_enc)){
                return false;
            }else{
                $tmp  =scandir($path_enc);
                $files=array(); //本階層下檔案陣列
                $dirs =array(); //本階層下目錄陣列

                foreach($tmp as $inx=>$item){
                    $full_path="{$path_enc}/$item";

                    if(is_file($full_path)){
                        $files[]=$full_path;
                    }elseif(is_dir($full_path)&&(!mb_eregi("[\.]{1,2}$",$full_path))){
                        $dirs[]=$full_path;
                    }
                }

                //本階層下檔案容量
                foreach($files as $inx=>$file){
                    $size+=filesize($file);
                }

                //回遞子目錄
                foreach($dirs as $inx=>$dir){
                    $path=mb_convert_encoding($dir,$page_enc,$fso_enc);
                    dir_size($path,$size,$fso_enc);
                }
            }
        }else{
            return false;
        }
    }
?>