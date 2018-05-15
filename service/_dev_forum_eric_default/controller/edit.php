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

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/_dev_forum_eric_default/inc/code',

            APP_ROOT.'lib/php/fso/code',
            APP_ROOT.'lib/php/upload/file_upload_save/code',
            APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize',
            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code',
            APP_ROOT.'lib/php/vaildate/code',
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
    //send_url  返回地址

        $method='';
        if(isset($_POST['method'])&&trim($_POST['method'])!=='')$method=trim($_POST['method']);
        if(isset($_GET['method'])&&trim($_GET['method'])!=='')$method=trim($_GET['method']);

        $send_url='';
        if(isset($_POST['send_url'])&&trim($_POST['send_url'])!=='')$send_url=trim($_POST['send_url']);
        if(isset($_GET['send_url'])&&trim($_GET['send_url'])!=='')$send_url=trim($_GET['send_url']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //method    函式名稱
    //send_url  返回地址

        if($method==='' || !function_exists($method) || $send_url===''){
            $msg="發生嚴重錯誤";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='{$send_url}';
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //呼叫函式
    //---------------------------------------------------

        call_user_func($method,$send_url,$arrys_sess_login_info);

    //---------------------------------------------------
    //函式列表
    //---------------------------------------------------

        //-----------------------------------------------
        //函式: edit_book_category_user_rev()
        //用途: 設定書籍類別(軟標籤)
        //-----------------------------------------------

            function edit_book_category_user_rev($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_book_category_user_rev()
            //用途: 設定書籍類別(軟標籤)
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

                    if(!empty($_POST)){
                        $post_chk=array(
                            'cat_code',
                            'book_sid'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $cat_code=trim($_POST[trim('cat_code')]);
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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($cat_code===''){
                       $arry_err[]='類別主索引,未輸入!';
                    }
                    if($book_sid===''){
                       $arry_err[]='書籍識別碼,未輸入!';
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $cat_code=mysql_prep(trim($cat_code));
                        $book_sid=mysql_prep(trim($book_sid));

                    //-----------------------------------
                    //檢核類別是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr`.`mssr_book_category`.`cat_code`
                            FROM `mssr`.`mssr_book_category`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_category`.`school_code`='{$sess_school_code}'
                                AND `mssr`.`mssr_book_category`.`cat1_id` <>1
                                AND `mssr`.`mssr_book_category`.`cat2_id` =1
                                AND `mssr`.`mssr_book_category`.`cat3_id` =1
                                AND `mssr`.`mssr_book_category`.`cat_code`='{$cat_code}'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重問題";
                            die($msg);
                        }

                    //-----------------------------------
                    //檢核書籍是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr`.`mssr_book_borrow_log`.`book_sid`
                            FROM `mssr`.`mssr_book_borrow_log`
                            WHERE 1=1
                                AND `mssr`.`mssr_book_borrow_log`.`user_id` = {$sess_user_id}
                                AND `mssr`.`mssr_book_borrow_log`.`book_sid`='{$book_sid    }'
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重問題";
                            die($msg);
                        }

                //---------------------------------------
                //檢核書籍最後一組類別組別
                //---------------------------------------

                    $sql="
                        SELECT `mssr`.`mssr_book_category_user_rev`.`cat_group`
                        FROM `mssr`.`mssr_book_category_user_rev`
                        WHERE 1=1
                            AND `mssr`.`mssr_book_category_user_rev`.`create_by` = {$sess_user_id}
                            AND `mssr`.`mssr_book_category_user_rev`.`book_sid`  ='{$book_sid}'
                        ORDER BY `mssr`.`mssr_book_category_user_rev`.`cat_group` DESC
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    if(!empty($db_results)){
                        $cat_group=((int)$db_results[0]['cat_group'])+1;
                    }else{
                        $cat_group=(int)1;
                    }

                //---------------------------------------
                //檢核是否已分類過
                //---------------------------------------

                    $sql="
                        SELECT `mssr`.`mssr_book_category_user_rev`.`cat_code`
                        FROM `mssr`.`mssr_book_category_user_rev`
                        WHERE 1=1
                            AND `mssr`.`mssr_book_category_user_rev`.`create_by` = {$sess_user_id}
                            AND `mssr`.`mssr_book_category_user_rev`.`book_sid`  ='{$book_sid}'
                            AND `mssr`.`mssr_book_category_user_rev`.`cat_code`  ='{$cat_code}'
                    ";
                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $create_by  =(int)$sess_user_id;
                    $cat_code   =mysql_prep(strip_tags(trim($cat_code)));
                    $book_sid   =mysql_prep(strip_tags(trim($book_sid)));
                    $rev_id     ="NULL";
                    $cat_group  =(int)$cat_group;
                    $keyin_cdate="NOW()";

                //---------------------------------------
                //處理
                //---------------------------------------

                    if(count($db_results)>=1){
                        $sql="
                            # for mssr_book_category_user_rev
                            DELETE FROM `mssr`.`mssr_book_category_user_rev`
                            WHERE 1=1
                                AND `create_by`= {$sess_user_id}
                                AND `book_sid` ='{$book_sid    }'
                                AND `cat_code` ='{$cat_code    }';
                        ";
                    }else{
                        $sql="
                            # for mssr_book_category_user_rev
                            INSERT INTO `mssr_book_category_user_rev` SET
                                `create_by`  = {$create_by  } ,
                                `cat_code`   ='{$cat_code   }',
                                `book_sid`   ='{$book_sid   }',
                                `rev_id`     = {$rev_id     } ,
                                `cat_group`  = {$cat_group  } ,
                                `keyin_cdate`= {$keyin_cdate} ;
                        ";
                    }
                    //echo "<Pre>";
                    //print_r($sql);
                    //echo "</Pre>";
                    //die();
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="分類成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_book_info()
        //用途: 修正書本資訊
        //-----------------------------------------------

            function edit_book_info($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_book_info()
            //用途: 修正書本資訊
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
                //book_name
                //book_author
                //book_publisher
                //book_isbn_10
                //book_isbn_13
                //book_sid_from
                //book_sid_to

                    if(!empty($_POST)){
                        $post_chk=array(
                            'book_sid_from',
                            'book_sid_to  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $book_sid_from =trim($_POST[trim('book_sid_from')]);
                    $book_sid_to   =trim($_POST[trim('book_sid_to')]);

                    $book_isbn_10  =(isset($_POST[trim('book_isbn_10  ')]))?trim($_POST[trim('book_isbn_10  ')]):'';
                    $book_isbn_13  =(isset($_POST[trim('book_isbn_13  ')]))?trim($_POST[trim('book_isbn_13  ')]):'';
                    $book_name     =(isset($_POST[trim('book_name     ')]))?trim($_POST[trim('book_name     ')]):'';
                    $book_author   =(isset($_POST[trim('book_author   ')]))?trim($_POST[trim('book_author   ')]):'';
                    $book_publisher=(isset($_POST[trim('book_publisher')]))?trim($_POST[trim('book_publisher')]):'';

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($book_sid_from==='' || $book_sid_to===''){
                       $arry_err[]='書籍識別碼,未輸入!';
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $book_sid_from=mysql_prep(trim($book_sid_from));
                        $book_sid_to  =mysql_prep(trim($book_sid_to));

                    //-----------------------------------
                    //檢核書籍是否存在
                    //-----------------------------------

                        $arry_book_infos=get_book_info($conn_mssr,$book_sid_from,$array_filter=array('book_sid','book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                        if(empty($arry_book_infos)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $rs_book_isbn_10_from=trim($arry_book_infos[0]['book_isbn_10']);
                            $rs_book_isbn_13_from=trim($arry_book_infos[0]['book_isbn_13']);
                            if($book_isbn_10===''&&$rs_book_isbn_10_from!=='')$book_isbn_10=$rs_book_isbn_10_from;
                            if($book_isbn_13===''&&$rs_book_isbn_13_from!=='')$book_isbn_13=$rs_book_isbn_13_from;
                        }

                        $arry_book_infos=get_book_info($conn_mssr,$book_sid_to,$array_filter=array('book_sid','book_isbn_10','book_isbn_13'),$arry_conn_mssr);
                        if(empty($arry_book_infos)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $rs_book_isbn_10_to=trim($arry_book_infos[0]['book_isbn_10']);
                            $rs_book_isbn_13_to=trim($arry_book_infos[0]['book_isbn_13']);
                            if($book_isbn_10===''&&$rs_book_isbn_10_to!=='')$book_isbn_10=$rs_book_isbn_10_to;
                            if($book_isbn_13===''&&$rs_book_isbn_13_to!=='')$book_isbn_13=$rs_book_isbn_13_to;
                        }

                    //-----------------------------------
                    //檢核ISBN碼是否錯誤
                    //-----------------------------------

                        if($book_isbn_10!=='')$ch_isbn_10=ch_isbn_10($book_isbn_10, $convert=false);
                        if($book_isbn_13!=='')$ch_isbn_13=ch_isbn_13($book_isbn_13, $convert=false);

                        if(isset($ch_isbn_10[trim('error')])){
                            $msg="ISBN10碼錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }

                        if(isset($ch_isbn_13[trim('error')])){
                            $msg="ISBN13碼錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $book_isbn_10   =mysql_prep(strip_tags(trim($book_isbn_10       )));
                    $book_isbn_13   =mysql_prep(strip_tags(trim($book_isbn_13       )));
                    $book_sid_from  =mysql_prep(trim($book_sid_from                 ));
                    $book_sid_to    =mysql_prep(trim($book_sid_to                   ));
                    //$book_name      =mysql_prep(strip_tags(trim($book_name       )));
                    //$book_author    =mysql_prep(strip_tags(trim($book_author     )));
                    //$book_publisher =mysql_prep(strip_tags(trim($book_publisher  )));
//echo "<Pre>";
//print_r($book_sid_from);
//echo "</Pre>";
//echo "<Pre>";
//print_r($book_sid_to);
//echo "</Pre>";
//echo "<Pre>";
//print_r($book_isbn_10);
//echo "</Pre>";
//echo "<Pre>";
//print_r($book_isbn_13);
//echo "</Pre>";
//die();
                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql ="";

                    //---------------------------------------
                    //from
                    //---------------------------------------

                        if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from!==''){}else{
                        $sql.="
                            # for mssr_book_global
                            UPDATE `mssr`.`mssr_book_global` SET
                        ";
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_global`.`book_sid`='{$book_sid_from}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from!==''){}else{
                        $sql.="
                            # for mssr_book_class
                            UPDATE `mssr`.`mssr_book_class` SET
                        ";
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_class`.`book_sid`='{$book_sid_from}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from!==''){}else{
                        $sql.="
                            # for mssr_book_library
                            UPDATE `mssr`.`mssr_book_library` SET
                        ";
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_library`.`book_sid`='{$book_sid_from}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from!==''){}else{
                        $sql.="
                            # for mssr_book_unverified
                            UPDATE `mssr`.`mssr_book_unverified` SET
                        ";
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_from===''&&$rs_book_isbn_13_from!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_from!==''&&$rs_book_isbn_13_from===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_unverified`.`book_sid`='{$book_sid_from}'
                            LIMIT 1;
                        ";
                        }

                    //---------------------------------------
                    //to
                    //---------------------------------------

                        if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to!==''){}else{
                        $sql.="
                            # for mssr_book_global
                            UPDATE `mssr`.`mssr_book_global` SET
                        ";
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_global`.`book_sid`='{$book_sid_to}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to!==''){}else{
                        $sql.="
                            # for mssr_book_class
                            UPDATE `mssr`.`mssr_book_class` SET
                        ";
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_class`.`book_sid`='{$book_sid_to}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to!==''){}else{
                        $sql.="
                            # for mssr_book_library
                            UPDATE `mssr`.`mssr_book_library` SET
                        ";
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_library`.`book_sid`='{$book_sid_to}'
                            LIMIT 1;
                        ";
                        }
                        if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to!==''){}else{
                        $sql.="
                            # for mssr_book_unverified
                            UPDATE `mssr`.`mssr_book_unverified` SET
                        ";
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}',";
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                            if($rs_book_isbn_10_to===''&&$rs_book_isbn_13_to!==''){
                                $sql.="`book_isbn_10`='{$book_isbn_10}'";
                            }
                            if($rs_book_isbn_10_to!==''&&$rs_book_isbn_13_to===''){
                                $sql.="`book_isbn_13`='{$book_isbn_13}'";
                            }
                        $sql.="
                            WHERE 1=1
                                AND `mssr`.`mssr_book_unverified`.`book_sid`='{$book_sid_to}'
                            LIMIT 1;
                        ";
                        }
                        //echo "<Pre>";
                        //print_r($rs_book_isbn_10_to);
                        //echo "</Pre>";
                        //echo "<Pre>";
                        //print_r($rs_book_isbn_13_to);
                        //echo "</Pre>";
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";
                        //die();

                        //送出
                        if(trim($sql)!=='')$conn_mssr->exec($sql);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="更新成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='{$send_url}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_article()
        //用途: 更新文章
        //-----------------------------------------------

            function edit_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_article()
            //用途: 更新文章
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;
                    global $fso_enc;
                    global $page_enc;

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //article_title
                //article_content
                //eagle_code
                //article_from
                //article_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'article_title  ',
                            'article_content',
                            'eagle_code     ',
                            'article_from   ',
                            'article_id     '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $article_title  =trim($_POST[trim('article_title  ')]);
                    $tmp_eagle_code =trim($_POST[trim('eagle_code     ')]);
                    $article_from   =trim($_POST[trim('article_from   ')]);
                    $article_id     =trim($_POST[trim('article_id     ')]);
                    $article_contents =array_map("trim",$_POST[trim('article_content')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($article_title===''){
                       $arry_err[]='文章標題,未輸入!';
                    }
                    if($tmp_eagle_code===''){
                       $arry_err[]='鷹架代號,未輸入!';
                    }
                    if($article_from===''){
                       $arry_err[]='文章來源,未輸入!';
                    }else{
                        $article_from=(int)$article_from;
                        if($article_from===0){
                            $arry_err[]='文章來源,錯誤!';
                        }
                    }
                    foreach($article_contents as $article_content){
                        if($article_content===''){
                            $arry_err[]='文章內容,未輸入!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $article_id=(int)$article_id;

                    //-----------------------------------
                    //檢核文章是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `user`.`member`.`name`,
                                `user`.`member`.`sex`,

                                `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                                `mssr_forum`.`mssr_forum_article`.`user_id`,
                                `mssr_forum`.`mssr_forum_article`.`group_id`,
                                `mssr_forum`.`mssr_forum_article`.`article_id`,
                                `mssr_forum`.`mssr_forum_article`.`article_from`,
                                `mssr_forum`.`mssr_forum_article`.`article_category`,
                                `mssr_forum`.`mssr_forum_article`.`article_like_cno`,
                                `mssr_forum`.`mssr_forum_article`.`article_report_cno`,
                                `mssr_forum`.`mssr_forum_article`.`keyin_mdate`,

                                `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                                `mssr_forum`.`mssr_forum_article_detail`.`article_content`
                            FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`

                                INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                                `mssr_forum`.`mssr_forum_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                                INNER JOIN `user`.`member` ON
                                `mssr_forum`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$article_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }else{
                            $group_id=(int)$db_results[0]['group_id'];
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $article_title      =bad_content_filter(mysql_prep(strip_tags($article_title)));
                    $article_from       =(int)$article_from;
                    $article_content    =bad_content_filter(mysql_prep(strip_tags(implode("",$article_contents))));
                    $article_content    =str_replace("\\r\\n","\n",$article_content);
                    $keyin_ip           =get_ip();

                    $tmp_eagle_code     =mysql_prep(strip_tags(trim($tmp_eagle_code)));
                    $arry_eagle_code    =explode(",",$tmp_eagle_code);
                    $arry_eagle_code    =array_diff($arry_eagle_code, array(null,'null','',' '));

                    $arry_lv=array();
                    foreach($arry_eagle_code as $eagle_code){
                        $eagle_code=(int)$eagle_code;
                        if($eagle_code===0){
                            $article_category=1;
                            break;
                        }
                        if(in_array($eagle_code,array(1,2,3,4,60,61,62,7,8,9,63,64,65,66,67,68,69,19,20,47,25,26,27,120,121))){
                            $arry_lv[]=(int)1;
                        }
                        if(in_array($eagle_code,array(13,14,15,5,6,70,71,72,73,74,10,11,12,75,76,77,78,79,122,123,124,125,126,127,128,28,29,30,45,55,56,42,43,22))){
                            $arry_lv[]=(int)3;
                        }
                        if(in_array($eagle_code,array(80,81,82,83,84,85,86,87,88,89,90,16,17,102,103,104,105,106,107,108,109,110,111,112,91,92,93,94,95,96,97,98,99,100,101,21,51,52,38,39,132,133,31,32,33,134,135,136,137,138,34,35,49,50,36,37,53,59,129,130,131))){
                            $arry_lv[]=(int)5;
                        }
                        if(in_array($eagle_code,array(48,139,140,141,142,143,57,113,114,115,116,117,118,119))){
                            $arry_lv[]=(int)7;
                        }
                    }
                    $arry_lv=array_unique($arry_lv);
                    $lv=0;
                    foreach($arry_lv as $val){
                        $lv=$lv+(int)$val;
                    }
                    if($lv===1){
                        $article_category=4;
                    }
                    if($lv===3){
                        $article_category=5;
                    }
                    if($lv===5){
                        $article_category=6;
                    }
                    if($lv===7){
                        $article_category=7;
                    }
                    if($lv!==1 && $lv!==3 && $lv!==5 && $lv!==7){
                        $article_category=1;
                    }

                //---------------------------------------
                //檔案處理
                //---------------------------------------

                    $arry_file=array('1.jpg','upload_1.jpg','upload_2.jpg','upload_3.jpg','1.mp3');
//echo "<Pre>";
//print_r($article_content);
//echo "</Pre>";
                    if(isset($file_server_enable)&&($file_server_enable)){
                        $arrys_preg_article_content=array();
                        preg_match_all('/src.*/',$article_content,$arrys_preg_article_content);
                        if(!empty($arrys_preg_article_content)&&isset($arrys_preg_article_content[0])&&!empty($arrys_preg_article_content[0])){
                            $ftp_root ="public_html/mssr/info/forum";
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            ftp_pasv($ftp_conn,TRUE);
                            $arry_preg_article_content=$arrys_preg_article_content[0];
                            $arry_copy_file_path=[];
                            $arry_old_ftp_path=[];
                            $arry_new_ftp_path=[];
                            foreach($arry_preg_article_content as $preg_article_content){
                                $preg_article_content=trim($preg_article_content);
                                $preg_article_content=str_replace("src=","",$preg_article_content);
                                $preg_article_content=str_replace('\"',"",$preg_article_content);
                                $preg_article_content=str_replace('img]',"",$preg_article_content);
                                $preg_article_content=str_replace('audio]',"",$preg_article_content);
                                $file_path=trim($preg_article_content);
                                $ftp_path=str_replace("http://{$arry_ftp1_info['host']}","public_html",$file_path);
                                $arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path);
                                if(!empty($arry_ftp_file)){
                                    $file_info=pathinfo($file_path);
                                    $dirname  =(isset($file_info['dirname']))?$file_info['dirname']:'';
                                    $basename =(isset($file_info['basename']))?$file_info['basename']:'';
                                    $extension=(isset($file_info['extension']))?$file_info['extension']:'';

                                    $root=str_repeat("../",3)."info/forum/group/{$group_id}/article";
                                    $path="{$root}/{$article_id}";

                                    //資料夾
                                    $arrys_path=array(
                                        "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                                        "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                                    );
                                    foreach($arrys_path as $path=>$path_enc){
                                        if(!file_exists($path_enc)){
                                            mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                                        }
                                    }

                                    //溢位判斷
                                    if(!fso_isunder($root,$path,$fso_enc)){
                                        $err_msg="上傳失敗,溢位.請重新上傳!";
                                        die($err_msg);
                                    }

                                    $copy_file_name=$sess_user_id.time().rand(0,9999999);
                                    $copy_file_path="{$path}/{$copy_file_name}.{$extension}";

                                    try{
                                        @copy($file_path,$copy_file_path);
                                        $arry_copy_file_path[]=$copy_file_path;
                                        $arry_old_ftp_path[]=$file_path;
                                    }catch(Exception $e){}
                                }
                            }
                            if(!empty($arry_copy_file_path)){
                                foreach($arry_copy_file_path as $copy_file_path){
                                    $copy_file_path=trim($copy_file_path);
                                    if(file_exists($copy_file_path)){
                                        $file_info=pathinfo($copy_file_path);
                                        $dirname  =(isset($file_info['dirname']))?$file_info['dirname']:'';
                                        $basename =(isset($file_info['basename']))?$file_info['basename']:'';
                                        $extension=(isset($file_info['extension']))?$file_info['extension']:'';

                                        //檢核FTP資料夾
                                        $arrys_ftp_path=array(
                                            "{$ftp_root}"                          =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                            "{$ftp_root}/group"                    =>mb_convert_encoding("{$ftp_root}/group",$fso_enc,$page_enc),
                                            "{$ftp_root}/group/{$group_id}"        =>mb_convert_encoding("{$ftp_root}/group/{$group_id}",$fso_enc,$page_enc),
                                            "{$ftp_root}/group/{$group_id}/article"=>mb_convert_encoding("{$ftp_root}/group/{$group_id}/article",$fso_enc,$page_enc),
                                            "{$ftp_root}/group/{$group_id}/article/{$article_id}"=>mb_convert_encoding("{$ftp_root}/group/{$group_id}/article/{$article_id}",$fso_enc,$page_enc)
                                        );
                                        foreach($arrys_ftp_path as $_path=>$_path_enc){
                                            //重新連接 | 重新登入 FTP
                                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                            if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                                mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                            }
                                            //關閉連線
                                            ftp_close($ftp_conn);
                                        }

                                        //重新連接 | 重新登入 FTP
                                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                        //設定被動模式
                                        ftp_pasv($ftp_conn,TRUE);

                                        //設置ftp路徑
                                        ftp_chdir($ftp_conn,"{$ftp_root}/group/{$group_id}/article/{$article_id}");

                                        //ftp上傳
                                        ftp_put($ftp_conn,"{$basename}","{$copy_file_path}",FTP_BINARY);

                                        //關閉連線
                                        ftp_close($ftp_conn);

                                        $arry_new_ftp_path[]="http://{$arry_ftp1_info['host']}/mssr/info/forum/group/{$group_id}/article/{$article_id}/{$basename}";
                                    }
                                }
                                //移除本機圖片
                                rm_dir($path,$fso_enc);
                            }
                        }
                        if(!empty($arry_old_ftp_path)){
                            foreach($arry_old_ftp_path as $inx=>$old_ftp_path){
                                $old_ftp_path=trim($old_ftp_path);
                                if(isset($arry_new_ftp_path[$inx])){
                                    $new_ftp_path=trim($arry_new_ftp_path[$inx]);
                                    //替換路徑
                                    $article_content=str_replace("{$old_ftp_path}","{$new_ftp_path}",$article_content);
                                }
                            }
                        }

                        //重新連接 | 重新登入 FTP
                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                        //設定被動模式
                        ftp_pasv($ftp_conn,TRUE);

                        $arry_ftp_file=ftp_nlist($ftp_conn,"{$ftp_root}/group/{$group_id}/article/{$article_id}");

                        preg_match_all('/src.*/',$article_content,$arrys_preg_article_content);
                        if(!empty($arrys_preg_article_content)&&isset($arrys_preg_article_content[0])&&!empty($arrys_preg_article_content[0])){
                            $arry_preg_article_content=$arrys_preg_article_content[0];
                            $tmp_arry_preg_article_content=[];
                            foreach($arry_preg_article_content as $preg_article_content){
                                $preg_article_content=trim($preg_article_content);
                                $preg_article_content=str_replace("src=","",$preg_article_content);
                                $preg_article_content=str_replace('\"',"",$preg_article_content);
                                $preg_article_content=str_replace('img]',"",$preg_article_content);
                                $preg_article_content=str_replace('audio]',"",$preg_article_content);
                                $preg_article_content=str_replace("http://{$arry_ftp1_info['host']}","public_html",$preg_article_content);
                                $tmp_arry_preg_article_content[]=trim($preg_article_content);
                            }
                            foreach($arry_ftp_file as $ftp_file_path){
                                $ftp_file_path=trim($ftp_file_path);
                                if(!in_array($ftp_file_path,$tmp_arry_preg_article_content)){
                                    ftp_delete($ftp_conn,$ftp_file_path);
                                }
                            }
                        }
//echo "<Pre>";
//print_r($arry_ftp_file);
//echo "</Pre>";
                    }else{
                        $arrys_preg_article_content=array();
                        preg_match_all('/src.*/',$article_content,$arrys_preg_article_content);
                        if(!empty($arrys_preg_article_content)&&isset($arrys_preg_article_content[0])&&!empty($arrys_preg_article_content[0])){
                            $arry_preg_article_content=$arrys_preg_article_content[0];
                            $arry_success_file_path=[];
                            $arry_fail_file_path=[];
                            foreach($arry_preg_article_content as $inx=>$preg_article_content){
                                $inx=(int)$inx;
                                $preg_article_content=trim($preg_article_content);
                                $preg_article_content=str_replace("src=","",$preg_article_content);
                                $preg_article_content=str_replace('\"',"",$preg_article_content);
                                $preg_article_content=str_replace('img]',"",$preg_article_content);
                                $preg_article_content=str_replace('audio]',"",$preg_article_content);
                                $file_path=trim($preg_article_content);
                                if(file_exists($file_path)){
                                    $arry_success_file_path[]=$file_path;
                                    $file_info=pathinfo($file_path);
                                    $dirname  =(isset($file_info['dirname']))?$file_info['dirname']:'';
                                    $basename =(isset($file_info['basename']))?$file_info['basename']:'';
                                    $extension=(isset($file_info['extension']))?$file_info['extension']:'';
                                    $root     =str_repeat("../",3)."info/forum/group/{$group_id}/article";
                                    $path     ="{$root}/{$article_id}";
                                    if(in_array(trim($basename),$arry_file)){
                                        //資料夾
                                        $arrys_path=array(
                                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                                        );
                                        foreach($arrys_path as $path=>$path_enc){
                                            if(!file_exists($path_enc)){
                                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                                            }
                                        }

                                        //溢位判斷
                                        if(!fso_isunder($root,$path,$fso_enc)){
                                            $err_msg="上傳失敗,溢位.請重新上傳!";
                                            die($err_msg);
                                        }

                                        $copy_file_name=$sess_user_id.time().rand(0,9999999);
                                        $copy_file_path="{$path}/{$copy_file_name}.{$extension}";

                                        try{
                                            @copy($file_path,$copy_file_path);
                                            $article_content=str_replace("{$dirname}/{$basename}","{$path}/{$copy_file_name}.{$extension}",$article_content);
                                            $arry_success_file_path[]=$copy_file_path;
                                        }catch(Exception $e){}
                                    }
                                    if($inx===count($arry_preg_article_content)-1){
                                        $arry_dir_files=dir_files($path,$fso_enc,$allow_exts=array());
                                        foreach($arry_dir_files as $dir_file){
                                            $dir_file=trim($dir_file);
                                            if(!in_array($dir_file,$arry_success_file_path)){
                                                @unlink($dir_file);
                                            }
                                        }
                                    }
                                }else{
                                    $arry_fail_file_path[]=$file_path;
                                    if($inx===count($arry_preg_article_content)-1){
                                        $root=str_repeat("../",3)."info/forum/group/{$group_id}/article";
                                        $path="{$root}/{$article_id}";
                                        $arry_dir_files=dir_files($path,$fso_enc,$allow_exts=array());
                                        foreach($arry_dir_files as $dir_file){
                                            $dir_file=trim($dir_file);
                                            if(!in_array($dir_file,$arry_fail_file_path)){
                                                @unlink($dir_file);
                                            }
                                        }
                                    }
                                    $arry_dir_files=dir_files($path,$fso_enc,$allow_exts=array());
                                    if(empty($arry_dir_files))rm_dir($path,$fso_enc);
                                }
                            }
                        }else{
                            $root=str_repeat("../",3)."info/forum/group/{$group_id}/article";
                            $path="{$root}/{$article_id}";
                            rm_dir($path,$fso_enc);
                        }
                    }
//echo "<Pre>";
//print_r($article_content);
//echo "</Pre>";
//die();
                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_article
                        UPDATE `mssr_forum`.`mssr_forum_article` SET
                            `article_category` = {$article_category}
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article`.`article_id`={$article_id}
                        LIMIT 1;

                        # for mssr_forum_article_detail
                        UPDATE `mssr_forum`.`mssr_forum_article_detail` SET
                            `article_title`   = '{$article_title  }',
                            `article_content` = '{$article_content}',
                            `keyin_ip`        = '{$keyin_ip       }'
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article_detail`.`article_id`={$article_id}
                        LIMIT 1;

                        # for mssr_forum_article_detail_log
                        INSERT IGNORE INTO `mssr_forum`.`mssr_forum_article_detail_log` SET
                            `article_id`        =  {$article_id     } ,
                            `log_id`            = NULL                ,
                            `article_title`     = '{$article_title  }',
                            `article_content`   = '{$article_content}',
                            `keyin_ip`          = '{$keyin_ip       }';

                        # for mssr_forum_article_eagle_rev
                        DELETE FROM `mssr_forum`.`mssr_forum_article_eagle_rev`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article_eagle_rev`.`article_id`={$article_id};
                    ";
                    //echo "<Pre>";
                    //print_r($sql);
                    //echo "</Pre>";
                    //送出
                    $conn_mssr->exec($sql);

                    foreach($arry_eagle_code as $eagle_code){
                        $eagle_code=(int)$eagle_code;
                        $sql="
                            # for mssr_forum_article_eagle_rev
                            INSERT IGNORE INTO `mssr_forum`.`mssr_forum_article_eagle_rev` SET
                                `eagle_code`    = {$eagle_code      },
                                `article_id`    = {$article_id      },
                                `keyin_mdate`   = NULL               ;
                        ";
                        //echo "<Pre>";
                        //print_r($sql);
                        //echo "</Pre>";
                        $conn_mssr->exec($sql);
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="更新成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/reply.php?get_from={$article_from}&article_id={$article_id}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_reply()
        //用途: 更新回覆
        //-----------------------------------------------

            function edit_reply($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_reply()
            //用途: 更新回覆
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
                //reply_content
                //article_id
                //reply_id
                //get_from

                    if(!empty($_POST)){
                        $post_chk=array(
                            'reply_content',
                            'article_id   ',
                            'reply_id     ',
                            'get_from     '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $article_id     =trim($_POST[trim('article_id   ')]);
                    $reply_id       =trim($_POST[trim('reply_id     ')]);
                    $get_from       =trim($_POST[trim('get_from     ')]);
                    $reply_contents =array_map("trim",$_POST[trim('reply_content')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($article_id===''){
                       $arry_err[]='文章主索引,未輸入!';
                    }else{
                        $article_id=(int)$article_id;
                        if($article_id===0){
                            $arry_err[]='文章主索引,錯誤!';
                        }
                    }
                    if($reply_id===''){
                       $arry_err[]='回文主索引,未輸入!';
                    }else{
                        $reply_id=(int)$reply_id;
                        if($reply_id===0){
                            $arry_err[]='回文主索引,錯誤!';
                        }
                    }
                    foreach($reply_contents as $reply_content){
                        if($reply_content===''){
                            $arry_err[]='回文內容,未輸入!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $article_id=(int)$article_id;
                        $reply_id  =(int)$reply_id;

                    //-----------------------------------
                    //檢核文章是否存在
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_reply`.`article_id`,
                                `mssr_forum`.`mssr_forum_reply`.`reply_id`
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
                                AND `mssr_forum`.`mssr_forum_article`.`article_state`=1 -- 文章狀態
                                AND `mssr_forum`.`mssr_forum_reply`.`reply_state`    =1 -- 回文狀態
                                AND `mssr_forum`.`mssr_forum_reply`.`article_id`     ={$article_id}
                                AND `mssr_forum`.`mssr_forum_reply`.`reply_id`       ={$reply_id  }
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            $jscript_back="
                                <script>
                                    alert('{$msg}');
                                    location.href='{$send_url}';
                                </script>
                            ";
                            die($jscript_back);
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $get_from       =(int)$get_from;
                    $article_id     =(int)$article_id;
                    $reply_id       =(int)$reply_id;
                    $reply_content  =bad_content_filter(mysql_prep(strip_tags(implode("",$reply_contents))));
                    $keyin_ip       =get_ip();

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_reply_detail
                        UPDATE `mssr_forum`.`mssr_forum_reply_detail` SET
                            `reply_content` = '{$reply_content  }',
                            `keyin_ip`      = '{$keyin_ip       }'
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_reply_detail`.`article_id`={$article_id}
                            AND `mssr_forum`.`mssr_forum_reply_detail`.`reply_id`  ={$reply_id  }
                        LIMIT 1;

                        # for mssr_forum_reply_detail_log
                        INSERT IGNORE INTO `mssr_forum`.`mssr_forum_reply_detail_log` SET
                            `article_id`        =  {$article_id     } ,
                            `reply_id`          =  {$reply_id       } ,
                            `log_id`            = NULL                ,
                            `reply_content`     = '{$reply_content  }',
                            `keyin_ip`          = '{$keyin_ip       }';
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="更新成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/reply.php?get_from={$get_from}&article_id={$article_id}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_user_info()
        //用途: 更新個人資訊
        //-----------------------------------------------

            function edit_user_info($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_user_info()
            //用途: 更新個人資訊
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

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_content'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $user_content=trim($_POST[trim('user_content')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $user_content=mysql_prep(trim($user_content));

                    //-----------------------------------
                    //檢核使用者是否存在
                    //-----------------------------------

                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_user_info`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_user_info`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_info`.`user_id`={$sess_user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $user_content=bad_content_filter(mysql_prep(strip_tags(trim($user_content))));

                //---------------------------------------
                //處理
                //---------------------------------------

                    if(count($db_results)!==0){
                        $sql="
                            # for mssr_forum_user_info
                            UPDATE `mssr_forum`.`mssr_forum_user_info` SET
                                `user_content`='{$user_content}'
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_info`.`user_id`={$sess_user_id}
                            LIMIT 1;
                        ";
                    }else{
                        $sql="
                            # for mssr_forum_user_info
                            INSERT INTO `mssr_forum`.`mssr_forum_user_info` SET
                                `user_id`       ={$sess_user_id } ,
                                `user_content`  ='{$user_content}',
                                `keyin_mdate`   =NULL             ;
                        ";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="更新成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='{$send_url}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_style_group()
        //用途: 更換小組頁面樣式
        //-----------------------------------------------

            function edit_style_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_style_group()
            //用途: 更換小組頁面樣式
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
                //style_id
                //group_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'style_id   ',
                            'group_id   '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $style_id=trim($_POST[trim('style_id')]);
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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($style_id===''){
                       $arry_err[]='樣式主索引,未輸入!';
                    }else{
                        $style_id=(int)$style_id;
                        if($style_id===0){
                            $arry_err[]='樣式主索引,錯誤!';
                        }
                    }
                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $style_id=(int)$style_id;
                        $group_id=(int)$group_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_group_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_group_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $style_id    =(int)$style_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 0:
                            $sql="
                                # for mssr_forum_style_group_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_style_group_rev` SET
                                    `create_by`     ={$sess_user_id },
                                    `group_id`      ={$group_id     },
                                    `style_id`      ={$style_id     },
                                    `style_from`    =1               ,
                                    `keyin_mdate`   =NULL            ;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_style_group_rev
                                UPDATE `mssr_forum`.`mssr_forum_style_group_rev` SET
                                    `style_id`  = {$style_id},
                                    `style_from`=1
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                                LIMIT 1;
                            ";
                        break;

                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_style_user()
        //用途: 更換個人頁面樣式
        //-----------------------------------------------

            function edit_style_user($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_style_user()
            //用途: 更換個人頁面樣式
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
                //style_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'style_id   '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $style_id=trim($_POST[trim('style_id')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($style_id===''){
                       $arry_err[]='樣式主索引,未輸入!';
                    }else{
                        $style_id=(int)$style_id;
                        if($style_id===0){
                            $arry_err[]='樣式主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $style_id=(int)$style_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_user_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_user_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $style_id    =(int)$style_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    switch(count($db_results)){

                        case 0:
                            $sql="
                                # for mssr_forum_style_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_style_user_rev` SET
                                    `user_id`       ={$sess_user_id },
                                    `style_id`      ={$style_id     },
                                    `style_from`    =1               ,
                                    `keyin_mdate`   =NULL            ;
                            ";
                        break;

                        default:
                            $sql="
                                # for mssr_forum_style_user_rev
                                UPDATE `mssr_forum`.`mssr_forum_style_user_rev` SET
                                    `style_id`  = {$style_id},
                                    `style_from`=1
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                                LIMIT 1;
                            ";
                        break;

                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group()
        //用途: 修改小組資訊
        //-----------------------------------------------

            function edit_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group()
            //用途: 修改小組資訊
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
                //group_type
                //group_id
                //group_name
                //group_content
                //group_rule

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_type   ',
                            'group_id     ',
                            'group_name   ',
                            'group_content',
                            'group_rule   '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_type   =trim($_POST[trim('group_type   ')]);
                    $group_id     =trim($_POST[trim('group_id     ')]);
                    $group_name   =trim($_POST[trim('group_name   ')]);
                    $group_content=trim($_POST[trim('group_content')]);
                    $group_rule   =trim($_POST[trim('group_rule   ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }
                    if($group_type===''){
                        $arry_err[]='小組類型,未輸入!';
                    }else{
                        $group_type=(int)$group_type;
                        if($group_type===0){
                            $arry_err[]='小組類型,錯誤!';
                        }
                    }
                    if($group_name===''){
                       $arry_err[]='小組名稱,未輸入!';
                    }
                    if($group_content===''){
                       $arry_err[]='小組簡介,未輸入!';
                    }
                    if($group_rule===''){
                       $arry_err[]='小組規範,未輸入!';
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $group_type     =(int)$group_type;
                        $group_id       =(int)$group_id  ;
                        $group_name     =mysql_prep($group_name);
                        $group_content  =mysql_prep($group_content);
                        $group_rule     =mysql_prep($group_rule);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_type     =(int)$group_type;
                    $group_id       =(int)$group_id  ;
                    $group_name     =mysql_prep(strip_tags($group_name));
                    $group_content  =mysql_prep(strip_tags($group_content));
                    $group_rule     =mysql_prep(strip_tags($group_rule));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group
                        UPDATE `mssr_forum`.`mssr_forum_group` SET
                            `group_type`   =  {$group_type   } ,
                            `group_name`   = '{$group_name   }',
                            `group_content`= '{$group_content}',
                            `group_rule`   = '{$group_rule   }'
                        WHERE 1=1
                            AND `group_id` =  {$group_id     }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: edit_ok_request_rec_us_book()
        //用途: 回覆好友推薦一本書籍給你
        //-----------------------------------------------

            function edit_ok_request_rec_us_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_ok_request_rec_us_book()
            //用途: 回覆好友推薦一本書籍給你
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
                //request_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id=trim($_POST[trim('request_id ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id=(int)$request_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id=(int)$request_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 1
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="回覆成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_rec_us_book()
        //用途: 回應好友請求推薦一本書籍給他
        //-----------------------------------------------

            function edit_request_rec_us_book($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_rec_us_book()
            //用途: 回應好友請求推薦一本書籍給他
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
                //request_id
                //book_sid
                //request_content

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'book_sid       ',
                            'request_content'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id     =trim($_POST[trim('request_id     ')]);
                    $book_sid       =trim($_POST[trim('book_sid       ')]);
                    $request_content=trim($_POST[trim('request_content')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($book_sid===''){
                       $arry_err[]='書籍識別碼,未輸入!';
                    }
                    if($request_content===''){
                       $arry_err[]='回覆請求原因,未輸入!';
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id     =(int)$request_id;
                        $book_sid       =mysql_prep($book_sid);
                        $request_content=mysql_prep($request_content);

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id     =(int)$request_id;
                    $book_sid       =mysql_prep(strip_tags($book_sid));
                    $request_content=mysql_prep(strip_tags($request_content));

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 2
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;

                        # for mssr_forum_user_request_rec_us_book_rev
                        UPDATE `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` SET
                            `book_sid`          = '{$book_sid       }',
                            `request_content`   = '{$request_content}'
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="已回應請求";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_friend()
        //用途: 回覆交友邀請
        //-----------------------------------------------

            function edit_request_friend($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_friend()
            //用途: 回覆交友邀請
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
                //create_by
                //user_id
                //friend_id
                //friend_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'create_by   ',
                            'user_id     ',
                            'friend_id   ',
                            'friend_state'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $create_by   =trim($_POST[trim('create_by   ')]);
                    $user_id     =trim($_POST[trim('user_id     ')]);
                    $friend_id   =trim($_POST[trim('friend_id   ')]);
                    $friend_state=trim($_POST[trim('friend_state')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($create_by===''){
                       $arry_err[]='建立者主索引,未輸入!';
                    }else{
                        $create_by=(int)$create_by;
                        if($create_by===0){
                            $arry_err[]='建立者主索引,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
                        }
                    }
                    if($friend_id===''){
                       $arry_err[]='好友主索引,未輸入!';
                    }else{
                        $friend_id=(int)$friend_id;
                        if($friend_id===0){
                            $arry_err[]='好友主索引,錯誤!';
                        }
                    }
                    if($friend_state===''){
                       $arry_err[]='交友狀態,未輸入!';
                    }else{
                        $friend_state=(int)$friend_state;
                        if($friend_state===0){
                            $arry_err[]='交友狀態,錯誤!';
                        }else{
                            if(!in_array($friend_state,array(1,2))){
                                $arry_err[]='交友狀態,錯誤!';
                            }
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $create_by   =(int)$create_by;
                        $user_id     =(int)$user_id;
                        $friend_id   =(int)$friend_id;
                        $friend_state=(int)$friend_state;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $create_by   =(int)$create_by;
                    $user_id     =(int)$user_id;
                    $friend_id   =(int)$friend_id;
                    $friend_state=(int)$friend_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($friend_state===1){
                        $sql="
                            # for mssr_forum_friend
                            UPDATE `mssr_forum`.`mssr_forum_friend` SET
                                `friend_state`  = 1
                            WHERE 1=1
                                AND `create_by` = {$create_by}
                                AND `user_id`   = {$user_id  }
                                AND `friend_id` = {$friend_id}
                            ;
                        ";
                        $msg="你們已成為朋友";
                    }else{
                        $sql="
                            # for mssr_forum_friend
                            UPDATE `mssr_forum`.`mssr_forum_friend` SET
                                `friend_state`  = 2
                            WHERE 1=1
                                AND `create_by` = {$create_by}
                                AND `user_id`   = {$user_id  }
                                AND `friend_id` = {$friend_id}
                            ;
                        ";
                        $msg="你已拒絕此交友邀請";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_join_us_group()
        //用途: 回覆邀請加入小組
        //-----------------------------------------------

            function edit_request_join_us_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_join_us_group()
            //用途: 回覆邀請加入小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`,

                                `mssr_forum`.`mssr_forum_user_request`.`request_from`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_us_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                                `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2,3)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_join_us_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $arry_group_user_type_2_user_id=array();
                            foreach($db_results as $db_result){
                                if((int)$db_result['user_type']===3 || (int)$db_result['user_type']===2){
                                    $group_user_type_2_user_id=(int)$db_result['user_id'];
                                    $arry_group_user_type_2_user_id[]=$group_user_type_2_user_id;
                                }
                            }

                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                            $request_from=(int)$db_results[0]['request_from'];
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($request_state===2){
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 2,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(1)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="你已拒絕此邀請";
                    }else{
                        if(in_array($request_from,$arry_group_user_type_2_user_id)){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT IGNORE INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =1                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;

                                # for mssr_forum_group_user_rev
                                UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `user_state`    =1,
                                    `keyin_mdate`   =NULL
                                WHERE 1=1
                                    AND `user_id`   = {$sess_user_id}
                                    AND `group_id`  = {$group_id    }
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(2)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="已加入小組";
                        }else{
                            $arry_request_id=[];
                            foreach($arry_group_user_type_2_user_id as $arry_val){
                                $group_user_type_2_user_id=(int)$arry_val;
                                $sql="
                                    SELECT
                                        `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                                        `mssr_forum`.`mssr_forum_user_request`.`request_state`
                                    FROM `mssr_forum`.`mssr_forum_user_request`
                                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_to_group_rev` ON
                                        `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_join_to_group_rev`.`request_id`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$sess_user_id}
                                        AND `mssr_forum`.`mssr_forum_user_request`.`request_to`={$group_user_type_2_user_id}
                                        AND `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`group_id`={$group_id}
                                ";
                                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                                if(!empty($db_results)){
                                    $rs_request_id   =(int)$db_results[0]['request_id'];
                                    $rs_request_state=(int)$db_results[0]['request_state'];
                                    if($rs_request_state===1){
                                        $msg="你已經通過審核，成為小組成員了";
                                        die($msg);
                                    }
                                    $arry_request_id[]=$rs_request_id;
                                }
                            }

                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT IGNORE INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =3                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;

                                # for mssr_forum_group_user_rev
                                UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `user_state`    =3,
                                    `keyin_mdate`   =NULL
                                WHERE 1=1
                                    AND `user_id`   = {$sess_user_id}
                                    AND `group_id`  = {$group_id    }
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(3)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            if(!empty($arry_request_id)){
                                foreach($arry_request_id as $rs_request_id){
                                    $rs_request_id=(int)$rs_request_id;
                                    $sql="
                                        # for mssr_forum_user_request
                                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_state`  = 3,
                                            `request_read`   = 2
                                        WHERE 1=1
                                            AND `request_id` = {$rs_request_id}
                                        LIMIT 1;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(4)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);
                                }
                            }else{
                                foreach($arry_group_user_type_2_user_id as $arry_val){
                                    $group_user_type_2_user_id=(int)$arry_val;

                                    $sql="
                                        # for mssr_forum_user_request
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_from`      = {$sess_user_id                },
                                            `request_to`        = {$group_user_type_2_user_id   },
                                            `request_id`        = NULL                           ,
                                            `request_state`     = 3                              ,
                                            `request_read`      = 2                              ,
                                            `keyin_cdate`       = NOW()                          ,
                                            `keyin_mdate`       = NULL                           ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(5)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                    //lastInsertId
                                    $last_request_id=(int)$conn_mssr->lastInsertId();

                                    $sql="
                                        # for mssr_forum_user_request_join_to_group_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request_join_to_group_rev` SET
                                            `request_id`    = {$last_request_id } ,
                                            `group_id`      = {$group_id        } ,
                                            `rev_id`        = NULL                ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(6)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);
                                }
                            }
                            $msg="已提出申請加入小組，請等待小組的組長審核!";
                        }
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_join_to_group()
        //用途: 回覆申請加入小組
        //-----------------------------------------------

            function edit_request_join_to_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_join_to_group()
            //用途: 回覆申請加入小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`,

                                `mssr_forum`.`mssr_forum_user_request`.`request_from`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_join_to_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                                `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_join_to_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                            $request_from=(int)$db_results[0]['request_from'];
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($request_state===2){
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 2,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(1)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="你已拒絕此申請";
                    }else{
                        $sql="
                            # for mssr_forum_user_request
                            UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                `request_state`  = 1,
                                `request_read`   = 1
                            WHERE 1=1
                                AND `request_id` = {$request_id}
                            LIMIT 1;

                            # for mssr_forum_group_user_rev
                            INSERT IGNORE INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                `edit_by`           = {$request_from    } ,
                                `group_id`          = {$group_id        } ,
                                `user_id`           = {$request_from    } ,
                                `user_type`         =1                    ,
                                `user_state`        =1                    ,
                                `user_intro`        =''                   ,
                                `keyin_cdate`       =NOW()                ,
                                `keyin_mdate`       =NULL                 ;

                            # for mssr_forum_group_user_rev
                            UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                                `user_state`    =1,
                                `keyin_mdate`   =NULL
                            WHERE 1=1
                                AND `user_id`   = {$request_from}
                                AND `group_id`  = {$group_id    }
                            LIMIT 1;
                        ";
                        //送出
                        $err ='DB QUERY FAIL(2)';
                        $sth=$conn_mssr->prepare($sql);
                        $sth->execute()or die($err);

                        $msg="已允許加入";
                    }
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_create_group()
        //用途: 回覆聯署建立小組
        //-----------------------------------------------

            function edit_request_create_group($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_create_group()
            //用途: 回覆聯署建立小組
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
                //request_id
                //request_state

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id     ',
                            'request_state  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id   =trim($_POST[trim('request_id ')]);
                    $request_state=trim($_POST[trim('request_state ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }
                    if($request_state===''){
                       $arry_err[]='邀請狀態,未輸入!';
                    }else{
                        $request_state=(int)$request_state;
                        if($request_state===0){
                            $arry_err[]='邀請狀態,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id   =(int)$request_id;
                        $request_state=(int)$request_state;

                    //-----------------------------------
                    //檢核版主是誰
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`,
                                `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`,

                                `mssr_forum`.`mssr_forum_group`.`group_state`
                            FROM `mssr_forum`.`mssr_forum_group`
                                INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`={$request_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(empty($db_results)){
                            $msg="發生嚴重錯誤";
                            die($msg);
                        }else{
                            $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
                            $group_id=(int)$db_results[0]['group_id'];
                            $group_state=(int)$db_results[0]['group_state'];
                        }

                    //-----------------------------------
                    //檢核小組是否已越過聯署門檻
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_state`=1
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        $group_flag=false;
                        if(count($db_results)>=1){
                            $group_flag=true;
                        }

                    //-----------------------------------
                    //檢核是否已聯署過
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`,
                                `mssr_forum`.`mssr_forum_user_request`.`request_state`
                            FROM `mssr_forum`.`mssr_forum_user_request`
                                INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                                `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum_user_request_create_group_rev`.`request_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_from`={$group_user_type_2_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request`.`request_to`={$sess_user_id}
                                AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_request_id   =(int)$db_results[0]['request_id'];
                            $rs_request_state=(int)$db_results[0]['request_state'];

                            if($rs_request_state===1){
                                $msg="你已經聯署過";
                                die($msg);
                            }

                            if($rs_request_state===2){
                                $msg="你已經拒絕聯署過";
                                die($msg);
                            }
                        }

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id   =(int)$request_id;
                    $request_state=(int)$request_state;
                    $group_id     =(int)$group_id;
                    $group_user_type_2_user_id=(int)$group_user_type_2_user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    if($group_state===1){
                        if($request_state===2){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 2,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(6)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="你已拒絕連署";
                        }else{
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 1,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;

                                # for mssr_forum_group_user_rev
                                INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                    `edit_by`           = {$sess_user_id    } ,
                                    `group_id`          = {$group_id        } ,
                                    `user_id`           = {$sess_user_id    } ,
                                    `user_type`         =1                    ,
                                    `user_state`        =1                    ,
                                    `user_intro`        =''                   ,
                                    `keyin_cdate`       =NOW()                ,
                                    `keyin_mdate`       =NULL                 ;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(7)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="聯屬成功，請耐心等待小組建立";
                        }
                    }else{
                        if($request_state===2){
                            $sql="
                                # for mssr_forum_user_request
                                UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                    `request_state`  = 2,
                                    `request_read`   = 1
                                WHERE 1=1
                                    AND `request_id` = {$request_id}
                                LIMIT 1;
                            ";
                            //送出
                            $err ='DB QUERY FAIL(5)';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            $msg="你已拒絕連署";
                        }else{
                            switch(count($db_results)){

                                case 1:

                                    $sql="
                                        # for mssr_forum_user_request
                                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_state`  = 1,
                                            `request_read`   = 1
                                        WHERE 1=1
                                            AND `request_id` = {$rs_request_id}
                                        LIMIT 1;

                                        # for mssr_forum_group_user_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                            `edit_by`           = {$sess_user_id    } ,
                                            `group_id`          = {$group_id        } ,
                                            `user_id`           = {$sess_user_id    } ,
                                            `user_type`         =1                    ,
                                            `user_state`        =1                    ,
                                            `user_intro`        =''                   ,
                                            `keyin_cdate`       =NOW()                ,
                                            `keyin_mdate`       =NULL                 ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(1)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                break;

                                default:

                                    $sql="
                                        # for mssr_forum_user_request
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request` SET
                                            `request_from`      = {$group_user_type_2_user_id   },
                                            `request_to`        = {$sess_user_id                },
                                            `request_id`        = NULL                           ,
                                            `request_state`     = 1                              ,
                                            `request_read`      = 1                              ,
                                            `keyin_cdate`       = NOW()                          ,
                                            `keyin_mdate`       = NULL                           ;

                                        # for mssr_forum_group_user_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_group_user_rev` SET
                                            `edit_by`           = {$sess_user_id    } ,
                                            `group_id`          = {$group_id        } ,
                                            `user_id`           = {$sess_user_id    } ,
                                            `user_type`         =1                    ,
                                            `user_state`        =1                    ,
                                            `user_intro`        =''                   ,
                                            `keyin_cdate`       =NOW()                ,
                                            `keyin_mdate`       =NULL                 ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(2)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                    //lastInsertId
                                    $last_request_id=(int)$conn_mssr->lastInsertId();

                                    $sql="
                                        # for mssr_forum_user_request_create_group_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_user_request_create_group_rev` SET
                                            `request_id`    = {$last_request_id } ,
                                            `group_id`      = {$group_id        } ,
                                            `rev_id`        = NULL                ;
                                    ";
                                    //送出
                                    $err ='DB QUERY FAIL(3)';
                                    $sth=$conn_mssr->prepare($sql);
                                    $sth->execute()or die($err);

                                break;

                            }

                            if($group_flag){

                                $sql="
                                    # for mssr_forum_group
                                    UPDATE `mssr_forum`.`mssr_forum_group` SET
                                        `group_state`  = 1
                                    WHERE 1=1
                                        AND `group_id`={$group_id}
                                    LIMIT 1;
                                ";
                                //送出
                                $err ='DB QUERY FAIL(4)';
                                $sth=$conn_mssr->prepare($sql);
                                $sth->execute()or die($err);

                            }
                            $msg="聯屬成功，請耐心等待小組建立";
                        }
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $jscript_back="
                        <script>
                            alert('{$msg}');
                        </script>
                    ";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_request_article()
        //用途: 回覆文章邀請
        //-----------------------------------------------

            function edit_request_article($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_request_article()
            //用途: 回覆文章邀請
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
                //request_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'request_id '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $request_id=trim($_POST[trim('request_id ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($request_id===''){
                       $arry_err[]='邀請主索引,未輸入!';
                    }else{
                        $request_id=(int)$request_id;
                        if($request_id===0){
                            $arry_err[]='邀請主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $request_id=(int)$request_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $request_id=(int)$request_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_user_request
                        UPDATE `mssr_forum`.`mssr_forum_user_request` SET
                            `request_state`  = 1,
                            `request_read`   = 1
                        WHERE 1=1
                            AND `request_id` = {$request_id}
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="回覆成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group_user_state()
        //用途: 切換小組使用者狀態
        //-----------------------------------------------

            function edit_group_user_state($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group_user_state()
            //用途: 切換小組使用者狀態
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
                //group_id
                //user_state
                //user_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id ',
                            'user_state',
                            'user_id  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id  =trim($_POST[trim('group_id ')]);
                    $user_state=trim($_POST[trim('user_state')]);
                    $user_id   =trim($_POST[trim('user_id  ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }
                    if($user_state===''){
                       $arry_err[]='使用者狀態,未輸入!';
                    }else{
                        $user_state=(int)$user_state;
                        if($user_state===0){
                            $arry_err[]='使用者狀態,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $group_id  =(int)$group_id;
                        $user_state=(int)$user_state;
                        $user_id   =(int)$user_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id  =(int)$group_id;
                    $user_state=(int)$user_state;
                    $user_id   =(int)$user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_user_rev
                        UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                            `user_state`  = {$user_state}
                        WHERE 1=1
                            AND `group_id`= {$group_id  }
                            AND `user_id` = {$user_id   }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group_user_type()
        //用途: 切換小組使用者身分
        //-----------------------------------------------

            function edit_group_user_type($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group_user_type()
            //用途: 切換小組使用者身分
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
                //group_id
                //user_type
                //user_id

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id ',
                            'user_type',
                            'user_id  '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post])){
                                die();
                            }
                        }
                    }else{die();}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id =trim($_POST[trim('group_id ')]);
                    $user_type=trim($_POST[trim('user_type')]);
                    $user_id  =trim($_POST[trim('user_id  ')]);

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
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if($group_id===''){
                       $arry_err[]='小組主索引,未輸入!';
                    }else{
                        $group_id=(int)$group_id;
                        if($group_id===0){
                            $arry_err[]='小組主索引,錯誤!';
                        }
                    }
                    if($user_type===''){
                       $arry_err[]='使用者身分,未輸入!';
                    }else{
                        $user_type=(int)$user_type;
                        if($user_type===0){
                            $arry_err[]='使用者身分,錯誤!';
                        }
                    }
                    if($user_id===''){
                       $arry_err[]='使用者主索引,未輸入!';
                    }else{
                        $user_id=(int)$user_id;
                        if($user_id===0){
                            $arry_err[]='使用者主索引,錯誤!';
                        }
                    }

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die();
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
                    //預處理
                    //-----------------------------------

                        $group_id =(int)$group_id;
                        $user_type=(int)$user_type;
                        $user_id  =(int)$user_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $group_id =(int)$group_id;
                    $user_type=(int)$user_type;
                    $user_id  =(int)$user_id;

                //---------------------------------------
                //處理
                //---------------------------------------

                    $sql="
                        # for mssr_forum_group_user_rev
                        UPDATE `mssr_forum`.`mssr_forum_group_user_rev` SET
                            `user_type`   = {$user_type}
                        WHERE 1=1
                            AND `group_id`= {$group_id }
                            AND `user_id` = {$user_id  }
                        LIMIT 1;
                    ";
                    //送出
                    $err ='DB QUERY FAIL';
                    $sth=$conn_mssr->prepare($sql);
                    $sth->execute()or die($err);

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="修改成功";
                    die($msg);
            }
?>

