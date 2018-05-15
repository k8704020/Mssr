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
                    APP_ROOT.'lib/php/string/code'
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

        //-----------------------------------------------
        //身份設定
        //-----------------------------------------------

            //初始化, 身份參考陣列, 1(校長使用者) | 2(主任使用者) | 3(老師使用者)
            $arrys_identity=array(1,2,3);

            //初始化, 身份數量
            $identity_cno=0;

            //初始化, 選擇身份指標
            $choose_identity_flag=true;

            //身份數量統計
            foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                $responsibilities=(int)$responsibilities;
                if(in_array($responsibilities,$arrys_identity)){
                    $identity_cno++;
                }
            }

            //只有1種身份, 自動回填至 $_SESSION['tc']['t|dt']
            if($identity_cno===0){
                $choose_identity_flag=false;
                $_SESSION['tc']['t|dt']=$arry_login_info;
            }else if($identity_cno===1){
                $choose_identity_flag=false;
                foreach($arrys_login_info as $responsibilities=>$arry_login_info){
                    $_SESSION['tc']['t|dt']=$arry_login_info;
                }
                //移除多餘的班級
                foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                    if($field_name==='arrys_class_code'){
                        foreach($field_value as $inx=>$val){
                            if($inx!==0){
                                unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                            }
                        }
                    }
                }
                //沒有任何班級，指派空陣列
                if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                    $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                }
            }else{
            //如果已選定身份的話
                if((isset($_SESSION['tc']['t|dt']))&&(!empty($_SESSION['tc']['t|dt']))){
                    $choose_identity_flag=false;
                    //移除多餘的班級
                    foreach($_SESSION['tc']['t|dt'] as $field_name=>$field_value){
                        if($field_name==='arrys_class_code'){
                            foreach($field_value as $inx=>$val){
                                if($inx!==0){
                                    unset($_SESSION['tc']['t|dt']['arrys_class_code'][$inx]);
                                }
                            }
                        }
                    }
                    //沒有任何班級，指派空陣列
                    if(!isset($_SESSION['tc']['t|dt']['arrys_class_code'])){
                        $_SESSION['tc']['t|dt']['arrys_class_code']=array();
                    }
                }
            }

        //-----------------------------------------------
        //basic
        //-----------------------------------------------

            $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

        //-----------------------------------------------
        //其餘設定
        //-----------------------------------------------

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

            //區域資訊
            if(!in_array('teacher_center',$_SESSION['config']['user_area'])){
                array_push($_SESSION['config']['user_area'],"teacher_center");
            }

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_forum_info');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

     //---------------------------------------------------
    //設定參數
    //---------------------------------------------------


    //GET
        

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------


    $class_code = $_GET['class_code'];

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);


        //-----------------------------------------------
        //撈取, 使用者名稱
        //-----------------------------------------------

            

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //註腳列
        $footer=footer($rd=4);

        //系統權限陣列(報表專用)
        $auth_sys_arry_report=auth_sys_arry_report();

        //系統權限陣列(功能專用)
        $auth_sys_arry_config=auth_sys_arry_config();

        //系統權限名稱陣列
        $auth_sys_name_arry=auth_sys_name_arry();

        //系統權限圖片陣列
        $auth_sys_img_arry=auth_sys_img_arry();

        //系統權限連結陣列
        $auth_sys_url_arry=auth_sys_url_arry();

        //系統權限連結框架陣列
        $auth_sys_target_arry=auth_sys_target_arry();

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-2];
            $mod_ename=$FOLDER[count($FOLDER)-1];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

            //清除模組查詢資料
            foreach(auth_sys_arry_report() as $sys_name=>$mods_arry){
                foreach($mods_arry as $mod_name=>$mod_arry){
                    if($mod_name!==$mod_ename){
                        unset($_SESSION[$mod_name]);
                    }
                }
            }

            //進入許可檢核
            // $access=$auth_sys_arry_report[$sys_ename][$mod_ename]['access'];
            // if(!$access){
            //     $url=str_repeat("../",6).'index.php';
            //     header("Location: {$url}");
            //     die();
            // }

            foreach($auth_sys_name_arry as $key=>$val){
                if($key==$sys_ename){
                    $sys_cname=$val;
                }elseif($key==$mod_ename){
                    $mod_cname=$val;
                }
            }

            if((trim($sys_cname)=='')||(trim($mod_cname)=='')){
                $err ='teacher_center_path err!';

                if(1==2){//除錯用
                    echo "<pre>";
                    print_r($err);
                    echo "</pre>";
                    die();
                }
            }

            //連結路徑
            $sys_url ="";
            $sys_page=str_repeat("../",3)."index.php";
            $sys_arg =array(
                'sys_ename'  =>addslashes($sys_ename)
            );
            $sys_arg=http_build_query($sys_arg);
            $sys_url=$sys_page."?".$sys_arg;
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=false);?>
    <?php echo robots($allow=false);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../../lib/jquery/ui/code.css" media="all" />
    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/plugin/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/jquery/ui/code.js"></script>

    <script type="text/javascript" src="../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../../lib/js/array/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../css/def.css" media="all" />
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 標題區塊 開始 -->
    <div id="header">

        <!-- 標題左方區塊 開始 -->
        <ul id="header_left">
            <li>
                <a href="#">
                    <span>
                        您好 !
                        <?php
                            if(isset($sess_login_info['name'])){
                                echo htmlspecialchars($sess_login_info['name']);
                            }
                        ?>
                        歡迎使用-學習歷程專區。
                    </span>
                </a>
            </li>
        </ul>
        <!-- 標題左方區塊 結束 -->

        <!-- 標題右方區塊 開始 -->
        <ul id="header_right" style="display:none;">
        <?php foreach($header_right_sys_arry as $sys=>$mod_arry):?>
        <?php
            $sys_en=trim($sys);
            $sys_ch=trim($header_right_name_arry[$sys_en]);
            $sys_url=trim($header_right_url_arry[$sys_en]);
            $sys_target=trim($header_right_target_arry[$sys_en]);
        ?>
            <li>
                <a href="#" target="<?php echo $sys_target;?>" rel="nofollow"><span onclick="parent.location.href='<?php echo $sys_url;?>';"><?php echo htmlspecialchars($sys_ch);?></span></a>
                <?php if(!empty($mod_arry)):?>
                <ul class="child">
                <?php foreach($mod_arry as $inx=>$mod):?>
                <?php
                    $mod_en=trim($mod);
                    $mod_ch=trim($header_right_name_arry[$mod_en]);
                    $mod_url=trim($header_right_url_arry[$mod_en]);
                    $mod_target=trim($header_right_target_arry[$mod_en]);
                ?>
                    <li><a href="#" target="<?php echo $mod_target;?>" rel="nofollow"><span onclick="parent.location.href='<?php echo $mod_url;?>';"><?php echo htmlspecialchars($mod_ch);?></span></a></li>
                <?php endforeach;?>
                </ul>
                <?php endif;?>
            </li>
        <?php endforeach;?>
        </ul>
        <!-- 標題右方區塊 結束 -->

    </div>
    <!-- 標題區塊 結束 -->

    <!-- 內容區塊 開始 -->
    <div id="content">

        <!-- 內容區塊(上半部) 開始 -->
        <div id="content_top">

            <!-- logo 開始 -->
            <div id="logo">
                明日書店
                <span id="logo_min">
                    <span class="fc_blue0"></span>
                    學生聊書票選
                </span>
            </div>
            <!-- logo 結束 -->

        </div>
        <!-- 內容區塊(上半部) 結束 -->

        <!-- 內容區塊(下半部) 開始 -->
        <div id="content_bottom">

            <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="left" valign="middle" width="450px">
                        <!-- 導覽列 開始 -->
                        <div id="navbar" style="display:none;">
                            <ul id="navbar_ul">
                            <?php foreach($auth_sys_arry_report as $sys=>$mod_arry):?>
                            <?php
                                $sys_en=trim($sys);
                                $sys_ch=trim($auth_sys_name_arry[$sys_en]);
                                $sys_url=trim($auth_sys_url_arry[$sys_en]);
                                $sys_target=trim($auth_sys_target_arry[$sys_en]);
                            ?>
                                <li>
                                    <a href="<?php echo $sys_url;?>" target="<?php echo $sys_target;?>" rel="nofollow"><?php echo htmlspecialchars($sys_ch);?></span></a>
                                    <?php if(!empty($mod_arry)):?>
                                    <ul class="child">
                                    <?php foreach($mod_arry as $mod=>$auth_arry):?>
                                    <?php
                                        $mod_en=trim($mod);
                                        $mod_ch=trim($auth_sys_name_arry[$mod_en]);
                                        $mod_url=trim($auth_sys_url_arry[$mod_en]);
                                        $mod_target=trim($auth_sys_target_arry[$mod_en]);
                                    ?>
                                        <li><a href="<?php echo $mod_url;?>" target="<?php echo $mod_target;?>" rel="nofollow"><?php echo htmlspecialchars($mod_ch);?></a></li>
                                    <?php endforeach;?>
                                    </ul>
                                    <?php endif;?>
                                </li>
                            <?php endforeach;?>
                            </ul>
                        </div>
                        <!-- 導覽列 結束 -->
                    </td>
                    <td align="right" valign="middle">
                        <!-- 查詢表單列 開始 -->

                        <!-- 查詢表單列 結束 -->
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <!-- 資料列表 開始 -->
                        <iframe id="IFC" name="IFC" src="content.php?class_code=<?php echo $class_code?>" frameborder="0"
                        style="width:100%;height:1200px;overflow:hidden;overflow-y:auto"></iframe>
                        <!-- 資料列表 結束 -->
                    </td>
                </tr>
            </table>

        </div>
        <!-- 內容區塊(下半部) 結束 -->

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
    <?php echo google_analysis($allow=false);?>
    <!-- google分析 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var choose_identity_flag=<?php echo ($choose_identity_flag)?1:0;?>;
    var child_classroom=$('.child_classroom');
    var oclassroom_title=$('#classroom_title');
    var json_school_code_rev=<?php echo $json_school_code_rev;?>;

    $('#school_name').keypress(function(e){
        if(e.which==13){
            sel_school2();
        }
    });

    function sel_school2(){
    //選擇學校

        var oschool_name=document.getElementById('school_name');
        var school_name =trim(oschool_name.value);
        var school_code ='';
        var country_code='';

        if(school_name===''){
            alert('請輸入學校!');
            return false;
        }else{
            for(key1 in json_school_code_rev){
                for(key2 in json_school_code_rev[key1]){
                    for(key3 in json_school_code_rev[key1][key2]){
                        for(key4 in json_school_code_rev[key1][key2][key3]){
                            var j_school_name=trim(json_school_code_rev[key1][key2][key3][key4]['school_name']);
                            if(school_name===j_school_name){
                                school_code=trim(json_school_code_rev[key1][key2][key3][key4]['school_code']);
                                country_code=trim(json_school_code_rev[key1][key2][key3][key4]['country_code']);
                            }
                        }
                    }
                }
            }
            if((school_code==='')||(country_code==='')){
                alert('無此學校!');
                return false;
            }
        }
        if(school_code!==""){

            var url ='';
            var page=str_repeat('../',0)+'query.php';
            var arg ={
                'school_code':school_code,
                'type':'self'
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

            //呼叫遮罩
            block_ui();

            go(url,'self');

        }else{
            return false;
        }
    }

    function block_ui(){
        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
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

    <?php if(in_array($auth_sys_check_lv,array(1,3,14,16,22,99))):?>

        //初始化, 全校年級班級json
        var jsons_class_code_rev=<?php echo $json_class_code_rev;?>;

        //初始化, 帶領的班級陣列
        var arry_sess_class_code=[];

        //回填, 帶領的班級陣列
        <?php foreach($arrys_login_info as $arry_login_info):?>
        <?php
            if((isset($arry_login_info['arrys_class_code']))&&(!empty($arry_login_info['arrys_class_code']))):
                $sess_arrys_class_code=$arry_login_info['arrys_class_code'];
        ?>
                <?php foreach($sess_arrys_class_code as $inx=>$sess_arry_class_code):?>
                <?php
                    $sess_class_code=trim($sess_arry_class_code['class_code']);
                ?>
                    var sess_class_code='<?php echo $sess_class_code;?>';
                    arry_sess_class_code.push(sess_class_code);
                <?php endforeach;?>
            <?php endif;?>
        <?php endforeach;?>

    <?php endif;?>

    function sel_school(school_code){
    //學校下拉設置
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'school_code':school_code,
            'type':'self'
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'self');
    }

    function sel_grade(grade){
    //年級班級下拉設置
        var grade=grade;

        //附加年級提示
        oclassroom_title.text(grade+'年級');

        //清除班級選項
        child_classroom.find("li").remove();

        //附加班級選項
        for(key1 in jsons_class_code_rev){
            if(key1==='classroom'){
                var json_classroom_rev=jsons_class_code_rev[key1];
            }
            if(key1==='class_code'){
                var json_class_code_rev=jsons_class_code_rev[key1];;
            }
        }
        for(key2 in json_class_code_rev[grade]){
            class_code=trim(json_class_code_rev[grade][key2]);
            classroom=trim(json_classroom_rev[grade][key2]);

            if(in_array(class_code,arry_sess_class_code)){
            //帶領中的班級, 顏色區分
                child_classroom.append(
                    '<li><a href="javascript:void(0);" target="" rel="nofollow"><span id="'+class_code+'" grade="'+grade+'" classroom="'+classroom+'" style="color:#ffff00;">'+grade+'年'+classroom+'班(帶班)</span></a></li>'
                );
            }else{
                child_classroom.append(
                    '<li><a href="javascript:void(0);" target="" rel="nofollow"><span id="'+class_code+'" grade="'+grade+'" classroom="'+classroom+'">'+grade+'年'+classroom+'班</span></a></li>'
                );
            }

            document.getElementById(class_code).onclick=function(){

                var class_code=this.id;
                var grade=this.getAttribute('grade');
                var classroom=this.getAttribute('classroom');

                //附加年級提示
                if(in_array(class_code,arry_sess_class_code)){
                    oclassroom_title.html("<span style='color:#ffff00;'>"+grade+'年'+classroom+'班'+"</span>");
                }else{
                    oclassroom_title.html("<span>"+grade+'年'+classroom+'班'+"</span>");
                }

                //送出查詢
                var class_code=trim(this.id);
                q_form(class_code);
            }
        }

        return true;
    }

    function q_form(class_code){
    //年級班級快速查詢
        var url ='';
        var page=str_repeat('../',0)+'query.php';
        var arg ={
            'class_code':class_code,
            'type':'blank'
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

        $.blockUI({
            message:'<h2 class="fc_white0">處理中，請稍後 !</h2>',
            css: {
                top:'500px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity:.8,
                color: '#437C85'
            }
        });

        go(url,'IFC');
    }

    function choose_identity(){
    //開啟身分選擇區塊
        $.blockUI({
            message:$('#choose_identity'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    function choose_class_code(){
    //開啟班級選擇區塊
        $.blockUI({
            message:$('#choose_class_code'),
            css:{
                width:'260px'
            },
            overlayCSS:{
                backgroundColor:'#000',
                opacity:0.6,
                cursor:'default'
            }
        });
    }

    $(function(){
        <?php if(!empty($arrys_school_code_rev)):?>
            var arry_school_name=[];
            <?php foreach($arrys_school_code_rev as $key=>$arrys_school_code_rev1):?>
                <?php foreach($arrys_school_code_rev1 as $key=>$arrys_school_code_rev2):?>
                    <?php foreach($arrys_school_code_rev2 as $key=>$arrys_school_code_rev3):?>
                        <?php foreach($arrys_school_code_rev3 as $arry_school_code_rev):?>
                            var school_name=trim('<?php echo $arry_school_code_rev["school_name"];?>');
                            arry_school_name.push(school_name);
                        <?php endforeach;?>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endforeach;?>
            $("#school_name").autocomplete({
                source: arry_school_name
            });
        <?php endif;?>
    });

</script>

<script type="text/javascript" src="../../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>
</Body>
</Html>