<?php
//-------------------------------------------------------
//mssr_forum
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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //sess_uid  使用者主索引
    //user_id   被加入的人主索引

        $post_chk=array(
            'sess_uid',
            'user_id '
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_GET[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //sess_uid  使用者主索引
    //user_id   被加入的人主索引

        //GET
        $sess_uid=trim($_GET[trim('sess_uid')]);
        $user_id =trim($_GET[trim('user_id ')]);

        //SESSION

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //sess_uid  使用者主索引
    //user_id   被加入的人主索引

        $arry_err=array();

        if($sess_uid===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $sess_uid=(int)$sess_uid;
            if($sess_uid===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }
        if($user_id===''){
           $arry_err[]='被加入的人主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='被加入的人主索引,錯誤!';
            }
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

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //sess_uid  使用者主索引
        //user_id   被加入的人主索引

            $sess_uid   =(int)$sess_uid;
            $user_id    =(int)$user_id;
            $has_record =false;

            //-------------------------------------------
            //檢核使用者是否存在
            //-------------------------------------------

                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$sess_uid}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(empty($arrys_result)){
                    $msg="使用者不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核被加入的人是否存在
            //-------------------------------------------

                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$user_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(empty($arrys_result)){
                    $msg="使用者不存在, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核是否已為朋友
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `friend_id`
                    FROM `mssr_forum_friend`
                    WHERE 1=1
                        AND (
                            `user_id` IN ({$sess_uid},{$user_id})
                                AND
                            `friend_id` IN ({$sess_uid},{$user_id})
                        )
                        AND `friend_state` IN ('成功')
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $msg="已經是朋友了!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核是否還在確認中
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `friend_id`
                    FROM `mssr_forum_friend`
                    WHERE 1=1
                        AND (
                            `user_id` IN ({$sess_uid},{$user_id})
                                AND
                            `friend_id` IN ({$sess_uid},{$user_id})
                        )
                        AND `friend_state` IN ('確認中')
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $msg="交友目前雙方還在確認中!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

            //-------------------------------------------
            //檢核是否已有交友紀錄
            //-------------------------------------------

                $sql="
                    SELECT
                        `user_id`,
                        `friend_id`
                    FROM `mssr_forum_friend`
                    WHERE 1=1
                        AND (
                            `user_id` IN ({$sess_uid},{$user_id})
                                AND
                            `friend_id` IN ({$sess_uid},{$user_id})
                        )
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $has_record=true;
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $sess_uid   =(int)$sess_uid;
            $user_id    =(int)$user_id;

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if($has_record){
                $sql="
                    # for mssr_forum_friend
                    UPDATE `mssr_forum_friend` SET
                        `friend_state`='確認中'
                    WHERE 1=1
                        AND (
                            `user_id` IN ({$sess_uid},{$user_id})
                                AND
                            `friend_id` IN ({$sess_uid},{$user_id})
                        )
                    LIMIT 1;
            ";
            }else{
                $sql="
                    # for mssr_forum_friend
                    INSERT INTO `mssr_forum_friend` SET
                        `user_id`       =   {$sess_uid  },
                        `friend_id`     =   {$user_id   },
                        `friend_state`  =   '確認中'     ,
                        `keyin_cdate`   =   NOW()        ;
                ";
            }

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $msg="申請成功，請耐心等待對方確認!";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='../mssr_forum_people_shelf.php?user_id={$user_id}&psize={$psize}&pinx={$pinx}';
            </script>
        ";
        die($jscript_back);
?>