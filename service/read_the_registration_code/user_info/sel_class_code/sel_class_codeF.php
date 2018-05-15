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
            $url=str_repeat("../",2).'login/loginF.php';
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

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        //班級數目
        $arrys_class_code=$_SESSION['t']['arrys_class_code'];
        $arrys_class_code_cno=count($arrys_class_code);

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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>

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
    <div id="content">

        <table id="read_the_registration_code_center_div" align="center" cellpadding="0" cellspacing="0" border="0"/>
            <tr>
                <td width="150px" align="left">
                    <!-- 大頭貼 開始 -->
                    <img width="111" height="110" src="../../img/user_admin.jpg" border="0">
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
                    <div class="fc_gray0 fsize_08" style="padding:15px 0;padding-left:15px;">
                        <input id="BtnS" type="button" value="回系統中心 " class="ibtn_gr9030" tabindex="1" onclick="location.href='../../index.php';" onmouseover="this.style.cursor='pointer'">
                    </div>
                    <div style="position:relative;margin-top:1px;"></div>
                    <!-- 個人功能 結束 -->
                </td>
                <td align="left" valign="top">
                    <!-- 內容 開始 -->
                    <?php foreach($arrys_class_code as $inx=>$arry_class_code):?>
                    <?php
                        $class_code     =trim($arry_class_code['class_code']);
                        $class_category =(int)$arry_class_code['class_category'];
                        $grade          =(int)$arry_class_code['grade'];
                        $classroom      =(int)$arry_class_code['classroom'];
                        $semester_code  =trim($arry_class_code['semester_code']);
                    ?>
                        <a href="javascript:sel_class_code('<?php echo addslashes($class_code);?>',<?php echo (int)$class_category;?>,<?php echo (int)$grade;?>,<?php echo (int)$classroom;?>,'<?php echo addslashes($semester_code);?>');">
                            <table width="100px" cellpadding="0" cellspacing="0" border="1" align="center" onclick="sel_class_code('<?php echo addslashes($class_code);?>',<?php echo (int)$class_category;?>,<?php echo (int)$grade;?>,<?php echo (int)$classroom;?>,'<?php echo addslashes($semester_code);?>');"
                            style="position:relative;float:left;top:40px;margin:0 20px;background-color:#c6ecff;border-color:#c6ecff;"/>
                                <tr align="center">
                                    <td height="100px">
                                        <span class='fc_gray0'>
                                            <?php echo htmlspecialchars($grade);?>年<?php echo htmlspecialchars($classroom);?>班
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </a>
                    <?php endforeach;?>
                    <!-- 內容 結束 -->
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

    window.onload=function(){

        //首頁logo
        logo(rd=2,'index.php',{});
    }

    function sel_class_code(class_code,class_category,grade,classroom,semester_code){
        var url ='';
        var page=str_repeat('../',0)+'sel_class_codeA.php';
        var arg ={
            'class_code':class_code,
            'class_category':class_category,
            'grade':grade,
            'classroom':classroom,
            'semester_code':semester_code
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

</script>

</Body>
</Html>