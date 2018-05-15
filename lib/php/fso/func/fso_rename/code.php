<?php
//-------------------------------------------------------
//函式: fso_rename()
//用途: 檔案目錄更名,採數值序列方式命名
//日期: 2011年12月7日
//作者: jeff@max-life
//-------------------------------------------------------

    function fso_rename($path,$padlen,$type='both',$fso_enc='BIG5'){
    //---------------------------------------------------
    //檔案目錄更名,採數值序列方式命名
    //---------------------------------------------------
    //$path     路徑(相對)
    //$padlen   padding 長度
    //$type     類型{both|dir|file} (both:預設)
    //$fso_enc  檔案系統語系編碼,預設 'BIG5'
    //---------------------------------------------------

        //參數檢驗
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($padlen)||(trim($padlen)=='')){
            return false;
        }
        if(!isset($type)||(trim($type)=='')){
            $type='both';
        }else{
            if(!in_array(strtolower($type),array('both','dir','file'))){
                $type='both';
            }
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

                $arry_new=array();
                $arry_files_new=array();
                $arry_dirs_new =array();
                switch(strtolower($type)){
                    case 'both':
                        //檔案部分
                        $pos=1;
                        foreach($arry_files as $inx=>$file){
                            $info=pathinfo($file);
                            $dirname =$info['dirname'];
                            $file_ext=$info['extension'];
                            $file_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $file_new="{$dirname}/{$file_new}.{$file_ext}";
                            $arry_files_new[]=$file_new;
                            $pos++;
                        }
                        //目錄部分
                        $pos=1;
                        foreach($arry_dirs as $inx=>$dir){
                            $info=pathinfo($dir);
                            $dirname =$info['dirname'];
                            $dir_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $dir_new="{$dirname}/{$dir_new}";
                            $arry_dirs_new[]=$dir_new;
                            $pos++;
                        }
                        $arry_new=array(
                            'file'=>$arry_files_new,
                            'dir' =>$arry_dirs_new
                        );
                        break;
                    case 'dir':
                        //目錄部分
                        $pos=1;
                        foreach($arry_dirs as $inx=>$dir){
                            $info=pathinfo($dir);
                            $dirname =$info['dirname'];
                            $dir_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $dir_new="{$dirname}/{$dir_new}";
                            $arry_dirs_new[]=$dir_new;
                            $pos++;
                        }
                        $arry_new=array(
                            'file'=>$arry_files_new,
                            'dir' =>$arry_dirs_new
                        );
                        break;
                    case 'file':
                        //檔案部分
                        $pos=1;
                        foreach($arry_files as $inx=>$file){
                            $info=pathinfo($file);
                            $dirname =$info['dirname'];
                            $file_ext=$info['extension'];
                            $file_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $file_new="{$dirname}/{$file_new}.{$file_ext}";
                            $arry_files_new[]=$file_new;
                            $pos++;
                        }
                        $arry_new=array(
                            'file'=>$arry_files_new,
                            'dir' =>$arry_dirs_new
                        );
                        break;
                    default:
                        //檔案部分
                        $pos=1;
                        foreach($arry_files as $inx=>$file){
                            $info=pathinfo($file);
                            $dirname =$info['dirname'];
                            $file_ext=$info['extension'];
                            $file_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $file_new="{$dirname}/{$file_new}.{$file_ext}";
                            $arry_files_new[]=$file_new;
                            $pos++;
                        }
                        //目錄部分
                        $pos=1;
                        foreach($arry_dirs as $inx=>$dir){
                            $info=pathinfo($dir);
                            $dirname =$info['dirname'];
                            $dir_new=str_pad($pos,$padlen,'0',STR_PAD_LEFT);
                            $dir_new="{$dirname}/{$dir_new}";
                            $arry_dirs_new[]=$dir_new;
                            $pos++;
                        }
                        $arry_new=array(
                            'file'=>$arry_files_new,
                            'dir' =>$arry_dirs_new
                        );
                        break;
                }

                //重新命名
                if(!empty($arry_new['file'])){
                    foreach($arry_new['file'] as $inx=>$file){
                        @rename($arry_files[$inx],$file);
                    }                
                }
                if(!empty($arry_new['dir'])){
                    foreach($arry_new['dir'] as $inx=>$dir){
                        @rename($arry_dirs[$inx],$dir);
                    }                                
                }
                return $arry_new;
            }
        }else{
            //echo '目錄不存在'.'<br/>';
            return false;
        }
    }
?>