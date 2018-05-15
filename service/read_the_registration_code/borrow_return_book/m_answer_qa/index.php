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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/read_the_registration_code/inc/code',

                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['service']['read_the_registration_code']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }



    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        if(!login_check(array('t'))){
            $url='login/loginF.php';
            header("Location: {$url}");

            die();
        }





    //---------------------------------------------------
    //連線
    //---------------------------------------------------

    $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        //初始化，承接變數
        $_sess_t=$_SESSION['t'];
        foreach($_sess_t as $field_name=>$field_value){
            $field_name=$field_value;
        }

        //清除學生還書紀錄
        unset($_SESSION['_read_the_registration_code']['_return']);

        $school_code=$_SESSION['t']['school_code'];




    // ---------------------------------------------------
    // 權限,與判斷
    // ----------------

        // $sess_user_id                       =(int)$sess_user_id;
        // $auth['registration_code_opinion']  =addslashes(trim($registration_code_opinion));
        // $class_code                         =addslashes(trim($class_code));
        // $auth                               =unserialize($auth);

       // $sql="
       //              SELECT * 
       //              FROM  `mssr_auth_user` 
       //              WHERE  create_by='{$_SESSION['t']['uid']}'

       //      ";


       //  $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

       //  $auth                               =unserialize($result);


       //  print_r($auth);


    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        // print_r($_SESSION);

        //網頁標題
        $title="明日星球,閱讀登記條碼版";

        //註腳列
        $footer=footer($rd=4);
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />

    <script type="text/javascript" src="../../../../lib/js/effect/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />
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
                        <img width="110" height="19" src="../../css/structure/img/header/home_2.jpg" target="_self" alt="回星球首頁" border="0">
                    </a> --><a href="../../login/logout.php" alt="登出" style="display:inline-block;">
                        <img width="113" height="19" src="../../css/structure/img/header/logout.jpg" target="_self" alt="登出" border="0">
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
    <div id="content" style="margin-top:5px;">

        <table id="read_the_registration_code_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="780px">
                    <!-- 閱讀登記條碼版中心路徑選單 開始 -->
                    <div id="read_the_registration_code_center_path">
                        <table id="read_the_registration_code_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../index.php">系統中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="../../index.php">回答問題系統</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);">回答問題</a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 閱讀登記條碼版中心路徑選單 結束 -->
                </td>
              <!--   <td align="right" valign="middle">
                    <input id="BtnG" type="button" value="我要還書" class="ibtn_gr9030" style="display:none;">
                </td> -->
            </tr>
            <tr>
                <td>
                    <!-- 資料列表 開始 -->
                    <div id="iframe_area">
                        <iframe id="IFC" name="IFC" src="page_opinion_menu/index.php" frameborder="0"
                        style="width:100%;height:550px;overflow:hidden;overflow-y:auto"></iframe>
                    </div>
                    <!-- 資料列表 結束 -->
                </td>
                <td align="right" valign="top">
                    <input id="BtnC" type="button" value="換下一位學生" class="ibtn_gr9030" style="position:relative;right:5px;">
                    <a href="../../borrow_return_book/m_borrow_book/index.php">
                        <img id="img_borrow_book" width="100" height="100" src="../../img/borrow_book.jpg" border="0"
                        style="margin-top:30px;">
                    </a>
                    <a href="../../borrow_return_book/m_return_book/index.php">
                        <img id="img_return_book" width="100" height="100" src="../../img/return_book.jpg" border="0"
                        style="margin-top:30px;">
                    </a>
                    <?php if($school_code==='idc'){?>
                    <a href="#">
                        <img id="img_qa" width="100" height="100" src="../../img/q&a.png" border="0"
                        style="margin-top:30px;">
                    </a>
                    <?php }?>
                </td>
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

</div>
<!-- 容器區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    // var oBtnG=document.getElementById('BtnG');
    var oBtnC=document.getElementById('BtnC');
    var oimg_borrow_book=document.getElementById('img_borrow_book');
    var oimg_return_book=document.getElementById('img_return_book');
    var oimg_qa=document.getElementById('img_qa');

    var about_book_sid;    
    var about_book_name;
    var about_book_author;
    var about_book_page_count;
    var about_book_publisher;
    var book_borrow_sid;
    var about_book_src;

    


    window.onload=function(){

        //首頁logo
        logo(rd=2,'index.php',{});

        //透明設定
        set_opacity(oimg_return_book,60);
        set_opacity(oimg_borrow_book,60);


    }

    oimg_return_book.onmouseover= function(){
    //動作
        //透明設定
        set_opacity(oimg_return_book,100);

    }

    oimg_return_book.onmouseout= function(){
    //動作
        //透明設定
        set_opacity(oimg_return_book,60)
    }

    oimg_borrow_book.onmouseover= function(){
    //動作
        //透明設定
        set_opacity(oimg_borrow_book,100);
        
    }

    oimg_borrow_book.onmouseout= function(){
    //動作
        //透明設定
        set_opacity(oimg_borrow_book,60)
    }

    // oBtnG.onclick=function(){
    // //我要還書
    //     parent.location.href='../m_return_book/index.php';
    // }

    oBtnC.onclick=function(){
    //換下一位學生
        window.location.href='login/loginF.php';
    }

    // oBtnG.onmouseover= function(){
    // //動作
    //     this.style.cursor='pointer';
    // }
    // oBtnG.onmouseout= function(){
    //     this.style.cursor='none';
    // }

    oBtnC.onmouseover= function(){
    //動作
        this.style.cursor='pointer';
    }
    oBtnC.onmouseout= function(){
        this.style.cursor='none';
    }


    // 開啟子功能頁面
    function change_page(value)
    {
        console.log("book:",book_borrow_sid);
        console.log("about_book_sid:",about_book_sid);
        console.log("about_book_name:",about_book_name);
        console.log("about_book_author:",about_book_author);
        console.log("about_book_page_count:",about_book_page_count);
        console.log("about_book_publisher:",about_book_publisher);
        console.log("about_book_src:",about_book_src);



        if(value !="")
        {

            window.document.getElementById("iframe_area").innerHTML = '<iframe id="" src="'+ value+'/index.php" width="900" height="500" scrolling="no" frameborder="0" style=" top:0px; left:0px; overflow:hidden;"></iframe>';
        }
        else
        {
            // cover("讀取中");
            window.document.getElementById("other_ifame").style.display = "block";
            window.document.getElementById("goout").style.display = "block";
            get_shelf();
            coin_coin();
            window.document.getElementById("iframe_area").innerHTML = '';
        }
    }

</script>

<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    // user_page_log(rd=4);
</script>
</Body>
</Html>