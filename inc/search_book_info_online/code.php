<?php
//-------------------------------------------------------
//函式: search_book_info_online()
//用途: 查找線上書籍資訊
//-------------------------------------------------------

    function search_book_info_online($book_code){
    //---------------------------------------------------
    //函式: search_book_info_online()
    //用途: 查找線上書籍資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($book_code)||trim($book_code)===''){
                die('SEARCH_BOOK_INFO_ONLINE: BOOK_CODE IS INVALD');
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
                "book_name"         =>array(),
                "book_author"       =>array(),
                "book_publisher"    =>array(),
                "book_note"         =>array(),
                "book_ch_no"        =>array(),
                "book_page_count"   =>array()
            );
            //curl目標網址
            $_arrys_url=array(
                //"Findbook"      =>"http://www.google.com.tw/#q={$book_code}+site:findbook.tw/book",

                "ylib"          =>"http://ylsrh1.ylib.com/Search/Search?SearchSite=1&Keyword=",
                "amazon"        =>"http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords=",
                "eslite"        =>"http://www.eslite.com/Search_BW.aspx?query=",
                "ncl_isbnnet"   =>"http://isbn.ncl.edu.tw/NCL_ISBNNet/opendata/isbn.xml",
                "Findbook"      =>"http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&q=",
                "金石堂"        =>"http://www.kingstone.com.tw/search/result.asp?c_name=",
                "博客來"        =>"http://search.books.com.tw/exep/prod_search.php?key=",
                "台灣大學圖書館"=>"http://tulips.ntu.edu.tw/search*cht/?searchtype=i&searcharg=",
                "金石堂行動版"  =>"http://m.kingstone.com.tw/search.asp?q=",
                "Findbook行動版"=>"http://findbook.tw/m/book/",
                "博客來行動版"  =>"http://m.books.com.tw/search?key=",
                "三民"          =>"http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&q=",
                "國圖"          =>"http://metanbi.ncl.edu.tw/iii/encore/search/C__S",
                "douban_api"    =>"https://api.douban.com/v2/book/isbn/"
            );

            //初始化, 找書狀況
            $has_find           =false;

        //-----------------------------------------------
        //自訂函式
        //-----------------------------------------------

            //-------------------------------------------
            //遠流書店
            //-------------------------------------------

                function find_book_ylib($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                                 //回傳成字串
                        CURLOPT_URL             =>$_url,                                //設定截取網址
                        CURLOPT_HEADER          =>false,                                //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                                 //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                            //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                            //次要逾時時間
                        CURLOPT_REFERER         =>"http://www.ylib.com/",               //模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent                   //瀏覽器的user agent
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
                            if(preg_match_all('/Oops! Something is Wrong!/i',$_output,$_err_msg,PREG_SET_ORDER)){
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

                            $_books_name=array();
                            if(empty($_arrys_book_info["book_name"])){
                                preg_match_all('/<a class="restit".*<\/a>/i',$_output,$_books_name,PREG_SET_ORDER);
                                if(!empty($_books_name)){
                                    $_books_name=$_books_name[0];

                                    foreach($_books_name as $inx => $val){
                                        //取代, 去標籤
                                        //$val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=trim($val);
                                        $_books_name[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //作者
                        //-------------------------------

                            $_authors=array();
                            if(empty($_arrys_book_info["book_author"])){
                                $_output_process=preg_replace('/\s+/','',$_output);
                                preg_match_all('/<spanclass="resAuthor">.*<\/a><\/span><spanclass="resBNO">/i',$_output_process,$_authors,PREG_SET_ORDER);
                                if(!empty($_authors)){
                                    $_authors=$_authors[0];

                                    foreach($_authors as $inx => $val){
                                        //取代, 去標籤
                                        //$val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=trim($val);
                                        $_authors[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_presss=array();
                            if(empty($_arrys_book_info["book_publisher"])){
                                preg_match_all('/<span class=\"resPub\">.*<\/a>/i',$_output,$_presss,PREG_SET_ORDER);
                                if(!empty($_presss)){
                                    $_presss=$_presss[0];

                                    foreach($_presss as $inx => $val){
                                        //取代, 去標籤
                                        //$val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=trim($val);
                                        $_presss[$inx]=$val;
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
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //亞馬遜書店
            //-------------------------------------------

                function find_book_amazon($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                                 //回傳成字串
                        CURLOPT_URL             =>$_url,                                //設定截取網址
                        CURLOPT_HEADER          =>false,                                //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                                 //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                            //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                            //次要逾時時間
                        CURLOPT_REFERER         =>"http://www.amazon.com/ref=gno_logo", //模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent                   //瀏覽器的user agent
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
                            if(preg_match_all('/did not match any products./i',$_output,$_err_msg,PREG_SET_ORDER)){
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

                            $_books_name=array();
                            if(empty($_arrys_book_info["book_name"])){
                                preg_match_all('/<span class="lrg bold">.*by/i',$_output,$_books_name,PREG_SET_ORDER);
                                if(!empty($_books_name)){
                                    $_books_name=$_books_name[0];

                                    foreach($_books_name as $inx => $val){
                                        //取代, 去標籤
                                        //$val=preg_replace('/\s+/','',$val);
                                        $val=str_replace(array("by"),"",$val);
                                        $val=strip_tags($val);
                                        $val=trim($val);
                                        $_books_name[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //作者
                        //-------------------------------

                            $_authors=array();
                            if(empty($_arrys_book_info["book_author"])){
                                preg_match_all('/<span class="med reg">by .*<\/a>/i',$_output,$_authors,PREG_SET_ORDER);
                                if(!empty($_authors)){
                                    $_authors=$_authors[0];

                                    foreach($_authors as $inx => $val){
                                        //取代, 去標籤
                                        //$val=preg_replace('/\s+/','',$val);
                                        $val=str_replace(array("by"),"",$val);
                                        $val=strip_tags($val);
                                        $val=trim($val);
                                        $_authors[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_presss=array();
                            if(empty($_arrys_book_info["book_publisher"])){
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
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //誠品書局
            //-------------------------------------------

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

            //-------------------------------------------
            //國圖書號中心
            //-------------------------------------------

                function find_book_ncl_isbnnet($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                //---------------------------------------
                //自訂函式
                //---------------------------------------

                    function object_to_arry($object){
                    //物件轉陣列
                        $array=array();
                        if(is_object($object)){
                            $array=get_object_vars($object);
                        }
                        return $array;
                    }

                //---------------------------------------
                //設定
                //---------------------------------------

                    global $book_code;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $oxmls=simplexml_load_file($_url);
                    $oxmls=object_to_arry($oxmls);
                    $arrys_xml=$oxmls[trim('Book')];

                    //-----------------------------------
                    //篩選
                    //-----------------------------------

                        if(!empty($arrys_xml)){

                            foreach($arrys_xml as $oxml){

                                $arry_xml=object_to_arry($oxml);

                                if(isset($arry_xml[trim('ISBN')])){

                                    $book_isbn=($arry_xml[trim('ISBN')]);
                                    $find_flag=false;
                                    if(is_string($book_isbn)){
                                        $book_isbn=trim($arry_xml[trim('ISBN')]);
                                        $book_isbn=trim(str_replace('-','',preg_replace('/\(.*\)/i','',$book_isbn)));
                                        if(trim($book_isbn)===trim($book_code)){
                                            $book_name      =(isset($arry_xml[trim('書名')]))?trim($arry_xml[trim('書名')]):'';
                                            $book_author    =(isset($arry_xml[trim('作者')]))?trim($arry_xml[trim('作者')]):'';
                                            $book_publisher =(isset($arry_xml[trim('出版單位')]))?trim($arry_xml[trim('出版單位')]):'';
                                            $find_flag=true;
                                            if($find_flag){
                                                break;
                                            }
                                        }
                                    }
                                    if(is_array($book_isbn)){
                                        $arry_book_isbn=array_map("trim",$book_isbn);
                                        foreach($arry_book_isbn as $book_isbn){
                                            $book_isbn=trim($book_isbn);
                                            $book_isbn=trim(str_replace('-','',preg_replace('/\(.*\)/i','',$book_isbn)));
                                            if(trim($book_isbn)===trim($book_code)){
                                                $book_name      =(isset($arry_xml[trim('書名')]))?trim($arry_xml[trim('書名')]):'';
                                                $book_author    =(isset($arry_xml[trim('作者')]))?trim($arry_xml[trim('作者')]):'';
                                                $book_publisher =(isset($arry_xml[trim('出版單位')]))?trim($arry_xml[trim('出版單位')]):'';
                                                $find_flag=true;
                                                if($find_flag){
                                                    break;
                                                }
                                            }
                                        }
                                        if($find_flag){
                                            break;
                                        }
                                    }
                                }
                            }

                        //-------------------------------
                        //書名
                        //-------------------------------

                            $_books_name=array();
                            if($find_flag){
                                if(trim($book_name)!==''){
                                    $_books_name[]=trim($book_name);
                                }
                            }

                        //-------------------------------
                        //作者
                        //-------------------------------

                            $_authors=array();
                            if($find_flag){
                                if(trim($book_author)!==''){
                                    $_authors[]=trim($book_author);
                                }
                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_presss=array();
                            if($find_flag){
                                if(trim($book_publisher)!==''){
                                    $_presss[]=trim($book_publisher);
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
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //台灣大學圖書館
            //-------------------------------------------

                function find_book_ntu($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                                     //回傳成字串
                        CURLOPT_URL             =>$_url,                                    //設定截取網址
                        CURLOPT_HEADER          =>false,                                    //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                                     //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                                //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                                //次要逾時時間
                        CURLOPT_REFERER         =>"http://tulips.ntu.edu.tw/search*cht/",   //模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent                       //瀏覽器的user agent
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
                            if(preg_match_all('/沒有查獲符合查詢條件/i',$_output,$_err_msg,PREG_SET_ORDER)){
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

                            $_books_name=array();
                            if(empty($_arrys_book_info["book_name"])){
                                preg_match_all('/<strong>.* \//i',$_output,$_books_name,PREG_SET_ORDER);
                                if(!empty($_books_name)){
                                    $_books_name=$_books_name[0];

                                    foreach($_books_name as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=str_replace(array("/"),"",$val);
                                        $_books_name[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //作者
                        //-------------------------------

                            $_authors=array();
                            if(empty($_arrys_book_info["book_author"])){
                                preg_match_all('/\/ .*<\/strong>/i',$_output,$_authors,PREG_SET_ORDER);
                                if(!empty($_authors)){
                                    $_authors=$_authors[0];

                                    foreach($_authors as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=str_replace(array("/"),"",$val);
                                        $_authors[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_presss=array();
                            if(empty($_arrys_book_info["book_publisher"])){
                                preg_match_all('/出版項<\/td>.*版本項<\/td>/i',preg_replace('/\s+/','',$_output),$_presss,PREG_SET_ORDER);
                                if(!empty($_presss)){
                                    $_presss=$_presss[0];

                                    foreach($_presss as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=str_replace(array("出版項","版本項"),"",$val);
                                        $_presss[$inx]=$val;
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
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //金石堂行動版
            //-------------------------------------------

                function find_book_kst_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                        CURLOPT_URL             =>$_url,                            //設定截取網址
                        CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                        CURLOPT_REFERER         =>"http://m.kingstone.com.tw/",     //模擬連結的頁面
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
                            if(preg_match_all('/目前無相關商品/i',$_output,$_err_msg,PREG_SET_ORDER)){
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

                            $_books_name=array();
                            if(empty($_arrys_book_info["book_name"])){
                                preg_match_all('/<h4 class=\"media-heading\">.*<\/h4>/i',$_output,$_books_name,PREG_SET_ORDER);
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
                                preg_match_all('/<span class=\"m_author\">.*<\/span> 著/i',$_output,$_authors,PREG_SET_ORDER);
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

                            $_publishers=array();
                            if(empty($_arrys_book_info["book_publisher"])){
                                preg_match_all('/<span class=\"m_author\">.*<\/span> 出版/i',$_output,$_publishers,PREG_SET_ORDER);
                                if(!empty($_publishers)){
                                    $_publishers=$_publishers[0][0];
                                    preg_match_all('/著.*出版/i',$_publishers,$_publishers,PREG_SET_ORDER);
                                    if(!empty($_publishers)){
                                        $_publishers=$_publishers[0];

                                        foreach($_publishers as $inx => $val){
                                            //取代, 去標籤
                                            $val=preg_replace('/\s+/','',$val);
                                            $val=strip_tags($val);
                                             $val=str_replace(array("著","出版"),"",$val);
                                            $_publishers[$inx]=$val;
                                        }
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

                            foreach($_publishers as $_publisher){
                                $_publisher=trim($_publisher);
                                $_arrys_book_info["book_publisher"][]=$_publisher;
                            }
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //金石堂
            //-------------------------------------------

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

            //-------------------------------------------
            //Findbook行動版
            //-------------------------------------------

                function find_book_fbk_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                        CURLOPT_URL             =>$_url,                            //設定截取網址
                        CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                        CURLOPT_REFERER         =>"http://findbook.tw/m",           //模擬連結的頁面
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
                            if(preg_match_all('/<h1>404 Page Not Found<\/h1>/i',$_output,$_err_msg,PREG_SET_ORDER)){
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

                            $_books_name=array();
                            if(empty($_arrys_book_info["book_name"])){
                                preg_match_all('/<span style=\"text-decoration:underline\">.*<\/span><div class=\"highlight\">/i',$_output,$_books_name,PREG_SET_ORDER);
                                if(!empty($_books_name)){
                                    $_books_name=$_books_name[0];

                                    foreach($_books_name as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=str_replace(array("&nbsp;"),"",$val);
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
                                preg_match_all('/作者:.*<\/a><\/span><span>&nbsp;/i',$_output,$_authors,PREG_SET_ORDER);
                                if(!empty($_authors)){
                                    $_authors=$_authors[0];

                                    foreach($_authors as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=str_replace(array("作者:"),"",$val);
                                        $val=str_replace(array("&nbsp;"),"",$val);
                                        $_authors[$inx]=$val;
                                    }
                                }
                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_publishers=array();
                            if(empty($_arrys_book_info["book_publisher"])){
                                preg_match_all('/&nbsp;出版社:.*<\/span><span>&nbsp;/i',$_output,$_publishers,PREG_SET_ORDER);
                                if(!empty($_publishers)){
                                    $_publishers=$_publishers[0];

                                    foreach($_publishers as $inx => $val){
                                        //取代, 去標籤
                                        $val=preg_replace('/\s+/','',$val);
                                        $val=strip_tags($val);
                                        $val=str_replace(array("出版社:"),"",$val);
                                        $val=str_replace(array("&nbsp;"),"",$val);
                                        $_publishers[$inx]=$val;
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

                            foreach($_publishers as $_publisher){
                                $_publisher=trim($_publisher);
                                $_arrys_book_info["book_publisher"][]=$_publisher;
                            }
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //Findbook
            //-------------------------------------------

                function find_book_fbk($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $options = array(
                        CURLOPT_RETURNTRANSFER  =>true,                             //回傳成字串
                        CURLOPT_URL             =>$_url,                            //設定截取網址
                        CURLOPT_HEADER          =>false,                            //是否截取header的資訊
                        CURLOPT_FOLLOWLOCATION  =>true,                             //是否抓取轉址
                        CURLOPT_TIMEOUT         =>$_timeout,                        //主要逾時時間
                        CURLOPT_CONNECTTIMEOUT  =>$_timeout,                        //次要逾時時間
                        CURLOPT_REFERER         =>"https://www.google.com.tw/",     //模擬連結的頁面
                        CURLOPT_USERAGENT       =>$_curlopt_useragent               //瀏覽器的user agent
                    );
                    curl_setopt_array($_curl, $options);

                    //間隔1~2秒
                    sleep(rand(1,2));

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
                            $_output    =json_decode($_output,true);

                            //結果集陣列
                            $_results=$_output['responseData']['results'];

                            //伺服器代碼
                            $_responsestatus=(int)$_output['responseStatus'];

                            //echo "<Pre>";
                            //print_r($_results);
                            //echo "</Pre>";
                            //echo "<Pre>";
                            //print_r($_responsestatus);
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

                        if((count($_results!==0))&&($_responsestatus===200)){

                            //---------------------------
                            //初始化, 參數
                            //---------------------------

                                $_book_name='';
                                $_author='';
                                $_press='';

                                foreach($_results as $inx=>$_result){
                                //echo "<Pre>";
                                //print_r($_result);
                                //echo "</Pre>";

                                    //目標位置
                                    $_url    =htmlspecialchars(trim($_result['url']));

                                    $_title  =htmlspecialchars(trim($_result['title']));
                                    $_content=htmlspecialchars(trim($_result['content']));


                                    if(preg_match('/basic/i',$_url)){
                                    //第一次比對

                                        //---------------
                                        //書名
                                        //---------------

                                            if(empty($_arrys_book_info["book_name"])){
                                                if($_book_name===''){
                                                    //取代, 去空白,標籤
                                                    $_book_name=preg_replace('/\s+/','',$_title);
                                                    $_book_name=str_replace(array('Findbook','商品簡介','&amp;gt;','-翻書客'),'',$_book_name);
                                                }else{
                                                    if($_book_name===''){$_book_name='';}
                                                }
                                            }

                                        //---------------
                                        //作者
                                        //---------------

                                            if(empty($_arrys_book_info["book_author"])){
                                                if($_author===''){
                                                    //取代, 去空白,標籤
                                                    $_author=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/作者：.*,出版社/i',$_author,$_author,PREG_SET_ORDER);
                                                    if(!empty($_author)){
                                                        $_author=str_replace(array('作者：',',出版社'),'',$_author[0][0]);
                                                    }else{
                                                        if(empty($_author)){$_author='';}
                                                    }
                                                }
                                            }

                                        //---------------
                                        //出版社
                                        //---------------
                                            if(empty($_arrys_book_info["book_publisher"])){
                                                if($_press===''){
                                                    //取代, 去空白,標籤
                                                    $_press=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/出版社：.*,出版日期/i',$_press,$_press,PREG_SET_ORDER);
                                                    if(!empty($_press)){
                                                        $_press=str_replace(array('出版社：',',出版日期'),'',$_press[0][0]);
                                                    }else{
                                                        if(empty($_press)){$_press='';}
                                                    }
                                                }
                                            }

                                    }else if(preg_match('/price/i',$_url)){
                                    //第二次比對

                                        //---------------
                                        //書名
                                        //---------------

                                            if(empty($_arrys_book_info["book_name"])){
                                                if($_book_name===''){
                                                    //取代, 去空白,標籤
                                                    $_book_name=preg_replace('/\s+/','',$_title);
                                                    $_book_name=str_replace(array('Findbook','比價資訊','&amp;gt;','-翻書客'),'',$_book_name);
                                                }else{
                                                    if($_book_name===''){$_book_name='';}
                                                }
                                            }

                                        //---------------
                                        //作者
                                        //---------------

                                            if(empty($_arrys_book_info["book_author"])){
                                                if($_author===''){
                                                    //取代, 去空白,標籤
                                                    $_author=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/作者：.*,出版社/i',$_author,$_author,PREG_SET_ORDER);
                                                    if(!empty($_author)){
                                                        $_author=str_replace(array('作者：',',出版社'),'',$_author[0][0]);
                                                    }else{
                                                        if(empty($_author)){$_author='';}
                                                    }
                                                }
                                            }

                                        //---------------
                                        //出版社
                                        //---------------

                                            if(empty($_arrys_book_info["book_publisher"])){
                                                if($_press===''){
                                                    //取代, 去空白,標籤
                                                    $_press=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/出版社：.*,出版日期/i',$_press,$_press,PREG_SET_ORDER);
                                                    if(!empty($_press)){
                                                        $_press=str_replace(array('出版社：',',出版日期'),'',$_press[0][0]);
                                                    }else{
                                                        if(empty($_press)){$_press='';}
                                                    }
                                                }
                                            }

                                    }else if(preg_match('/preview/i',$_url)){
                                    //第三次比對

                                        //---------------
                                        //書名
                                        //---------------

                                            if(empty($_arrys_book_info["book_name"])){
                                                if($_book_name===''){
                                                    //取代, 去空白,標籤
                                                    $_book_name=preg_replace('/\s+/','',$_title);
                                                    $_book_name=str_replace(array('Findbook','部落格貼紙','&amp;gt;','-翻書客'),'',$_book_name);
                                                }else{
                                                    if($_book_name===''){$_book_name='';}
                                                }
                                            }

                                        //---------------
                                        //作者
                                        //---------------

                                            if(empty($_arrys_book_info["book_author"])){
                                                if($_author===''){
                                                    //取代, 去空白,標籤
                                                    $_author=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/作者：.*,出版社/i',$_author,$_author,PREG_SET_ORDER);
                                                    if(!empty($_author)){
                                                        $_author=str_replace(array('作者：',',出版社'),'',$_author[0][0]);
                                                    }else{
                                                        if(empty($_author)){$_author='';}
                                                    }
                                                }
                                            }

                                        //---------------
                                        //出版社
                                        //---------------

                                            if(empty($_arrys_book_info["book_publisher"])){
                                                if($_press===''){
                                                    //取代, 去空白,標籤
                                                    $_press=preg_replace('/\s+/','',$_content);
                                                    preg_match_all('/出版社：.*,出版日期/i',$_press,$_press,PREG_SET_ORDER);
                                                    if(!empty($_press)){
                                                        $_press=str_replace(array('出版社：',',出版日期'),'',$_press[0][0]);
                                                    }else{
                                                        if(empty($_press)){$_press='';}
                                                    }
                                                }
                                            }

                                    }else{
                                    //預設動作

                                    }

                            }

                        //-------------------------------
                        //回填, 不覆蓋紀錄
                        //-------------------------------

                            $_arrys_book_info["book_name"][]=$_book_name;
                            $_arrys_book_info["book_author"][]=$_author;
                            $_arrys_book_info["book_publisher"][]=$_press;

                            //回傳
                            return $_arrys_book_info;

                        }else{

                            //回傳
                            return $_arrys_book_info;
                        }
                }

            //-------------------------------------------
            //博客來行動版
            //-------------------------------------------

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

                        //-------------------------------
                        //書名
                        //-------------------------------

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

                        //-------------------------------
                        //作者
                        //-------------------------------

                            $_authors=array();
                            if(empty($_arrys_book_info["book_author"])){

                            }

                        //-------------------------------
                        //出版社
                        //-------------------------------

                            $_presss=array();
                            if(empty($_arrys_book_info["book_publisher"])){

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
                        }

                    return $_arrys_book_info;
                }

            //-------------------------------------------
            //博客來
            //-------------------------------------------

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

            //-------------------------------------------
            //豆瓣api
            //-------------------------------------------

                function find_book_douban_api($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){

                    $file_get_contents=@file_get_contents($_url);

                    if($file_get_contents===false){
                        return $_arrys_book_info;
                    }else{
                        $file_get_contents=json_decode($file_get_contents,true);
                    }

                    //-----------------------------------
                    //篩選
                    //-----------------------------------
//echo "<Pre>";
//print_r($file_get_contents);
//echo "</Pre>";
                        $_book_page_count=array();
                        $_book_notes     =array();
                        $_books_name     =array();
                        $_authors        =array();
                        $_presss         =array();

                        foreach($file_get_contents as $key=>$file_get_content){
                            if(trim($key)==='title'){
                            //書名
                                $val=trim($file_get_content);
                                $_arrys_book_info["book_name"][]=$val;
                                //echo "<Pre>";
                                //print_r($val);
                                //echo "</Pre>";
                            }
                            if(trim($key)==='publisher'){
                            //出版社
                                $val=trim($file_get_content);
                                $_arrys_book_info["book_publisher"][]=$val;
                                //echo "<Pre>";
                                //print_r($val);
                                //echo "</Pre>";
                            }
                            if(trim($key)==='author'){
                            //作者
                                $val=trim($file_get_content[0]);
                                $_arrys_book_info["book_author"][]=$val;
                                //echo "<Pre>";
                                //print_r($val);
                                //echo "</Pre>";
                            }
                            if(trim($key)==='author_intro'){
                            //簡介
                                $val=trim($file_get_content);
                                $_arrys_book_info["book_note"][]=$val;
                                //echo "<Pre>";
                                //print_r($val);
                                //echo "</Pre>";
                            }
                            if(trim($key)==='pages'){
                            //頁數
                                $val=trim($file_get_content);
                                $_arrys_book_info["book_page_count"][]=$val;
                                //echo "<Pre>";
                                //print_r($val);
                                //echo "</Pre>";
                            }
                        }

                    return $_arrys_book_info;
                }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(!$has_find){
            //-------------------------------------------
            //查找, 豆瓣api
            //-------------------------------------------

                if(empty($_arrys_book_info["book_name"])||empty($_arrys_book_info["book_author"])||empty($_arrys_book_info["book_publisher"])||empty($_arrys_book_info["book_note"])||empty($_arrys_book_info["book_page_count"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["douban_api"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_douban_api($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }

            //-------------------------------------------
            //查找, 國圖書號中心
            //-------------------------------------------
                if(empty($_arrys_book_info["book_name"])||empty($_arrys_book_info["book_author"])||empty($_arrys_book_info["book_publisher"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["ncl_isbnnet"];
                    $_arrys_book_info=find_book_ncl_isbnnet($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 金石堂行動版
            //-------------------------------------------
                if(empty($_arrys_book_info["book_name"])||empty($_arrys_book_info["book_author"])||empty($_arrys_book_info["book_publisher"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["金石堂行動版"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_kst_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 博客來行動版
            //-------------------------------------------
                if(empty($_arrys_book_info["book_name"])||empty($_arrys_book_info["book_author"])||empty($_arrys_book_info["book_publisher"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["博客來行動版"];
                    $_url.=addslashes($book_code).'&cat=all&g='.time();
                    $_arrys_book_info=find_book_bkl_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, Findbook行動版
            //-------------------------------------------
                if(empty($_arrys_book_info["book_name"])||empty($_arrys_book_info["book_author"])||empty($_arrys_book_info["book_publisher"])){
                    $_curl=curl_init();
                    $_url=$_arrys_url["Findbook行動版"];
                    $_url.=addslashes($book_code).'/price';
                    $_arrys_book_info=find_book_fbk_m($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 遠流書店
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["ylib"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_ylib($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
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
            //查找, Findbook
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["Findbook"];
                    $_url.=addslashes($book_code).'+site:findbook.tw';
                    $_arrys_book_info=find_book_fbk($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
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
            //-------------------------------------------
            //查找, 台灣大學圖書館
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["台灣大學圖書館"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_ntu($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            //-------------------------------------------
            //查找, 亞馬遜書店
            //-------------------------------------------
                if(!empty($_arrys_book_info["book_name"])&&!empty($_arrys_book_info["book_author"])&&!empty($_arrys_book_info["book_publisher"])){
                    //continue;
                }else{
                    $_curl=curl_init();
                    $_url=$_arrys_url["amazon"];
                    $_url.=addslashes($book_code);
                    $_arrys_book_info=find_book_amazon($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }


//            //-------------------------------------------
//            //查找, 三民
//            //-------------------------------------------
//                if(empty($_arrys_book_info["book_ch_no"])){
//                    $_curl=curl_init();
//                    $_url=$_arrys_url["三民"];
//                    $_url.=addslashes($book_code);
//                    $_url.="+site:sanmin.com.tw";
//                    $_arrys_book_info=find_book_samin($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
//                    curl_close($_curl);
//                }
//            //-------------------------------------------
//            //查找, 國圖
//            //-------------------------------------------
//                if(empty($_arrys_book_info["book_ch_no"])){
//                    $_curl=curl_init();
//                    $_url=$_arrys_url["國圖"];
//                    $_url.=addslashes($book_code);
//                    $_url.="__Orightresult__U1?lang=cht&suite=pearl";
//                    $_arrys_book_info=find_book_country_library($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
//                    curl_close($_curl);
//                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_arrys_book_info;
    }
?>