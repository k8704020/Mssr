<?php
//-------------------------------------------------------
//函式: fso_chmod()
//用途: 檔案目錄變更權限
//日期: 2011年12月9日
//作者: jeff@max-life
//-------------------------------------------------------

    function fso_chmod($path,$type='both',$mode=0777,$fso_enc='BIG5'){
    //---------------------------------------------------
    //檔案目錄變更權限
    //---------------------------------------------------
    //$path     路徑(相對)
    //$type     類型{both|dir|file} (both:預設)
    //$mode     權限,預設 0777
    //          組成:
    //              擁有者  擁有者所在群組  everyone
    //
    //          權限:
    //              1   執行(execute)
    //              2   寫入(writeable)
    //              4   讀取(readable)
    //
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        //參數檢驗
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($type)||(trim($type)=='')){
            $type='both';
        }else{
            if(!in_array(strtolower($type),array('both','dir','file'))){
                $type='both';
            }
        }
        if(!isset($mode)||(trim($mode)=='')){
            $mode=0777;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        //編碼處理
        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $page_enc=mb_internal_encoding();
        $path_enc=mb_convert_encoding($path,$fso_enc,$page_enc);

        $arry=array();
        $arry_return=array();
        $arry_files =array();
        $arry_dirs  =array();

        if(file_exists($path_enc)){
            if(!is_dir($path_enc)){
                //echo '非目錄'.'<br/>';
                return false;
            }else{
                //echo 'ok'.'<br/>';

                //處理檔案,目錄陣列
                $arry=scandir($path_enc);
                foreach($arry as $inx=>$item){
                    $path="{$path_enc}/{$item}";
                    if(is_file($path)){
                        $arry_files[]=$path;
                    }elseif(is_dir($path)&&(!mb_eregi("[\.]{1,2}$",$path))){
                        $arry_dirs[] =$path;
                    }
                }
                //將自己目錄也附加進去
                $arry_dirs[]=$path_enc;

                //重新設定權限
                switch(strtolower($type)){
                    case 'both':
                        //目錄部分
                        foreach($arry_dirs as $inx=>$dir){
                            @chmod($dir,$mode);
                        }
                        //檔案部分
                        foreach($arry_files as $inx=>$file){
                            @chmod($file,$mode);
                        }
                        $arry_return=array(
                            'file'=>$arry_files,
                            'dir' =>$arry_dirs
                        );
                        return $arry_return;

                        break;
                    case 'dir':
                        //目錄部分
                        foreach($arry_dirs as $inx=>$dir){
                            @chmod($dir,$mode);
                        }
                        $arry_return=array(
                            'file'=>array(),
                            'dir' =>$arry_dirs
                        );
                        return $arry_return;

                        break;
                    case 'file':
                        //檔案部分
                        foreach($arry_files as $inx=>$file){
                            @chmod($file,$mode);
                        }
                        $arry_return=array(
                            'file'=>$arry_files,
                            'dir' =>array()
                        );
                        return $arry_return;

                        break;
                    default:
                        //目錄部分
                        foreach($arry_dirs as $inx=>$dir){
                            @chmod($dir,$mode);
                        }
                        //檔案部分
                        foreach($arry_files as $inx=>$file){
                            @chmod($file,$mode);
                        }
                        $arry_return=array(
                            'file'=>$arry_files,
                            'dir' =>$arry_dirs
                        );
                        return $arry_return;

                        break;
                }

            }
        }else{
            //echo '目錄不存在'.'<br/>';
            return false;
        }
    }
?>