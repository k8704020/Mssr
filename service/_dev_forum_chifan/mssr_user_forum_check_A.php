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
	//forum_id		討論區索引
	//flag			指標

        $get_chk=array(
			'user_id	',
            'forum_id	',
			'flag		'
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
	//forum_id		討論區索引
	//flag			指標

        //GET
		$user_id	=trim($_GET[trim('user_id	')]);
		$forum_id	=trim($_GET[trim('forum_id	')]);
        $flag		=trim($_GET[trim('flag		')]);

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
	//forum_id		討論區索引
	//flag			指標

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者索引,未輸入!';
        }else{
			$user_id=(int)$user_id;
			if($user_id===0){
				$arry_err[]='使用者索引,錯誤!';
			}
		}

        if($forum_id===''){
           $arry_err[]='討論區索引,未輸入!';
        }else{
			$forum_id=(int)$forum_id;
			if($forum_id===0){
				$arry_err[]='討論區索引,錯誤!';
			}
		}

        if($flag===''){
           $arry_err[]='指標,未輸入!';
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
        //forum_id		討論區索引
        //flag			指標

            $user_id    =(int)$user_id;
            $forum_id  =(int)$forum_id;
            $flag      =mysql_prep($flag);

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
            //檢核討論區存在與否
            //-------------------------------------------

                $sql="
                    SELECT
                        `forum_id`
                    FROM `mssr_forum`
                    WHERE 1=1
                        AND `forum_id`={$forum_id}
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $numrow=count($arrys_result);
                if($numrow===0){
                    $msg="討論區不存在, 請重新輸入!";
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
            $forum_id   =(int)$forum_id;
            $flag       =mysql_prep(strip_tags(trim($flag)));

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($flag){

                case 'yes':
                    $sql="
                        # for mssr_user_forum
                        UPDATE `mssr_user_forum` SET
                            `user_state`  ='啟用',
                            `keyin_mdate` =NOW()
                        WHERE 1=1
                            AND `forum_id`={$forum_id}
                            AND `user_id` ={$user_id }
                        LIMIT 1;
                    ";
                    $msg="已允許加入小組!";
                break;

                case 'no':
                    $sql="
                        # for mssr_user_forum
                        DELETE FROM `mssr_user_forum`
                        WHERE 1=1
                            AND `forum_id`={$forum_id}
                            AND `user_id` ={$user_id }
                        LIMIT 1;
                    ";
                    $msg="已否決加入小組!";
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

        $jscript_back="
            <script>
                alert('{$msg}');
                location.href='index.php';
            </script>
        ";
        die($jscript_back);
?>



