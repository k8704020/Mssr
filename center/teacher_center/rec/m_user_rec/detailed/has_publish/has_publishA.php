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
    //area              回轉的頁面
    //publish_state     上架條件
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //anchor            錨點
    //date_filter       時間條件

        $get_chk=array(
            'area         ',
            'publish_state',
            'user_id      ',
            'book_sid     ',
            'anchor       '
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
    //area              回轉的頁面
    //publish_state     上架條件
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //anchor            錨點
    //date_filter       時間條件

        //GET
        $area            =trim($_GET[trim('area         ')]);
        $publish_state   =trim($_GET[trim('publish_state')]);
        $user_id         =trim($_GET[trim('user_id      ')]);
        $book_sid        =trim($_GET[trim('book_sid     ')]);
        $anchor          =trim($_GET[trim('anchor       ')]);
        $scrolltop       =(isset($_GET['scrolltop']))?(int)$_GET['scrolltop']:0;

        //date_filter   時間條件
        if(isset($_GET[trim('date_filter')])){
            $date_filter=trim($_GET[trim('date_filter')]);
            if(!in_array($date_filter,array("three_day","one_week","two_week"))){
                $date_filter='';
            }
        }else{
            $date_filter='';
        }

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
    //area              回轉的頁面
    //publish_state     上架條件
    //user_id           使用者主索引(被評論人)
    //book_sid          書籍識別碼
    //anchor            錨點
    //date_filter       時間條件

        $arry_err=array();

        if($area===''){
           $arry_err[]='回轉的頁面,未輸入!';
        }
        if($publish_state===''){
           $arry_err[]='上架條件,未輸入!';
        }else{
            $publish_state=trim($publish_state);
            if(!in_array($publish_state,array('可','否'))){
                $arry_err[]='上架條件,錯誤!';
            }
        }
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
        //area              回轉的頁面
        //publish_state     上架條件
        //user_id           使用者主索引(被評論人)
        //book_sid          書籍識別碼
        //anchor            錨點
        //date_filter       時間條件

            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            $publish_state  =mysql_prep($publish_state);
            $user_id        =(int)$user_id;
            $book_sid       =mysql_prep($book_sid     );
            $anchor         =mysql_prep($anchor       );

            //-------------------------------------------
            //檢核
            //-------------------------------------------

                //---------------------------------------
                //檢核推薦內容
                //---------------------------------------

                    $sql="
                        SELECT
                            `user_id`
                        FROM `mssr_rec_book_cno`
                        WHERE 1=1
                            AND `user_id`  = {$user_id  }
                            AND `book_sid` ='{$book_sid }'
                    ";
                    $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    $numrow=count($arrys_result);
                    if($numrow===0){
                        $msg="推薦內容不存在, 請重新輸入!";
                        $jscript_back="
                            <script>
                                alert('{$msg}');
                                history.back(-1);
                            </script>
                        ";
                        die($jscript_back);
                    }

                //---------------------------------------
                //檢核書籍資訊
                //---------------------------------------

                    $get_book_info=get_book_info($conn_mssr,$book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    $book_name='查無書名!';
                    if(!empty($get_book_info)){
                        $book_name=trim($get_book_info[0]['book_name']);
                    }

        //-----------------------------------------------
        //預設值
        //-----------------------------------------------

            //SESSION
            $sess_user_id   =(int)$sess_user_id;
            $sess_grade     =(int)$sess_grade;
            $sess_classroom =(int)$sess_classroom;

            //-------------------------------------------
            //mssr_rec_book_cno 部分
            //-------------------------------------------

                $edit_by        =(int)$sess_user_id;
                $user_id        =(int)$user_id;
                $book_sid       =mysql_prep($book_sid     );
                $publish_state  =mysql_prep($publish_state);
                $anchor         =mysql_prep($anchor       );

            //-------------------------------------------
            //mssr_msg_log 部分
            //-------------------------------------------

                $log_id     ="NULL";

                if($publish_state==='可'){
                    $log_text   ="老師已經核准讓你的 {$book_name} 書籍可以上架 !";
                }else{
                    $log_text   ="老師已經不允許讓你的 {$book_name} 書籍上架 !";
                }

                $keyin_cdate="NOW()";
                $keyin_mdate="NOW()";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $sql="
                # for mssr_rec_book_cno
                UPDATE `mssr_rec_book_cno` SET
                    `edit_by`       = {$edit_by         },
                    `has_publish`   ='{$publish_state   }'
                WHERE 1=1
                    AND `user_id`   = {$user_id         }
                    AND `book_sid`  ='{$book_sid        }'
                LIMIT 1;

                # for mssr_rec_book_cno_one_week
                UPDATE `mssr_rec_book_cno_one_week` SET
                    `edit_by`       = {$edit_by         },
                    `has_publish`   ='{$publish_state   }'
                WHERE 1=1
                    AND `user_id`   = {$user_id         }
                    AND `book_sid`  ='{$book_sid        }'
                LIMIT 1;

                # for mssr_rec_book_cno_semester
                UPDATE `mssr_rec_book_cno_semester` SET
                    `edit_by`       = {$edit_by         },
                    `has_publish`   ='{$publish_state   }'
                WHERE 1=1
                    AND `user_id`   = {$user_id         }
                    AND `book_sid`  ='{$book_sid        }'
                LIMIT 1;

                # for mssr_msg_log
                INSERT INTO `mssr_msg_log` SET
                    `user_id`       = {$user_id         } ,
                    `log_id`        = {$log_id          } ,
                    `log_text`      ='{$log_text        }',
                    `log_state`     =1                    ,
                    `keyin_cdate`   = {$keyin_cdate     } ,
                    `keyin_mdate`   = {$keyin_mdate     } ;
            ";

            //送出
            $err ='DB QUERY FAIL';
            $sth=$conn_mssr->prepare($sql);
            $sth->execute()or die($err);

    //---------------------------------------------------
    //重導頁面
    //---------------------------------------------------

        $url ="";
        $page=str_repeat("../",1)."{$area}/content.php";
        $arg =array(
            'psize'=>$psize,
            'pinx' =>$pinx,
            'user_id'=>$user_id
        );
        if($date_filter!==''){
            $arg['date_filter']=$date_filter;
        }
        $arg['scrolltop']=$scrolltop;
        $arg =http_build_query($arg);

        if(!empty($arg)){
            $url="{$page}?{$arg}";

            //建立錨點
            $url="{$url}#{$anchor}";
        }else{
            $url="{$page}";
        }

        header("Location: {$url}");

        //呼叫頁面
        page_ok($url);
?>


<?php function page_ok($url){?>
<script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//重導頁面
//-------------------------------------------------------

    var url='<?php echo $url;?>';
    var par_scrolltop=$(parent).scrollTop();

    window.setTimeout(
        function(){
            location.replace(url);
            callback1();
        },
        50
    );

    function callback1(){
        window.setTimeout(
            function(){
                var $body=(window.opera)?(document.compatMode=="CSS1Compat"?parent.$('html'):parent.$('body')):parent.$('html,body');
                $body.animate({
                    scrollTop:par_scrolltop
                },0);
                callback2();
            },
            300
        );
    }

    function callback2(){
        window.setTimeout(
            function(){
                window.close();
            },
            50
        );
    }

</script>
<?php }?>