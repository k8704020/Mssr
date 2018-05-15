<?php
//-------------------------------------------------------
//閱讀登記條碼版
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
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",3).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url=str_repeat("../",0).'login/loginF.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $$field_name=$field_value;
        }

        //清空用戶類型
        switch(is_array($_SESSION['config']['user_type'])){
            case true:
                foreach($_SESSION['config']['user_types'] as $inx=>$val){
                    if(!in_array(trim($val),array_map("trim",$_SESSION['config']['user_type']))){
                        unset($_SESSION[$val]);
                    }
                }
            break;

            default:
                foreach($_SESSION['config']['user_types'] as $inx=>$val){
                    if(trim($val)!=trim($_SESSION['config']['user_type'])){
                        unset($_SESSION[$val]);
                    }
                }
            break;
        }

        //清除學生登入紀錄
        unset($_SESSION['_read_the_registration_code']['_login']);

        //清除學生還書紀錄
        unset($_SESSION['_read_the_registration_code']['_return']);

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        $sess_class_code=addslashes(trim($_sess_t['class_code']));

        $query_sql="
            SELECT
                `mssr`.`mssr_auth_class`.`auth`
            FROM `mssr`.`mssr_auth_class`
            WHERE 1=1
                AND `mssr`.`mssr_auth_class`.`class_code`='{$sess_class_code}'
        ";
        $auth_results=db_result($conn_type='pdo',$conn_mssr='',$query_sql,array(0,1),$arry_conn_mssr);

        //權限資訊
        $registration_code_opinion='no';
        if(false===@unserialize($auth_results[0]['auth'])){
            $auth=array();
        }else{
            $auth=@unserialize($auth_results[0]['auth']);
        }
        if((!empty($auth))&&(isset($auth['registration_code_opinion']))){
            $registration_code_opinion=trim($auth['registration_code_opinion']);
        }
        $arry_registration_code_opinion=array(
            "yes"=>"開啟",
            "no" =>"關閉"
        );

    //---------------------------------------------------
    //i_v判斷
    //---------------------------------------------------

        $_sess_uid=(int)$_SESSION['t']['uid'];
        $is_i_v =false;
        $query_sql="
            SELECT `user`.`permissions`.`status`
            FROM `user`.`member`
                INNER JOIN `user`.`permissions` ON
                `user`.`member`.`permission` =`user`.`permissions`.`permission`
            WHERE 1=1
                AND `user`.`permissions`.`status`='i_v'
                AND `user`.`member`.`uid`={$_sess_uid}
        ";
        $i_v_results=db_result($conn_type='pdo',$conn_mssr='',$query_sql,array(0,1),$arry_conn_mssr);
        if(!empty($i_v_results)){
            $is_i_v=true;
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        //班級數目
        $arrys_class_code=$_SESSION['t']['arrys_class_code'];
        $arrys_class_code_cno=count($arrys_class_code);
        $school_code=$_SESSION['t']['school_code'];

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------


        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=2);
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
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <script type="text/javascript" src="../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="inc/code.css" media="all" />
    <script type="text/javascript" src="inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header">

        <!-- 標題上方 開始 -->
        <div id="header_top">
            <div id="header_menu_div">
                <!-- 標題選單列 開始 -->
                <span id="header_menu">
                    <!-- <a href="#" alt="回星球首頁" style="display:inline-block;">
                        <img width="110" height="19" src="css/structure/img/header/home_2.jpg" target="_self" alt="回星球首頁" border="0">
                    </a> --><a href="login/logout.php" alt="登出" style="display:inline-block;">
                        <img width="113" height="19" src="css/structure/img/header/logout.jpg" target="_self" alt="登出" border="0">
                    </a>
                </span>
                <!-- 標題選單列 結束 -->
            </div>
        </div>
        <!-- 標題上方 結束 -->

        <!-- 標題下方 開始 -->
        <div id="header_bottom">
            <div id="logo_div">
                <!-- logo 開始 -->
                <div id="logo"></div>
                <!-- logo 結束 -->
            </div>
        </div>
        <!-- 標題下方 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <table id="read_the_registration_code_center_div" align="center" cellpadding="0" cellspacing="0" border="0"/>
            <tr>
                <td width="150px" align="left">
                    <!-- 大頭貼 開始 -->
                    <img width="111" height="110" src="img/user_admin.jpg" border="0">
                    <!-- 大頭貼 結束 -->
                </td>
                <td>
                    <table cellpadding="10" cellspacing="0" border="0" width="95%"
                    style="border-top:1px dashed #868686;border-bottom:1px dashed #868686"/>
                        <tr>
                            <td width="60px" valign="middle">
                                <div id="sys_list"></div>
                            </td>
                            <td valign="middle">
                                <!-- 系統功能 開始 -->
                                <span class="fc_gray0" style="margin:0 15px;">
                                    <a href="#">借還書系統</a>
                                </span>
                                <!-- 系統功能 結束 -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="150px" align="left">
                    <!-- 個人資訊 開始 -->
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">姓名：<?php echo htmlspecialchars($name);?>   </div>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">身分：老師                                    </div>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">帳號：<?php echo htmlspecialchars($account);?></div>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">
                        使用班級：<?php echo $grade=(isset($grade))?(int)$grade.'年':'';?><?php echo $classroom=(isset($classroom))?(int)$classroom.'班':'';?>
                    </div>
                    <!-- 個人資訊 結束 -->

                    <!-- 個人功能 開始 -->
                    <?php if($arrys_class_code_cno>1):?>
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">
                        <input id="BtnS" type="button" value="班級列表 " class="ibtn_gr9030" tabindex="1" onclick="location.href='user_info/sel_class_code/sel_class_codeF.php';" onmouseover="this.style.cursor='pointer'">
                    </div>
                    <?php endif;?>
                    <?php if($arrys_class_code_cno>1):?>
                        <div style="position:relative;margin-top:1px;"></div>
                    <?php else:?>
                        <div style="position:relative;margin-top:61px;"></div>
                    <?php endif;?>
                    <!-- 個人功能 結束 -->
                </td>
                <!-- 內容 開始 -->
                <?php if(isset($class_code)):?>
                    <td align="left" valign="top">
                        <?php if(trim($registration_code_opinion)==='no' && $is_i_v===false):?>
                            <a href="borrow_return_book/m_borrow_book/login/loginF.php">
                                <img width="200" height="200" src="img/borrow_book_b.png" border="0" style="position:relative;top:40px;left:20px;">
                            </a>
                            <a href="borrow_return_book/m_return_book/index.php">
                                <img width="200" height="200" src="img/return_book_b.png" border="0" style="position:relative;top:40px;left:50px">
                            </a>
                            <?php if($school_code==='idc'){?>
                                <a href="borrow_return_book/m_answer_qa/login/loginF.php">
                                    <img width="200" height="200" src="img/q&a_b.png" border="0" style="position:relative;top:40px;left:80px">
                                </a>
                            <?php }?>
                        <?php endif;?>
                        <?php if($is_i_v===true):?>
                            <a href="borrow_return_book/m_opinion_book_exp/login/loginF.php">
                                <img width="200" height="200" src="img/opinion_book_b.jpg" border="0" style="position:relative;top:40px;left:80px;">
                            </a>
                           <?php if($school_code==='idc'){?>
                                <a href="borrow_return_book/m_answer_qa/login/loginF.php">
                                    <img width="200" height="200" src="img/q&a_b.png" border="0" style="position:relative;top:40px;left:100px">
                                </a>
                            <?php }?>
                        <?php endif;?>
                        <?php if(trim($registration_code_opinion)==='yes' && $is_i_v===false):?>
                            <a href="borrow_return_book/m_opinion_book/login/loginF.php">
                                <img width="200" height="200" src="img/opinion_book_b.jpg" border="0" style="position:relative;top:40px;left:80px;">
                            </a>
                            <?php if($school_code==='idc'){?>
                                <a href="borrow_return_book/m_answer_qa/login/loginF.php">
                                    <img width="200" height="200" src="img/q&a_b.png" border="0" style="position:relative;top:40px;left:100px">
                                </a>
                            <?php }?>
                        <?php endif;?>
                    </td>
                <?php else:?>
                    <?php
                        $jscript_back="
                            <script>
                                location.href='user_info/sel_class_code/sel_class_codeF.php';
                            </script>
                        ";
                        die($jscript_back);
                    ?>
                    <!-- <td align="center" valign="middle">
                        <img width="13" height="13" src="../../img/icon/red.jpg" border="0">
                        <span class="fc_blue0">
                            請選擇左方班級列表
                        </span>
                    </td> -->
                <?php endif;?>
                <!-- 內容 結束 -->
            </tr>
        </table>

    </div>
    <!-- 內容區塊 結束 -->

    <!-- 註腳區塊 開始 -->
    <div id="footer">
        <div id="footline"></div>
        <span id="footbar">
            <!-- 註腳列 -->
            <?php foreach($footer as $footer_item) :?>
            <?php
            //-------------------------------------------
            //選項
            //-------------------------------------------
            //key       代碼
            //cname     文字
            //url       路徑
            //target    框架    _blank | _self | 視窗id
            //state     狀態    on:顯示 | off:隱藏
            //-------------------------------------------

                $key   =trim($footer_item['key']);
                $cname =trim($footer_item['cname']);
                $url   =trim($footer_item['url']);
                $target=trim($footer_item['target']);
                $state =trim($footer_item['state']);
            ?>
                <a href="<?php echo $url;?>" target="<?php echo $target;?>"><?php echo $cname;?></a>&nbsp;
            <?php endforeach ;?>
        </span>
    </div>
    <!-- 註腳區塊 結束 -->

    <!-- google分析 開始 -->
    <?php echo google_analysis($allow=true);?>
    <!-- google分析 結束 -->

    <!-- 隱藏區塊 開始 -->
    <span id="hidden_area" style="display:none;">
        <input id="BtnR" type="button" value="馬上歸還" style="color:#ff0000;position:relative;margin:0 5px;" class="ibtn_gr6030" onmouseover="this.style.cursor='pointer'">
        <input id="BtnW" type="button" value="下次再說" style="color:#ff0000;position:relative;margin:0 5px;" class="ibtn_gr6030" onmouseover="this.style.cursor='pointer'">
    </span>
    <!-- 隱藏區塊 結束 -->
</div>
<!-- 容器區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){

        if(localStorage){
            if(localStorage['arrys_return_false']!==undefined){

                //參數
                var storage_cno=localStorage['arrys_return_false'].split(",").length/2;

                //ajax設定
                var $_url       ="borrow_return_book/m_return_book/multi_add/addA.php";
                var $_type      ="POST";
                var $_datatype  ="json";

                //物件
                $('#hidden_area').prepend('<h1>上次尚有'+storage_cno+'本書未成功歸還 !</h1>');
                var oBtnR=document.getElementById('BtnR');
                var oBtnW=document.getElementById('BtnW');

                //最大還書數量
                if(storage_cno>100){
                    $(oBtnW).hide();
                }

                $.blockUI({
                    message:$('#hidden_area'),
                    centerX: true,
                    centerY: true,
                    css:{
                        width:'35%',
                        height:'85px'
                    },
                    overlayCSS:{
                        backgroundColor:'#000',
                        opacity:0.6,
                        cursor:'default'
                    }
                });

                oBtnR.onclick=function(){
                //重新歸還書籍
                    if(localStorage){
                        if(localStorage['arrys_return_false']!==undefined){
                        //ajax設置
                            $.ajax({
                            //參數設置
                                async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
                                cache      :false,     //快取的回應。
                                global     :true,      //是否使用全局 AJAX 事件。
                                timeout    :10000,
                                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                                url        :$_url,     //請求的頁面
                                type       :$_type,    //GET or POST
                                datatype   :$_datatype,
                                data       :{
                                    arrys_return_false:encodeURI(trim(localStorage['arrys_return_false']))
                                },

                            //事件
                                beforeSend  :function(){
                                //傳送前處理
                                    $.blockUI({
                                        message:'<h2>還書中...</h2>',
                                        overlayCSS:{
                                            backgroundColor:'#000',
                                            opacity:0.9,
                                            cursor:'default'
                                        },
                                        timeout: 10000
                                    });
                                },
                                success     :function(respones){
                                //成功處理
                                    var state=trim(respones);
                                    if(state==='ok'){

                                        //移除書籍清單
                                        localStorage.removeItem('arrys_return_false');

                                        $.blockUI({
                                            message:'<h2>書籍已歸還成功 !</h2>',
                                            overlayCSS:{
                                                backgroundColor:'#000',
                                                opacity:0.9,
                                                cursor:'default'
                                            },
                                            timeout: 2000
                                        });
                                        setTimeout(
                                            function _location_href(){
                                                location.href='index.php';
                                            },
                                            2000
                                        );
                                    }
                                },
                                error       :function(xhr, ajaxoptions, thrownerror){
                                //失敗處理
                                    if(ajaxoptions==='timeout'){
                                        $.blockUI({
                                            message:'<h2 class="fc_red1">連線過久，請檢查網路是否斷線 !</h2>',
                                            overlayCSS:{
                                                backgroundColor:'#000',
                                                opacity:0.9,
                                                cursor:'default'
                                            },
                                            timeout: 2000
                                        });
                                        setTimeout(
                                            function _location_reload(){
                                                $.blockUI({
                                                    message:$('#hidden_area'),
                                                    centerX: true,
                                                    centerY: true,
                                                    css:{
                                                        width:'35%',
                                                        height:'85px'
                                                    },
                                                    overlayCSS:{
                                                        backgroundColor:'#000',
                                                        opacity:0.6,
                                                        cursor:'default'
                                                    }
                                                });
                                            },
                                            2000
                                        );
                                    }else{
                                        $.blockUI({
                                            message:'<h2 class="fc_red1">連線失敗，請檢查網路是否斷線 !</h2>',
                                            overlayCSS:{
                                                backgroundColor:'#000',
                                                opacity:0.9,
                                                cursor:'default'
                                            },
                                            timeout: 2000
                                        });
                                        setTimeout(
                                            function _location_reload(){
                                                $.blockUI({
                                                    message:$('#hidden_area'),
                                                    centerX: true,
                                                    centerY: true,
                                                    css:{
                                                        width:'35%',
                                                        height:'85px'
                                                    },
                                                    overlayCSS:{
                                                        backgroundColor:'#000',
                                                        opacity:0.6,
                                                        cursor:'default'
                                                    }
                                                });
                                            },
                                            2000
                                        );
                                    }
                                },
                                complete    :function(){
                                //傳送後處理
                                }
                            });
                        }
                    }else{
                        alert('您的瀏覽器目前不支援此功能，請改用google chrome瀏覽器 !');
                    }
                }

                oBtnW.onclick=function(){
                    $.unblockUI();
                }
            }
        }

        //首頁logo
        logo(rd=0,'index.php',{});
    }

</script>

<script type="text/javascript" src="../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=2);
</script>
</Body>
</Html>