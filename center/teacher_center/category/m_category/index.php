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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_category');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

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

        if(!in_array($sess_school_code,array('gcp','dles','pmc'))){
            $msg="本區域尚未開放!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        $sess_school_code=mysql_prep($sess_school_code);

        //初始化, 階層陣列
        $arrys_lv_info=array();
        $lv_flag=0;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //檢核預設值
        //---------------------------------------------------

            $sql="
                SELECT
                    `cat_id`
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `mssr_book_category`.`school_code` ='{$sess_school_code  }'
                    AND `mssr_book_category`.`cat1_id`     =1
                    AND `mssr_book_category`.`cat2_id`     =1
                    AND `mssr_book_category`.`cat3_id`     =1
            ";

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

            if(empty($arrys_result)){
            //新增預設值

                $create_by          =(int)$sess_user_id;
                $edit_by            =(int)$sess_user_id;
                $sess_school_code   =mysql_prep(strip_tags($sess_school_code));
                $cat_id             ="NULL";
                $cat_name           ='未分類';
                $cat_code           =cat_code($create_by,mb_internal_encoding());
                $cat1_id            =1;
                $cat2_id            =1;
                $cat3_id            =1;
                $cat_state          ='啟用';
                $keyin_cdate        ="NOW()";
                $keyin_mdate        ="NULL";

                $sql="
                    # for mssr_book_category
                    INSERT INTO `mssr_book_category` SET
                        `create_by`         =  {$create_by          } ,
                        `edit_by`           =  {$edit_by            } ,
                        `school_code`       = '{$sess_school_code   }',
                        `cat_id`            =  {$cat_id             } ,
                        `cat_name`          = '{$cat_name           }',
                        `cat_code`          = '{$cat_code           }',
                        `cat1_id`           =  {$cat1_id            } ,
                        `cat2_id`           =  {$cat2_id            } ,
                        `cat3_id`           =  {$cat3_id            } ,
                        `cat_state`         = '{$cat_state          }',
                        `keyin_cdate`       =  {$keyin_cdate        } ,
                        `keyin_mdate`       =  {$keyin_mdate        } ;
                ";

                //送出
                $err ='DB QUERY FAIL';
                $conn_mssr->exec($sql);
            }

        //---------------------------------------------------
        //SQL處理
        //---------------------------------------------------

            $query_sql="
                SELECT *
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `mssr_book_category`.`cat1_id`<>1
                    #AND `mssr_book_category`.`cat2_id`=1
                    #AND `mssr_book_category`.`cat3_id`=1
                    AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                    AND `mssr_book_category`.`cat_state`  ='啟用'
            ";

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);

            if(!empty($arrys_result)){
            //回填各階層相關陣列
                foreach($arrys_result as $inx=>$arry_result){
                    $cat1_id=(int)$arry_result['cat1_id'];
                    $cat2_id=(int)$arry_result['cat2_id'];
                    $cat3_id=(int)$arry_result['cat3_id'];

                    if(($cat2_id===1)&&($cat3_id===1)){
                        $lv_flag=1;
                        $arrys_lv_info['arrys_lv1'][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id===1)){
                        $lv_flag=2;
                        $arrys_lv_info['arrys_lv2'][$cat1_id][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id!==1)){
                        $lv_flag=3;
                        $arrys_lv_info['arrys_lv3'][$cat2_id][]=$arry_result;
                    }else{
                        $lv_flag=0;
                        die("發生嚴重錯誤，請洽詢明日星球團隊人員!");
                    }
                }
            }
//echo "<Pre>";
//print_r($arrys_lv_info);
//echo "</Pre>";
    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

            $auth_sys_name_arry=auth_sys_name_arry();
            $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            $sys_ename=$FOLDER[count($FOLDER)-2];
            $mod_ename=$FOLDER[count($FOLDER)-1];
            $sys_cname='';  //系統名稱
            $mod_cname='';  //模組名稱

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
            $sys_page=str_repeat("../",2)."index.php";
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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container, #content, #teacher_datalist_tbl{
            width:760px;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <div id="content">
        <table id="teacher_datalist_tbl" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td align="left" valign="middle" width="400px">
                    <!-- 教師中心路徑選單 開始 -->
                    <div id="teacher_center_path">
                        <table id="teacher_center_path_cont" border="0" width="100%">
                            <tr>
                                <td align="left" valign="middle" class="menu_dot">
                                    <span class="menu_cont">
                                        <img width="12" height="12" src="../../../../img/icon/blue.jpg" border="0">
                                        <a href="../../index.php">教師中心</a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="<?php echo htmlspecialchars($sys_url);?>">
                                            <?php echo htmlspecialchars($sys_cname);?>
                                        </a>

                                        <span class="fc_gray0" style="margin:0 3px;">&gt;&gt;</span>
                                        <a href="javascript:void(0);"><?php echo htmlspecialchars($mod_cname);?></a>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- 教師中心路徑選單 結束 -->
                </td>
                <td align="right" valign="middle">
                    <!-- 查詢表單列 開始 -->
                    <div id="qform">
                        <span id="qform1"></span>
                    </div>
                    <!-- 查詢表單列 結束 -->
                </td>
            </tr>
            <tr>
                <td width="760px" colspan="2">
                    <!-- 資料列表 開始 -->
                    <?php
                        if(!empty($arrys_result)){
                            page_hrs($title);
                        }else{
                            page_nrs($title);
                        }
                    ?>
                    <!-- 資料列表 結束 -->
                </td>
            </tr>
        </table>
    </div>
    <!-- 內容區塊 結束 -->

</div>
<!-- 容器區塊 結束 -->

<!-- 快速切換區塊 開始 -->
<?php //echo fast_area($rd=2);?>
<!-- 快速切換區塊 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    $(function(){

        //快速切換設置
        //fast_area_config('#fast_area',0,0);

    });

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

</script>
</Body>
</Html>


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
        global $arry_conn_user;
        global $arry_conn_mssr;
        global $APP_ROOT;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arrys_lv_info;
        global $sess_login_info;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
    //echo "<Pre>";
    //print_r($arrys_lv_info);
    //echo "</Pre>";

        $json_lv_info=json_encode($arrys_lv_info,true);
?>
<!-- 資料列表 開始 -->
<table align="center" border="0" width="100%" class="table_style0" style="position:relative;top:10px;">
    <tr valign="center">
        <td height="40px" class="fc_blue0">類別設定</td>
        <td height="40px" class="fc_blue0" align="right">
            <input type="button" value="新增類別" class="ibtn_gr6030" onclick="add();" onmouseover="this.style.cursor='pointer'">
        </td>
    </tr>
    <tr valign="left">
        <td class="b_line gr_dashed" colspan="2">

            <!-- 內容 -->
            <table border="0" width="33%" cellpadding="0" cellspacing="0" style="position:relative;margin-left:2px;margin-top:10px;" align="left" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td height="25px" class="fc_white0">第一階類別</td>
                </tr>
                <tr>
                    <td height="345px" align="left" valign="top">
                    <?php foreach($arrys_lv_info['arrys_lv1'] as $inx=>$arry_result):?>
                    <?php
                    //-----------------------------------------------
                    //接收欄位
                    //-----------------------------------------------
                    //create_by       建立者
                    //edit_by         修改者
                    //school_code     學校代號
                    //cat_id          類別主索引
                    //cat_name        類別名稱
                    //cat_code        類別代號
                    //cat1_id         類別1主索引
                    //cat2_id         類別2主索引
                    //cat3_id         類別3主索引
                    //cat_state       類別狀態
                    //keyin_cdate     建立時間
                    //keyin_mdate     修改時間

                        extract($arry_result, EXTR_PREFIX_ALL, "rs");

                    //-----------------------------------------------
                    //處理欄位
                    //-----------------------------------------------

                        //cat_id            類別主索引
                        $rs_cat_id=(int)$rs_cat_id;

                        //cat1_id           類別1主索引
                        $rs_cat1_id=(int)$rs_cat1_id;

                        //cat_code          類別代號
                        $rs_cat_code=trim($rs_cat_code);

                        //cat_name          類別名稱
                        if(mb_strlen($rs_cat_name)>5){
                            $rs_cat_name=mb_substr($rs_cat_name,0,5)."..";
                        }

                        //cat_state         類別狀態
                        $rs_cat_state_cname="";
                        $rs_cat_state_html ="";
                        $rs_cat_state=trim($rs_cat_state);
                        switch(trim($rs_cat_state)){
                            case '啟用':
                                $rs_cat_state_class="";
                            break;
                            case '停用':
                                $rs_cat_state_class="fc_red1";
                            break;
                        }

                    //-----------------------------------------------
                    //特殊處理
                    //-----------------------------------------------
                    ?>
                    <table id="lv1_id_<?php echo $rs_cat1_id;?>" name="lv1_name" cellpadding="0" cellspacing="0" border="3" width="118px" align="left" bgcolor="#ffffff"
                    lv1_id=<?php echo $rs_cat1_id;?>;
                    style="position:relative;margin:2px;border-color:#dbdbdb"
                    onmouseover="this.style.cursor='pointer';lv1_set(<?php echo $rs_cat1_id;?>);" onclick="lv1_set(<?php echo $rs_cat1_id;?>);"/>
                        <tr align="center" valign="middle">
                            <td width="5px" height="20px">
                                <input type="checkbox" id="cat_state" name="cat_state" value="<?php echo $rs_cat_state;?>"
                                <?php if($rs_cat_state==='啟用')echo 'checked';?> onclick="edit_state(event,this,this.checked,'<?php echo addslashes($rs_cat_code);?>');">
                            </td>
                            <td width="" height="20px">
                                <span class="<?php echo $rs_cat_state_class;?>"><?php echo htmlspecialchars($rs_cat_name);?></span>
                            </td>
                            <td width="5px" height="20px">
                                <!-- <span class="fc_blue0 fsize_18" onclick="edit_cat_lv(event,'<?php echo addslashes($rs_cat_code);?>');">⊕</span> -->
                            </td>
                        </tr>
                    </table>
                    <?php endforeach;?>
                    </td>
                </tr>
            </table>

            <table id="lv2_tbl" border="0" width="33%" cellpadding="0" cellspacing="0" style="position:relative;margin-left:2px;margin-top:10px;" align="left" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td height="25px" class="fc_white0">第二階類別</td>
                </tr>
                <tr>
                    <td id="lv2_td" height="345px" align="left" valign="top"></td>
                </tr>
            </table>

            <table id="lv3_tbl" border="0" width="33%" cellpadding="0" cellspacing="0" style="position:relative;margin-left:2px;margin-top:10px;" align="left" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td height="25px" class="fc_white0">第三階類別</td>
                </tr>
                <tr>
                    <td id="lv3_td" height="345px" align="left" valign="top"></td>
                </tr>
            </table>
            <!-- 內容 -->

        </td>
    </tr>
</table>
<!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------
//create_by       建立者
//edit_by         修改者
//school_code     學校代號
//cat_id          類別主索引
//cat_name        類別名稱
//cat_code        類別代號
//cat1_id         類別1主索引
//cat2_id         類別2主索引
//cat3_id         類別3主索引
//cat_state       類別狀態
//keyin_cdate     建立時間
//keyin_mdate     修改時間

    var json_lv_info=<?php echo ($json_lv_info);?>;

    window.onload=function(){

    }

    function edit_state(event,obj,check_state,cat_code){
    //修改類別狀態
//alert(cat_code);

        event.stopPropagation();
        return false;
    }

    function edit_cat_lv(event,cat_code){
    //修改類別階層

        var url ='';
        var page=str_repeat('../',0)+'edit/editF.php';
        var arg ={
            //'psize':psize,
            //'pinx' :pinx,
            'cat_code':cat_code
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

        event.stopPropagation();
        return false;
    }

    function lv1_set(cat1_id){
    //設置第一階類別

        var cat1_id   =cat1_id;
        var olv1_id   =document.getElementById('lv1_id_'+cat1_id);
        var olv1_names=document.getElementsByName('lv1_name');
        var $lv2_td   =$('#lv2_td');
        var $lv3_td   =$('#lv3_td');

        //bgcolor重置
        for(i=0;i<olv1_names.length;i++){
            olv1_name=olv1_names[i];
            olv1_name.bgColor="#ffffff"
        }
        olv1_id.bgColor="#ffff99"

        //移除第二階舊有資訊
        $lv2_td.find("TABLE").remove();

        //移除第三階舊有資訊
        $lv3_td.find("TABLE").remove();

        try{
            var json_lv2=json_lv_info['arrys_lv2'][cat1_id];
            for(key in json_lv2){
                var js_cat_name =trim(json_lv2[key]['cat_name']);
                var js_cat1_id  =parseInt(json_lv2[key]['cat1_id']);
                var js_cat2_id  =parseInt(json_lv2[key]['cat2_id']);
                var js_cat3_id  =parseInt(json_lv2[key]['cat3_id']);
                var js_cat_state=trim(json_lv2[key]['cat_state']);
                var js_cat_code =trim(json_lv2[key]['cat_code']);
                f_cat_code      ="'"+js_cat_code+"'";

                //類別名稱
                if(js_cat_name.length>=6){
                    js_cat_name=substr(js_cat_name,0,6)+'..';
                }

                //狀態區分
                var f_color='#000000';
                var check_state='checked';
                if(js_cat_state!=='啟用'){
                    f_color='#ff0000';
                    check_state='';
                }

                //設置附加元素
                var _html_tbl='';
                _html_tbl+='';
                _html_tbl+='<table id=lv2_id_'+js_cat2_id+' name="lv2_name" cellpadding="0" cellspacing="0" border="3" width="118px" align="left" bgcolor="#ffffff" lv2_id='+js_cat2_id+' style="position:relative;margin:2px;border-color:#dbdbdb;color:'+f_color+';" onmouseover="_mouseover(this);lv2_set('+cat1_id+','+js_cat2_id+');" onclick="lv2_set('+cat1_id+','+js_cat2_id+');">';
                    _html_tbl+='<tr align="center" valign="middle">';
                        _html_tbl+='<td width="5px" height="20px">';
                            _html_tbl+='<input type="checkbox" id="js_cat_state" name="js_cat_state" value="'+js_cat_state+'"'+'onclick="edit_state(event,this,this.checked,'+f_cat_code+');" '+check_state+'>';
                        _html_tbl+='</td>';
                        _html_tbl+='<td width="" height="20px">';
                            _html_tbl+=js_cat_name;
                        _html_tbl+='</td>';
                        _html_tbl+='<td width="5px" height="20px">';
                            _html_tbl+='<span class="fc_blue0 fsize_18" onclick="edit_cat_lv(event,'+"'"+js_cat_code+"'"+');" onmouseover="_mouseover(this);">⊕</span>';
                        _html_tbl+='</td>';
                    _html_tbl+='</tr>';
                _html_tbl+='</table>';

                //附加
                $lv2_td.append(_html_tbl);
            }
        }catch(err){
            return false;
        }
    }

    function lv2_set(cat1_id,cat2_id){
    //設置第二階類別
        var cat1_id   =cat1_id;
        var cat2_id   =cat2_id;
        var olv2_id   =document.getElementById('lv2_id_'+cat2_id);
        var olv2_names=document.getElementsByName('lv2_name');
        var $lv3_td   =$('#lv3_td');

        //bgcolor重置
        for(i=0;i<olv2_names.length;i++){
            olv2_name=olv2_names[i];
            olv2_name.bgColor="#ffffff"
        }
        olv2_id.bgColor="#ffff99"

        //移除第三階舊有資訊
        $lv3_td.find("TABLE").remove();

        try{
            var json_lv3=json_lv_info['arrys_lv3'][cat2_id];
            for(key in json_lv3){
                var js_cat_name =trim(json_lv3[key]['cat_name']);
                var js_cat1_id  =parseInt(json_lv3[key]['cat1_id']);
                var js_cat2_id  =parseInt(json_lv3[key]['cat2_id']);
                var js_cat3_id  =parseInt(json_lv3[key]['cat3_id']);
                var js_cat_state=trim(json_lv3[key]['cat_state']);
                var js_cat_code =trim(json_lv3[key]['cat_code']);
                f_cat_code   ="'"+js_cat_code+"'";

                //類別名稱
                if(js_cat_name.length>=6){
                    js_cat_name=substr(js_cat_name,0,6)+'..';
                }

                //狀態區分
                var f_color='#000000';
                var check_state='checked';
                if(js_cat_state!=='啟用'){
                    f_color='#ff0000';
                    check_state='';
                }

                //設置附加元素
                var _html_tbl='';
                _html_tbl+='';
                _html_tbl+='<table id=lv3_id_'+js_cat3_id+' name="lv3_name" cellpadding="0" cellspacing="0" border="3" width="118px" align="left" bgcolor="#ffffff" lv3_id='+js_cat3_id+' style="position:relative;margin:2px;border-color:#dbdbdb;color:'+f_color+';" onmouseover="void(0);">';
                    _html_tbl+='<tr align="center" valign="middle">';
                        _html_tbl+='<td width="5px" height="20px">';
                            _html_tbl+='<input type="checkbox" id="js_cat_state" name="js_cat_state" value="'+js_cat_state+'"'+'onclick="edit_state(event,this,this.checked,'+f_cat_code+');" '+check_state+'>';
                        _html_tbl+='</td>';
                        _html_tbl+='<td width="" height="20px">';
                            _html_tbl+=js_cat_name;
                        _html_tbl+='</td>';
                        _html_tbl+='<td width="5px" height="20px">';
                            _html_tbl+='<span class="fc_blue0 fsize_18" onclick="edit_cat_lv(event,'+"'"+js_cat_code+"'"+');" onmouseover="_mouseover(this);">⊕</span>';
                        _html_tbl+='</td>';
                    _html_tbl+='</tr>';
                _html_tbl+='</table>';

                //附加
                if(cat1_id===js_cat1_id){
                    $lv3_td.append(_html_tbl);
                }
            }
        }catch(err){
            return false;
        }
    }

    function add(){
    //新增類別
        var url ='';
        var page=str_repeat('../',0)+'add/addF.php';
        var arg ={

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

    function _mouseover(obj){
        obj.style.cursor='pointer';
    }

</script>

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

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $config_arrys;
        global $conn_mssr;

    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=0;  //欄位個數
        $btn_nos=0;  //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
?>
<!-- 資料列表 開始 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <!-- 在此設定寬高 -->
        <td width="100%" height="250px" align="center" valign="top">
            <!-- 內容 -->
            <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="table_style2">
                <tr align="center" valign="middle" class="bg_gray1">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td height="250px" align="center" valign="middle">
                        <img src="../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                        目前系統無資料，或查無資料!<br/>
                        <input type="button" value="新增類別" class="ibtn_gr6030" onclick="add();" onmouseover="this.style.cursor='pointer'">
                    </td>
                </tr>
            </table>
            <!-- 內容 -->
        </td>
    </tr>
</table>
<!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    window.onload=function(){

    }

    function add(){
    //新增類別
        var url ='';
        var page=str_repeat('../',0)+'add/addF.php';
        var arg ={

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

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>

<script type="text/javascript" src="../../../../inc/external/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//呼叫使用者頁面紀錄
//-------------------------------------------------------
    user_page_log(rd=4);
</script>