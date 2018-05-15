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

                    APP_ROOT.'lib/php/db/code'
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_articile');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //from
    //user_id       使用者主索引
    //article_id    文章主索引
    //reply_id      回覆主索引
    //g_pinx
    //g_psize

        $get_chk=array(
            'from       ',
            'user_id    ',
            'article_id ',
            'reply_id   ',
            'g_pinx     ',
            'g_psize    '
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
    //from
    //user_id       使用者主索引
    //article_id    文章主索引
    //reply_id      回覆主索引
    //g_pinx
    //g_psize

        //GET
        $from       =trim($_GET[trim('from      ')]);
        $user_id    =trim($_GET[trim('user_id   ')]);
        $article_id =trim($_GET[trim('article_id')]);
        $reply_id   =trim($_GET[trim('reply_id  ')]);
        $g_pinx     =trim($_GET[trim('g_pinx    ')]);
        $g_psize    =trim($_GET[trim('g_psize   ')]);
        $nav_flag   =(isset($_GET[trim('nav_flag')]))?trim($_GET[trim('nav_flag')]):'yes';

        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //from
    //user_id       使用者主索引
    //article_id    文章主索引
    //reply_id      回覆主索引
    //g_pinx
    //g_psize

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
           $user_id=(int)$user_id;
           if($user_id===0){
              $arry_err[]='使用者主索引,不為整數!';
           }
        }

        if($article_id===''){
           $arry_err[]='文章主索引,未輸入!';
        }else{
           $article_id=(int)$article_id;
           if($article_id===0){
              $arry_err[]='文章主索引,不為整數!';
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
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //文章資訊
        //---------------------------------------------------

            $user_id    =(int)$user_id;
            $article_id =(int)$article_id;

            $query_sql="
                SELECT
                    `user_id`,
                    `article_id`,
                    `article_title`,
                    `article_content`,
                    `article_state`,
                    `keyin_cdate`
                FROM `mssr_forum_article`
                WHERE 1=1
                    AND `article_id`={$article_id   }
                ORDER BY `keyin_cdate` DESC
                LIMIT 1;
            ";
            $arry_article_info=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

        //---------------------------------------------------
        //回覆資訊
        //---------------------------------------------------

            $reply_id=(int)$reply_id;
            $arrys_reply=array();

            $query_sql="
                SELECT
                    `user_id`,
                    `article_id`,
                    `reply_id`,
                    `reply_content`,
                    `reply_state`,
                    `keyin_cdate`
                FROM `mssr_forum_article_reply`
                WHERE 1=1
                    AND `article_id`={$article_id   }
                ORDER BY `keyin_cdate` ASC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);
            if($db_results_cno!==0){
                foreach($db_results as $db_result){
                    $rs_reply_id=(int)$db_result['reply_id'];
                    $arrys_reply[]=$rs_reply_id;
                }
            }

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;  //資料總筆數
        $psize =10; //單頁筆數,預設10筆
        $pnos  =0;  //分頁筆數
        $pinx  =1;  //目前分頁索引,預設1
        $sinx  =0;  //值域起始值
        $einx  =0;  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

        $numrow=$db_results_cno;

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        if($numrow!==0){
            $arrys_chunk =array_chunk($db_results,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
            page_hrs($title);
            die();
        }else{
            page_nrs($title);
            die();
        }
?>
<?php function page_hrs($title="") {?>
<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_mssr;
        global $arry_conn_user;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arry_article_info;
        global $arrys_result;
        global $arrys_reply;
        global $config_arrys;
        global $conn_mssr;
        global $conn_user;

        global $from;
        global $user_id;
        global $g_pinx;
        global $g_psize;
        global $sess_user_id;
        global $article_id;
        global $reply_id;
        global $nav_flag;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0; //欄位個數
        $btn_nos=0; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $goal_pinx=1;
        $arrys_reply=array_chunk($arrys_reply,$psize);
        foreach($arrys_reply as $inx=>$arry_reply){
            foreach($arry_reply as $rs_reply_id){
                $rs_reply_id=(int)$rs_reply_id;
                if($rs_reply_id===$reply_id){
                    $goal_pinx=$inx+1;
                }
            }
        }

        $sess_user_id=(int)$sess_user_id;

        $a_user_id        =(int)$arry_article_info[0][trim('user_id        ')];
        $a_article_id     =(int)$arry_article_info[0][trim('article_id     ')];
        $a_article_title  =trim($arry_article_info[0][trim('article_title  ')]);
        $a_article_content=trim($arry_article_info[0][trim('article_content')]);
        $a_article_state  =trim($arry_article_info[0][trim('article_state  ')]);
        $a_keyin_cdate    =trim($arry_article_info[0][trim('keyin_cdate    ')]);
        $a_keyin_cdate    =date("Y-m-d H:i",strtotime($a_keyin_cdate));

        $a_user_name='';
        $get_user_info=get_user_info($conn_user,$a_user_id,$array_filter=array('name'),$arry_conn_user);
        if(!empty($get_user_info))$a_user_name =trim($get_user_info[0]['name']);

        //文章狀態
        $a_article_state_code=1;
        $a_article_state =trim($a_article_state);
        if($a_article_state!=='刪除'){
            $a_article_state_code=2;
        }

        //是否按讚
        $a_article_like_code=1;
        $sql="
            SELECT
                `user_id`
            FROM `mssr_forum_article_like_log`
            WHERE 1=1
                AND `mssr_forum_article_like_log`.`user_id`   ={$sess_user_id }
                AND `mssr_forum_article_like_log`.`article_id`={$a_article_id }
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
        if(!empty($db_results)){
            $a_article_like_code=2;
        }
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
</Head>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:0px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <span class="fc_red1">●請注意! 此頁為新開頁面，</span>
                                    <input type="button" value="上一頁" class="ibtn_gr9030" onclick="back();void(0);" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close();" onmouseover="this.style.cursor='pointer'">
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 文章表格 開始 -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:10px;"/>
                    <tr class="fsize_18 font-weight1 font-family1 fc_green0">
                        <td align='left'>
                            <span style='position:relative;left:10px;top:5px;'>文章資訊</span>
                        </td>
                    </tr>
                </table>
                <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;margin-bottom:15px" class="table_style3">
                    <tr align="left" valign="middle" class="fsize_16">
                        <th width="" class='fsize_16 fc_brown0' height="40px" colspan='2'>
                            <span class='fc_brown0' style='display:inline;float:left;'>
                                標題：
                                <?php echo htmlspecialchars($a_article_title);?>
                            </span>
                            <span class='fc_brown0' style='display:inline;float:right;'>
                                時間：
                                <?php echo htmlspecialchars($a_keyin_cdate);?>
                            </span>
                        </th>
                    </tr>
                    <tr align="center" valign="middle" class="fsize_15">
                        <td width="135px" height="135px">
                            <?php echo htmlspecialchars($a_user_name);?><br/><br/>

                            <input type="button" value="<?php if($a_article_like_code===1){echo '讚';}else{echo '收回讚';}?>"
                            class=" <?php if($a_article_like_code!==1)echo 'fc_blue0';?>" onmouseover="this.style.cursor='pointer'"
                            a_user_id=<?php echo (int)$a_user_id;?>
                            a_article_id=<?php echo $a_article_id;?>
                            onclick="a_like(this,<?php echo (int)$a_user_id;?>,<?php echo $a_article_id;?>,<?php echo (int)$a_article_like_code;?>);void(0);">

                            <input type="button" value="<?php if($a_article_state==='刪除'){echo '不隱藏';}else{echo '隱藏';}?>"
                            class=" <?php if($a_article_state==='刪除')echo 'fc_red0';?>" onmouseover="this.style.cursor='pointer'"
                            a_user_id=<?php echo (int)$a_user_id;?>
                            a_article_id=<?php echo $a_article_id;?>
                            onclick="a_del(this,<?php echo (int)$a_user_id;?>,<?php echo $a_article_id;?>,<?php echo (int)$a_article_state_code;?>);void(0);">
                        </td>
                        <td align='left' valign='top' width="" height="40px">
                            <?php echo htmlspecialchars($a_article_content);?>
                        </td>
                    </tr>
                </table>
                <!-- 文章表格 結束 -->

                <!-- 回覆表格 開始 -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%"/>
                    <tr class="fsize_18 font-weight1 font-family1 fc_green0">
                        <td align='left'>
                            <span style='position:relative;left:10px;top:5px;'>回覆資訊</span>
                        </td>
                    </tr>
                </table>
                <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;" class="table_style3">
                    <tr align="center" valign="middle" class="fsize_18">
                        <th width="135px" height="40px">回覆人</th>
                        <th width="" height="40px">
                            <span class='' style='display:inline;float:left;'>
                                回覆內容
                            </span>
                        </th>
                    </tr>

                    <?php foreach($arrys_result as $inx=>$arry_result) :?>
                    <?php
                    //---------------------------------------------------
                    //接收欄位
                    //---------------------------------------------------

                        extract($arry_result, EXTR_PREFIX_ALL, "rs");

                    //---------------------------------------------------
                    //處理欄位
                    //---------------------------------------------------

                        //回覆人主索引
                        $rs_user_id         =(int)$rs_user_id;

                        //文章主索引
                        $rs_article_id      =(int)$rs_article_id;

                        //回覆主索引
                        $rs_reply_id        =(int)$rs_reply_id;

                        //回覆內容
                        $rs_reply_content   =trim($rs_reply_content);

                        //回覆時間
                        $rs_keyin_cdate     =trim($rs_keyin_cdate);
                        $rs_keyin_cdate     =date("Y-m-d H:i",strtotime($rs_keyin_cdate));

                        //回覆狀態
                        $rs_reply_state_code=1;
                        $rs_reply_state =trim($rs_reply_state);
                        if($rs_reply_state!=='刪除'){
                            $rs_reply_state_code=2;
                        }

                    //---------------------------------------------------
                    //特殊處理
                    //---------------------------------------------------

                        $get_user_info=get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user);
                        if(!empty($get_user_info))$rs_user_name =trim($get_user_info[0]['name']);

                        //-----------------------------------------------
                        //是否按讚
                        //-----------------------------------------------

                            $rs_reply_like_code=1;
                            $sql="
                                SELECT
                                    `user_id`
                                FROM `mssr_forum_article_reply_like_log`
                                WHERE 1=1
                                    AND `mssr_forum_article_reply_like_log`.`user_id`   ={$sess_user_id }
                                    AND `mssr_forum_article_reply_like_log`.`reply_id`  ={$rs_reply_id  }
                            ";
                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(!empty($db_results)){
                                $rs_reply_like_code=2;
                            }
                    ?>
                    <tr class="fsize_16">
                        <td height="150px" align="center" valign="top"
                        style='<?php if($reply_id===$rs_reply_id)echo 'border-top:3px solid #f00;border-bottom:3px solid #f00;border-left:3px solid #f00;';?>'>
                            <br/>
                            <?php echo htmlspecialchars($rs_keyin_cdate);?><br/><br/>
                            <?php echo htmlspecialchars($rs_user_name);?><br/><br/>

                            <input type="button" value="<?php if($rs_reply_like_code===1){echo '讚';}else{echo '收回讚';}?>"
                            class=" <?php if($rs_reply_like_code!==1)echo 'fc_blue0';?>" onmouseover="this.style.cursor='pointer'"
                            r_user_id=<?php echo (int)$rs_user_id;?>
                            r_reply_id=<?php echo $rs_reply_id;?>
                            onclick="r_like(this,<?php echo (int)$rs_user_id;?>,<?php echo $rs_reply_id;?>,<?php echo (int)$rs_reply_like_code;?>);void(0);">

                            <input type="button" value="<?php if($rs_reply_state==='刪除'){echo '不隱藏';}else{echo '隱藏';}?>"
                            class=" <?php if($rs_reply_state==='刪除')echo 'fc_red0';?>" onmouseover="this.style.cursor='pointer'"
                            r_user_id=<?php echo (int)$rs_user_id;?>
                            r_reply_id=<?php echo $rs_reply_id;?>
                            onclick="r_del(this,<?php echo (int)$rs_user_id;?>,<?php echo $rs_reply_id;?>,<?php echo (int)$rs_reply_state_code;?>);void(0);">
                        </td>
                        <td height="150px" align="left" valign="top"
                        style='<?php if($reply_id===$rs_reply_id)echo 'border-top:3px solid #f00;border-bottom:3px solid #f00;border-right:3px solid #f00;';?>'>
                            <?php echo htmlspecialchars($rs_reply_content);?>
                        </td>
                    </tr>
                    <?php endforeach ;?>
                </table>

                <table border="0" width="100%" style=''>
                    <tr valign="middle">
                        <td align="left">
                            <!-- 分頁列 -->
                            <span id="page" style="position:relative;margin-top:10px;"></span>
                        </td>
                    </tr>
                </table>
                <!-- 回覆表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var g_psize=<?php echo $g_psize;?>;
    var g_pinx =<?php echo $g_pinx;?>;

    var user_id=<?php echo $user_id;?>;
    var from='<?php echo trim($from);?>';
    var article_id=<?php echo $article_id;?>;
    var reply_id=<?php echo $reply_id;?>;
    var goal_pinx=<?php echo $goal_pinx;?>;
    var nav_flag='<?php echo $nav_flag;?>';

    function back(){
    //上一頁
        var url ='';
        var page=str_repeat('../',1)+from+'/content.php';
        var arg ={
            'user_id':user_id,
            'psize':g_psize,
            'pinx' :g_pinx
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        blockui();
        go(url,'self');
    }

    function a_like(obj,a_user_id,a_article_id,like_code){
    //讚文章
        var a_user_id   =parseInt(a_user_id);
        var a_article_id=parseInt(a_article_id);
        var like_code   =parseInt(like_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(a_user_id)),
            article_id  :encodeURI(trim(a_article_id)),
            like_code   :encodeURI(trim(like_code))
        };
        var url='../article/likeA.php';
        ajax(obj,'a_like',url,data);
    }

    function a_del(obj,a_user_id,a_article_id,del_code){
    //隱藏文章
        var a_user_id   =parseInt(a_user_id);
        var a_article_id=parseInt(a_article_id);
        var del_code    =parseInt(del_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(a_user_id)),
            article_id  :encodeURI(trim(a_article_id)),
            del_code    :encodeURI(trim(del_code))
        };
        var url='../article/delA.php';
        ajax(obj,'a_del',url,data);
    }

    function r_like(obj,r_user_id,r_reply_id,like_code){
    //讚回覆
        var r_user_id   =parseInt(r_user_id);
        var r_reply_id  =parseInt(r_reply_id);
        var like_code   =parseInt(like_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(r_user_id)),
            reply_id    :encodeURI(trim(r_reply_id)),
            like_code   :encodeURI(trim(like_code))
        };
        var url='../reply/likeA.php';
        ajax(obj,'r_like',url,data);
    }

    function r_del(obj,r_user_id,r_reply_id,del_code){
    //隱藏回覆
        var r_user_id   =parseInt(r_user_id);
        var r_reply_id  =parseInt(r_reply_id);
        var del_code    =parseInt(del_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(r_user_id)),
            reply_id    :encodeURI(trim(r_reply_id)),
            del_code    :encodeURI(trim(del_code))
        };
        var url='../reply/delA.php';
        ajax(obj,'r_del',url,data);
    }

    function ajax(obj,even,url,data){
    //ajax設置
        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :0,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :url,
            type       :"GET",
            datatype   :"json",
            data       :data,

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理

                var respones=jQuery.parseJSON(respones);
                var state=trim(respones.state);
                var code=parseInt(respones.code);

                if(state==='ok'){
                    switch(trim(even)){
                        case 'a_like':
                            var a_user_id   =parseInt(obj.getAttribute('a_user_id'));
                            var a_article_id=parseInt(obj.getAttribute('a_article_id'));
                            if(code===2){
                                obj.value='讚';
                                obj.style.color='#000000';
                                code--;
                            }else if(code===1){
                                obj.value='收回讚';
                                obj.style.color='#3571d4';
                                code++;
                            }else{
                            }
                            obj.onclick=function(){
                                a_like(this,a_user_id,a_article_id,code);
                            }
                        break;

                        case 'a_del':
                            var a_user_id   =parseInt(obj.getAttribute('a_user_id'));
                            var a_article_id=parseInt(obj.getAttribute('a_article_id'));
                            if(code===1){
                                obj.value='隱藏';
                                obj.style.color='#000000';
                                code++;
                            }else if(code===2){
                                obj.value='不隱藏';
                                obj.style.color='#ff0000';
                                code--;
                            }else{
                            }
                            obj.onclick=function(){
                                a_del(this,a_user_id,a_article_id,code);
                            }
                        break;

                        case 'r_like':
                            var r_user_id   =parseInt(obj.getAttribute('r_user_id'));
                            var r_reply_id=parseInt(obj.getAttribute('r_reply_id'));
                            if(code===2){
                                obj.value='讚';
                                obj.style.color='#000000';
                                code--;
                            }else if(code===1){
                                obj.value='收回讚';
                                obj.style.color='#3571d4';
                                code++;
                            }else{
                            }
                            obj.onclick=function(){
                                r_like(this,r_user_id,r_reply_id,code);
                            }
                        break;

                        case 'r_del':
                            var r_user_id   =parseInt(obj.getAttribute('r_user_id'));
                            var r_reply_id=parseInt(obj.getAttribute('r_reply_id'));
                            if(code===1){
                                obj.value='隱藏';
                                obj.style.color='#000000';
                                code++;
                            }else if(code===2){
                                obj.value='不隱藏';
                                obj.style.color='#ff0000';
                                code--;
                            }else{
                            }
                            obj.onclick=function(){
                                r_del(this,r_user_id,r_reply_id,code);
                            }
                        break;
                    }
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function blockui(){
        $.blockUI({
            message:'<h2 class="fc_white0">處理中...</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });
    }

    window.onload=function(){

        //scroll事件
		var $body = (window.opera)?(document.compatMode == "CSS1Compat"?parent.$('html'):parent.$('body')):parent.$('html,body');
		$body.animate({
			scrollTop: 0
		},0);

        //設定動態高度
        var oIFC=parent.document.getElementById('IFC');
        if(parseInt($(document).height())>1500){
            oIFC.style.height=parseInt($(document).height()+75)+'px';
        }else{
            oIFC.style.height='1500px';
        }

        if(nav_flag==='yes'){
            var psize=<?php echo $psize;?>;
            var pinx =<?php echo $pinx;?>;
            //console.log(pinx);
            //console.log(psize);
            //console.log(g_pinx);
            //console.log(g_psize);
            //console.log(goal_pinx);

            var url ='';
            var page=str_repeat('../',0)+'content.php';
            var arg ={
                'user_id'   :user_id,
                'article_id':article_id,
                'reply_id'  :reply_id,
                'from'      :from,
                'pinx'      :goal_pinx,
                'psize'     :psize,
                'g_pinx'    :g_pinx,
                'g_psize'   :g_psize,
                'nav_flag'  :'no'
            };
            var _arg=[];
            for(var key in arg){
                _arg.push(key+"="+encodeURI(arg[key]));
            }
            arg=_arg.join("&");

            if(arg.length!=0){
                url+=page+"?"+arg;
            }else{
                url+=page;
            }
            go(url,'self');
        }

        //分頁列
        var cid         ="page";                        //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'content.php',
            'page_args' :{
                'user_id'   :user_id,
                'article_id':article_id,
                'reply_id'  :reply_id,
                'from'      :from,
                'g_pinx'    :g_pinx,
                'g_psize'   :g_psize,
                'nav_flag'  :'no'
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    }
</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_hrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>


<?php function page_nrs($title="") {?>
<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 開始
//-------------------------------------------------------

    //---------------------------------------------------
    //外部變數
    //---------------------------------------------------

        //config.php
        global $PAGE_SELF;
        global $FOLDER_SELF;
        global $nl;
        global $tab;
        global $fso_enc;
        global $page_enc;
        global $arry_conn_mssr;
        global $arry_conn_user;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arry_article_info;
        global $arrys_result;
        global $arrys_reply;
        global $config_arrys;
        global $conn_mssr;
        global $conn_user;

        global $from;
        global $user_id;
        global $g_pinx;
        global $g_psize;
        global $sess_user_id;
        global $article_id;
        global $reply_id;
        global $nav_flag;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $goal_pinx=1;
        $arrys_reply=array_chunk($arrys_reply,$psize);
        foreach($arrys_reply as $inx=>$arry_reply){
            foreach($arry_reply as $rs_reply_id){
                $rs_reply_id=(int)$rs_reply_id;
                if($rs_reply_id===$reply_id){
                    $goal_pinx=$inx+1;
                }
            }
        }

        $sess_user_id=(int)$sess_user_id;

        $a_user_id        =(int)$arry_article_info[0][trim('user_id        ')];
        $a_article_id     =(int)$arry_article_info[0][trim('article_id     ')];
        $a_article_title  =trim($arry_article_info[0][trim('article_title  ')]);
        $a_article_content=trim($arry_article_info[0][trim('article_content')]);
        $a_article_state  =trim($arry_article_info[0][trim('article_state  ')]);
        $a_keyin_cdate    =trim($arry_article_info[0][trim('keyin_cdate    ')]);
        $a_keyin_cdate    =date("Y-m-d H:i",strtotime($a_keyin_cdate));

        $a_user_name='';
        $get_user_info=get_user_info($conn_user,$a_user_id,$array_filter=array('name'),$arry_conn_user);
        if(!empty($get_user_info))$a_user_name =trim($get_user_info[0]['name']);

        //文章狀態
        $a_article_state_code=1;
        $a_article_state =trim($a_article_state);
        if($a_article_state!=='刪除'){
            $a_article_state_code=2;
        }

        //是否按讚
        $a_article_like_code=1;
        $sql="
            SELECT
                `user_id`
            FROM `mssr_forum_article_like_log`
            WHERE 1=1
                AND `mssr_forum_article_like_log`.`user_id`   ={$sess_user_id }
                AND `mssr_forum_article_like_log`.`article_id`={$a_article_id }
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
        if(!empty($db_results)){
            $a_article_like_code=2;
        }
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
</Head>

<Body>
    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:0px;">
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <span class="fc_red1">●請注意! 此頁為新開頁面，</span>
                                    <input type="button" value="上一頁" class="ibtn_gr9030" onclick="back();void(0);" onmouseover="this.style.cursor='pointer'">
                                    <input type="button" value="請按我關閉" class="ibtn_gr9030" onclick="parent.close();" onmouseover="this.style.cursor='pointer'">
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 文章表格 開始 -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:10px;"/>
                    <tr class="fsize_18 font-weight1 font-family1 fc_green0">
                        <td align='left'>
                            <span style='position:relative;left:10px;top:5px;'>文章資訊</span>
                        </td>
                    </tr>
                </table>
                <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;margin-bottom:15px" class="table_style3">
                    <tr align="left" valign="middle" class="fsize_16">
                        <th width="" class='fsize_16 fc_brown0' height="40px" colspan='2'>
                            <span class='fc_brown0' style='display:inline;float:left;'>
                                標題：
                                <?php echo htmlspecialchars($a_article_title);?>
                            </span>
                            <span class='fc_brown0' style='display:inline;float:right;'>
                                時間：
                                <?php echo htmlspecialchars($a_keyin_cdate);?>
                            </span>
                        </th>
                    </tr>
                    <tr align="center" valign="middle" class="fsize_15">
                        <td width="135px" height="135px">
                            <?php echo htmlspecialchars($a_user_name);?><br/><br/>

                            <input type="button" value="<?php if($a_article_like_code===1){echo '讚';}else{echo '收回讚';}?>"
                            class=" <?php if($a_article_like_code!==1)echo 'fc_blue0';?>" onmouseover="this.style.cursor='pointer'"
                            a_user_id=<?php echo (int)$a_user_id;?>
                            a_article_id=<?php echo $a_article_id;?>
                            onclick="a_like(this,<?php echo (int)$a_user_id;?>,<?php echo $a_article_id;?>,<?php echo (int)$a_article_like_code;?>);void(0);">

                            <input type="button" value="<?php if($a_article_state==='刪除'){echo '不隱藏';}else{echo '隱藏';}?>"
                            class=" <?php if($a_article_state==='刪除')echo 'fc_red0';?>" onmouseover="this.style.cursor='pointer'"
                            a_user_id=<?php echo (int)$a_user_id;?>
                            a_article_id=<?php echo $a_article_id;?>
                            onclick="a_del(this,<?php echo (int)$a_user_id;?>,<?php echo $a_article_id;?>,<?php echo (int)$a_article_state_code;?>);void(0);">
                        </td>
                        <td align='left' valign='top' width="" height="40px">
                            <?php echo htmlspecialchars($a_article_content);?>
                        </td>
                    </tr>
                </table>
                <!-- 文章表格 結束 -->

                <!-- 回覆表格 開始 -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%"/>
                    <tr class="fsize_18 font-weight1 font-family1 fc_green0">
                        <td align='left'>
                            <span style='position:relative;left:10px;top:5px;'>回覆資訊</span>
                        </td>
                    </tr>
                </table>
                <table width="95%" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <!-- 在此設定寬高 -->
                        <td width="100%" height="200px" align="center" valign="top">
                            <!-- 內容 -->
                            <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:30px;" class="mod_data_tbl_outline">
                                <tr align="center" valign="middle">
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td height="200px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                                        <img src="../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                                        目前無回覆資料!
                                    </td>
                                </tr>
                            </table>
                            <!-- 內容 -->
                        </td>
                    </tr>
                </table>
                <!-- 回覆表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var g_psize=<?php echo $g_psize;?>;
    var g_pinx =<?php echo $g_pinx;?>;

    var user_id=<?php echo $user_id;?>;
    var from='<?php echo trim($from);?>';
    var article_id=<?php echo $article_id;?>;
    var reply_id=<?php echo $reply_id;?>;
    var goal_pinx=<?php echo $goal_pinx;?>;
    var nav_flag='<?php echo $nav_flag;?>';

    function back(){
    //上一頁
        var url ='';
        var page=str_repeat('../',1)+from+'/content.php';
        var arg ={
            'user_id':user_id,
            'psize':g_psize,
            'pinx' :g_pinx
        };
        var _arg=[];
        for(var key in arg){
            _arg.push(key+"="+encodeURI(arg[key]));
        }
        arg=_arg.join("&");

        if(arg.length!=0){
            url+=page+"?"+arg;
        }else{
            url+=page;
        }

        blockui();
        go(url,'self');
    }

    function a_like(obj,a_user_id,a_article_id,like_code){
    //讚文章
        var a_user_id   =parseInt(a_user_id);
        var a_article_id=parseInt(a_article_id);
        var like_code   =parseInt(like_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(a_user_id)),
            article_id  :encodeURI(trim(a_article_id)),
            like_code   :encodeURI(trim(like_code))
        };
        var url='../article/likeA.php';
        ajax(obj,'a_like',url,data);
    }

    function a_del(obj,a_user_id,a_article_id,del_code){
    //隱藏文章
        var a_user_id   =parseInt(a_user_id);
        var a_article_id=parseInt(a_article_id);
        var del_code    =parseInt(del_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(a_user_id)),
            article_id  :encodeURI(trim(a_article_id)),
            del_code    :encodeURI(trim(del_code))
        };
        var url='../article/delA.php';
        ajax(obj,'a_del',url,data);
    }

    function r_like(obj,r_user_id,r_reply_id,like_code){
    //讚回覆
        var r_user_id   =parseInt(r_user_id);
        var r_reply_id  =parseInt(r_reply_id);
        var like_code   =parseInt(like_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(r_user_id)),
            reply_id    :encodeURI(trim(r_reply_id)),
            like_code   :encodeURI(trim(like_code))
        };
        var url='../reply/likeA.php';
        ajax(obj,'r_like',url,data);
    }

    function r_del(obj,r_user_id,r_reply_id,del_code){
    //隱藏回覆
        var r_user_id   =parseInt(r_user_id);
        var r_reply_id  =parseInt(r_reply_id);
        var del_code    =parseInt(del_code);
        var data={
            ajax_flag   :1,
            user_id     :encodeURI(trim(r_user_id)),
            reply_id    :encodeURI(trim(r_reply_id)),
            del_code    :encodeURI(trim(del_code))
        };
        var url='../reply/delA.php';
        ajax(obj,'r_del',url,data);
    }

    function ajax(obj,even,url,data){
    //ajax設置
        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :0,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :url,
            type       :"GET",
            datatype   :"json",
            data       :data,

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理

                var respones=jQuery.parseJSON(respones);
                var state=trim(respones.state);
                var code=parseInt(respones.code);

                if(state==='ok'){
                    switch(trim(even)){
                        case 'a_like':
                            var a_user_id   =parseInt(obj.getAttribute('a_user_id'));
                            var a_article_id=parseInt(obj.getAttribute('a_article_id'));
                            if(code===2){
                                obj.value='讚';
                                obj.style.color='#000000';
                                code--;
                            }else if(code===1){
                                obj.value='收回讚';
                                obj.style.color='#3571d4';
                                code++;
                            }else{
                            }
                            obj.onclick=function(){
                                a_like(this,a_user_id,a_article_id,code);
                            }
                        break;

                        case 'a_del':
                            var a_user_id   =parseInt(obj.getAttribute('a_user_id'));
                            var a_article_id=parseInt(obj.getAttribute('a_article_id'));
                            if(code===1){
                                obj.value='隱藏';
                                obj.style.color='#000000';
                                code++;
                            }else if(code===2){
                                obj.value='不隱藏';
                                obj.style.color='#ff0000';
                                code--;
                            }else{
                            }
                            obj.onclick=function(){
                                a_del(this,a_user_id,a_article_id,code);
                            }
                        break;

                        case 'r_like':
                            var r_user_id   =parseInt(obj.getAttribute('r_user_id'));
                            var r_reply_id=parseInt(obj.getAttribute('r_reply_id'));
                            if(code===2){
                                obj.value='讚';
                                obj.style.color='#000000';
                                code--;
                            }else if(code===1){
                                obj.value='收回讚';
                                obj.style.color='#3571d4';
                                code++;
                            }else{
                            }
                            obj.onclick=function(){
                                r_like(this,r_user_id,r_reply_id,code);
                            }
                        break;

                        case 'r_del':
                            var r_user_id   =parseInt(obj.getAttribute('r_user_id'));
                            var r_reply_id=parseInt(obj.getAttribute('r_reply_id'));
                            if(code===1){
                                obj.value='隱藏';
                                obj.style.color='#000000';
                                code++;
                            }else if(code===2){
                                obj.value='不隱藏';
                                obj.style.color='#ff0000';
                                code--;
                            }else{
                            }
                            obj.onclick=function(){
                                r_del(this,r_user_id,r_reply_id,code);
                            }
                        break;
                    }
                }
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    return false;
                }else{
                    return false;
                }
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function blockui(){
        $.blockUI({
            message:'<h2 class="fc_white0">處理中...</h2>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });
    }

    window.onload=function(){
        //scroll事件
		var $body = (window.opera)?(document.compatMode == "CSS1Compat"?parent.$('html'):parent.$('body')):parent.$('html,body');
		$body.animate({
			scrollTop: 0
		},0);
    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>