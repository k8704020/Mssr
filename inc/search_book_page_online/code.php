<?php
//-------------------------------------------------------
//函式: search_book_page_online()
//用途: 查找線上書籍頁數資訊
//-------------------------------------------------------

    ////設定頁面語系
    //header("Content-Type: text/html; charset=UTF-8");
    //
    ////設定文字內部編碼
    //mb_internal_encoding("UTF-8");
    //
    ////設定台灣時區
    //date_default_timezone_set('Asia/Taipei');
    //
    //$search_book_page_online=search_book_page_online('9573317249');
    //echo "<Pre>";
    //print_r($search_book_page_online);
    //echo "</Pre>";
    //die();

    function search_book_page_online($book_code){
    //---------------------------------------------------
    //函式: search_book_page_online()
    //用途: 查找線上書籍頁數資訊
    //---------------------------------------------------
    //$book_code    書籍代碼
    //
    //---------------------------------------------------

        //curl一般設置
        $_timeout=10;
        $_curlopt_useragent="Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0";

        //curl書籍資訊
        $_arrys_book_info=array(
            "page"=>''
        );

        //curl目標網址
        $_arrys_url=array(
            "douban"=>"http://book.douban.com/subject_search?search_text=",
        );

        //初始化, 找書狀況
        $has_find           =false;

        //-----------------------------------------------
        //自訂函式
        //-----------------------------------------------

            function douban($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info){
//echo "<Pre>";
//print_r($_url);
//echo "</Pre>";
//die();
//echo "<Pre>";
//print_r(file_get_contents($_url));
//echo "</Pre>";
//die();
                $options = array(
                    CURLOPT_NOBODY          =>true,
                    CURLOPT_CUSTOMREQUEST   =>'GET',
                    CURLOPT_RETURNTRANSFER  =>1,                    //回傳成字串
                    CURLOPT_URL             =>$_url,                //設定截取網址
                    CURLOPT_HEADER          =>false,                //是否截取header的資訊
                    CURLOPT_FOLLOWLOCATION  =>true,                 //是否抓取轉址
                    CURLOPT_TIMEOUT         =>$_timeout,            //主要逾時時間
                    CURLOPT_CONNECTTIMEOUT  =>$_timeout,            //次要逾時時間
                    CURLOPT_REFERER         =>"www.google.com.tw",  //模擬連結的頁面
                    CURLOPT_USERAGENT       =>$_curlopt_useragent   //瀏覽器的user agent
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
//echo "<Pre>";
//print_r($_output);
//echo "</Pre>";
//die();
                    if($_output!==""){
                        $arry_targets=array();
                        $get_page='';
                        if(empty($_arrys_book_info["page"])){
                            preg_match_all('/<a\s+([\s\S]*?)>([\s\S]*?)<\/a\s*>/i',$_output,$arry_targets,PREG_SET_ORDER);
//echo "<Pre>";
//print_r($arry_targets);
//echo "</Pre>";
//die();
                            if(!empty($arry_targets)){
                                foreach($arry_targets as $inx1=>$arry_target){
                                    foreach($arry_target as $inx2=>$target){
                                        if(preg_match('/class="nbg"/i',$target)){
                                            $tmp1_target=$arry_targets[$inx1+1][0];
                                        }
                                    }
                                }
                                if(trim($tmp1_target)!==''){
                                    preg_match_all('/http:.*title="/i',$tmp1_target,$tmp2_target,PREG_SET_ORDER);
                                    $tmp2_target=$tmp2_target[0][0];
                                    $tmp2_target=preg_replace('/\s+/','',$tmp2_target);
                                    $tmp2_target=preg_replace('/"title="/','',$tmp2_target);
//echo "<Pre>";
//print_r($tmp2_target);
//echo "</Pre>";

                                    if(trim(file_get_contents($tmp2_target))!==''){
                                        $tmp2_target=file_get_contents($tmp2_target);
                                        $tmp2_target=preg_replace('/\s+/','',$tmp2_target);
                                        //echo "<Pre>";
                                        //print_r($tmp2_target);
                                        //echo "</Pre>";
                                        //die();
                                        preg_match_all('/<spanclass="pl">页数:.*<br\/><spanclass="pl">ISBN:/i',$tmp2_target,$tmp3_target,PREG_SET_ORDER);
                                        if(!empty($tmp3_target)){
                                            $tmp3_target=$tmp3_target[0][0];
                                            $tmp3_target=preg_replace('/\s+/','',$tmp3_target);
                                            $tmp3_target=strip_tags($tmp3_target);
                                            $tmp3_target=preg_replace('/页数:/','',$tmp3_target);
                                            $tmp3_target=preg_replace('/ISBN:/','',$tmp3_target);
                                            $get_page   =(int)strip_tags($tmp3_target);

                                            //回填
                                            if($get_page!==0){
                                                $_arrys_book_info["page"]=$get_page;
                                                return $_arrys_book_info;
                                            }
                                        }else{
                                            preg_match_all('/<spanclass="pl">页数:.*<br\/><spanclass="pl">定价:/i',$tmp2_target,$tmp3_target,PREG_SET_ORDER);
                                            if(!empty($tmp3_target)){
                                                $tmp3_target=$tmp3_target[0][0];
                                                $tmp3_target=preg_replace('/\s+/','',$tmp3_target);
                                                $tmp3_target=strip_tags($tmp3_target);
                                                $tmp3_target=preg_replace('/页数:/','',$tmp3_target);
                                                $tmp3_target=preg_replace('/ISBN:/','',$tmp3_target);
                                                $get_page   =(int)strip_tags($tmp3_target);

                                                //回填
                                                if($get_page!==0){
                                                    $_arrys_book_info["page"]=$get_page;
                                                    return $_arrys_book_info;
                                                }
                                            }else{

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(!$has_find){
                if($_arrys_book_info["page"]===''){
                    $_curl=curl_init();
                    $_url =$_arrys_url["douban"];
                    $_url.=$book_code;
                    $_url.="&cat=1001";
                    $_arrys_book_info=douban($_curl,$_url,$_timeout,$_curlopt_useragent,$_arrys_book_info);
                    curl_close($_curl);
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $_arrys_book_info;
    }
?>