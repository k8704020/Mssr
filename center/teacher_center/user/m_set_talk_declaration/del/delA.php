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
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

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
            $url=str_repeat("../",6).'index.php';
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
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_set_talk_declaration');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id   使用者主索引
    //del_flag  移除類型
    //del_inx	移除編號

        $get_chk=array(
            'user_id ',
            'del_flag',
            'del_inx'
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
    //user_id   使用者主索引
    //del_flag  移除類型
    //del_inx	移除編號

        //GET
        $user_id	=trim($_GET[trim('user_id ')]);
        $del_flag	=trim($_GET[trim('del_flag')]);
		$del_inx	=trim($_GET[trim('del_inx')]);

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id   使用者主索引
    //del_flag  移除類型
    //del_inx	移除編號

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($del_flag===''){
           $arry_err[]='移除類型,未輸入!';
        }else{
            $del_flag=trim($del_flag);
            if(!in_array($del_flag,array('clerk_talk_inx','clerk_talk_all','star_declaration'))){
                $arry_err[]='移除類型,錯誤!';
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
        //user_id   使用者主索引
        //del_flag  移除類型
		//del_inx	移除編號
		
            $user_id        =(int)($user_id );
            $del_flag       =mysql_prep($del_flag);
			$del_inx		=mysql_prep($del_inx);
			
        //-------------------------------------------
        //檢核使用者主索引
        //-------------------------------------------

            $sql="
                SELECT
                    `user_id`
                FROM `mssr_user_info`
                WHERE 1=1
                    AND `user_id`={$user_id}
            ";

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
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

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            $user_id	=(int)($user_id );
            $del_flag	=mysql_prep($del_flag);
			$del_inx	=mysql_prep($del_inx);

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            switch($del_flag){
            	case 'clerk_talk_inx'://刪除單筆招呼語
            		//先找使用者的星球招呼語先解碼並重新組成陣列
                    $query_sql="
                    SELECT
                    	`user_id`,
                        `clerk_talk`
                    FROM `mssr_user_info`
                    WHERE 1=1
                        AND `user_id` = '$user_id'
	                ";
					$numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
		        	$numrow=count($numrow);
					
					if($numrow!==0){
		                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
		            }
					
					
					$del_array = array();
					$arry_rs_clerk_talk = array();
					foreach ($arrys_result as $key => $val) {
						$del_array['user_id'] = $val['user_id'];
						$arry_rs_clerk_talk=@unserialize($val['clerk_talk']);
						foreach ($arry_rs_clerk_talk as $inx => $rs_clerk_talk) {
							$del_array['clerk_talk_array'][$inx]=trim(gzuncompress(base64_decode($rs_clerk_talk)));
							
						}
						
					}
					
					//刪除單筆招呼語
					if( isset($del_inx) ){
						$del_array['clerk_talk_array'][$del_inx-1]='';
					}
					
					
					
					//重新存成陣列
					$ix= array();
					$str_num = 0;
					foreach ($del_array['clerk_talk_array'] as $key => $value) {
						$str_num += strlen($value);
						$ix[$key] = mysql_prep(base64_encode(gzcompress($value)));
					}
					$text = serialize($ix);
					
					if($str_num != 0){
						$sql = "
							UPDATE `mssr_user_info` SET clerk_talk =  '$text'
							WHERE  user_id = {$user_id}
						";
					}else{//當招呼語都沒有文字時清空
						$sql = "
							UPDATE `mssr_user_info` SET clerk_talk =  ''
							WHERE  user_id = {$user_id}
						";
					}
                break;
                case 'clerk_talk_all'://刪除全部招呼語
                    $sql="
                        # for mssr_user_info
                        UPDATE `mssr_user_info` SET
                            `clerk_talk` =''
                        WHERE 1=1
                            AND `user_id`={$user_id}
                        LIMIT 1;
                    ";
                break;

                case 'star_declaration':
                    $sql="
                        # for mssr_user_info
                        UPDATE `mssr_user_info` SET
                            `star_declaration` =''
                        WHERE 1=1
                            AND `user_id`={$user_id}
                        LIMIT 1;
                    ";
                break;
            }

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."index.php";
        $arg =array(
            'psize'=>$psize,
            'pinx' =>$pinx
        );
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");
?>