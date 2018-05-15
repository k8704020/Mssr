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
        require_once(str_repeat("../",4).'config/config.php');
        require_once(str_repeat("../",0)."inc/php_mailer/class.phpmailer.php");

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

        //間隔10秒
        sleep(10);

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_mail');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //cno
    //total
    //send_from_name
    //send_from_email
    //email_title
    //email_content
    //school_en

        $post_chk=array(
            'cno            ',
            'total          ',
            'send_from_name ',
            'send_from_email',
            'email_title    ',
            'email_content  ',
            'school_en      '
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
    //cno
    //total
    //send_from_name
    //send_from_email
    //email_title
    //email_content
    //school_en

        //POST
        $cno            =trim($_POST[trim('cno            ')]);
        $total          =trim($_POST[trim('total          ')]);
        $send_from_name =trim($_POST[trim('send_from_name ')]);
        $send_from_email=trim($_POST[trim('send_from_email')]);
        $email_title    =trim($_POST[trim('email_title    ')]);
        $email_content  =trim($_POST[trim('email_content  ')]);
        $school_en      =trim($_POST[trim('school_en      ')]);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //cno
    //total
    //send_from_name
    //send_from_email
    //email_title
    //email_content
    //school_en

        $arry_err=array();

        if($send_from_name===''){
           $arry_err[]='寄件者姓名,未輸入!';
        }

        if($send_from_email===''){
           $arry_err[]='寄件者信箱,未輸入!';
        }

        if($email_title===''){
           $arry_err[]='郵件標題,未輸入!';
        }

        if($email_content===''){
           $arry_err[]='郵件內容,未輸入!';
        }

        if($school_en===''){
           $arry_err[]='發送的學校,未輸入!';
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
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //cno
        //total
        //send_from_name
        //send_from_email
        //email_title
        //email_content
        //school_en

            $cno            =(int)$cno;
            $total          =(int)$total;
            $send_from_name =nl2br($send_from_name  );
            $send_from_email=nl2br($send_from_email );
            $email_title    =nl2br($email_title     );
            $email_content  =nl2br($email_content   );
            $school_en      =mysql_prep($school_en  );

            $send_flag      =array('flag'=>'true');

            //-------------------------------------------
            //檢核學校
            //-------------------------------------------

                if($send_flag['flag']==='true'){
                    $sql="
                        SELECT
                            `school_code`
                        FROM `school`
                        WHERE 1=1
                            AND `school_code` ='{$school_en}'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    if(empty($arrys_result)){
                        $send_flag['flag']       ='false';
                        $send_flag['school_type']='en';
                        $send_flag['school_code']=$school_en;
                        $send_flag['cno']        =$cno;
                        $send_flag['total']      =$total;
                        die(json_encode($send_flag,true));
                    }
                }

            //-------------------------------------------
            //檢核信箱
            //-------------------------------------------

                $curdate=date("Y-m-d");

                if($send_flag['flag']==='true'){
                    $sql="
                        SELECT
                            `member_email`.`email`,
                            `member`.`name`
                        FROM `personnel`
                            INNER JOIN `member_email` ON
                            `personnel`.`uid`=`member_email`.`uid`

                            INNER JOIN `member` ON
                            `personnel`.`uid`=`member`.`uid`
                        WHERE 1=1
                            AND `personnel`.`school_code`      ='{$school_en}'
                            AND `personnel`.`responsibilities` =1
                            AND `personnel`.`start`           <='{$curdate}'
                            AND `personnel`.`end`             >='{$curdate}'

                            AND `member`.`permission`         <>'x'
                            AND `member_email`.`email`        <>''
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                    if(empty($arrys_result)){
                        $send_flag['flag']       ='false';
                        $send_flag['school_type']='en';
                        $send_flag['school_code']=$school_en;
                        $send_flag['cno']        =$cno;
                        $send_flag['total']      =$total;
                        die(json_encode($send_flag,true));
                    }else{
                        $rs_send_to_email=trim($arrys_result[0]['email']);
                        $rs_send_to_name =trim($arrys_result[0]['name']);
                    }
                }

        //-----------------------------------------------
        //信件設置
        //-----------------------------------------------

            //-------------------------------------------
            //報表信件內容
            //-------------------------------------------

                //報表信件內容
                $body ="";
                $body.=$email_content;

            //-------------------------------------------
            //報表參數設置
            //-------------------------------------------

                if($send_flag['flag']==='true'){
                    $mail = new PHPMailer();                                            //建立新物件
                    $mail->IsSMTP();                                                    //設定使用SMTP方式寄信
                    $mail->SMTPAuth = false;                                            //設定SMTP需要驗證
                    $mail->Host = '140.115.135.2';                                      //設定SMTP主機
                    $mail->Port = 25;                                                   //設定SMTP埠位，預設為25埠。
                    $mail->CharSet = $page_enc;                                         //設定郵件編碼

                    $mail->Username ="";                                                //設定驗證帳號
                    $mail->Password ="";                                                //設定驗證密碼

                    $mail->From = $send_from_email;                                     //設定寄件者信箱
                    $mail->FromName = $send_from_name;                                  //設定寄件者姓名

                    $mail->Subject = $email_title;                                      //設定郵件標題
                    $mail->Body = $body;                                                //設定郵件內容
                    $mail->IsHTML(true);                                                //設定郵件內容為HTML
                    $mail->AddAddress(trim($rs_send_to_email), trim($rs_send_to_name)); //設定收件者郵件及名稱

                    //發送
                    if(!$mail->Send()){
                          $send_flag['flag']       ='false';
                          $send_flag['school_type']='en';
                          $send_flag['school_code']=$school_en;
                          $send_flag['cno']        =$cno;
                          $send_flag['total']      =$total;
                          die(json_encode($send_flag,true));
                    }
                }

        //-----------------------------------------------
        //關閉連線
        //-----------------------------------------------

            $conn_user=NULL;

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $send_flag['flag']  ='true';
        $send_flag['cno']   =$cno;
        $send_flag['total'] =$total;
        die(json_encode($send_flag,true));
?>