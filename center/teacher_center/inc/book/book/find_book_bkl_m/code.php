<?php
//---------------------------------------------------
//博客來行動版
//---------------------------------------------------

    function find_book_bkl_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

        $options = array(
            CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
            CURLOPT_URL             =>$_url,                            //設定截取網址
            CURLOPT_HEADER          =>false,                            //是否截取header的資訊
            CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
            CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
            CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
            CURLOPT_REFERER         =>"http://m.books.com.tw/",         //模擬連結的頁面
            CURLOPT_USERAGENT       =>$_curlopt_useragent               //瀏覽器的user agent
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
                $_output    =trim(mb_convert_encoding($_output,"UTF-8"));
                //echo "<Pre>";
                //print_r($_output);
                //echo "</Pre>";
                //die();
            }
        }else{
            $_msg="curl_errno error!";
            die($_msg);
        }

        //-----------------------------------------------
        //篩選
        //-----------------------------------------------

            if($_output!==""){

                //錯誤指標
                $_err_msgs=array();

                //錯誤提示1
                if(preg_match_all('/找不到您所查詢的/i',$_output,$_err_msg,PREG_SET_ORDER)){
                    array_push($_err_msgs,$_err_msg[0][0]);
                }
                //錯誤提示2
                if(preg_match_all('/請輸入其他關鍵字/i',$_output,$_err_msg,PREG_SET_ORDER)){
                    array_push($_err_msgs,$_err_msg[0][0]);
                }

                //捕捉到錯誤訊息
                if(count($_err_msgs)!==0){
                    return $_arrys_book_info;
                    exit;
                }

            //-------------------------------------------
            //書名
            //-------------------------------------------

                $_books_name=array();
                if(empty($_arrys_book_info["book_name"])){
                    preg_match_all('/class=\"item-name\">.*<\/a>/i',$_output,$_books_name,PREG_SET_ORDER);
                    if(!empty($_books_name)){
                        $_books_name=$_books_name[0];

                        foreach($_books_name as $inx => $val){
                            //取代, 去標籤
                            $val=preg_replace('/\s+/','',$val);
                            $val=strip_tags($val);
                            $val=str_replace(array('class="item-name">'),"",$val);
                            $_books_name[$inx]=$val;
                        }
                    }
                }

            //-------------------------------------------
            //作者
            //-------------------------------------------

                $_authors=array();
                if(empty($_arrys_book_info["book_author"])){

                }

            //-------------------------------------------
            //出版社
            //-------------------------------------------

                $_presss=array();
                if(empty($_arrys_book_info["book_publisher"])){

                }

            //-------------------------------------------
            //回填, 不覆蓋紀錄
            //-------------------------------------------

                foreach($_books_name as $_book_name){
                    $_book_name=trim($_book_name);
                    $_arrys_book_info["book_name"][]=$_book_name;
                }

                foreach($_authors as $_author){
                    $_author=trim($_author);
                    $_arrys_book_info["book_author"][]=$_author;
                }

                foreach($_presss as $_press){
                    $_press=trim($_press);
                    $_arrys_book_info["book_publisher"][]=$_press;
                }
            }

        return $_arrys_book_info;
    }
?>