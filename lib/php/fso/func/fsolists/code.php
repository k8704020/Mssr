<?php
//-------------------------------------------------------
//函式: fsolists()
//用途: 檔案目錄清單
//日期: 2011年12月6日
//作者: jeff@max-life
//-------------------------------------------------------

    function fsolists($path,$fso_enc='BIG5'){
    //---------------------------------------------------
    //檔案目錄清單
    //---------------------------------------------------
    //$path     路徑(相對)
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
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

        $arry=array();
        $arry_files=array();
        $arry_dirs =array();

        if(file_exists($path_enc)){
            if(!is_dir($path_enc)){
                //echo '非目錄'.'<br/>';
                return false;
            }else{
                //echo 'ok'.'<br/>';
                $arry=scandir($path_enc);

                foreach($arry as $inx=>$item){
                    $path="$path_enc/$item";
                    if(is_file($path)){
                        $arry_files[]=$path;
                    }elseif(is_dir($path)&&(!mb_eregi("[\.]{1,2}$",$path))){
                        $arry_dirs[] =$path;
                    }
                }

                $arry=array(
                    'dir' =>$arry_dirs,
                    'file'=>$arry_files
                );

                return $arry;
            }
        }else{
            //echo '目錄不存在'.'<br/>';
            return false;
        }
    }
?>
