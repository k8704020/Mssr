<?php
//-------------------------------------------------------
//函式: search_book_note_online()
//用途: 查找線上書籍簡介資訊
//-------------------------------------------------------

    function search_book_note_online($book_code){
    //---------------------------------------------------
    //函式: search_book_note_online()
    //用途: 查找線上書籍簡介資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($book_code)||trim($book_code)===''){
                die('SEARCH_BOOK_NOTE_ONLINE: BOOK_CODE IS INVALD');
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
                "book_name"     =>array(),
                "book_author"   =>array(),
                "book_publisher"=>array(),
                "book_note"     =>array()
            );
            //curl目標網址
            $_arrys_url=array(
                "eslite"        =>"http://www.eslite.com/Search_BW.aspx?query=",
                "金石堂"        =>"http://www.kingstone.com.tw/search/result.asp?c_name=",
                "博客來"        =>"http://search.books.com.tw/exep/prod_search.php?key="
            );

            //初始化, 找書狀況
            $has_find           =false;

        //-----------------------------------------------
        //自訂函式
        //-----------------------------------------------

            //-------------------------------------------
            //誠品書局
            //-------------------------------------------

                if(!function_exists('find_book_eslite')){
                    function find_book_eslite($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                        $options = array(
                            CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                            CURLOPT_URL             =>$_url,                            //設定截取網址
                            CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                            CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                            CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                            CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                            CURLOPT_REFERER         =>"http://www.eslite.com/",         //模擬連結的頁面
                            CURLOPT_USERAGENT       =>$_curlopt_useragent               //瀏覽器的user agent
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
                                if(preg_match_all('/<span id=\"ctl00_ContentPlaceHolder1_lbTotalResultCount\">.*<\/span>/i',$_output,$_err_msg,PREG_SET_ORDER)){

                                    if(!empty($_err_msg)){
                                        $_err_msg=(int)strip_tags(trim($_err_msg[0][0]));
                                        if($_err_msg===0){
                                            array_push($_err_msgs,$_err_msg);
                                        }
                                    }
                                }

                                //捕捉到錯誤訊息
                                if(count($_err_msgs)!==0){
                                    return $_arrys_book_info;
                                    exit;
                                }

                            //-------------------------------
                            //書名
                            //-------------------------------

                                $_books_name=array();
                                if(empty($_arrys_book_info["book_name"])){
                                    preg_match_all('/<span id="ctl00_ContentPlaceHolder1_rptProducts_ctl00_LblName">.*<\/span>/i',$_output,$_books_name,PREG_SET_ORDER);
                                    if(!empty($_books_name)){
                                        $_books_name=$_books_name[0];

                                        foreach($_books_name as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $_books_name[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //作者
                            //-------------------------------

                                $_authors=array();
                                if(empty($_arrys_book_info["book_author"])){
                                    preg_match_all('/<span id="ctl00_ContentPlaceHolder1_rptProducts_ctl00_LblCharacterName">.*<\/span>/i',$_output,$_authors,PREG_SET_ORDER);
                                    if(!empty($_authors)){
                                        $_authors=$_authors[0];

                                        foreach($_authors as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $_authors[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //出版社
                            //-------------------------------

                                $_presss=array();
                                if(empty($_arrys_book_info["book_publisher"])){
                                    preg_match_all('/<span id="ctl00_ContentPlaceHolder1_rptProducts_ctl00_LblManufacturerName">.*<\/span>/i',$_output,$_presss,PREG_SET_ORDER);
                                    if(!empty($_presss)){
                                        $_presss=$_presss[0];

                                        foreach($_presss as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $_presss[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //簡介
                            //-------------------------------

                                $_book_notes=array();
                                if(empty($_arrys_book_info["book_note"])){
                                    preg_match_all('/<spanid="ctl00_ContentPlaceHolder1_rptProducts_ctl00_LblShortDescription">.*<\/span>/i',preg_replace('/\s+/','',$_output),$_book_notes,PREG_SET_ORDER);
                                    if(!empty($_book_notes)){
                                        $_book_notes=$_book_notes[0];

                                        foreach($_book_notes as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $_book_notes[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //回填, 不覆蓋紀錄
                            //-------------------------------

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

                                foreach($_book_notes as $_book_note){
                                    $_book_note=trim($_book_note);
                                    $_arrys_book_info["book_note"][]=$_book_note;
                                }
                            }

                        return $_arrys_book_info;
                    }
                }

            //-------------------------------------------
            //金石堂
            //-------------------------------------------

                if(!function_exists('find_book_kst')){
                    function find_book_kst($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                        $options = array(
                            CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                            CURLOPT_URL             =>$_url,                            //設定截取網址
                            CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                            CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                            CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                            CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                            CURLOPT_REFERER         =>"http://www.kingstone.com.tw/",   //模擬連結的頁面
                            CURLOPT_USERAGENT       =>$_curlopt_useragent               //瀏覽器的user agent
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
                                if(preg_match_all('/沒有符合的條件/i',$_output,$_err_msg,PREG_SET_ORDER)){
                                    array_push($_err_msgs,$_err_msg[0][0]);
                                }

                                //捕捉到錯誤訊息
                                if(count($_err_msgs)!==0){
                                    return $_arrys_book_info;
                                    exit;
                                }

                            //-------------------------------
                            //書名
                            //-------------------------------

                                //暫無篩選。
                                $_books_name=array();
                                if(empty($_arrys_book_info["book_name"])){

                                }

                            //-------------------------------
                            //作者
                            //-------------------------------

                                $_authors=array();
                                if(empty($_arrys_book_info["book_author"])){
                                    preg_match_all('/<span class=\"author\">.*<\/a>著/i',$_output,$_authors,PREG_SET_ORDER);
                                    if(!empty($_authors)){
                                        $_authors=$_authors[0];

                                        foreach($_authors as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $val=str_replace(array("著"),"",$val);
                                            $_authors[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //出版社
                            //-------------------------------

                                $_presss=array();
                                if(empty($_arrys_book_info["book_publisher"])){
                                    preg_match_all('/<span class=\"publisher\">.* <\/a>/i',$_output,$_presss,PREG_SET_ORDER);
                                    if(!empty($_presss)){
                                        $_presss=$_presss[0];

                                        foreach($_presss as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $_presss[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //簡介
                            //-------------------------------

                                $_book_notes=array();
                                if(empty($_arrys_book_info["book_note"])){
                                    preg_match_all('/<spanclass="publish_time">.*<\/p><spanclass="price">/i',preg_replace('/\s+/','',$_output),$_book_notes,PREG_SET_ORDER);
                                    if(!empty($_book_notes)){
                                        $_book_notes=$_book_notes[0];

                                        foreach($_book_notes as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=preg_replace('/<spanclass="publish_time">.*<\/span>/','',$val);
                                            $val=strip_tags($val);
                                            $_book_notes[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //回填, 不覆蓋紀錄
                            //-------------------------------

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

                                foreach($_book_notes as $_book_note){
                                    $_book_note=trim($_book_note);
                                    $_arrys_book_info["book_note"][]=$_book_note;
                                }
                            }

                        return $_arrys_book_info;
                    }
                }

            //-------------------------------------------
            //博客來
            //-------------------------------------------

                if(!function_exists('find_book_bkl')){
                    function find_book_bkl($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                        $options = array(
                            CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                            CURLOPT_URL             =>$_url,                            //設定截取網址
                            CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                            CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                            CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                            CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                            CURLOPT_REFERER         =>"http://search.books.com.tw/",    //模擬連結的頁面
                            CURLOPT_USERAGENT       =>$_curlopt_useragent               //瀏覽器的user agent
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

                        //---------------------------------------
                        //篩選
                        //---------------------------------------

                            if($_output!==""){

                                //錯誤指標
                                $_err_msgs=array();

                                //錯誤提示1
                                if(preg_match_all('/找不到您所查詢的/i',$_output,$_err_msg,PREG_SET_ORDER)){
                                    array_push($_err_msgs,$_err_msg[0][0]);
                                }
                                //錯誤提示2
                                if(preg_match_all('/請輸入其他關鍵字或縮小搜尋範圍條件/i',$_output,$_err_msg,PREG_SET_ORDER)){
                                    array_push($_err_msgs,$_err_msg[0][0]);
                                }

                                //捕捉到錯誤訊息
                                if(count($_err_msgs)!==0){
                                    return $_arrys_book_info;
                                    exit;
                                }

                            //-----------------------------------
                            //書名
                            //-----------------------------------

                                $_books_name=array();
                                if(empty($_arrys_book_info["book_name"])){
                                    preg_match_all('/\">.*<\/a><\/h3>/i',$_output,$_books_name,PREG_SET_ORDER);
                                    if(!empty($_books_name)){
                                        $_books_name=$_books_name[0];

                                        foreach($_books_name as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                            $val=str_replace(array("\">"),"",$val);
                                            $_books_name[$inx]=$val;
                                        }
                                    }
                                }

                            //-----------------------------------
                            //作者
                            //-----------------------------------

                                $_authors=array();
                                if(empty($_arrys_book_info["book_author"])){
                                    preg_match_all('/author\".*<\/a>/i',$_output,$_authors,PREG_SET_ORDER);
                                    if(!empty($_authors)){
                                        $_authors=$_authors[0];

                                        foreach($_authors as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=preg_replace('/author\"href\=\"/','<',$val);
                                            $val=preg_replace('/<\/a>/','<a></a>',$val);
                                            $val=preg_replace('/\">/','></a>',$val);
                                            $val=strip_tags($val);
                                            $_authors[$inx]=$val;
                                        }
                                    }
                                }

                            //-----------------------------------
                            //出版社
                            //-----------------------------------

                                $_presss=array();
                                if(empty($_arrys_book_info["book_publisher"])){
                                    preg_match_all('/pubid.*<\/a>/i',$_output,$_presss,PREG_SET_ORDER);
                                    if(!empty($_presss)){
                                        $_presss=$_presss[0];

                                        foreach($_presss as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=preg_replace('/pubid=/','<a ',$val);
                                            $val=preg_replace('/\"title\=/','></a><a title=',$val);
                                            $val=preg_replace('/\">/','"></a>',$val);

                                            //$val=preg_replace('/<\/a>/','<a></a>',$val);
                                            //
                                            //$val=preg_replace('/><a>/','"></a>',$val);

                                            //$val=strip_tags($val);
                                            $_presss[$inx]=$val;
                                        }
                                    }
                                }

                            //-------------------------------
                            //簡介
                            //-------------------------------

                                $_book_notes=array();
                                if(empty($_arrys_book_info["book_note"])){
                                    preg_match_all('/放入購物車"\/><\/a><\/span><p>.*<\/p>/i',preg_replace('/\s+/','',$_output),$_book_notes,PREG_SET_ORDER);
                                    if(!empty($_book_notes)){
                                        $_book_notes=$_book_notes[0];

                                        foreach($_book_notes as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/放入購物車"\/><\/a><\/span>/','',$val);
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=preg_replace('/<spanclass="publish_time">.*<\/span>/','',$val);
                                            $val=strip_tags($val);
                                            $_book_notes[$inx]=$val;
                                        }
                                    }
                                }

                            //-----------------------------------
                            //回填, 不覆蓋紀錄
                            //-----------------------------------

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

                                foreach($_book_notes as $_book_note){
                                    $_book_note=trim($_book_note);
                                    $_arrys_book_info["book_note"][]=$_book_note;
                                }
                            }

                        return $_arrys_book_info;
                    }

                }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(!$has_find){
            //-------------------------------------------
            //查找, 誠品書局
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])&&!empty($_arrys_book_info["book_note"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["eslite"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_eslite($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 金石堂
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])&&!empty($_arrys_book_info["book_note"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["金石堂"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_kst($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 博客來
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])&&!empty($_arrys_book_info["book_note"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["博客來"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_bkl($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_arrys_book_info;
    }
?>