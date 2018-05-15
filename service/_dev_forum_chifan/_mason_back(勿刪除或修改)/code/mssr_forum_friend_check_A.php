<?php
//-------------------------------------------------------
//mssr_fourm
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

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
	//user_id		使用者索引
	//friend_id		朋友索引
	//state			狀態

        $get_chk=array(
			'user_id    ',
            'friend_id  ',
			'state      '
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
	//user_id		使用者索引
	//friend_id		朋友索引
	//state			狀態

        //GET
		$user_id    =trim($_GET[trim('user_id    ')]);
		$friend_id  =trim($_GET[trim('friend_id  ')]);
        $state      =trim($_GET[trim('state      ')]);

        //SESSION

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
	//user_id		使用者索引
	//friend_id		朋友索引
	//state			狀態

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$user_id=(int)$user_id;
			if($user_id===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}

        if($friend_id===''){
           $arry_err[]='朋友索引,未輸入!';
        }else{
			$friend_id=(int)$friend_id;
			if($friend_id===0){
				$arry_err[]='朋友索引,錯誤!';
			}
		}

        if($state===''){
           $arry_err[]='狀態,未輸入!';
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
			echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
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
        //user_id		使用者索引
        //friend_id		朋友索引
        //state			狀態

            $user_id    =(int)$user_id;
            $friend_id  =(int)$friend_id;
            $state      =mysql_prep($state);

            //-------------------------------------------
            //檢核使用者存在與否
            //-------------------------------------------

                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$user_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);
                if($numrow===0){
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
            //檢核朋友存在與否
            //-------------------------------------------

                $sql="
                    SELECT
                        `uid`
                    FROM `member`
                    WHERE 1=1
                        AND `uid`={$friend_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="朋友不存在, 請重新輸入!";
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
                            `user_id` IN ({$friend_id},{$user_id})
                                AND
                            `friend_id` IN ({$friend_id},{$user_id})
                        )
                        AND `friend_state` IN ('確認中')
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(empty($arrys_result)){
                    $msg="交友情況錯誤, 請重新輸入!";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";
                    die($jscript_back);
                }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $user_id    =(int)$user_id;
            $friend_id  =(int)$friend_id;
            $state      =mysql_prep(strip_tags(trim($state)));

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($state){

                case '成功':
                    $sql="
                        # for mssr_forum_friend
                        UPDATE `mssr_forum_friend` SET
                            `friend_state`='成功'
                        WHERE 1=1
                            AND (
                                `user_id` IN ({$friend_id},{$user_id})
                                    AND
                                `friend_id` IN ({$friend_id},{$user_id})
                            )
                        LIMIT 1;
                    ";
                break;

                case '失敗':
                    $sql="
                        # for mssr_forum_friend
                        UPDATE `mssr_forum_friend` SET
                            `friend_state`='失敗'
                        WHERE 1=1
                            AND (
                                `user_id` IN ({$friend_id},{$user_id})
                                    AND
                                `friend_id` IN ({$friend_id},{$user_id})
                            )
                        LIMIT 1;
                    ";
                break;

                default:
                    $err ='DB QUERY FAIL';
                    die($err);
                break;
            }

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

	///---------------------------------------------------
    //重導頁面
    //----------------------------------------------------

        $msg="交友{$state}!";
        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='index.php';
            </script>
        ";
        die($jscript_back);
?>



