<?php
//---------------------------------------------------
//Findbook(圖片)
//---------------------------------------------------

    function find_book_fbk_img($_curl,$_url,$_timeout,$_curlopt_useragent,$allow_exts,$allow_mimes,$page_enc,$fso_enc,$_img_path,$_img_path_enc){

        $_headers[]='Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $_headers[]='Connection: Keep-Alive';
        $_headers[]='Content-type: application/x-www-form-urlencoded;charset=UTF-8';

        $options = array(
            CURLOPT_HTTPHEADER      =>$_headers,                //設定httpheader
            CURLOPT_RETURNTRANSFER  =>true,                     //回傳成字串
            CURLOPT_URL             =>$_url,                    //設定截取網址
            CURLOPT_HEADER          =>false,                    //是否截取header的資訊
            CURLOPT_FOLLOWLOCATION  =>true,                     //是否抓取轉址
            CURLOPT_TIMEOUT         =>$_timeout,                //主要逾時時間
            CURLOPT_CONNECTTIMEOUT  =>$_timeout,                //次要逾時時間
            CURLOPT_REFERER         =>"http://findbook.tw/",    //模擬連結的頁面
            CURLOPT_USERAGENT       =>$_curlopt_useragent       //瀏覽器的user agent
        );
        curl_setopt_array($_curl, $options);

        //檢核錯誤
        if(!curl_errno($_curl)){
            //解析
            if(curl_exec($_curl)===false){
                $_msg="curl_exec error!";
                die($_msg);
            }else{
                $_curl_info =curl_getinfo($_curl);
                $_output    =curl_exec($_curl);
                $_output    =trim($_output);
                $_output_len=(mb_strlen($_output, $page_enc));   //抓取字串長度
                //echo "<Pre>";
                //print_r($_output);
                //echo "</Pre>";
                //die();
            }
        }else{
            $_msg="curl_errno error!";
            die($_msg);
        }

        //判斷字串長度
        if($_output_len>2000){

            //判斷檔案是否存在
            if(!file_exists($_img_path_enc)){
                //寫入
                $_fwrite=@fopen($_img_path_enc,"w");
                @fwrite($_fwrite,$_output);
                fclose($_fwrite);
            }

        //-------------------------------------------
        //判斷是否為圖片
        //-------------------------------------------

            //刪除指標
            $_unlink_state="";

            $_img_size=getimagesize($_img_path_enc);
            $_img_extension=image_type_to_extension($_img_size['2']);
            $_img_size_mime=$_img_size["mime"];

            if(!in_array($_img_extension,$allow_exts)){
                $_unlink_state="true";
            }elseif(!in_array($_img_size_mime,$allow_mimes)){
                $_unlink_state="true";
            }else{
                $_unlink_state="false";
            }

            //移除非法檔案
            if($_unlink_state==="true"){
                @unlink($_img_path);
            }
        }
    }
?>