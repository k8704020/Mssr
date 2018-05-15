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
            APP_ROOT.'service/forum/inc/code',

            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code',
            APP_ROOT.'lib/php/date/code'
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
        if(empty($arrys_sess_login_info)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='/ac/index.php';
                </script>
            ";
            die($jscript_back);
        }

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

        //特殊處理
        if(!isset($sess_country_code)){
            $sess_country_code='tw';
        }
        if($sess_country_code!=='tw'){
            die();
        }

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

        if($method==='' || !function_exists($method)){
            die('發生嚴重錯誤');
        }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,明日聊書";

        //標籤
        $meta=meta($rd=1);

        //導覽列
        $navbar=navbar($rd=1);

        //廣告牆
        $carousel=carousel($rd=1);

        //註腳列
        $footbar=footbar($rd=1);

    //---------------------------------------------------
    //函式列表
    //---------------------------------------------------

        //add_group()   建立聊書小組

    //---------------------------------------------------
    //呼叫函式
    //---------------------------------------------------

        call_user_func($method,$arrys_sess_login_info);
?>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //OBJ
    var notification=new notification();

    //ONLOAD
    $(function(){
        setTimeout(function(){
            if(window.EventSource){
                var source = new EventSource('../pages/require/msg_cno/code.php');
                source.onmessage = function(e) {
                    var request_cno=e.data;
                    for(var i=0; i<$('.request_cno').length;i++){
                        var $request_cno=$('.request_cno').eq(i);
                        if($request_cno.html()!=request_cno){
                            if(parseInt($request_cno.html())<parseInt(request_cno)){
                                var notification_tag    =1;
                                var notification_title  ="明日聊書系統: "+ new Date().toLocaleString();
                                var notification_icon   ="http://www.cot.org.tw/mssr/service/forum/img/logo.png";
                                var notification_content="您有一條新的訊息，請進入聊書系統觀看！";
                                notification.show_notification(notification_tag, notification_title, notification_icon, notification_content);
                            }
                            $request_cno.empty().append(request_cno).animate({opacity:'0'},250).animate({opacity:'1'},500);
                        }
                    }
                };
            }
        }, 100);
    })

</script>


<?php function edit_article() {?>
<?php
//-------------------------------------------------------
//edit_article 區塊 -- 開始
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
        global $file_server_enable;
        global $arry_ftp1_info;

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $get_article_id=0;
        if(isset($_GET['article_id'])&&(int)($_GET['article_id'])!==0)$get_article_id=(int)($_GET['article_id']);

        //-----------------------------------------------
        //發文鷹架
        //-----------------------------------------------

            $article_eagle_content=article_eagle(1);
            $article_eagle_code   =article_eagle(2);

        //-----------------------------------------------
        //個人書櫃 SQL
        //-----------------------------------------------

            $sess_user_book_results=array();
            if(isset($sess_user_id)){
                $sql="
                    SELECT `mssr`.`mssr_rec_book_cno`.`book_sid`
                    FROM `mssr`.`mssr_rec_book_cno`
                    WHERE 1=1
                        AND `mssr`.`mssr_rec_book_cno`.`user_id`={$sess_user_id}
                    GROUP BY `mssr`.`mssr_rec_book_cno`.`user_id`,
                             `mssr`.`mssr_rec_book_cno`.`book_sid`
                    ORDER BY `mssr`.`mssr_rec_book_cno`.`keyin_cdate` DESC
                ";
                $sess_user_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

        //-----------------------------------------------
        //文章資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,
                    `user`.`member`.`sex`,

                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_article`.`user_id`,
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
                    AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$get_article_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($db_results)){
                @header("Location:user.php?user_id={$sess_user_id}&tab=1");
                die();
            }else{
                $db_results_cno         =count($db_results);
                $article_results        =$db_results;

                $rs_article_book_sid    =trim($article_results[0]['book_sid']);
                $rs_article_title       =trim($article_results[0]['article_title']);
                $rs_article_content     =trim($article_results[0]['article_content']);
                $rs_article_user_name   =trim($article_results[0]['name']);
                $rs_article_keyin_mdate =trim($article_results[0]['keyin_mdate']);
                $rs_article_like_cno    =(int)($article_results[0]['article_like_cno']);
                $rs_article_id          =(int)($article_results[0]['article_id']);
                $rs_article_from        =(int)($article_results[0]['article_from']);
                $rs_article_category    =(int)($article_results[0]['article_category']);
                $rs_article_user_id     =(int)($article_results[0]['user_id']);
                $rs_article_user_sex    =(int)($article_results[0]['sex']);

                //特殊處理
                $rs_article_book_name='';
                $arry_book_infos=get_book_info($conn_mssr,$rs_article_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                if(!empty($arry_book_infos)){$rs_article_book_name=trim($arry_book_infos[0]['book_name']);}else{}
                $get_book_sid=mysql_prep(trim($rs_article_book_sid));

                //---------------------------------------
                //鷹架資訊 SQL
                //---------------------------------------

                    $sql="
                        SELECT
                            `mssr_forum`.`mssr_forum_article_eagle_rev`.`eagle_code`
                        FROM `mssr_forum`.`mssr_forum_article_eagle_rev`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_article_eagle_rev`.`article_id`={$get_article_id}
                    ";
                    $eagle_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    foreach($eagle_rev_results as $eagle_rev_result){
                        $arry_eagle_code[]=(int)$eagle_rev_result['eagle_code'];
                    }
                    $list_eagle_code=implode(",",$arry_eagle_code).',';

                //---------------------------------------
                //其他資訊
                //---------------------------------------

                    if(isset($_GET['psize'])){
                        $psize=(int)$_GET['psize'];
                        if($psize===0){
                            $psize=20;
                        }
                    }
                    if(isset($_GET['pinx'])){
                        $pinx=(int)$_GET['pinx'];
                        if($pinx===0){
                            $pinx=1;
                        }
                    }

                    $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
                    $modal_bookstore_rec=modal_bookstore_rec($rd=1);
            }
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->

    <style>
        /* 頁面微調 */
        .edit_article{
            position: relative;
            margin: 0 auto;
            margin-bottom: 20px;
            border-radius: 5px;
            min-height: 125px;
            border: 1px solid #ebebeb;
            background-color: #fdfdfd;
        }
        #article_title{
            position: relative;
            margin-top: 20px;
        }
        .btn_add_article{
            position: relative;
            margin: 0 3px;
            margin-bottom: 20px;
        }
    </style>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116055812-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-116055812-1');
</script>

</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- forum,start -->
            <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3 style='color:#ec6c01;font-weight:bold;'>編輯文章...</h3>
                    <hr></hr>
                    <div class="row edit_article">
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg3 text-center visible-xs add_article_help-visible-xs" style="margin-top:20px;">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
                            role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
                            >引用書店推薦</a>

                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
                            role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
                            >使用發文輔助</a>
                            <div class="row eagle_lv_1" style="display:none;">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
                                    <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
                                        <option disabled="disabled" selected>請選擇書本類型</option>
                                        <?php foreach($article_eagle_content as $key=>$arry_val):?>
                                            <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-9 col-md-9 col-lg9" style="border-right:1px solid #eeeeee;">
                            <form id="Form1"  name="Form1" method="post" onsubmit="return false;">
                                <div class="form-group">
                                    <input type="text" id="article_title" name="article_title" class="form-control" placeholder="1.請輸入文章標題"
                                    value="<?php echo htmlspecialchars($rs_article_title);?>">
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control article_content" id="article_content[]" name="article_content[]" rows="5" placeholder="2.請輸入文章內容"
                                    ><?php echo htmlspecialchars($rs_article_content);?></textarea>
                                </div>
                                <select class="form-control" id="article_category" name="article_category" style="margin-bottom:10px;">
                                    <option value="1" <?php if($rs_article_category===1)echo 'selected';?>>綜合討論</option>
                                    <option value="2" <?php if($rs_article_category===2)echo 'selected';?>>我想要問</option>
                                    <option value="3" <?php if($rs_article_category===3)echo 'selected';?>>我想要分享</option>
                                </select>
                                <div class="checkbox">
                                   <label>
                                       <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
                                   </label>
                                    <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
                                </div>
                                <hr></hr>
                                <button type="button" class="btn btn-default pull-right btn_add_article" onclick="Btn_edit_article();void(0);">送出</button>
                                <button style='margin:0px 2px;margin-bottom:20px;' type="button" class="btn btn-default pull-right" onclick="window.history.back(-1);void(0);">返回</button>
                                <div class="form-group hidden">
                                    <input type="text" class="form-control" name="eagle_code" value="<?php echo htmlspecialchars($list_eagle_code);?>" id="eagle_code">
                                    <input type="text" class="form-control" name="article_from" value="<?php echo (int)$rs_article_from;?>">
                                    <input type="text" class="form-control" name="article_id" value="<?php echo (int)($rs_article_id);?>">
                                    <input type="text" class="form-control" name="psize" value="<?php echo (int)($psize);?>">
                                    <input type="text" class="form-control" name="pinx" value="<?php echo (int)($pinx);?>">
                                    <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                    <input type="text" class="form-control" name="method" value="edit_article">
                                </div>
                            </form>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg3 text-center hidden-xs add_article_help-hidden-xs"
                        style="margin-top:20px;">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
                            role="button" style="color:#ffffff;" onclick="show_bookstore_rec(0);void(0);"
                            >引用書店推薦</a>

                            <a href="javascript:void(0);" class="btn btn-primary btn-block"
                            role="button" style="color:#ffffff;" onclick="$('div.eagle_lv_1').fadeIn();void(0);"
                            >使用發文輔助</a>
                            <div class="row eagle_lv_1" style="display:none;">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg12" style="margin-top:10px;">
                                    <select class="form-control eagle_lv_1 select_eagle_lv_1" onchange="article_eagle(eagle_lv=1);void(0);">
                                        <option disabled="disabled" selected>請選擇書本類型</option>
                                        <?php foreach($article_eagle_content as $key=>$arry_val):?>
                                            <option>&nbsp;&nbsp;<?php echo trim($key);?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- forum,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

    <!-- modal_bookstore_rec,start -->
    <?php echo $modal_bookstore_rec;?>
    <!-- modal_bookstore_rec,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //OBJ
    var article_eagle_content=<?php echo json_encode($article_eagle_content,true);?>;
    var article_eagle_code   =<?php echo json_encode($article_eagle_code,true);?>;
    var arry_my_borrow       =<?php echo json_encode(array(),true);?>;
    var json_sess_user_book_results ='<?php echo json_encode($sess_user_book_results,true);?>';


    //FUNCTION
    function show_bookstore_rec(no){
    //引用書店推薦

        var no=parseInt(no);

        if(no===0){
            $('#modal_bookstore_rec').modal();
            show_bookstore_rec(1);
        }else{
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :50000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :"../controller/load.php",
                type       :"POST",
                data       :{
                    no      :encodeURI(trim(no                  )),
                    sess_user_book_results:(trim(json_sess_user_book_results)),
                    method  :encodeURI(trim('load_bookstore_rec')),
                    send_url:encodeURI(trim(send_url            ))
                },
            //事件
                beforeSend  :function(){
                //傳送前處理
                    $.blockUI({
                        message:'<h3>資料讀取中...</h3>',
                        baseZ: 2000,
                        css:{
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .6,
                            color: '#fff'
                        }
                    });
                },
                success     :function(respones){
                //成功處理
                    var respones=jQuery.parseJSON(respones);
                    if(no===1){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center'>";
                                        _html+="<img src='"+file_path+"' width='120' height='120' class='img-responsive' border='0' alt='bookstore_rec' style='width:120px;height:120px;'>";
                                        _html+="<div class='caption'>";
                                            _html+="<p>";
                                                _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='show_bookstore_rec_draw(this);' style='margin:0 2px;margin-top:2px;'>觀看</button>";
                                                _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else if(no===2){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var content=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-4 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:120px;'>";
                                        _html+="<p class='text-left' style='font-size:8pt;height:60px;'>"+content+"</p>";
                                        _html+="<div class='caption'>";
                                            _html+="<p>";
                                                _html+="<button content='"+content+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else if(no===3){
                        if(parseInt(respones.length)>0){
                            $(".show_bookstore_rec_content").empty();
                            for(key in respones){
                                var _html="";
                                var file_path=trim(respones[key]);
                                _html+="<div class='col-xs-12 col-sm-6 col-md-3 col-lg-3' style='margin:5px 0;'>";
                                    _html+="<div class='thumbnail text-center' style='word-break:break-all;height:80px;'>";
                                        _html+="<audio controls style='position:relative;top:10px;width:140px;'>";
                                            _html+="<source src='"+file_path+"' type='audio/mpeg'>";
                                        _html+="</audio>";
                                        _html+="<div class='caption text-center'>";
                                            _html+="<p>";
                                                _html+="<button file_path='"+file_path+"' type='button' class='btn btn-primary btn-xs' onclick='use_bookstore_rec(this,"+no+");' style='margin:0 2px;margin-top:2px;'>使用</button>";
                                            _html+="</p>";
                                        _html+="</div>";
                                    _html+="</div>";
                                _html+="</div>";
                                $(".show_bookstore_rec_content").append(_html);
                            }
                        }
                    }else{return false;}
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    return false;
                },
                complete    :function(){
                //傳送後處理
                    $.unblockUI();
                }
            });
        }
    }

    function use_bookstore_rec(obj,no){
        var no              =parseInt(no);
        var oarticle_content=document.getElementById('article_content[]');

        if(no===1){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[img src="'+trim(file_path)+'" img]'+nl;
            alert('圖片檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else if(no===2){
            var content=nl+trim($(obj).attr('content'))+nl;
            alert('文字即將貼在文章內容！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+content);
        }else if(no===3){
            var file_path=trim($(obj).attr('file_path'));
            var file_tag=nl+'[audio src="'+trim(file_path)+'" audio]'+nl;
            alert('錄音檔即將貼在文章內容，請勿更改格式！');
            $(oarticle_content).val($.trim($(oarticle_content).val())+file_tag);
        }else{return false;}

        $('#modal_bookstore_rec').modal('hide');
        oarticle_content.focus();
    }

    function show_bookstore_rec_draw(obj){
        var file_path=$(obj).attr('file_path');
        window.open(file_path);
    }

    function Btn_edit_article(){
    //修改文章

        var oForm1              =$('#Form1')[0];
        var osend_chk           =$('#send_chk')[0];
        var oarticle_title      =$('#article_title')[0];
        var oeagle_code         =document.getElementById('eagle_code');
        var oarticle_contents   =document.getElementsByName('article_content[]');
        var article_content_err =0;
        var arry_err            =[];

        if(trim(oarticle_title.value)===''){
            arry_err.push('請輸入文章標題');
        }
        if(oarticle_contents!==undefined && oarticle_contents.length!==0){
            for(var i=0;i<oarticle_contents.length;i++){
                oarticle_content=oarticle_contents[i];
                var placeholder=trim(oarticle_content.getAttribute('placeholder'));
                if(trim(oarticle_content.value)==='' || trim(oarticle_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    article_content_err++;
                }
                if(trim(oarticle_content.value).match(/…/gi)){
                    arry_err.push('請填補文章內容');
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oarticle_contents.length)===parseInt(article_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }
        if(trim(oeagle_code.value)===''){
            oeagle_code.value=0;
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                $.blockUI({
                    message:'<h3>發送文章中...</h3>',
                    baseZ: 2000,
                    css:{
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .6,
                        color: '#fff'
                    }
                });
                oForm1.action='../controller/edit.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    //ONLOAD
    $(function(){
        //發文輔助顯示
        if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
            $('.add_article_help-hidden-xs').remove();
        }else{
            $('.add_article_help-visible-xs').remove();
        }
    })

    $(document).ready(function() {
        $(document).get(0).oncontextmenu = function() {
            return false;
        };
    });
    //將滑鼠右鍵事件取消
    document.oncontextmenu = function(){
        window.event.returnValue=false;
    }
    $(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
            event.preventDefault();
         }
    });

</script>
</html>
<?php
//-------------------------------------------------------
//edit_article 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function edit_reply() {?>
<?php
//-------------------------------------------------------
//edit_reply 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $get_reply_id=0;
        if(isset($_GET['reply_id'])&&(int)($_GET['reply_id'])!==0)$get_reply_id=(int)($_GET['reply_id']);

        //-----------------------------------------------
        //回覆資訊 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `user`.`member`.`name`,

                    `mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`,

                    `mssr_forum`.`mssr_forum_reply`.`user_id`,
                    `mssr_forum`.`mssr_forum_reply`.`article_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_id`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_from`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_like_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`reply_report_cno`,
                    `mssr_forum`.`mssr_forum_reply`.`keyin_mdate`,

                    `mssr_forum`.`mssr_forum_reply_detail`.`reply_content`,

                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`,
                    `mssr_forum`.`mssr_forum_article_detail`.`article_content`
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
                    AND `mssr_forum`.`mssr_forum_reply`.`reply_id`       ={$get_reply_id}
                ORDER BY `mssr_forum`.`mssr_forum_reply`.`keyin_cdate` ASC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($db_results)){
                @header("Location:user.php?user_id={$sess_user_id}&tab=1");
                die();
            }else{
                $db_results_cno      =count($db_results);
                $reply_results       =$db_results;

                $rs_article_title    =trim($reply_results[0]['article_title']);
                $rs_article_content  =trim($reply_results[0]['article_content']);

                $rs_reply_book_sid   =trim($reply_results[0]['book_sid']);
                $rs_reply_user_name  =trim($reply_results[0]['name']);
                $rs_reply_keyin_mdate=trim($reply_results[0]['keyin_mdate']);
                $rs_reply_like_cno   =(int)($reply_results[0]['reply_like_cno']);
                $rs_article_id       =(int)($reply_results[0]['article_id']);
                $rs_reply_id         =(int)($reply_results[0]['reply_id']);
                $rs_reply_from       =(int)($reply_results[0]['reply_from']);
                $rs_reply_user_id    =(int)($reply_results[0]['user_id']);
                $rs_reply_content    =trim($reply_results[0]['reply_content']);

                if(isset($_GET['psize'])){
                    $psize=(int)$_GET['psize'];
                    if($psize===0){
                        $psize=20;
                    }
                }
                if(isset($_GET['pinx'])){
                    $pinx=(int)$_GET['pinx'];
                    if($pinx===0){
                        $pinx=1;
                    }
                }

                $send_url=trim('http://').trim($_SERVER['HTTP_HOST']).trim($_SERVER['REQUEST_URI']);
            }
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->

    <style>
        /* 頁面微調 */
        #reply_article_row{
            position: relative;
            margin-bottom: 20px;
        }
        #view_article .article_title{
            position: relative;
            margin-top: -5px;
            margin-bottom: -5px;
            font-size: 22px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- forum,start -->
            <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h3 style='color:#ec6c01;font-weight:bold;'>編輯回覆...</h3>
                    <hr></hr>
                    <div id="reply_article_row" class="row">
                        <div id="reply_article" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="row reply_eagle" style="position:relative;">
                                <div id="view_article" class="col-xs-12 col-sm-12 col-md-12 col-lg12">

                                    <div class="article_title row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            主題：<?php echo htmlspecialchars($rs_article_title);?>
                                        </div>
                                    </div>

                                    <hr></hr>
                                    <div class="form-group">
                                        <textarea class="form-control" id="reply_content[]" name="reply_content[]" rows="10" placeholder="請輸入文章內容"
                                        ><?php echo htmlspecialchars($rs_reply_content);?></textarea>
                                    </div>

                                    <div class="checkbox">
                                       <label>
                                           <input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則
                                       </label>
                                        <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>
                                    </div>

                                    <hr></hr>
                                    <button style='margin:0px 2px;margin-bottom:20px;' type="button" class="btn btn-default pull-right" onclick="Btn_edit_reply();void(0);">送出</button>
                                    <button style='margin:0px 2px;margin-bottom:20px;' type="button" class="btn btn-default pull-right" onclick="window.history.back(-1);void(0);">返回</button>

                                    <div class="form-group hidden">
                                        <input type="text" class="form-control" name="article_id" value="<?php echo (int)($rs_article_id);?>">
                                        <input type="text" class="form-control" name="reply_id" value="<?php echo (int)($rs_reply_id);?>">
                                        <input type="text" class="form-control" name="get_from" value="<?php echo (int)($rs_reply_from);?>">
                                        <input type="text" class="form-control" name="psize" value="<?php echo (int)($psize);?>">
                                        <input type="text" class="form-control" name="pinx" value="<?php echo (int)($pinx);?>">
                                        <input type="text" class="form-control" name="method" value="edit_reply">
                                        <input type="text" class="form-control" name="send_url" value="<?php echo trim($send_url);?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- forum,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //FUNCTION
    function Btn_edit_reply(){
    //修改回文

        var oForm1              =document.getElementById('Form1');
        var osend_chk           =$('#reply_article').find('#send_chk')[0];
        var oreply_contents     =document.getElementsByName('reply_content[]');
        var reply_content_err   =0;
        var arry_err            =[];

        if(oreply_contents!==undefined && oreply_contents.length!==0){
            for(var i=0;i<oreply_contents.length;i++){
                oreply_content=oreply_contents[i];
                var placeholder=trim(oreply_content.getAttribute('placeholder'));
                if(trim(oreply_content.value)==='' || trim(oreply_content.value)===trim(placeholder)){
                    //arry_err.push('請輸入文章內容 '+(i+1));
                    reply_content_err++;
                }
            }
        }else{
            arry_err.push('文章內容框錯誤');
        }
        if(parseInt(oreply_contents.length)===parseInt(reply_content_err)){
            arry_err.push('請輸入文章內容');
        }
        if(!osend_chk.checked){
            arry_err.push('請閱讀並勾選同意討論區規則');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                oForm1.action='../controller/edit.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    }

    $(document).ready(function() {
        $(document).get(0).oncontextmenu = function() {
            return false;
        };
    });
    //將滑鼠右鍵事件取消
    document.oncontextmenu = function(){
        window.event.returnValue=false;
    }
    $(document).keydown(function(event) {
        if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
            event.preventDefault();
         }
    });

</script>
</html>
<?php
//-------------------------------------------------------
//edit_reply 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function add_group() {?>
<?php
//-------------------------------------------------------
//add_group 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //-----------------------------------------------
        //書友 SQL
        //-----------------------------------------------

            $friend_results=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs" style="background-image:url('../img/default/front_cover_group.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="../img/default/group.jpg"
                width="160" height="160" border="0" alt="user_img"/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">建立聊書小組</span>
                <!-- jumbotron_name,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg" style="background-image:url('../img/default/front_cover_group.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="../img/default/group.jpg"
                width="100" height="100" border="0" alt="user_img"/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name">建立聊書小組</span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <!-- forum,start -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                    <h3 style='color:#ec6c01;font-weight:bold;'>讓我們開始建立聊書小組...</h3>
                    <hr></hr>
                    <div style='font-size:15px;color:#999999;'>
                        <span class='hidden-xs'>小組類型：</span>
                       <label class="checkbox-inline">
                          <input type="radio" name="group_type" value="1" checked>
                          公開小組
                       </label>
                       <label class="checkbox-inline">
                          <input type="radio" name="group_type" value="2">
                          私密小組
                       </label>
                    </div>
                    <div class="form-group" style="position:relative;margin-top:25px;">
                        <input class="form-control" type="text" id="group_name" name="group_name" placeholder="請輸入小組名稱">
                    </div>
                    <div class="form-group" style="position:relative;margin-top:25px;">
                        <textarea class="form-control" id="group_content" name="group_content" rows="3" placeholder="請輸入小組介紹"></textarea>
                    </div>
                    <div class="form-group" style="position:relative;margin-top:25px;">
                        <textarea class="form-control" id="group_rule" name="group_rule" rows="3" placeholder="請輸入小組規範"></textarea>
                    </div>
                    <div class="input-group" style="position:relative;margin-top:25px;">
                        <input type="text" class="form-control friend_name" name="friend_name[]" placeholder="請選擇或輸入第一位書友來聯署建立">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                選擇書友 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php
                                if(!empty($friend_results)){
                                    foreach($friend_results as $inx=>$friend_result):
                                        $rs_friend_state=(int)$friend_result['friend_state'];
                                        $rs_user_id     =(int)$friend_result['user_id'];
                                        $rs_friend_id   =(int)$friend_result['friend_id'];
                                        if($rs_user_id!==$sess_user_id || $rs_friend_id!==$sess_user_id){
                                            $tmp_user_id=0;
                                            if($rs_user_id!==$sess_user_id)$tmp_user_id=(int)$rs_user_id;
                                            if($rs_friend_id!==$sess_user_id)$tmp_user_id=(int)$rs_friend_id;
                                            $sql="
                                                SELECT
                                                    `name`,`sex`
                                                FROM `user`.`member`
                                                WHERE 1=1
                                                    AND `user`.`member`.`uid`={$tmp_user_id}
                                            ";
                                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                            $user_name ='';
                                            if(!empty($db_results)){
                                                $user_name=trim($db_results[0]['name']);
                                            }else{continue;}
                                        }
                                    if($rs_friend_state===1){
                                ?>
                                <li><a href="javascript:void(0);" onclick="auto(this,0);void(0);"><?php echo htmlspecialchars($user_name);?></a></li>
                                <?php }endforeach;}?>
                            </ul>
                        </div>
                    </div>
                    <div class="input-group" style="position:relative;margin:25px 0;">
                        <input type="text" class="form-control friend_name" name="friend_name[]" placeholder="請選擇或輸入第二位書友來聯署建立">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                選擇書友 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php
                                if(!empty($friend_results)){
                                    foreach($friend_results as $inx=>$friend_result):
                                        $rs_friend_state=(int)$friend_result['friend_state'];
                                        $rs_user_id     =(int)$friend_result['user_id'];
                                        $rs_friend_id   =(int)$friend_result['friend_id'];
                                        if($rs_user_id!==$sess_user_id || $rs_friend_id!==$sess_user_id){
                                            $tmp_user_id=0;
                                            if($rs_user_id!==$sess_user_id)$tmp_user_id=(int)$rs_user_id;
                                            if($rs_friend_id!==$sess_user_id)$tmp_user_id=(int)$rs_friend_id;
                                            $sql="
                                                SELECT
                                                    `name`,`sex`
                                                FROM `user`.`member`
                                                WHERE 1=1
                                                    AND `user`.`member`.`uid`={$tmp_user_id}
                                            ";
                                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                            $user_name ='';
                                            if(!empty($db_results)){
                                                $user_name=trim($db_results[0]['name']);
                                            }else{continue;}
                                        }
                                    if($rs_friend_state===1){
                                ?>
                                <li><a href="javascript:void(0);" onclick="auto(this,1);void(0);"><?php echo htmlspecialchars($user_name);?></a></li>
                                <?php }endforeach;}?>
                            </ul>
                        </div>
                    </div>
                    <hr></hr>

                    <div class="form-group pull-right" style="position:relative;margin-bottom:25px;">
                        <input type="button" onclick="history.back()" class="btn btn-default" value="放棄建立小組">
                        <button type="button" id="BtnS" class="btn btn-default">送出</button>
                    </div>

                    <div class="form-group hidden">
                        <input type="hidden" class="form-control" name="friend_id[]" value="0">
                        <input type="hidden" class="form-control" name="friend_id[]" value="0">
                        <input type="hidden" class="form-control" name="method" value="add_group">
                        <input type="hidden" class="form-control" name="send_url" value="#">
                    </div>
                </form>
            </div>
            <!-- forum,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    var arry_friend_info={};
    var arry_friend_name=[];

    <?php
    if(!empty($friend_results)){
        foreach($friend_results as $inx=>$friend_result):
            $rs_friend_state=(int)$friend_result['friend_state'];
            $rs_user_id     =(int)$friend_result['user_id'];
            $rs_friend_id   =(int)$friend_result['friend_id'];
            if($rs_user_id!==$sess_user_id || $rs_friend_id!==$sess_user_id){
                $tmp_user_id=0;
                if($rs_user_id!==$sess_user_id)$tmp_user_id=(int)$rs_user_id;
                if($rs_friend_id!==$sess_user_id)$tmp_user_id=(int)$rs_friend_id;
                $sql="
                    SELECT
                        `name`,`sex`
                    FROM `user`.`member`
                    WHERE 1=1
                        AND `user`.`member`.`uid`={$tmp_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $user_name ='';
                if(!empty($db_results)){
                    $user_name=trim($db_results[0]['name']);
                }else{continue;}
            }
        if($rs_friend_state===1){
    ?>
        arry_friend_info['<?php echo $user_name;?>']=<?php echo $tmp_user_id;?>;
        arry_friend_name.push('<?php echo $user_name;?>');
    <?php }endforeach;}?>


    //FUNCTION
    $(".friend_name").autocomplete({
        source: arry_friend_name
    });

    function auto(obj,no){
    //選單貼上

        var friend_name   =trim($(obj).text());
        var no            =parseInt(no);
        var ofriend_id    =document.getElementsByName('friend_id[]')[no];
        var ofriend_name  =document.getElementsByName('friend_name[]')[no];
        ofriend_id.value  =parseInt(arry_friend_info[friend_name]);
        ofriend_name.value=trim(friend_name);
    }

    $('#BtnS').click(function(){
    //建立小組

        var oForm1          =document.getElementById('Form1');
        var ogroup_name     =document.getElementById('group_name');
        var ogroup_content  =document.getElementById('group_content');
        var oggroup_rule    =document.getElementById('group_rule');
        var ofriend_ids     =document.getElementsByName('friend_id[]');
        var ofriend_names   =document.getElementsByName('friend_name[]');
        var arry_err        =[];
        var tmp_friend_names=[];

        if(trim(ogroup_name.value)===''){
            arry_err.push('請輸入小組名稱');
        }else{
            if(trim(ogroup_name.value).length>50){
                arry_err.push('小組名稱限制50字');
            }
        }
        if(trim(ogroup_content.value)===''){
            arry_err.push('請輸入小組介紹');
        }else{
            if(trim(ogroup_content.value).length>300){
                arry_err.push('小組介紹限制300字');
            }
        }
        if(trim(oggroup_rule.value)===''){
            arry_err.push('請輸入小組規範');
        }else{
            if(trim(oggroup_rule.value).length>300){
                arry_err.push('小組規範限制300字');
            }
        }
        for(var i=0;i<ofriend_names.length;i++){
            var ofriend_name=ofriend_names[i];
            if((trim(ofriend_name.value)==='')){
                arry_err.push('請選擇書友來聯署建立');
            }else{
                if(!in_array(trim(ofriend_name.value),arry_friend_name)){
                    arry_err.push('請選擇或輸入正確的書友來聯署建立');
                }else{
                    if(!in_array(trim(ofriend_name.value),tmp_friend_names)){
                        tmp_friend_names.push(trim(ofriend_name.value));
                    }else{
                        arry_err.push('請選擇不一樣的書友來聯署建立');
                    }
                }
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                for(var i=0;i<ofriend_names.length;i++){
                    var ofriend_name=ofriend_names[i];
                    ofriend_ids[i].value=arry_friend_info[trim(ofriend_name.value)];
                }
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    });

</script>
</html>
<?php
//-------------------------------------------------------
//add_group 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function view_mssr_forum_article_reply_rule() {?>
<?php
//-------------------------------------------------------
//view_mssr_forum_article_reply_rule 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<style>
    pre{
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 16px;
    }
    pre b{
        color: #ff0000;
    }
</style>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- forum,start -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                    <h3 style='color:#ec6c01;font-weight:bold;'>明日聊書討論規則...</h3>
                    <hr></hr>
                    <pre style="background-color:#ffffdd;position:relative;">發表文章前請先閱讀討論規則。</pre>
                    <div class="row">
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
<pre style="background-color:#ffffff;border:0px;position:relative;">
『明日聊書』討論規則，並<!-- 同時 -->適用於會員名稱<!-- 、會員個人頭像、會員個人簽名檔及私人訊息 -->

<b>本站對會員發布的文字<!-- 、圖片或檔案 -->保有片面修改或移除的權利。當會員使用本網站服務時，代表會員已經詳細閱讀並完全了解，並同意配合下述規定：</b>

1. 請勿一文多貼或是大意內容相同/類似的文章重覆刊登。
2. 為避免討論變成發洩區和口水版，禁止無發表自己意見或是看法的轉貼新聞。
3. 內文勿要求對方回覆<!-- 或傳檔案至電子信箱 -->，也勿提供電子信箱或私人連絡方式<!-- ，請善用站上私人訊息功能 -->。
4. 發表及回應文章，請不要文不對題，故意離題及語意不明，選字要正確並且請使用標點符號。
5. 每個人都有自己的政治與宗教立場，為避免政治與宗教口水在此討論肆虐與蔓延，一概請勿討論政治與宗教性話題。
6. 本站倡導正版軟體，因此嚴禁發文要求提供或討論破解軟體、註冊碼、音樂、影片、軟體複製等違反智慧財產權之文章。
7. 發言涉及攻擊、侮辱、影射或其他有違社會善良風俗、社會正義、國家安全、政府法令之內容，文章將會直接移除。
8. 發文請將文章的標題填寫清楚，內文也請詳細的說明。
9. 請勿以發文、回文<!-- 、私人訊息或簽名檔 -->等方式，進行商業廣告、騷擾別人...等違反版規行為。一經發現，會直接鎖住您的發言權限。
10. 『明日聊書』重視原創精神和智慧財產權的重要性，請盡量避免轉載圖片或文章，如果真的需要請一併列出原文圖出處<!-- ，圖片請勿上傳到『明日聊書』 -->。
11. 發文或回文時，請以理性的態度將事情原委說明清楚。若有蓄意攻訐、挑起戰火等挑釁行為的內容，會視狀況移文處理。
12. 討論內勿發表商業性質廣告或是為特定網站、blog宣傳；如果是討論上需要也盡可能避免引用購物/拍賣網站資料或連結，請盡量使用官方的    資料或圖片。(請注意勿觸犯第10條規定)
13. 文章內容請勿只打『as title』或『如題』等字眼，這是很不禮貌的行為。另外無意義的回應、刻意補足15字的發言都屬灌水的情形 (譬如15字了沒，12345678…等) 文章都會直接移除不另行通知<!-- ，請善用引言功能解決字數不足問題 -->。
<!-- 16. 給分功能是希望給予發文者的一種鼓勵，如果有不當使用或是灌分者，甚至是利用積分作為攻擊別人的工具，除將追回所有分數之外，該會員帳號積分歸零，並且帳號將設為黑名單，永遠不能給/得分。 -->

<b>14. 違反上述版規，且一再違反的會員，站方會視狀況設定不同懲罰處理方式。請會員發言前，務必先閱讀板規，珍惜得來不易的網路資源，謝謝配合。</b>
</pre>
                        </div>
                    </div>
                </form>
            </div>
            <!-- forum,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //FUNCTION


</script>
</html>
<?php
//-------------------------------------------------------
//view_mssr_forum_article_reply_rule 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function view_create_group_info() {?>
<?php
//-------------------------------------------------------
//view_create_group_info 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //-----------------------------------------------
        //接收參數
        //-----------------------------------------------
        //group_id  小組主索引

            $group_id='';
            if(isset($_GET['group_id'])&&trim($_GET['group_id'])!=='')$group_id=(int)($_GET['group_id']);

        //-----------------------------------------------
        //檢驗參數
        //-----------------------------------------------
        //group_id  小組主索引

            if($group_id==='' || (int)$group_id===0){
                die('發生嚴重錯誤');
            }

        //-----------------------------------------------
        //書友 SQL
        //-----------------------------------------------

            $friend_results=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);

        //-----------------------------------------------
        //檢核版主是誰
        //-----------------------------------------------

            $group_id=(int)$group_id;

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`  ={$group_id}
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_type` IN (2)
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`IN (1)
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($db_results)){
                $msg="發生嚴重錯誤";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }else{
                $group_user_type_2_user_id=(int)$db_results[0]['user_id'];
            }

        //-----------------------------------------------
        //連署進度 SQL
        //-----------------------------------------------

            $create_cno    =0;
            $create_success=0;
            $create_percent=0;
            $sql="
                SELECT
                    IFNULL((
                        SELECT `user`.`member`.`name`
                        FROM `user`.`member`
                        WHERE `mssr_forum`.`mssr_forum_user_request`.`request_from`=`user`.`member`.`uid`
                        LIMIT 1
                    ),'') AS `request_from_name`,

                    IFNULL((
                        SELECT `user`.`member`.`name`
                        FROM `user`.`member`
                        WHERE `mssr_forum`.`mssr_forum_user_request`.`request_to`=`user`.`member`.`uid`
                        LIMIT 1
                    ),'') AS `request_to_name`,

                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                    `mssr_forum`.`mssr_forum_group`.`group_content`,
                    `mssr_forum`.`mssr_forum_group`.`group_rule`,

                    `mssr_forum`.`mssr_forum_user_request`.`request_from`,
                    `mssr_forum`.`mssr_forum_user_request`.`request_to`,
                    `mssr_forum`.`mssr_forum_user_request`.`request_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                    `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$group_id}
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $create_cno=2;
            if(!empty($db_results)){
                $request_from       =(int)$db_results[0]['request_from'];
                $request_from_name  =trim($db_results[0]['request_from_name']);
                $group_id           =(int)$db_results[0]['group_id'];
                $group_name         =trim($db_results[0]['group_name']);
                $group_content      =trim($db_results[0]['group_content']);
                $group_rule         =trim($db_results[0]['group_rule']);
                foreach($db_results as $db_result){
                    $rs_request_state=(int)$db_result['request_state'];
                    if($rs_request_state===1)$create_success++;
                }
                $create_percent=ceil(($create_success/$create_cno)*100);
            }else{
                die('發生嚴重錯誤');
            }
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- forum,start -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <form id="Form1" name="Form1" method="post" onsubmit="return false;">
                    <h3 style='color:#ec6c01;font-weight:bold;'>小組連署情報...</h3>
                    <hr></hr>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-center">
                            <img class="" src="../img/default/group.jpg" width="160" height="160" border="0" alt="user_img"/>
                            <h3><?php echo htmlspecialchars($group_name);?></h3><br/>
                            <h4>連署條件：兩人以上同意連署</h4><br/>
                            <h4>連署進度</h4>
                            <div class="progress progress-striped active" style="position:relative;margin-bottom:5px;">
                                <div class="progress-bar progress-bar-success" role="progressbar"
                                    style="width: <?php echo $create_percent;?>%;">
                                    <span class=""><?php echo $create_percent;?>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                            <h4>版主：<?php echo htmlspecialchars($request_from_name);?></h4><hr></hr>
                            <?php
                                if(!empty($db_results)){
                                    foreach($db_results as $inx=>$db_result){
                                    $request_to       =(int)$db_result['request_to'];
                                    $request_to_name  =trim($db_result['request_to_name']);

                                    $rs_request_state =(int)$db_result['request_state'];
                                    $rs_request_state_html='';
                                    switch($rs_request_state){
                                        case 1:
                                            $rs_request_state_html='已連署';
                                        break;
                                        case 2:
                                            $rs_request_state_html='拒絕連署';
                                        break;
                                        case 3:
                                            $rs_request_state_html='考慮中';
                                        break;
                                        default:
                                            continue;
                                        break;
                                    }
                            ?>
                                <h4>
                                    連署人 <?php echo ($inx+1);?>：
                                    <?php echo htmlspecialchars($request_to_name);?>,
                                    <?php echo htmlspecialchars($rs_request_state_html);?>
                                </h4><hr></hr>
                            <?php }};?>
                            <?php if($group_user_type_2_user_id===$sess_user_id):?>
                                <div class="input-group" style="position:relative;margin-top:25px;">
                                    <input type="text" class="form-control friend_name" name="friend_name[]" placeholder="請選擇或輸入其他書友來聯署建立">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            選擇書友 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php
                                            if(!empty($friend_results)){
                                                foreach($friend_results as $inx=>$friend_result):
                                                    $rs_friend_state=(int)$friend_result['friend_state'];
                                                    $rs_user_id     =(int)$friend_result['user_id'];
                                                    $rs_friend_id   =(int)$friend_result['friend_id'];
                                                    if($rs_user_id!==$sess_user_id || $rs_friend_id!==$sess_user_id){
                                                        $tmp_user_id=0;
                                                        if($rs_user_id!==$sess_user_id)$tmp_user_id=(int)$rs_user_id;
                                                        if($rs_friend_id!==$sess_user_id)$tmp_user_id=(int)$rs_friend_id;
                                                        $sql="
                                                            SELECT
                                                                `name`,`sex`
                                                            FROM `user`.`member`
                                                            WHERE 1=1
                                                                AND `user`.`member`.`uid`={$tmp_user_id}
                                                        ";
                                                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                        $user_name ='';
                                                        if(!empty($db_results)){
                                                            $user_name=trim($db_results[0]['name']);
                                                        }else{continue;}
                                                    }
                                                if($rs_friend_state===1){
                                            ?>
                                            <li><a href="javascript:void(0);" onclick="auto(this,0);void(0);"><?php echo htmlspecialchars($user_name);?></a></li>
                                            <?php }endforeach;}?>
                                        </ul>
                                    </div>
                                </div>
                                <hr></hr>

                                <div class="form-group pull-right" style="position:relative;margin-bottom:25px;">
                                    <button type="button" id="BtnS" class="btn btn-default">送出</button>
                                </div>

                                <div class="form-group hidden">
                                    <input type="hidden" class="form-control" name="friend_id[]" value="0">
                                    <input type="hidden" class="form-control" name="group_id" value="<?php echo $group_id;?>">
                                    <input type="hidden" class="form-control" name="method" value="add_request_more_create_group">
                                    <input type="hidden" class="form-control" name="send_url" value="#">
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </form>
            </div>
            <!-- forum,end -->

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;
    var arry_friend_info={};
    var arry_friend_name=[];

    <?php
    if(!empty($friend_results)){
        foreach($friend_results as $inx=>$friend_result):
            $rs_friend_state=(int)$friend_result['friend_state'];
            $rs_user_id     =(int)$friend_result['user_id'];
            $rs_friend_id   =(int)$friend_result['friend_id'];
            if($rs_user_id!==$sess_user_id || $rs_friend_id!==$sess_user_id){
                $tmp_user_id=0;
                if($rs_user_id!==$sess_user_id)$tmp_user_id=(int)$rs_user_id;
                if($rs_friend_id!==$sess_user_id)$tmp_user_id=(int)$rs_friend_id;
                $sql="
                    SELECT
                        `name`,`sex`
                    FROM `user`.`member`
                    WHERE 1=1
                        AND `user`.`member`.`uid`={$tmp_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $user_name ='';
                if(!empty($db_results)){
                    $user_name=trim($db_results[0]['name']);
                }else{continue;}
            }
        if($rs_friend_state===1){
    ?>
        arry_friend_info['<?php echo $user_name;?>']=<?php echo $tmp_user_id;?>;
        arry_friend_name.push('<?php echo $user_name;?>');
    <?php }endforeach;}?>


    //FUNCTION
    $(".friend_name").autocomplete({
        source: arry_friend_name
    });

    function auto(obj,no){
    //選單貼上

        var friend_name   =trim($(obj).text());
        var no            =parseInt(no);
        var ofriend_id    =document.getElementsByName('friend_id[]')[no];
        var ofriend_name  =document.getElementsByName('friend_name[]')[no];
        ofriend_id.value  =parseInt(arry_friend_info[friend_name]);
        ofriend_name.value=trim(friend_name);
    }

    $('#BtnS').click(function(){
    //建立小組

        var oForm1          =document.getElementById('Form1');
        var ofriend_ids     =document.getElementsByName('friend_id[]');
        var ofriend_names   =document.getElementsByName('friend_name[]');
        var arry_err        =[];
        var tmp_friend_names=[];

        for(var i=0;i<ofriend_names.length;i++){
            var ofriend_name=ofriend_names[i];
            if((trim(ofriend_name.value)==='')){
                arry_err.push('請選擇書友來聯署建立');
            }else{
                if(!in_array(trim(ofriend_name.value),arry_friend_name)){
                    arry_err.push('請選擇或輸入正確的書友來聯署建立');
                }else{
                    if(!in_array(trim(ofriend_name.value),tmp_friend_names)){
                        tmp_friend_names.push(trim(ofriend_name.value));
                    }else{
                        arry_err.push('請選擇不一樣的書友來聯署建立');
                    }
                }
            }
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎 ?')){
                for(var i=0;i<ofriend_names.length;i++){
                    var ofriend_name=ofriend_names[i];
                    ofriend_ids[i].value=arry_friend_info[trim(ofriend_name.value)];
                }
                oForm1.action='../controller/add.php'
                oForm1.submit();
                return true;
            }else{
                return false;
            }
        }
    });

</script>
</html>
<?php
//-------------------------------------------------------
//view_create_group_info 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>


<?php function view_hot_booklist() {?>
<?php
//-------------------------------------------------------
//view_hot_booklist 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        //$year          =date("Y");
        //$month         =date("m");
        //$date_now      =(int)date('j');
        //$week_cno      =(int)(ceil($date_now/7)-1);
        //$arry_date_week=date_week_array($year,$month);
        //$week_sdate    =trim($arry_date_week[$week_cno]['sdate']);
        //$week_edate    =trim($arry_date_week[$week_cno]['edate']);
        $week_sdate    =trim(date('Y-m-d', time()-86400*date('w')+(date('w')>0?86400:-6*86400)));
        $week_edate    =trim(date("Y-m-d",strtotime($week_sdate)+(86400*6)));

        $sql="
            SELECT
                COUNT(`mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`) AS `cno`,
                `mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`
            FROM  `mssr_forum`.`mssr_forum_hot_booklist`
            WHERE 1=1
                AND `mssr_forum`.`mssr_forum_hot_booklist`.`keyin_cdate` BETWEEN '{$week_sdate} 00:00:00' AND '{$week_edate} 23:59:59'
            GROUP BY `mssr_forum`.`mssr_forum_hot_booklist`.`book_sid`
            ORDER BY `cno` DESC
            LIMIT 20;
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="../img/default/book.png"
                width="160" height="160" border="0" alt="user_img"/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">本週熱門書單</span>
                <!-- jumbotron_name,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="../img/default/book.png"
                width="100" height="100" border="0" alt="user_img"/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name">本週熱門書單</span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h3 style='color:#ec6c01;font-weight:bold;'>讓我們看看本週熱門書單...</h3>
                <hr></hr>
            </div>

            <?php
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result):
                    $rs_cno=(int)($db_result['cno']);
                    $rs_book_sid=trim($db_result['book_sid']);
                    if($rs_book_sid!==''){
                        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                        if(empty($arry_book_infos))continue;
                        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                        $all_booK_name = $rs_book_name;
                        if(mb_strlen($rs_book_name)>20){
                            $all_booK_name = $rs_book_name;
                            $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                        }
                        $rs_book_img    ='../img/default/book.png';
                        if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                            $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                        }
                    }else{continue;}
            ?>
                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2" style="margin:25px 0;">
                    <div class="thumbnail text-center" style="height:225px;">
                        <h4>No.<?php echo $inx+1;?></h4>
                        <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                            <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                            <div class="caption" style="text-decoration:none;" data-toggle="tooltip" data-placement="bottom" title="<?php echo $all_booK_name;?>">  <!-- 新增完整書名 -->
                                投票人數：<?php echo $rs_cno;?><br>
                                <?php echo htmlspecialchars($rs_book_name);?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach;}else{?>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" style="margin:25px 0;">
                    <h2>本週尚未有人投票！</h2>
                </div>
            <?php }?>

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //FUNCTION
    //完整書名
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });

</script>
</html>
<?php
//-------------------------------------------------------
//view_hot_booklist 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
?>


<?php function view_all_group() {?>
<?php
//-------------------------------------------------------
//view_all_group 區塊 -- 開始
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

        //local
        global $arrys_sess_login_info;
        global $title;

        global $conn_mssr;
        global $arry_conn_mssr;

        global $meta;
        global $navbar;
        global $carousel;
        global $footbar;

        global $_GET;
        global $_POST;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

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

    //---------------------------------------------------
    //身分判斷
    //---------------------------------------------------

        $arry_user_status=array();
        $sql="
            SELECT
                `user`.`permissions`.`status`
            FROM `user`.`member`
                INNER JOIN `user`.`permissions` ON
                `user`.`member`.`permission`=`user`.`permissions`.`permission`
            WHERE 1=1
                AND `user`.`member`.`uid` ={$sess_user_id}
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(!empty($db_results)){
            foreach($db_results as $db_result){
                $rs_status=trim($db_result['status']);
                $arry_user_status[]=$rs_status;
            }
        }

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------

        $sql="
            SELECT
                `mssr_forum`.`mssr_forum_group`.`group_id`,
                `mssr_forum`.`mssr_forum_group`.`group_name`,
                `mssr_forum`.`mssr_forum_group`.`group_state`,
                `mssr_forum`.`mssr_forum_group`.`keyin_cdate`
            FROM `mssr_forum`.`mssr_forum_group`
            WHERE 1=1
                AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
            ORDER BY `mssr_forum`.`mssr_forum_group`.`group_id` DESC
        ";
        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
        if(empty($db_results)){
            die('發生嚴重錯誤');
        }
?>
<!DOCTYPE html>
<html lang='zh-TW' prefix='og: http://ogp.me/ns#'>
<head>
    <title><?php echo $title;?></title>

    <!-- 標籤,start -->
    <?php echo $meta;?>
    <!-- 標籤,end -->

    <!-- icon -->
    <link rel="shortcut icon" href="">

    <!-- 通用 -->
    <link href="../../../lib/jquery/ui/code.css" rel="stylesheet" type="text/css">
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">
    <link href="../../../inc/code.css" rel="stylesheet" type="text/css">

    <!-- 專屬 -->
    <link href="../css/site.css" rel="stylesheet" type="text/css">

    <!--[if (gte IE 6)&(lte IE 8)]>
        <script>self.location.href='../pages/browser_update/index.php'</script>
    <![endif]-->

    <!--[if lt IE 9]>
        <script src="../../../lib/js/html5/code.js"></script>
        <script src="../../../lib/js/css/code.js"></script>
    <![endif]-->
</head>
<body>

    <!-- 導覽列,容器,start -->
    <?php echo $navbar;?>
    <!-- 導覽列,容器,end -->

    <!-- 頁面,容器,start -->
    <div class="container">

        <!-- 內容,start -->
        <div class="row">

            <!-- jumbotron,start -->
            <div class="jumbotron hidden-xs" style="background-image:url('../img/default/front_cover_group.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,大解析度,start -->
                <img class="jumbotron_img hidden-xs"
                src="../img/default/group.jpg"
                width="160" height="160" border="0" alt="group_img"/>
                <!-- 大頭貼,大解析度,end -->

                <!-- jumbotron_name,start -->
                <span class="jumbotron_name">所有小組名單</span>
                <!-- jumbotron_name,end -->

            </div>
            <!-- jumbotron,end -->

            <!-- jumbotron-xs,start -->
            <div class="jumbotron jumbotron-xs hidden-sm hidden-md hidden-lg" style="background-image:url('../img/default/front_cover_group.jpg');background-position:center top;background-size:100% auto;">

                <!-- 大頭貼,小解析度,start -->
                <img class="jumbotron-xs_img hidden-sm hidden-md hidden-lg"
                src="../img/default/group.jpg"
                width="100" height="100" border="0" alt="group_img"/>
                <!-- 大頭貼,小解析度,end -->

                <!-- jumbotron-xs_name,start -->
                <span class="jumbotron-xs_name">所有小組名單</span>
                <!-- jumbotron-xs_name,end -->

            </div>
            <!-- jumbotron-xs,end -->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h3 style='color:#ec6c01;font-weight:bold;'>讓我們看看所有小組名單...</h3>
                <hr></hr>
            </div>

            <?php
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result):
                    $group_id     =(int)$db_result['group_id'];
                    $group_name   =trim($db_result['group_name']);
                    $keyin_cdate  =trim($db_result['keyin_cdate']);

                    if(mb_strlen($group_name)>20){
                        $group_name=mb_substr($group_name,0,20)."..";
                    }

                    $keyin_cdate=date("Y-m-d",strtotime($keyin_cdate));

                    ////小組人數
                    //$group_user_cno=0;
                    //$sql="
                    //    SELECT COUNT(*) AS `group_user_cno`
                    //    FROM `mssr_forum`.`mssr_forum_group_user_rev`
                    //    WHERE 1=1
                    //        AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`={$group_id}
                    //        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                    //";
                    //$group_user_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    //if(!empty($group_user_rev_results))$group_user_cno=(int)$group_user_rev_results[0]['group_user_cno'];
                    //
                    ////小組發文
                    //$group_article_cno=0;
                    //$sql="
                    //    SELECT COUNT(*) AS `group_article_cno`
                    //    FROM `mssr_forum`.`mssr_forum_article`
                    //    WHERE 1=1
                    //        AND `mssr_forum`.`mssr_forum_article`.`group_id`={$group_id}
                    //        AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
                    //";
                    //$group_article_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                    //if(!empty($group_article_rev_results))$group_article_cno=(int)$group_article_rev_results[0]['group_article_cno'];
            ?>
                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3" style="margin:25px 0;">
                    <div class="thumbnail text-center" style="min-height:225px;overflow:hidden;">
                        <h4>No.<?php echo $inx+1;?></h4>
                        <a href="article.php?get_from=2&group_id=<?php echo $group_id;?>">
                            <img width="80" height="80" style="weight:80px;height:80px;" src="../img/default/group.jpg" alt="Generic placeholder thumbnail">
                            <div class="caption">
                                【<?php echo htmlspecialchars($group_name);?>】<br>
                                <!-- 小組人數：<?php echo htmlspecialchars($group_user_cno);?><br>
                                小組發文數：<?php echo htmlspecialchars($group_article_cno);?><br> -->
                                建立日期：<?php echo htmlspecialchars($keyin_cdate);?><br>
                            </div>
                        </a>
                        <div>
                            <button type="button" class="btn btn-default btn-xs" style="margin-bottom:5px;" onclick="window.open('article.php?get_from=2&group_id=<?php echo $group_id;?>');void(0);">前往</button>
                            <?php if(in_array("i_a",$arry_user_status)):?>
                                <button type="button" class="btn_del_group btn btn-default btn-xs" style="margin-bottom:5px;"
                                group_id="<?php echo $group_id;?>"
                                >關閉小組</button>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php endforeach;}?>

        </div>
        <!-- 內容,end -->

    </div>
    <!-- 頁面,容器,end -->

    <!-- 註腳列,容器,start -->
    <?php echo $footbar;?>
    <!-- 註腳列,容器,end -->

</body>

<!-- 通用 -->
<script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>
<script type="text/javascript" src="../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
<script type="text/javascript" src="../../../inc/code.js"></script>

<!-- 專屬 -->
<script type="text/javascript" src="../inc/code.js"></script>

<script type="text/javascript">
//-------------------------------------------------------
//SCRIPT BLOCK
//-------------------------------------------------------

    //變數
    var nl='\r\n';
    var send_url=document.URL;


    //FUNCTION
    $('.btn_del_group').click(function(){
    //關閉小組

        if(!confirm('你確定要關閉小組嗎?')){
            return false;
        }

        var group_id=parseInt(($(this).attr('group_id')));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/del.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                group_id    :encodeURI(trim(group_id    )),
                method      :encodeURI(trim('del_group' )),
                send_url    :encodeURI(trim(send_url    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                location.reload();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    });


</script>
</html>
<?php
//-------------------------------------------------------
//view_all_group 區塊 -- 結束
//-------------------------------------------------------
    $conn_user=NULL;
    $conn_mssr=NULL;
?>
<?php };?>
<?php
    @$conn_user=NULL;
    @$conn_mssr=NULL;
    @ftp_close($ftp_conn);
?>