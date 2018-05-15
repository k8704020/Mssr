<?php
//---------------------------------------------------
//Findbook
//---------------------------------------------------

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
            $_msg="curl_errno error!";
            die($_msg);
        }

        //-----------------------------------------------
        //篩選
        //-----------------------------------------------

            if((count($_results!==0))&&($_responsestatus===200)){

                //---------------------------------------
                //初始化, 參數
                //---------------------------------------

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

                            //-------------------------------
                            //書名
                            //-------------------------------

                                if(empty($_arrys_book_info["book_name"])){
                                    if($_book_name===''){
                                        //取代, 去空白,標籤
                                        $_book_name=preg_replace('/\s+/','',$_title);
                                        $_book_name=str_replace(array('Findbook','商品簡介','&amp;gt;','-翻書客'),'',$_book_name);
                                    }else{
                                        if($_book_name===''){$_book_name='';}
                                    }
                                }

                            //-------------------------------
                            //作者
                            //-------------------------------

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

                            //-------------------------------
                            //出版社
                            //-------------------------------
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

                            //-------------------------------
                            //書名
                            //-------------------------------

                                if(empty($_arrys_book_info["book_name"])){
                                    if($_book_name===''){
                                        //取代, 去空白,標籤
                                        $_book_name=preg_replace('/\s+/','',$_title);
                                        $_book_name=str_replace(array('Findbook','比價資訊','&amp;gt;','-翻書客'),'',$_book_name);
                                    }else{
                                        if($_book_name===''){$_book_name='';}
                                    }
                                }

                            //-------------------------------
                            //作者
                            //-------------------------------

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

                            //-------------------------------
                            //出版社
                            //-------------------------------

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

                            //-------------------------------
                            //書名
                            //-------------------------------

                                if(empty($_arrys_book_info["book_name"])){
                                    if($_book_name===''){
                                        //取代, 去空白,標籤
                                        $_book_name=preg_replace('/\s+/','',$_title);
                                        $_book_name=str_replace(array('Findbook','部落格貼紙','&amp;gt;','-翻書客'),'',$_book_name);
                                    }else{
                                        if($_book_name===''){$_book_name='';}
                                    }
                                }

                            //-------------------------------
                            //作者
                            //-------------------------------

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

                            //-------------------------------
                            //出版社
                            //-------------------------------

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

            //-------------------------------------------
            //回填, 不覆蓋紀錄
            //-------------------------------------------

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
?>