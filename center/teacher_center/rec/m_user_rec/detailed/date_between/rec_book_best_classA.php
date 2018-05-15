<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",6).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/vaildate/code',
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

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",7).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼

        $post_chk=array(
            'user_id ',
            'book_sid'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼

        //POST
        $user_id =trim($_POST[trim('user_id ')]);
        $book_sid=trim($_POST[trim('book_sid')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        $pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id    使用者主索引(被閱讀人)
    //book_sid   書籍識別碼

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引(被評論人),未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引(被評論人),錯誤!';
            }
        }
        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==1){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //user_id    使用者主索引(被閱讀人)
        //book_sid   書籍識別碼

            $sess_user_id   =(int)$sess_user_id;
            $user_id        =(int)$user_id;
            $book_sid       =mysql_prep($book_sid);

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //檢核是否已有被閱讀紀錄
                //---------------------------------------

                    $sql="
                        SELECT `book_sid`
                        FROM `mssr_rec_book_cno`
                        WHERE 1=1
                            AND `user_id`={$user_id}
                            AND `book_sid`='{$book_sid}'
                            AND `rec_state`=1
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    if(!empty($arrys_result)){
                        $rec_star_info  =get_rec_info($conn_mssr,$user_id,trim($book_sid),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        $rec_text_info  =get_rec_info($conn_mssr,$user_id,trim($book_sid),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        $rec_record_info=get_rec_info($conn_mssr,$user_id,trim($book_sid),$rec_type='record',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        $rec_draw_info  =get_rec_info($conn_mssr,$user_id,trim($book_sid),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                        if(empty($rec_star_info) && empty($rec_text_info) && empty($rec_record_info) && empty($rec_draw_info)){
                            die('存取發生問題，請再試一次 !');
                        }
                    }else{
                        die('存取發生問題，請再試一次 !');
                    }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_user_id    =(int)$sess_user_id;
            $create_by       =(int)$sess_user_id;
            $class_code      =mysql_prep(strip_tags($sess_class_code));
            $user_id         =(int)$user_id;
            $book_sid        =mysql_prep(strip_tags($book_sid));

            $rec_draw_sid    =(isset($rec_draw_info[0]['rec_sid']))?trim($rec_draw_info[0]['rec_sid']):'';
            $rec_record_sid  =(isset($rec_record_info[0]['rec_sid']))?trim($rec_record_info[0]['rec_sid']):'';
            $rec_star_sid    =(isset($rec_star_info[0]['rec_sid']))?trim($rec_star_info[0]['rec_sid']):'';
            $rec_text_sid    =(isset($rec_text_info[0]['rec_sid']))?trim($rec_text_info[0]['rec_sid']):'';

            $rec_star_rank   =(isset($rec_star_info[0]['rec_rank']))?(int)trim($rec_star_info[0]['rec_rank']):0;
            $rec_star_reason =(isset($rec_star_info[0]['rec_reason']))?trim($rec_star_info[0]['rec_reason']):'';
            $rec_text_content=(isset($rec_text_info[0]['rec_content']))?trim($rec_text_info[0]['rec_content']):'';

//echo "<Pre>";
//print_r($rec_draw_sid);
//echo "</Pre>";
//echo "<Pre>";
//print_r($rec_record_sid);
//echo "</Pre>";
//echo "<Pre>";
//print_r($rec_star_sid);
//echo "</Pre>";
//echo "<Pre>";
//print_r($rec_text_sid);
//echo "</Pre>";
//die();
        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_rec_book_best_class
                INSERT IGNORE INTO `mssr_rec_book_best_class` SET
                    `create_by`     = {$create_by   } ,
                    `class_code`    ='{$class_code  }',
                    `user_id`       = {$user_id     } ,
                    `book_sid`      ='{$book_sid    }',
            ";

            if($rec_draw_sid!=='')$sql.="`rec_draw_sid`         ='{$rec_draw_sid        }',";
            if($rec_record_sid!=='')$sql.="`rec_record_sid`     ='{$rec_record_sid      }',";
            if($rec_star_sid!=='')$sql.="`rec_star_sid`         ='{$rec_star_sid        }',";
            if($rec_text_sid!=='')$sql.="`rec_text_sid`         ='{$rec_text_sid        }',";

            if($rec_star_rank!==0)$sql.="`rec_star_rank`        = {$rec_star_rank       } ,";
            if($rec_star_reason!=='')$sql.="`rec_star_reason`   ='{$rec_star_reason     }',";
            if($rec_text_content!=='')$sql.="`rec_text_content` ='{$rec_text_content    }',";

            $sql.="`keyin_cdate`=NULL;";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);
//echo "<Pre>";
//print_r($sql);
//echo "</Pre>";
//die();
    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        die('儲存成功!');
?>