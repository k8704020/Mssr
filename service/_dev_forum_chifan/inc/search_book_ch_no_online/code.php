<?php
//-------------------------------------------------------
//函式: search_book_ch_no_online()
//用途: 查找線上書籍資訊
//-------------------------------------------------------

    function search_book_ch_no_online($book_code){
    //---------------------------------------------------
    //函式: search_book_ch_no_online()
    //用途: 查找線上書籍資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($book_code)||trim($book_code)===''){
                die('SEARCH_BOOK_CH_NO_ONLINE: BOOK_CODE IS INVALD');
            }else{
                $book_code=trim($book_code);
            }

        //-----------------------------------------------
        //自訂參數, curl設定
        //-----------------------------------------------

            @set_time_limit(0);

            //curl一般設置
            $_timeout=20;
            $_curlopt_useragent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)";

            //curl書籍資訊
            $_arrys_book_info=array(
                "book_ch_no"    =>array()
            );
            //curl目標網址
            $_arrys_url=array(
                "三民"          =>"http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&q=",
                "國圖"          =>"http://metanbi.ncl.edu.tw/iii/encore/search/C__S"
            );

            //初始化, 找書狀況
            $has_find           =false;

        //-----------------------------------------------
        //自訂函式
        //-----------------------------------------------

            //-------------------------------------------
            //三民
            //-------------------------------------------

                function find_book_samin($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                        //回傳成字串
                        CURLOPT_URL             =>$_url,                       //設定截取網址
                        CURLOPT_HEADER          =>false,                       //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                        //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                   //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                   //次要逾時時間
                        CURLOPT_REFERER         =>"http://www.m.sanmin.com.tw",//模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent          //瀏覽器的user agent
                    );
                    curl_setopt_array($_curl, $options);
                    //檢核錯誤
                    if(!curl_errno($_curl)){
                        //解析
                        if(curl_exec($_curl)===false){
                            return $_arrys_book_info;
                        }else{
                            $_curl_info =curl_getinfo($_curl);
                            $_output    =curl_exec($_curl);
                            $_output    =trim(mb_convert_encoding($_output,"UTF-8"));
                            $_output    =json_decode($_output,true);
                            //結果集陣列
                            $_results=$_output['responseData']['results'];
                            //伺服器代碼
                            $_responsestatus=(int)$_output['responseStatus'];
                        }
                    }else{
                        return $_arrys_book_info;
                    }

                    //-----------------------------------
                    //第一次篩選
                    //-----------------------------------

                        //-------------------------------
                        //錯誤訊息
                        //-------------------------------

                            if((count($_results)===0)||($_responsestatus!==200)){
                                return $_arrys_book_info;
                            }

                        //-------------------------------
                        //取得資料
                        //-------------------------------

                            $_output=file_get_contents(trim($_results[0]['url']));

                    //-----------------------------------
                    //第二次篩選
                    //-----------------------------------

                        //-------------------------------
                        //中國圖書分類
                        //-------------------------------

                            $_book_ch_nos=array();
                            if(empty($_arrys_book_info["book_ch_no"])){
                                preg_match_all('/中國圖書分類：.*<u>/i',$_output,$_book_ch_nos,PREG_SET_ORDER);
                                if(!empty($_book_ch_nos)){
                                    $_book_ch_nos=$_book_ch_nos[0];

                                    foreach($_book_ch_nos as $inx => $val){
                                        //取代, 去標籤
                                        $val=str_replace("<u>","",$val);
                                        $val=str_replace('中國圖書分類：<a href="/Product/Scheme2/?id=',"",$val);
                                        $val=str_replace("\">","",$val);
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $_book_ch_nos[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //回填, 不覆蓋紀錄
                        //-------------------------------

                            foreach($_book_ch_nos as $_book_ch_no){
                                $_book_ch_no=trim($_book_ch_no);
                                $_arrys_book_info["book_ch_no"][]=$_book_ch_no;
                            }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //國圖
            //-------------------------------------------

                function find_book_country_library($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                        //回傳成字串
                        CURLOPT_URL             =>$_url,                       //設定截取網址
                        CURLOPT_HEADER          =>false,                       //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                        //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                   //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                   //次要逾時時間
                        CURLOPT_REFERER         =>"http://metanbi.ncl.edu.tw", //模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent          //瀏覽器的user agent
                    );
                    curl_setopt_array($_curl, $options);
                    //檢核錯誤
                    if(!curl_errno($_curl)){
                        //解析
                        if(curl_exec($_curl)===false){
                            return $_arrys_book_info;
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
                        return $_arrys_book_info;
                        $_msg="curl_errno error!";
                        die($_msg);
                    }

                    //-----------------------------------
                    //篩選
                    //-----------------------------------

                        if($_output!==""){

                            //錯誤指標
                            $_err_msgs=array();

                            //錯誤提示1
                            if(preg_match_all('/noResultsMessage/i',$_output,$_err_msg,PREG_SET_ORDER)){
                                array_push($_err_msgs,$_err_msg[0][0]);
                            }

                            //捕捉到錯誤訊息
                            if(count($_err_msgs)!==0){
                                return $_arrys_book_info;
                                exit;
                            }

                            $_output=htmlspecialchars($_output);
                            $_output=preg_replace('/\s+/','',$_output);
                            $_output=str_replace("nbsp;","",$_output);

                        //-------------------------------
                        //中國圖書分類
                        //-------------------------------

                            $_book_ch_nos=array();
                            if(empty($_arrys_book_info["book_ch_no"])){
                                preg_match('/圖書館&amp;.*&amp;依各館館藏為準/',$_output,$_book_ch_nos);
                                if(!empty($_book_ch_nos)){
                                    $_book_ch_no=$_book_ch_nos[0];
                                    $_book_ch_no=str_replace("圖書館&","",$_book_ch_no);
                                    $_book_ch_no=str_replace("amp;","",$_book_ch_no);
                                    $_book_ch_no=preg_replace('/[a-z|A-Z]/','',$_book_ch_no);
                                    $_book_ch_no=mb_substr($_book_ch_no,0,3);
                                    $_book_ch_no=(int)$_book_ch_no;
                                }
                            }

                        //-------------------------------
                        //回填, 不覆蓋紀錄
                        //-------------------------------

                            if($_book_ch_no!==0){
                                $_arrys_book_info["book_ch_no"][]=$_book_ch_no;
                            }
                        }

                    return $_arrys_book_info;
                }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(!$has_find){
            //-------------------------------------------
            //查找, 三民
            //-------------------------------------------
                if(empty($_arrys_book_info["book_ch_no"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["三民"];
                    $_url.=addslashes($book_code);
                    $_url.="+site:sanmin.com.tw";
                    $_arrys_book_info=find_book_samin($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 國圖
            //-------------------------------------------
                if(empty($_arrys_book_info["book_ch_no"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["國圖"];
                    $_url.=addslashes($book_code);
                    $_url.="__Orightresult__U1?lang=cht&suite=pearl";
                    $_arrys_book_info=find_book_country_library($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_arrys_book_info;
    }
?>