<?php
//-------------------------------------------------------
//明日聊書
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",1).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/_dev_forum_eric_default/inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //method    函式名稱

        $method='';
        if(isset($_POST['method'])&&trim($_POST['method'])!=='')$method=trim($_POST['method']);
        if(isset($_GET['method'])&&trim($_GET['method'])!=='')$method=trim($_GET['method']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //method    函式名稱

        if($method==='' || !function_exists($method)){
            die(json_encode(array(),true));
        }

    //---------------------------------------------------
    //呼叫函式
    //---------------------------------------------------

        call_user_func($method,$arrys_sess_login_info);

    //---------------------------------------------------
    //函式列表
    //---------------------------------------------------

        //-----------------------------------------------
        //函式: load_bookstore_rec()
        //用途: 讀取書店推薦
        //-----------------------------------------------

            function load_bookstore_rec($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_bookstore_rec()
            //用途: 讀取書店推薦
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //no

                    if(!empty($_POST)){
                        $post_chk=array(
                            'no'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $no=trim($_POST[trim('no')]);
                    $sess_user_book_results=trim($_POST[trim('sess_user_book_results')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                        $no=(int)$no;
                        $sess_user_book_results=json_decode($sess_user_book_results,true);
//echo "<Pre>";
//print_r($sess_user_book_results);
//echo "</Pre>";
//die();
                    ////-----------------------------------
                    ////個人書櫃 SQL
                    ////-----------------------------------
                    //
                    //    $sql="
                    //        SELECT `mssr`.`mssr_rec_book_cno`.`book_sid`
                    //        FROM `mssr`.`mssr_rec_book_cno`
                    //        WHERE 1=1
                    //            AND `mssr`.`mssr_rec_book_cno`.`user_id`={$sess_user_id}
                    //        GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`,
                    //                 `mssr`.`mssr_rec_book_cno`.`book_sid`
                    //        ORDER BY `mssr`.`mssr_rec_book_cno`.`keyin_cdate` DESC
                    //    ";
                    //    $sess_user_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $arry_file_path =array();
                    $json_file_path =json_encode($arry_file_path,true);
                    $no             =(int)$no;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($no===1){
                        $arry_img=array('1.jpg','upload_1.jpg','upload_2.jpg','upload_3.jpg');
                        if(isset($file_server_enable)&&($file_server_enable)){
                            $ftp_root ="public_html/mssr/info/user";
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            ftp_pasv($ftp_conn,TRUE);
                            foreach($sess_user_book_results as $sess_user_book_result){
                                $rs_books_sid=trim($sess_user_book_result['book_sid']);
                                foreach($arry_img as $path){
                                    $path=trim($path);
                                    $ftp_path="{$ftp_root}/{$sess_user_id}/book/{$rs_books_sid}/draw/bimg/{$path}";
                                    $arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path);
                                    if(!empty($arry_ftp_file)){
                                        $file_path="http://".$arry_ftp1_info['host']."/mssr/info/user/{$sess_user_id}/book/{$rs_books_sid}/draw/bimg/{$path}";
                                        $arry_file_path[]=trim($file_path);
                                    }
                                }
                            }
                        }else{
                            foreach($sess_user_book_results as $sess_user_book_result){
                                $rs_books_sid=trim($sess_user_book_result['book_sid']);
                                foreach($arry_img as $path){
                                    $path=trim($path);
                                    $file_path="../../../info/user/{$sess_user_id}/book/{$rs_books_sid}/draw/bimg/{$path}";
                                    if(file_exists($file_path)){
                                        $arry_file_path[]=trim($file_path);
                                    }
                                }
                            }
                        }
                        $json_file_path=json_encode($arry_file_path,true);
                    }elseif($no===2){
                        $sql="
                            SELECT `mssr`.`mssr_rec_book_text_log`.`rec_content`
                            FROM `mssr`.`mssr_rec_book_text_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_rec_book_text_log`.`user_id`={$sess_user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            foreach($db_results as $db_result){
                                $rs_arry_rec_content=trim($db_result['rec_content']);
                                if(@unserialize($rs_arry_rec_content)){
                                    $rs_arry_rec_content=@unserialize($rs_arry_rec_content);
                                    foreach($rs_arry_rec_content as $rs_rec_content){
                                        try{
                                            @$rs_rec_content=htmlspecialchars(trim(gzuncompress(base64_decode($rs_rec_content))));
                                            if(mb_strlen($rs_rec_content)>55){
                                                $rs_rec_content=mb_substr($rs_rec_content,0,55)."..";
                                            }
                                            if(trim($rs_rec_content)!=='')$arry_file_path[]=$rs_rec_content;
                                        }catch(PDOException $e){
                                            continue;
                                        }
                                    }
                                }else{continue;}
                            }
                        }
                        $json_file_path=json_encode($arry_file_path,true);
                    }elseif($no===3){
                        if(isset($file_server_enable)&&($file_server_enable)){
                            $ftp_root ="public_html/mssr/info/user";
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            ftp_pasv($ftp_conn,TRUE);
                            foreach($sess_user_book_results as $sess_user_book_result){
                                $rs_books_sid=trim($sess_user_book_result['book_sid']);
                                $ftp_path="{$ftp_root}/{$sess_user_id}/book/{$rs_books_sid}/record/1.mp3";
                                $arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path);
                                if(!empty($arry_ftp_file)){
                                    $file_path="http://".$arry_ftp1_info['host']."/mssr/info/user/{$sess_user_id}/book/{$rs_books_sid}/record/1.mp3";
                                    $arry_file_path[]=trim($file_path);
                                }
                            }
                        }else{
                            foreach($sess_user_book_results as $sess_user_book_result){
                                $rs_books_sid=trim($sess_user_book_result['book_sid']);
                                $file_path="../../../info/user/{$sess_user_id}/book/{$rs_books_sid}/record/1.mp3";
                                if(file_exists($file_path)){
                                    $arry_file_path[]=trim($file_path);
                                }
                            }
                        }
                        $json_file_path=json_encode($arry_file_path,true);
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die($json_file_path);
            }

        //-----------------------------------------------
        //函式: load_sess_user_book()
        //用途: 讀取書櫃的書籍資訊
        //-----------------------------------------------

            function load_sess_user_book($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_sess_user_book()
            //用途: 讀取書櫃的書籍資訊
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //book_sid

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $book_sid=trim($_POST[trim('book_sid')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_sid =mysql_prep(($book_sid));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $arry_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn_mssr);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($arry_book_info[0],true));
            }

        //-----------------------------------------------
        //函式: load_article_draft()
        //用途: 載入草稿
        //-----------------------------------------------

            function load_article_draft($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_article_draft()
            //用途: 載入草稿
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //book_sid
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid',
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $book_sid=trim($_POST[trim('book_sid')]);
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_sid =mysql_prep(strip_tags($book_sid));
                    $group_id =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        SELECT *
                        FROM `mssr_forum`.`mssr_forum_article_draft`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article_draft`.`user_id` = {$sess_user_id}
                            AND `mssr_forum`.`mssr_forum_article_draft`.`book_sid`='{$book_sid    }'
                            AND `mssr_forum`.`mssr_forum_article_draft`.`group_id`= {$group_id    }
                    ";
                    $draft_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($draft_results,true));
            }

        //-----------------------------------------------
        //函式: load_user_article()
        //用途: 讀取使用者文章
        //-----------------------------------------------

            function load_user_article($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_user_article()
            //用途: 讀取使用者文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //page_article_cno
                //user_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'page_article_cno',
                            'user_id         '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $page_article_cno=trim($_POST[trim('page_article_cno')]);
                    $user_id         =trim($_POST[trim('user_id         ')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $page_article_cno=(int)$page_article_cno;
                    $user_id         =(int)$user_id;

                    //載入筆數
                    $psize           =20;
                    $json_html       =array();

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                            SELECT
                                `user`.`member`.`name`,

                                `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                                `mssr_forum`.`mssr_forum_article`.`user_id`,
                                `mssr_forum`.`mssr_forum_article`.`group_id`,
                                `mssr_forum`.`mssr_forum_article`.`article_id`,
                                0 AS `reply_id`,
                                `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,
                                `mssr_forum`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,

                                `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                                '' AS `reply_content`,
                                'article' AS `type`
                            FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                INNER JOIN `user`.`member` ON
                                `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                AND `mssr_forum`.`mssr_forum_article`.`user_id`      ={$user_id}

                        UNION ALL

                            SELECT
                                `user`.`member`.`name`,

                                `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                                `mssr_forum`.`mssr_forum_reply`.`user_id`,
                                `mssr_forum`.`mssr_forum_reply`.`group_id`,
                                `mssr_forum`.`mssr_forum_reply`.`article_id`,
                                `mssr_forum`.`mssr_forum_reply`.`reply_id`,
                                `mssr_forum`.`mssr_forum_reply`.`keyin_mdate`,
                                `mssr_forum`.`mssr_forum_reply`.`reply_like_cno` AS `like_cno`,

                                `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                                `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`,

                                'reply' AS `type`
                            FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                INNER JOIN `mssr_forum`.`mssr_forum_reply` ON
                                `mssr_forum`.`mssr_forum_reply_book_rev`.`reply_id`=`mssr_forum`.`mssr_forum_reply`.`reply_id`

                                INNER JOIN `mssr_forum`.`mssr_forum_reply_detail` ON
                                `mssr_forum`.`mssr_forum_reply`.`reply_id`=`mssr_forum`.`mssr_forum_reply_detail`.`reply_id`

                                INNER JOIN `user`.`member` ON
                                `mssr_forum`.`mssr_forum_reply`.`user_id`=`user`.`member`.`uid`

                                INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                `mssr_forum`.`mssr_forum_reply`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`=1 -- 回文狀態
                                AND `mssr_forum`.`mssr_forum_reply`.`user_id`    ={$user_id}
                            ORDER BY `keyin_mdate` DESC
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array($page_article_cno,$psize),$arry_conn_mssr);
                    $article_reply_results=array();
                    if(!empty($db_results)){
                        foreach($db_results as $db_result){

                            extract($db_result, EXTR_PREFIX_ALL, "rs");

                            $rs_user_id         =(int)$rs_user_id;
                            $rs_group_id        =(int)$rs_group_id;
                            $rs_article_id      =(int)$rs_article_id;
                            $rs_reply_id        =(int)$rs_reply_id;
                            $rs_like_cno        =(int)$rs_like_cno;

                            $rs_book_sid        =trim($rs_book_sid);
                            $rs_keyin_mdate     =trim($rs_keyin_mdate);
                            $rs_article_title   =trim($rs_article_title);
                            $rs_article_content =trim($rs_article_content);
                            $rs_reply_content   =trim($rs_reply_content);
                            $rs_type            =trim($rs_type);
                            $rs_keyin_time      =strtotime($rs_keyin_mdate);

                            if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                if(!empty($arry_blacklist_group_school))continue;
                            }

                            $article_reply_results[$rs_keyin_time][trim('book_sid       ')]=$rs_book_sid;
                            $article_reply_results[$rs_keyin_time][trim('user_id        ')]=$rs_user_id;
                            $article_reply_results[$rs_keyin_time][trim('group_id       ')]=$rs_group_id;
                            $article_reply_results[$rs_keyin_time][trim('article_id     ')]=$rs_article_id;
                            $article_reply_results[$rs_keyin_time][trim('reply_id       ')]=$rs_reply_id;
                            $article_reply_results[$rs_keyin_time][trim('like_cno       ')]=$rs_like_cno;
                            $article_reply_results[$rs_keyin_time][trim('keyin_mdate    ')]=$rs_keyin_mdate;
                            $article_reply_results[$rs_keyin_time][trim('article_title  ')]=$rs_article_title;
                            $article_reply_results[$rs_keyin_time][trim('article_content')]=$rs_article_content;
                            $article_reply_results[$rs_keyin_time][trim('reply_content  ')]=$rs_reply_content;
                            $article_reply_results[$rs_keyin_time][trim('type           ')]=$rs_type;
                        }
                        //時間排序
                        krsort($article_reply_results);

                        if(!empty($article_reply_results)){
                            $json_html[0] ="";
                            $cno=0;
                            foreach($article_reply_results as $article_reply_result){
                                extract($article_reply_result, EXTR_PREFIX_ALL, "rs");
                                $rs_user_id         =(int)$rs_user_id;
                                $rs_group_id        =(int)$rs_group_id;
                                $rs_article_id      =(int)$rs_article_id;
                                $rs_reply_id        =(int)$rs_reply_id;
                                $rs_like_cno        =(int)$rs_like_cno;
                                $rs_name            =htmlspecialchars(trim($rs_name));
                                $rs_book_sid        =trim($rs_book_sid);
                                $rs_keyin_mdate     =htmlspecialchars(date("Y-m-d H:i",strtotime(trim($rs_keyin_mdate))));
                                $rs_article_title   =htmlspecialchars(trim($rs_article_title));
                                $rs_article_content =trim($rs_article_content);
                                $rs_reply_content   =trim($rs_reply_content);
                                $rs_type            =trim($rs_type);

                                if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                    $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                    if(!empty($arry_blacklist_group_school))continue;
                                }

                                if($rs_group_id===0)$get_from=1;
                                if($rs_group_id!==0)$get_from=2;

                                $rs_arry_content_img=[];
                                if($rs_type==='article'){
                                    $rs_content=$rs_article_content;
                                }else{
                                    $rs_content=$rs_reply_content;
                                }

                                //特殊處理
                                if($get_from===1){
                                    $rs_book_name='';
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}
                                }else{
                                    $rs_group_name='';
                                    $sql="
                                        SELECT
                                            `mssr_forum`.`mssr_forum_group`.`group_name`
                                        FROM `mssr_forum`.`mssr_forum_group`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                    ";
                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                    $rs_group_name=trim(($db_results[0]['group_name']));
                                }

                                //回文次數
                                $sql="
                                    SELECT COUNT(*) AS `cno`
                                    FROM `mssr_forum`.`mssr_forum_reply`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_reply`.`article_id`= {$rs_article_id}
                                ";
                                $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                $reply_article_cno    =(int)($reply_article_results[0]['cno']);

                                $rs_content=htmlspecialchars($rs_content);
                                preg_match_all('/src.*/',$rs_content,$arrys_preg_content);
                                if(!empty($arrys_preg_content)&&isset($arrys_preg_content[0])&&!empty($arrys_preg_content[0])){
                                    $arry_preg_article_content=$arrys_preg_content[0];
                                    foreach($arry_preg_article_content as $preg_article_content){
                                        $preg_article_content=trim($preg_article_content);
                                        $preg_article_content=str_replace("src=","",$preg_article_content);
                                        $preg_article_content=str_replace('&quot;',"",$preg_article_content);
                                        $preg_article_content=str_replace('img]',"",$preg_article_content);
                                        $preg_article_content=str_replace('audio]',"",$preg_article_content);
                                        $file_path=trim($preg_article_content);

                                        if(isset($file_server_enable)&&($file_server_enable)){
                                            $tmp_ftp_root ="public_html/mssr/info/forum";
                                            $tmp_ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                            $tmp_ftp_login=ftp_login($tmp_ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                            ftp_pasv($tmp_ftp_conn,TRUE);
                                            $tmp_file_path=str_replace("http://{$arry_ftp1_info['host']}","public_html",$file_path);
                                            $arry_ftp_file=ftp_nlist($tmp_ftp_conn,$tmp_file_path);
                                            if(!empty($arry_ftp_file)){
                                                $file_info=pathinfo($file_path);
                                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                                if($extension==='jpg'){
                                                    $replace="<img class='img-responsive' src='{$file_path}' width='100%' border='0' alt='rec_draw' style='border:0px solid red;margin:0 auto;'>";
                                                    $rs_arry_content_img[]=$replace;
                                                    $rs_content=str_replace("[img src=&quot;{$file_path}&quot; img]","",$rs_content);
                                                }elseif($extension==='mp3'){
                                                    $replace="
                                                        <audio controls>
                                                            <source src='{$file_path}' type='audio/mpeg'>
                                                        </audio>
                                                    ";
                                                    $rs_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]","",$rs_content);
                                                }else{continue;}
                                            }
                                        }else{
                                            if(file_exists($file_path)){
                                                $file_info=pathinfo($file_path);
                                                $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                                if($extension==='jpg'){
                                                    $replace="<img class='img-responsive' src='{$file_path}' width='100%' border='0' alt='rec_draw' style='border:0px solid red;margin:0 auto;'>";
                                                    $rs_content=str_replace("[img src=&quot;{$file_path}&quot; img]","",$rs_content);
                                                }elseif($extension==='mp3'){
                                                    $replace="
                                                        <audio controls>
                                                            <source src='{$file_path}' type='audio/mpeg'>
                                                        </audio>
                                                    ";
                                                    $rs_content=str_replace("[audio src=&quot;{$file_path}&quot; audio]","",$rs_content);
                                                }else{continue;}
                                            }
                                        }
                                    }
                                }
                                if(mb_strlen($rs_content)>200){
                                    $rs_content=mb_substr($rs_content,0,200)."...";
                                }

                                $json_html[0].="
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12' style='float:none;margin:0 auto;width:92%;border-bottom:1px dashed #000000;'>
                                            <div class='row' style='margin-top:15px;margin-bottom:30px;background-color:#e1e1e1;border-radius:3px;'>
                                                <div class='text-left col-xs-6 col-sm-9 col-md-9 col-lg-9' style='margin-top:15px;margin-bottom:15px;border:0px solid red;'>
                                                    <b>{$rs_article_title}</b>
                                                </div>
                                                <div class='text-right col-xs-6 col-sm-3 col-md-3 col-lg-3' style='margin-top:15px;margin-bottom:15px;border:0px solid red;'>
                                                    <b>{$rs_keyin_mdate}</b>
                                                </div>
                                            </div>

                                ";
                                             if(!empty($rs_arry_content_img)){
                                                $json_html[0].="
                                                    <div class='text-center'>{$rs_arry_content_img[0]}</div><br>
                                                    <p class='text-left'>{$rs_content}</p>
                                                ";
                                             }else{
                                                $json_html[0].="
                                                    <p class='text-left'>{$rs_content}</p>
                                                ";
                                             }
                                $json_html[0].="
                                            <br>
                                            <p style='float:left;'>
                                                <a target='_blank' href='reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>'>
                                                    <span style='color:#428bca;'>(前往觀看...)</span>
                                                </a>
                                            </p>

                                            <p style='float:right;'>
                                                {$rs_like_cno}人說這讚&nbsp;&nbsp;|&nbsp;
                                                {$rs_name}發表 留言({$reply_article_cno})
                                            </p>
                                            <div style='clear:right;'></div>
                                        </div>
                                    </div>
                                ";
                            }
                        }
                    }
//echo "<Pre>";
//print_r($json_html);
//echo "</Pre>";
//die();
                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($json_html,true));
            }

        //-----------------------------------------------
        //函式: load_wall()
        //用途: 讀取動態牆
        //-----------------------------------------------

            function load_wall($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_wall()
            //用途: 讀取動態牆
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //wall_cno

                    if(!empty($_POST)){
                        $post_chk=array(
                            'wall_cno'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $wall_cno=trim($_POST[trim('wall_cno')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $wall_cno    =(int)$wall_cno;
                    $sess_user_id=(int)$sess_user_id;

                    //載入筆數
                    $psize      =30;
                    $json_html  =array();

                //---------------------------------------
                //處理
                //---------------------------------------

                    //-----------------------------------
                    //FTP 登入
                    //-----------------------------------

                        if(isset($file_server_enable)&&($file_server_enable)){
                            ////FTP 路徑
                            //$ftp_root="public_html/mssr/info/user";
                            //
                            ////連接 | 登入 FTP
                            //$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            //$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            //
                            ////設定被動模式
                            //ftp_pasv($ftp_conn,TRUE);
                        }

                    //-----------------------------------
                    //動態牆 SQL
                    //-----------------------------------
                    //好友動態      SQL
                    //追蹤書籍動態  SQL
                    //加入小組動態  SQL

                        //取得好友名單
                        $arry_forum_friend=array();
                        $forum_friend_list='';
                        $friend_results   =get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
                        if(!empty($friend_results)){
                            foreach($friend_results as $friend_result){
                                if((int)$friend_result['friend_state']===1){
                                    if((int)$friend_result['user_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['user_id'];
                                    if((int)$friend_result['friend_id']!==$sess_user_id)$arry_forum_friend[]=$friend_result['friend_id'];
                                }
                            }
                            $forum_friend_list=implode(',',$arry_forum_friend);
                        }

                        //取得追蹤書籍名單
                        $arry_forum_track_book=array();
                        $forum_track_book_list='';
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                            FROM `mssr_forum`.`mssr_forum_track_book`
                            WHERE `mssr_forum`.`mssr_forum_track_book`.`user_id`={$sess_user_id}
                            GROUP BY `mssr_forum`.`mssr_forum_track_book`.`user_id`, `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            foreach($db_results as $db_result){
                                $rs_book_sid=trim($db_result['book_sid']);
                                $arry_forum_track_book[]=$rs_book_sid;
                            }
                            $forum_track_book_list="'".implode("','",$arry_forum_track_book)."'";
                        }

                        //取得加入小組名單
                        $arry_forum_group_user=array();
                        $forum_group_user_list='';
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_group_user_rev`
                            WHERE `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$sess_user_id}
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                            GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`, `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            foreach($db_results as $db_result){
                                $rs_group_id=trim($db_result['group_id']);
                                $arry_forum_group_user[]=$rs_group_id;
                            }
                            $forum_group_user_list=implode(',',$arry_forum_group_user);
                        }

                        //動態牆SQL
                        $wall_results=array();
                        $wall_sql="";

                        if(!empty($arry_forum_friend)){
                            $wall_sql.="
                                SELECT
                                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                                    `user`.`member`.`name`,
                                    `user`.`member`.`sex`,
                                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                                    `mssr_forum`.`mssr_forum_article`.`group_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_from`,
                                    `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                                FROM `mssr_forum`.`mssr_forum_article`
                                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                    INNER JOIN `user`.`member` ON
                                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                                    LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                                    LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                                    `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`user_id` IN ($forum_friend_list)
                                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                    AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                            ";
                            if(!empty($arry_forum_track_book)||!empty($arry_forum_group_user)){
                                $wall_sql.="UNION";
                            }
                        }
                        if(!empty($arry_forum_track_book)){
                            $wall_sql.="
                                SELECT
                                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                                    `user`.`member`.`name`,
                                    `user`.`member`.`sex`,
                                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                                    `mssr_forum`.`mssr_forum_article`.`group_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_from`,
                                    `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                                FROM `mssr_forum`.`mssr_forum_article`
                                    INNER JOIN `mssr_forum`.`mssr_forum_article_book_rev` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                    INNER JOIN `user`.`member` ON
                                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                                    LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                                    `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`user_id` <> {$sess_user_id}
                                    AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid` IN ($forum_track_book_list)
                                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                    AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                            ";
                            if(!empty($arry_forum_group_user)){
                                $wall_sql.="UNION";
                            }
                        }
                        if(!empty($arry_forum_group_user)){
                            $wall_sql.="
                                SELECT
                                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,
                                    `user`.`member`.`name`,
                                    `user`.`member`.`sex`,
                                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                                    `mssr_forum`.`mssr_forum_article`.`group_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_from`,
                                    `mssr_forum`.`mssr_forum_article`.`keyin_cdate`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                                FROM `mssr_forum`.`mssr_forum_article`
                                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                    `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`

                                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                    INNER JOIN `user`.`member` ON
                                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

                                    LEFT JOIN `mssr_forum`.`mssr_forum_article_book_rev`  ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_book_rev`.`article_id`

                                    LEFT JOIN `mssr_forum`.`mssr_forum_group`  ON
                                    `mssr_forum`.`mssr_forum_article`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`user_id` <> {$sess_user_id}
                                    AND `mssr_forum`.`mssr_forum_article`.`group_id` IN ($forum_group_user_list)
                                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                    AND `mssr_forum`.`mssr_forum_article`.`article_from` IN (1,2)
                            ";
                        }

                        //彙整
                        if($wall_sql!==''){
                            $wall_sql.="ORDER BY `keyin_cdate` DESC";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$wall_sql,array($wall_cno,$psize),$arry_conn_mssr);
                            if(!empty($db_results)){
                                foreach($db_results as $db_result){
                                    extract($db_result, EXTR_PREFIX_ALL, "rs");
                                    $rs_group_name     =trim($rs_group_name);
                                    $rs_group_content  =trim($rs_group_content);
                                    $rs_book_sid       =trim($rs_book_sid);
                                    $rs_user_sex       =(int)$rs_sex;
                                    $rs_user_name      =trim($rs_name);
                                    $rs_user_id        =(int)$rs_user_id;
                                    $rs_group_id       =(int)$rs_group_id;
                                    $rs_article_id     =(int)$rs_article_id;
                                    $rs_article_from   =(int)$rs_article_from;
                                    $rs_keyin_cdate    =trim($rs_keyin_cdate);
                                    $rs_article_title  =trim($rs_article_title);
                                    $rs_article_content=trim($rs_article_content);
                                    $rs_time           =trim(strtotime($rs_keyin_cdate));
                                    $arry_user_title   =get_member_title($rs_user_id,$arry_conn_mssr);
                                    $rs_user_title     =(isset($arry_user_title[0]['title_name']))?trim("- ".$arry_user_title[0]['title_name']):'';

                                    $wall_results[$rs_time][trim('group_name     ')]=$rs_group_name;
                                    $wall_results[$rs_time][trim('group_content  ')]=$rs_group_content;
                                    $wall_results[$rs_time][trim('book_sid       ')]=$rs_book_sid;
                                    $wall_results[$rs_time][trim('user_sex       ')]=$rs_user_sex;
                                    $wall_results[$rs_time][trim('user_name      ')]=$rs_user_name;
                                    $wall_results[$rs_time][trim('user_id        ')]=$rs_user_id;
                                    $wall_results[$rs_time][trim('group_id       ')]=$rs_group_id;
                                    $wall_results[$rs_time][trim('article_id     ')]=$rs_article_id;
                                    $wall_results[$rs_time][trim('article_from   ')]=$rs_article_from;
                                    $wall_results[$rs_time][trim('keyin_cdate    ')]=$rs_keyin_cdate;
                                    $wall_results[$rs_time][trim('article_title  ')]=$rs_article_title;
                                    $wall_results[$rs_time][trim('article_content')]=$rs_article_content;
                                    $wall_results[$rs_time][trim('time           ')]=$rs_time;
                                }
                                krsort($wall_results);

                                foreach($wall_results as $rs_time=>$wall_result){
                                    $rs_group_name      =trim($wall_result[trim('group_name     ')]);
                                    $rs_group_content   =trim($wall_result[trim('group_content  ')]);
                                    $rs_book_sid        =trim($wall_result[trim('book_sid       ')]);
                                    $rs_user_sex        =(int)$wall_result[trim('user_sex       ')];
                                    $rs_user_name       =trim($wall_result[trim('user_name      ')]);
                                    $rs_user_id         =(int)$wall_result[trim('user_id        ')];
                                    $rs_group_id        =(int)$wall_result[trim('group_id       ')];
                                    $rs_article_id      =(int)$wall_result[trim('article_id     ')];
                                    $rs_article_from    =(int)$wall_result[trim('article_from   ')];
                                    $rs_keyin_cdate     =trim($wall_result[trim('keyin_cdate    ')]);
                                    $rs_article_title   =trim($wall_result[trim('article_title  ')]);
                                    $rs_article_content =trim($wall_result[trim('article_content')]);
                                    $rs_user_img        ='../img/default/user_boy.png';

                                    if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';

                                    if(isset($file_server_enable)&&($file_server_enable)){
                                        //$rs_user_img_ftp_path="{$ftp_root}/{$rs_user_id}/forum/user_sticker";
                                        //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                                        //if(isset($arry_rs_user_img_ftp_file[0])){
                                        //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        //}
                                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }else{
                                        if(file_exists("../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                                            $rs_user_img="../../../info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                                        }
                                    }

                                    if($rs_group_id!==0){
                                        $sql="
                                            SELECT `mssr_forum`.`mssr_forum_group`.`group_state`
                                            FROM `mssr_forum`.`mssr_forum_group`
                                            WHERE `mssr_forum`.`mssr_forum_group`.`group_id`={$rs_group_id}
                                        ";
                                        $tmp_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(!empty($tmp_results)){
                                            if((int)($tmp_results[0]['group_state'])!==1)continue;
                                        }
                                    }

                                    if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                        $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                        if(!empty($arry_blacklist_group_school))continue;
                                    }

                                    if(trim($rs_book_sid)!==''){
                                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name','book_author','book_publisher'),$arry_conn_mssr);
                                        if(empty($arry_book_infos))continue;
                                        $rs_book_name     =trim($arry_book_infos[0]['book_name']);
                                        $rs_book_author   =trim($arry_book_infos[0]['book_author']);
                                        $rs_book_publisher=trim($arry_book_infos[0]['book_publisher']);
                                    }

                                    if($rs_group_id===0){
                                        $get_from    =1;
                                        $rs_img_1    =trim('../img/default/book.png');
                                        $rs_href_1   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                                        $rs_href_2   =trim("article.php?get_from=1&book_sid={$rs_book_sid}");
                                        $rs_content_1=trim("【<a href='article.php?get_from=1&book_sid={$rs_book_sid}' style='color:#324fe1;'>{$rs_book_name}</a>】 說");
                                        $rs_content_2=trim("{$rs_book_name}");
                                        $rs_content_3=trim("作者：{$rs_book_author}</br/>出版社：{$rs_book_publisher}");
                                    }else{
                                        $get_from    =2;
                                        $rs_img_1    =trim('../img/default/group.jpg');
                                        $rs_href_1   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                                        $rs_href_2   =trim("article.php?get_from=2&group_id={$rs_group_id}");
                                        $rs_content_1=trim("【<a href='article.php?get_from=2&group_id={$rs_group_id}' style='color:#324fe1;'>{$rs_group_name}</a>】 說");
                                        $rs_content_2=trim("{$rs_group_name}");
                                        $rs_content_3=trim("小組簡介：{$rs_group_content}");
                                    }

                                    //動態消息是否為好友
                                    $is_forum_friend    =false;
                                    if($rs_user_id!==$sess_user_id){
                                        $sql="
                                            SELECT
                                                `mssr_forum`.`mssr_forum_friend`.`user_id`,
                                                `mssr_forum`.`mssr_forum_friend`.`friend_id`
                                            FROM `mssr_forum`.`mssr_forum_friend`
                                            WHERE 1=1
                                                AND (
                                                    `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$sess_user_id}
                                                        OR
                                                    `mssr_forum`.`mssr_forum_friend`.`friend_id`={$sess_user_id}
                                                )
                                                AND (
                                                    `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$rs_user_id}
                                                        OR
                                                    `mssr_forum`.`mssr_forum_friend`.`friend_id`={$rs_user_id}
                                                )
                                                AND `mssr_forum`.`mssr_forum_friend`.`friend_state`=1
                                        ";
                                        $friend_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                        if(!empty($friend_results))$is_forum_friend=true;
                                    }

                                    if(!$is_forum_friend){
                                        if(mb_strlen($rs_article_content)>250){
                                            $rs_article_content=mb_substr($rs_article_content,0,250)."..";
                                        }
                                    }else{
                                        if(mb_strlen($rs_article_content)>100){
                                            $rs_article_content=mb_substr($rs_article_content,0,100)."..";
                                        }
                                    }
                                    if($rs_article_from!==3){
                                        $rs_article_content="
                                            <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
                                                {$rs_article_content}
                                            </a>
                                        ";
                                    }else{
                                       $sql="
                                            SELECT `group_task_id`
                                            FROM `mssr_forum`.`dev_article_group_mission_rev`
                                            WHERE `article_id`={$rs_article_id}
                                        ";
                                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        if(!empty($db_results)){
                                            $rs_group_task_id=(int)($db_results[0]['group_task_id']);
                                        }
                                        $rs_article_content="
                                            <a target='_blank' href='_dev_group_mission.php?get_from=3&group_task_id={$rs_group_task_id}&article_id={$rs_article_id}'>
                                                {$rs_article_content}
                                            </a>
                                        ";
                                    }

                                    $html='';
                                    if(!$is_forum_friend){
                                        $html.="
                                            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                                <div class='media' style='margin:10px 0;display:none;'>
                                                    <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                                        <img class='media-object hidden-xs' src='{$rs_user_img}' width='108' height='108' alt='Media'>
                                                        <img class='media-object visible-xs' src='{$rs_user_img}' width='72' height='72' alt='Media'>
                                                    </a>
                                                    <div class='media-body'>
                                                        <h4 class='media-heading hidden-xs' style='position:relative;left:0px;'>
                                                            <span style='color:#324fe1;'><b>
                                                                【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                                                在 {$rs_content_1}
                                                            </b></span>
                                                            <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                                        </h4>
                                                        <h5 class='media-heading visible-xs' style='position:relative;left:0px;'>
                                                            <span style='color:#324fe1;'><b>
                                                                【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                                                在 {$rs_content_1}
                                                            </b></span>
                                                            <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                                        </h5>
                                                        <p style='position:relative;top:5px;'>
                                                            <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}' style='color:#5f5f5f;'>
                                                                <b>{$rs_article_content}</b>
                                                            </a>
                                                            <div class='hidden-xs' style='border-bottom:1px dashed #e1e1e1;margin-top:20px;margin-bottom:20px;'></div>
                                                            <a class='hidden-xs' href='{$rs_href_1}' >
                                                                <div style='background-color:#e1e1e1;'>
                                                                    <img class='media-object' src='{$rs_img_1}' width='60' height='60' alt='Media' style='float:left;'>
                                                                    <div style='font-size:10pt;position:relative;margin-top:10px;left:5px;min-height:60px;color:#5f5f5f;'>
                                                                        {$rs_content_2}<br>
                                                                        {$rs_content_3}
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ";
                                    }else{
                                        $html.="
                                            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                                <div class='media' style='margin:10px 0;display:none;'>
                                                    <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                                        <img class='media-object hidden-xs' src='{$rs_user_img}' width='108' height='108' alt='Media'>
                                                        <img class='media-object visible-xs' src='{$rs_user_img}' width='72' height='72' alt='Media'>
                                                    </a>
                                                    <div class='media-body'>
                                                        <h4 class='media-heading hidden-xs' style='position:relative;left:0px;'>
                                                            <span style='color:#324fe1;'><b>
                                                                你的朋友【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                                                在 {$rs_content_1}
                                                            </b></span>
                                                            <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                                        </h4>
                                                        <h5 class='media-heading visible-xs' style='position:relative;left:0px;'>
                                                            <span style='color:#324fe1;'><b>
                                                                【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                                                在 {$rs_content_1}
                                                            </b></span>
                                                            <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                                        </h5>
                                                        <p style='position:relative;top:5px;'>
                                                            <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}' style='color:#5f5f5f;'>
                                                                <b>{$rs_article_content}</b>
                                                            </a>
                                                            <div class='hidden-xs' style='border-bottom:1px dashed #e1e1e1;margin-top:20px;margin-bottom:20px;'></div>
                                                            <a class='hidden-xs' href='{$rs_href_1}' >
                                                                <div style='background-color:#e1e1e1;'>
                                                                    <img class='media-object' src='{$rs_img_1}' width='60' height='60' alt='Media' style='float:left;'>
                                                                    <div style='font-size:10pt;position:relative;margin-top:10px;left:5px;min-height:60px;color:#5f5f5f;'>
                                                                        {$rs_content_2}<br>
                                                                        {$rs_content_3}
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ";
                                    }
                                    $json_html[].=$html;
                                }
                            }
                        }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($json_html,true));
            }

        //-----------------------------------------------
        //函式: load_book_note()
        //用途: 讀取內容簡介
        //-----------------------------------------------

            function load_book_note($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_book_note()
            //用途: 讀取內容簡介
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //book_sid

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $book_sid=trim($_POST[trim('book_sid')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //檢核書籍資訊
                    //-----------------------------------

                        $book_sid=mysql_prep($book_sid);

                        $arry_book_infos=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                        if(empty($arry_book_infos))die(json_encode($json_html,true));

                        $book_isbn_10='';
                        if(trim($arry_book_infos[0]['book_isbn_10'])!=='')$book_isbn_10=trim($arry_book_infos[0]['book_isbn_10']);

                        $book_isbn_13='';
                        if(trim($arry_book_infos[0]['book_isbn_13'])!=='')$book_isbn_13=trim($arry_book_infos[0]['book_isbn_13']);

                    //-----------------------------------
                    //線上搜尋書籍資訊
                    //-----------------------------------

                        $book_note=trim('');
                        $arry_book_note_online=array();

                        if($book_isbn_10!=='' && empty($arry_book_note_online))$tmp_book_note_online=search_book_note_online($book_isbn_10);
                        if(!empty($tmp_book_note_online))$arry_book_note_online=$tmp_book_note_online;

                        if($book_isbn_13!=='' && empty($arry_book_note_online))$tmp_book_note_online=search_book_note_online($book_isbn_13);
                        if(!empty($tmp_book_note_online))$arry_book_note_online=$tmp_book_note_online;

                        if(isset($arry_book_note_online['book_note'][0])&&!empty($arry_book_note_online)&&(trim($arry_book_note_online['book_note'][0])!=='')){
                            $book_note=trim($arry_book_note_online['book_note'][0]);
                        }

                        //if($book_note==='')$book_note=trim('暫無簡介......');

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_sid       =mysql_prep(strip_tags($book_sid));
                    $book_isbn_10   =mysql_prep(strip_tags($book_isbn_10));
                    $book_isbn_13   =mysql_prep(strip_tags($book_isbn_13));
                    $book_note      =mysql_prep(strip_tags($book_note));

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($book_note!==''){
                        $sql="
                            # for mssr_book_library
                            UPDATE `mssr`.`mssr_book_library` SET
                                `book_note`     = '{$book_note  }'
                            WHERE 1=1
                                AND `book_sid`  = '{$book_sid   }'
                            LIMIT 1;

                            # for mssr_book_class
                            UPDATE `mssr`.`mssr_book_class` SET
                                `book_note`     = '{$book_note  }'
                            WHERE 1=1
                                AND `book_sid`  = '{$book_sid   }'
                            LIMIT 1;

                            # for mssr_book_global
                            UPDATE `mssr`.`mssr_book_global` SET
                                `book_note`     = '{$book_note  }'
                            WHERE 1=1
                                AND `book_sid`  = '{$book_sid   }'
                            LIMIT 1;
                        ";
                        //送出
                        $conn_mssr->exec($sql);
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($book_note,true));
            }

        //-----------------------------------------------
        //函式: load_right_side()
        //用途: 讀取側邊欄
        //-----------------------------------------------

            function load_right_side($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_right_side()
            //用途: 讀取側邊欄
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $arry_conn_user;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //sess_user_id
                //fun

                    if(!empty($_POST)){
                        $post_chk=array(
                            'sess_user_id',
                            'fun         '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $sess_user_id=trim($_POST[trim('sess_user_id')]);
                    $fun         =trim($_POST[trim('fun         ')]);
                    $user_id     =(isset($_POST['user_id']))?(int)$_POST['user_id']:0;
                    $book_sid    =(isset($_POST['book_sid']))?trim($_POST['book_sid']):'';
                    $group_id    =(isset($_POST['group_id']))?(int)$_POST['group_id']:0;

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                    if(!isset($sess_school_code))$sess_school_code='';

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                        //建立連線 user
                        $conn_user=conn($db_type='mysql',$arry_conn_user);

                    //-----------------------------------
                    //檢核6小時內是否已有紀錄
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_cache_right_side_{$fun}`.`json_html`,
                                `mssr_forum`.`mssr_forum_cache_right_side_{$fun}`.`keyin_cdate`
                            FROM `mssr_forum`.`mssr_forum_cache_right_side_{$fun}`
                            WHERE `mssr_forum`.`mssr_forum_cache_right_side_{$fun}`.`user_id`={$sess_user_id}
                            ORDER BY `mssr_forum`.`mssr_forum_cache_right_side_{$fun}`.`keyin_cdate` DESC
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_json_html  =base64_decode($db_results[0]['json_html']);
                            $rs_keyin_cdate=(int)strtotime(trim($db_results[0]['keyin_cdate']));
                            $now_time      =(int)time();
                            $time_diff     =(int)$now_time-$rs_keyin_cdate;
                            if($time_diff<21600 && $now_time>$rs_keyin_cdate){
                                die($rs_json_html);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $fun         =mysql_prep($fun);
                    $user_id     =(int)$user_id;
                    $book_sid    =mysql_prep($book_sid);
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $oright_side=new right_side($sess_user_id,$sess_school_code,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
                    $json_html  ='';

                    switch(trim($fun)){
                    //側邊欄類型

                        case 'member_self':
                        //側欄-人(登入者看到自己)

                            $arrys_member_self=$oright_side->member_self($sess_user_id,$sess_school_code,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
                            if(!empty($arrys_member_self)){
                                foreach($arrys_member_self as $key1=>$arry_member_self){
                                    $key1=(int)$key1;

                                    if($key1===0 && !empty($arry_member_self)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>我可能有興趣的人</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_self as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_self['submain'][$inx])&&!empty($arry_member_self['submain'])){
                                                            if(trim($arry_member_self['submain'][$inx])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● 你們共同讀過：{$arry_member_self['submain'][$inx]}</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===1 && !empty($arry_member_self)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>我可能有興趣的書籍</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_self as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_self['submain'][$inx]['user_id'])&&!empty($arry_member_self['submain'][$inx])){
                                                            if(trim($arry_member_self['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● {$arry_member_self['submain'][$inx]['user_id']}
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_member_self['submain'][$inx]['borrow_cno'])&&!empty($arry_member_self['submain'][$inx])){
                                                            if((int)$arry_member_self['submain'][$inx]['borrow_cno']>0){
                                                                $json_html.="
                                                                    和其他{$arry_member_self['submain'][$inx]['borrow_cno']}人
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_member_self['submain'][$inx]['user_id'])&&!empty($arry_member_self['submain'][$inx])){
                                                            if(trim($arry_member_self['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    也借閱過</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===2 && !empty($arry_member_self)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>我可能有興趣的聊書小組</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_self as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_self['submain'][$inx])&&!empty($arry_member_self['submain'])){
                                                            $json_html.="
                                                                <br><div style='padding:0 5px;font-size:15px;'>● {$arry_member_self['submain'][$inx]}也加入此小組</div>
                                                            ";
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }
                                }
                            }

                        break;

                        case 'member_other':
                        //側欄-人(登入者看到其他人)

                            if((int)$user_id===0)die(json_encode(array(),true));
                            $arrys_member_other=$oright_side->member_other($sess_user_id,$sess_school_code,$user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
                            if(!empty($arrys_member_other)){
                                foreach($arrys_member_other as $key1=>$arry_member_other){
                                    $key1=(int)$key1;

                                    if($key1===0 && !empty($arry_member_other)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>共同好友</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_other as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_other['submain'][$inx])&&!empty($arry_member_other['submain'])){
                                                            if(trim($arry_member_other['submain'][$inx])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● 你們共同讀過：{$arry_member_other['submain'][$inx]}</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===1 && !empty($arry_member_other)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>共同有興趣的書籍</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_other as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_other['submain'][$inx]['user_id'])&&!empty($arry_member_other['submain'][$inx])){
                                                            if(trim($arry_member_other['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● {$arry_member_other['submain'][$inx]['user_id']}
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_member_other['submain'][$inx]['borrow_cno'])&&!empty($arry_member_other['submain'][$inx])){
                                                            if((int)$arry_member_other['submain'][$inx]['borrow_cno']>0){
                                                                $json_html.="
                                                                    和其他{$arry_member_other['submain'][$inx]['borrow_cno']}人
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_member_other['submain'][$inx]['user_id'])&&!empty($arry_member_other['submain'][$inx])){
                                                            if(trim($arry_member_other['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    也借閱過</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===2 && !empty($arry_member_other)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>共同加入的聊書小組</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_member_other as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_member_other['submain'][$inx])&&!empty($arry_member_other['submain'])){
                                                            $json_html.="
                                                                <br><div style='padding:0 5px;font-size:15px;'>● {$arry_member_other['submain'][$inx]}也加入此小組</div>
                                                            ";
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }
                                }
                            }

                        break;

                        case 'book':
                        //側欄-書

                            if(trim($book_sid)==='')die(json_encode(array(),true));
                            $arrys_book=$oright_side->book($sess_user_id,$sess_school_code,$book_sid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
                            if(!empty($arrys_book)){
                                foreach($arrys_book as $key1=>$arry_book){
                                    $key1=(int)$key1;

                                    if($key1===0 && !empty($arry_book)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>誰也看過這本書</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_book as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_book['submain'][$inx])&&!empty($arry_book['submain'])){
                                                            if(trim($arry_book['submain'][$inx])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● 因為{$arry_book['submain'][$inx]}</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===1 && !empty($arry_book)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-left'>看過這本書的人，他們也看了哪些書</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_book as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_book['submain'][$inx]['user_id'])&&!empty($arry_book['submain'][$inx])){
                                                            if(trim($arry_book['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● {$arry_book['submain'][$inx]['user_id']}
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_book['submain'][$inx]['borrow_cno'])&&!empty($arry_book['submain'][$inx])){
                                                            if((int)$arry_book['submain'][$inx]['borrow_cno']>0){
                                                                $json_html.="
                                                                    和其他{$arry_book['submain'][$inx]['borrow_cno']}人
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_book['submain'][$inx]['user_id'])&&!empty($arry_book['submain'][$inx])){
                                                            if(trim($arry_book['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    也借閱過</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===2 && !empty($arry_book)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>哪些小組討論這本書</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_book as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_book['submain'][$inx])&&!empty($arry_book['submain'])){
                                                            $json_html.="
                                                                <br><div style='padding:0 5px;font-size:15px;'>● 他們也討論{$arry_book['submain'][$inx]}</div>
                                                            ";
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }
                                }
                            }

                        break;

                        case 'group':
                        //側欄-組

                            if((int)$group_id===0)die(json_encode(array(),true));
                            $arrys_group=$oright_side->group($sess_user_id,$sess_school_code,$group_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
                            if(!empty($arrys_group)){
                                foreach($arrys_group as $key1=>$arry_group){
                                    $key1=(int)$key1;

                                    if($key1===0 && !empty($arry_group)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>誰也參加這個聊書小組</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_group as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_group['submain'][$inx])&&!empty($arry_group['submain'])){
                                                            if(trim($arry_group['submain'][$inx])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● 也看過{$arry_group['submain'][$inx]}</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===1 && !empty($arry_group)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-center'>小組的熱門書籍</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_group as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_group['submain'][$inx])&&!empty($arry_group['submain'])){
                                                            $json_html.="
                                                                <br><div style='padding:0 5px;font-size:15px;'>● 共討論了{$arry_group['submain'][$inx]}次</div>
                                                            ";
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }

                                    if($key1===2 && !empty($arry_group)){
                                        $json_html.="
                                            <table class='table'>
                                                <thead>
                                                    <tr><td class='text-left'>參加這個聊書小組的人也參加了那些聊書小組?</td></tr>
                                                </thead>
                                                <tbody>
                                        ";
                                        foreach($arry_group as $key2=>$arry_val1){
                                            $key2=trim($key2);
                                            if(!empty($arry_val1)){
                                                foreach($arry_val1 as $inx=>$val1){
                                                    if($key2==='main'){
                                                        $json_html.="
                                                            <tr><td>{$val1}
                                                        ";
                                                        if(isset($arry_group['submain'][$inx]['user_id'])&&!empty($arry_group['submain'][$inx])){
                                                            if(trim($arry_group['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    <br><div style='padding:0 5px;font-size:15px;'>● {$arry_group['submain'][$inx]['user_id']}
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_group['submain'][$inx]['borrow_cno'])&&!empty($arry_group['submain'][$inx])){
                                                            if((int)$arry_group['submain'][$inx]['borrow_cno']>0){
                                                                $json_html.="
                                                                    和其他{$arry_group['submain'][$inx]['borrow_cno']}人
                                                                ";
                                                            }
                                                        }
                                                        if(isset($arry_group['submain'][$inx]['user_id'])&&!empty($arry_group['submain'][$inx])){
                                                            if(trim($arry_group['submain'][$inx]['user_id'])!==''){
                                                                $json_html.="
                                                                    也參加了</div>
                                                                ";
                                                            }
                                                        }
                                                        $json_html.="
                                                            </td></tr>
                                                        ";
                                                    }
                                                }
                                            }
                                        }
                                        $json_html.="
                                                </tbody>
                                            </table>
                                        ";
                                    }
                                }
                            }

                        break;

                        default:

                            die(json_encode(array(),true));

                        break;

                    }

                //---------------------------------------
                //寫入快取
                //---------------------------------------

                    $json_html=json_encode($json_html,true);
                    $base64_encode_json_html=base64_encode($json_html);
                    $keyin_cdate=date("Y-m-d H;:i:s");

                    $sql="
                        INSERT INTO `mssr_forum`.`mssr_forum_cache_right_side_{$fun}` SET
                            `user_id`       =  {$sess_user_id           }  ,
                            `json_html`     = '{$base64_encode_json_html}' ,
                            `keyin_cdate`   = '{$keyin_cdate            }' ;
                    ";
                    $conn_mssr->exec($sql);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die($json_html);
            }

        //-----------------------------------------------
        //函式: load_article()
        //用途: 讀取文章
        //-----------------------------------------------

            function load_article($arrys_sess_login_info){
            //-------------------------------------------
            //函式: load_article()
            //用途: 讀取文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //page_article_cno
                //book_isbn_10
                //book_isbn_13
                //get_book_sid
                //get_group_id
                //get_from

                    if(!empty($_POST)){
                        $post_chk=array(
                            'page_article_cno',
                            'book_isbn_10    ',
                            'book_isbn_13    ',
                            'get_book_sid    ',
                            'get_group_id    ',
                            'get_from        '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die(json_encode(array(),true));
                            }
                        }
                    }else{die(json_encode(array(),true));}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $page_article_cno=trim($_POST[trim('page_article_cno')]);
                    $book_isbn_10    =trim($_POST[trim('book_isbn_10    ')]);
                    $book_isbn_13    =trim($_POST[trim('book_isbn_13    ')]);
                    $get_book_sid    =trim($_POST[trim('get_book_sid    ')]);
                    $get_group_id    =trim($_POST[trim('get_group_id    ')]);
                    $get_from        =trim($_POST[trim('get_from        ')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $page_article_cno=(int)$page_article_cno;
                    $book_isbn_10    =mysql_prep($book_isbn_10);
                    $book_isbn_13    =mysql_prep($book_isbn_13);
                    $get_book_sid    =mysql_prep($get_book_sid);
                    $get_group_id    =(int)$get_group_id;
                    $get_from        =(int)$get_from;

                    //載入筆數
                    $psize           =20;
                    $json_html       =array();

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch($get_from){
                    //文章來源

                        case 1:
                        //書籍

                            $sql="
                                SELECT
                                    `user`.`member`.`name`,

                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                                    `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                                    `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`
                                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                    INNER JOIN `user`.`member` ON
                                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =1 -- 文章來源
                                    AND `mssr_forum`.`mssr_forum_article`.`article_type` =1 -- 文章類型
                                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                            ";
                            if($book_isbn_10!=='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_isbn_10`='{$book_isbn_10}'";
                            if($book_isbn_13!=='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_isbn_13`='{$book_isbn_13}'";
                            if($book_isbn_10===''&&$book_isbn_13==='')$sql.="AND `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`='{$get_book_sid}'";
                            $sql.="ORDER BY `mssr_forum`.`mssr_forum_article`.`keyin_mdate` DESC";
                            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array($page_article_cno,$psize),$arry_conn_mssr);
                            if(!empty($article_results)){
                                $cno=0;
                                foreach($article_results as $inx=>$article_result){
                                    $rs_book_sid        =trim($article_result['book_sid']);
                                    $rs_article_title   =trim($article_result['article_title']);
                                    $rs_user_name       =trim($article_result['name']);
                                    $rs_keyin_mdate     =trim($article_result['keyin_mdate']);
                                    $rs_article_like_cno=(int)($article_result['article_like_cno']);
                                    $rs_article_id      =(int)($article_result['article_id']);
                                    $rs_user_id         =(int)($article_result['user_id']);

                                    //特殊處理
                                    $rs_book_name='';
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{}

                                    $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";
                                    $a_href_2="reply.php?get_from=1&article_id={$rs_article_id}";
                                    $a_href_3="user.php?user_id={$rs_user_id}&tab=1";

                                    //回文次數
                                    $sql="
                                        SELECT
                                            COUNT(*) AS `cno`
                                        FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`  ='{$get_book_sid }'
                                            AND `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`= {$rs_article_id}
                                    ";
                                    $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    $reply_article_cno=(int)($reply_article_results[0]['cno']);

                                    if((int)$page_article_cno%2===0){
                                        $tr_color="#f9f9f9";
                                        if((int)$cno%2!==0)$tr_color="#ffffff";
                                    }else{
                                        $tr_color="#ffffff";
                                        if((int)$cno%2!==0)$tr_color="#f9f9f9";
                                    }

                                    $json_html[].="
                                        <tr align='left' style='background-color:{$tr_color};'>
                                            <!-- 討論,大解析度,start -->
                                            <td class='hidden-xs'><a href='{$a_href_1}'><span style='position:relative;left:8px;'>{$rs_book_name}         </span></a></td>
                                            <td class='hidden-xs'><a href='{$a_href_2}' target='_blank'><span style='position:relative;left:8px;'>{$rs_article_title}     </span></a></td>
                                            <td class='hidden-xs'><a href='{$a_href_3}'><span style='position:relative;left:8px;'>{$rs_user_name}         </span></a></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$rs_keyin_mdate}       </span></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$rs_article_like_cno}  </span></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$reply_article_cno}    </span></td>
                                            <!-- 討論,大解析度,end -->

                                            <!-- 討論,小解析度,start -->
                                            <td class='hidden-sm hidden-md hidden-lg'>
                                                <a href='{$a_href_2}' target='_blank'><span style='position:relative;top:-5px;left:8px;font-size:16px;'>{$rs_article_title}</span></a><br>
                                                <span style='position:relative;top:3px;left:8px;'>
                                                    <a href='{$a_href_1}'>{$rs_book_name}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$a_href_3}'>{$rs_user_name}</a>
                                                </span>
                                            </td>
                                            <!-- 討論,小解析度,end -->
                                        </tr>
                                    ";
                                    $cno++;
                                }
                            }

                        break;

                        case 2:
                        //小組

                            $sql="
                                SELECT
                                    `user`.`member`.`name` AS `user_name`,

                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                                    `mssr_forum`.`mssr_forum_article`.`user_id`,
                                    `mssr_forum`.`mssr_forum_article`.`group_id`,
                                    `mssr_forum`.`mssr_forum_article`.`article_id`,
                                    0 AS `reply_id`,
                                    `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,
                                    `mssr_forum`.`mssr_forum_article`.`article_like_cno` AS `like_cno`,

                                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`,
                                    '' AS `reply_content`,
                                    'article' AS `type`
                                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                    `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                    INNER JOIN `user`.`member` ON
                                    `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`article_from` =2 -- 文章來源
                                    AND `mssr_forum`.`mssr_forum_article`.`article_type` =1 -- 文章類型
                                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                    AND `mssr_forum`.`mssr_forum_article`.`group_id`     ={$get_group_id}
                                ORDER BY `mssr_forum`.`mssr_forum_article`.`keyin_mdate` DESC
                            ";
                            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array($page_article_cno,$psize),$arry_conn_mssr);
                            if(!empty($article_results)){
                                $cno=0;
                                foreach($article_results as $inx=>$article_result){
                                    extract($article_result, EXTR_PREFIX_ALL, "rs");
                                    $rs_user_id         =(int)$rs_user_id;
                                    $rs_group_id        =(int)$rs_group_id;
                                    $rs_article_id      =(int)$rs_article_id;
                                    $rs_reply_id        =(int)$rs_reply_id;
                                    $rs_like_cno        =(int)$rs_like_cno;
                                    $rs_user_name       =trim($rs_user_name);
                                    $rs_book_sid        =trim($rs_book_sid);
                                    $rs_keyin_mdate     =trim($rs_keyin_mdate);
                                    $rs_article_title   =trim($rs_article_title);
                                    $rs_article_content =trim($rs_article_content);
                                    $rs_reply_content   =trim($rs_reply_content);
                                    $rs_type            =trim($rs_type);

                                    if($rs_type!=='article')continue;

                                    if($rs_group_id===0)$get_from=1;
                                    if($rs_group_id!==0)$get_from=2;

                                    //特殊處理
                                    $rs_book_name='';
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(!empty($arry_book_infos)){$rs_book_name=trim($arry_book_infos[0]['book_name']);}else{continue;}

                                    $a_href_1="article.php?get_from=1&book_sid={$rs_book_sid}";
                                    $a_href_2="reply.php?get_from=2&article_id={$rs_article_id}";
                                    $a_href_3="user.php?user_id={$rs_user_id}&tab=1";

                                    //回文次數
                                    $sql="
                                        SELECT
                                            COUNT(*) AS `cno`
                                        FROM `mssr_forum`.`mssr_forum_reply_book_rev`
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_reply_book_rev`.`article_id`={$rs_article_id}
                                    ";
                                    $reply_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                    $reply_article_cno=(int)($reply_article_results[0]['cno']);

                                    if((int)$page_article_cno%2===0){
                                        $tr_color="#f9f9f9";
                                        if((int)$cno%2!==0)$tr_color="#ffffff";
                                    }else{
                                        $tr_color="#ffffff";
                                        if((int)$cno%2!==0)$tr_color="#f9f9f9";
                                    }

                                    $json_html[].="
                                        <tr align='left' style='background-color:{$tr_color};'>
                                            <!-- 討論,大解析度,start -->
                                            <td class='hidden-xs'><a href='{$a_href_1}'><span style='position:relative;left:8px;'>{$rs_book_name}         </span></a></td>
                                            <td class='hidden-xs'><a href='{$a_href_2}' target='_blank'><span style='position:relative;left:8px;'>{$rs_article_title}     </span></a></td>
                                            <td class='hidden-xs'><a href='{$a_href_3}'><span style='position:relative;left:8px;'>{$rs_user_name}         </span></a></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$rs_keyin_mdate}       </span></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$rs_article_like_cno}  </span></td>
                                            <td class='hidden-xs'><span style='position:relative;left:8px;'>{$reply_article_cno}    </span></td>
                                            <!-- 討論,大解析度,end -->

                                            <!-- 討論,小解析度,start -->
                                            <td class='hidden-sm hidden-md hidden-lg'>
                                                <a href='{$a_href_2}' target='_blank'><span style='position:relative;top:-5px;left:8px;font-size:16px;'>{$rs_article_title}</span></a><br>
                                                <span style='position:relative;top:3px;left:8px;'>
                                                    <a href='{$a_href_1}'>{$rs_book_name}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$a_href_3}'>{$rs_user_name}</a>
                                                </span>
                                            </td>
                                            <!-- 討論,小解析度,end -->
                                        </tr>
                                    ";
                                    $cno++;
                                }
                            }

                        break;

                        default:
                            die(json_encode(array(),true));
                        break;

                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //回傳JSON
                //---------------------------------------

                    die(json_encode($json_html,true));
            }
?>

