<?php
//-------------------------------------------------------
//函式: dir_files()
//用途: 取得目錄下檔案陣列
//日期: 2011年12月3日
//作者: jeff@max-life
//-------------------------------------------------------

    function dir_files($dir,$fso_enc='BIG5',$allow_exts=array()){
    //---------------------------------------------------
    //取得目錄下檔案陣列
    //---------------------------------------------------
    //$dir          目錄路徑(相對或絕對路徑)
    //$fso_enc      檔案系統語系編碼 'UTF-8','GB2312', 'BIG5'預設
    //$allow_exts   允許類型(陣列), array('txt','gif')
    //---------------------------------------------------

        //參數檢驗
        if(!isset($dir)||(trim($dir)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        //編碼處理
        $arry_enc =array('UTF-8','BIG-5','GB2312');
        $page_enc =mb_internal_encoding();
        $dir_enc  =mb_convert_encoding($dir,$fso_enc,$page_enc);
        $file_arry=array();

        if(!empty($allow_exts)&&is_array($allow_exts)){
            $allow_exts=array_map('strtolower',$allow_exts);
        }

        if(!file_exists($dir_enc)){
            //echo '資料夾不存在'.'<br/>';
            return false;
        }

        if(false===($files=@scandir($dir_enc))){
            //echo '無法開啟資料夾'.'<br/>';
            return false;
        }else{
            foreach($files as $key=>$file){

                //檔案完整路徑
                $file_full="{$dir_enc}/{$file}";

                //檔案完整資訊
                $info=pathinfo($file_full);
                $file_name    =$info['filename'];
                $file_ext     =$info['extension'];
                $file_dir     =$info['dirname'];
                $file_basename=$info['basename'];

                //檢驗檔案
                if(is_file($file_full)){
                //是檔案

                    //驗證檔案類型
                    if(!empty($allow_exts)){
                        if(in_array(strtolower($file_ext),$allow_exts)){
                            //echo $file.'<br/>';
                            $file_arry[]=$file_full;
                        }
                    }else{
                        //echo $file.'<br/>';
                        $file_arry[]=$file_full;
                    }
                }
            }
        }

        //列出符合檔案清單
        //echo "<pre>";
        //print_r($file_arry);
        //echo "</pre>";

        //回傳結果
        return $file_arry;
    }
?>