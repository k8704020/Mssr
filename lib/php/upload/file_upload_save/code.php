<?php
//-------------------------------------------------------
//函式: file_upload_save()
//用途: 檔案上傳處理
//日期: 2011年12月13日
//作者: jeff@max-life
//-------------------------------------------------------

    function file_upload_save($file,$path,$fso_enc='BIG5',$allow_exts=array(),$allow_mimes=array(),$allow_size=array(),$rename=true){
    //---------------------------------------------------
	//檔案上傳處理
    //---------------------------------------------------
    //$file         $_FILE 檔案物件
    //$path         儲存路徑
    //$fso_enc      檔案系統語系編碼,預設 'BIG5'
    //$allow_exts   類型清單陣列,預設空陣列,表示不檢驗
    //$allow_mimes  mime清單陣列,預設空陣列,表示不檢驗
    //$allow_size   檔案容量上限,預設空陣列,表示不檢驗
    //              $allow_size=array('bytes'=>10)
    //              $allow_size=array('kb'   =>10)
    //              $allow_size=array('mb'   =>10)
    //              $allow_size=array('gb'   =>10)
    //              $allow_size=array('tb'   =>10)
    //              允許單位:{'bytes'|'kb'|'mb'|'gb'|'tb'}
    //$rename       是否重新名,預設 true,以yyyymmddhhiiss命名
    //
    //執行成功時,本函式會傳回儲存的路徑(檔案系統編碼)
    //執行失敗時,本函式會傳回false
    //---------------------------------------------------

        if(!isset($file)){
            return false;
        }
        if(!isset($path)||(trim($path)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }

        if(!function_exists('upload_errmsg')){
            if(false===@include_once("inc/upload_errmsg.php")){
                //echo "外掛函式檔失敗-->upload_errmsg.php"."<br/>";
                return false;
            }
        }

        if(!function_exists('size_to')){
            if(false===@include_once("inc/size_to.php")){
                //echo "外掛函式檔失敗-->size_to.php"."<br/>";
                return false;
            }
        }

        if(!function_exists('get_datesid')){
            if(false===@include_once("inc/get_datesid.php")){
                //echo "外掛函式檔失敗-->get_datesid.php"."<br/>";
                return false;
            }
        }

        //編碼
        $arry_enc=array('UTF-8','BIG-5','GB2312');
        $str_enc =mb_detect_encoding($path,$arry_enc);
        $path_enc=mb_convert_encoding($path,$fso_enc,$str_enc);

        //目錄是否存在
        if((realpath($path_enc)=="")||(@is_dir($path_enc)===false)){
            //echo "儲存路徑不存在"."<br/>";
            return false;
        }

        //目錄是否可以寫入
        if(@is_writable($path_enc)===false){
            //echo "儲存路徑不可寫入"."<br/>";
            return false;
        }

    	if($file['error']!=0){
			//echo "上傳錯誤發生"."<br/>";

            $error =$file['error'];
            $errmsg=upload_errmsg($file);

            //echo "error ={$error }"."<br/>";
            //echo "errmsg={$errmsg}"."<br/>";
            return false;
		}else{
			$name	 =$file['name'];        //檔案原始名稱
			$type	 =$file['type'];        //檔案mime類型
			$size	 =$file['size'];		//檔案大小
			$tmp_name=$file['tmp_name'];	//檔案暫時檔名
			$error	 =$file['error'];       //上傳狀態代碼

            $info=pathinfo($name);
            $basename =(isset($info['basename']))?$info['basename']:'';
            $filename =(isset($info['filename']))?$info['filename']:'';
            $extension=(isset($info['extension']))?$info['extension']:'';

			$out ='';
			$out.='$name	 =' . $name		. "<br/>";
			$out.='$type	 =' . $type		. "<br/>";
			$out.='$size	 =' . $size		. "<br/>";
			$out.='$tmp_name =' . $tmp_name . "<br/>";
			$out.='$error	 =' . $error	. "<br/>";
			$out.='$basename =' . $basename . "<br/>";
			$out.='$filename =' . $filename . "<br/>";
			$out.='$extension=' . $extension. "<br/>";
			//echo $out."<br/>";

			//檢查是否為上傳檔
			if(@is_uploaded_file($tmp_name)===false){
                //echo '檔案不是上傳檔'.'<br/>';
                return false;
			}

            //檢核mime
            if(isset($allow_mimes)&&!empty($allow_mimes)){
                array_map('strtolower',$allow_mimes);
                if(!in_array(strtolower($type),$allow_mimes)){
                    //echo '檔案的mime_type不在允許清單陣列裡'.'<br/>';
                    return false;
                }
            }

            //檢核類型
            if(isset($allow_exts)&&!empty($allow_exts)){
                array_map('strtolower',$allow_exts);
                if(!in_array(strtolower($extension),$allow_exts)){
                    //echo '檔案的類型不在允許清單陣列裡'.'<br/>';
                    return false;
                }
            }

            //檢核檔案容量上限
            if(isset($allow_size)&&!empty($allow_size)){
                $arry_unit=array('bytes','kb','mb','gb','tb');
                $unit_max=key($allow_size);
                $size_max=(int)$allow_size[$unit_max];

                if(in_array(strtolower($unit_max),$arry_unit)&&($size!==0)){
                    $file_size=size_to($size,'bytes',$unit_max);
                    if($file_size>$size_max){
                        //echo "檔案的大小超出允許值-->{$size_max}($unit_max)"."<br/>";
                        return false;
                    }
                }
            }

            //重新命名
            if($rename===true){
                if($extension!==''){
                    $file_save=get_datesid(false).".".$extension;
                }else{
                    $file_save=get_datesid(false);
                }
                $file_save_enc=mb_convert_encoding($file_save,$fso_enc,$str_enc);
            }else{
                $file_save=$basename;
                $file_save_enc=mb_convert_encoding($file_save,$fso_enc,$str_enc);
            }
            //echo "file_save     ={$file_save}"."<br/>";
            //echo "file_save_enc ={$file_save_enc}"."<br/>";


            //儲存上傳檔案
            $save="{$path_enc}/{$file_save_enc}";
            //echo $save."<br/>";

            if(false===@move_uploaded_file($tmp_name,$save)){
                //echo '儲存上傳檔案失敗'.'<br/>';
                return false;
            }else{
                //echo '儲存上傳檔案ok'.'<br/>';
                return $save;
            }
        }
    }
?>
