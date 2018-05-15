<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();
        // $_SESSION['uid']=5029;

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');
        require_once(str_repeat("../",3)."/inc/get_black_book_info/code.php");

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
        unset($_SESSION['tc']['t|dt']['add_book_tip']);

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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
        $filter      ='';   //查詢條件式
        $query_fields='';   //查詢欄位,顯示用

        if(isset($_SESSION['m_book']['filter'])){
            $filter=$_SESSION['m_book']['filter'];
        }
        if(isset($_SESSION['m_book']['query_fields'])){
            $query_fields=$_SESSION['m_book']['query_fields'];
        }
        if(isset($_SESSION['m_book'])&&(isset($_SESSION['m_book']['adscription']))){
            $sess_adscription=trim($_SESSION['m_book']['adscription']);
        }else{
            $sess_adscription='school';
        }

        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22,99))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        $sess_school_code=mysql_prep($sess_school_code);

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

        //-----------------------------------------------
        //檢核借閱書學校關聯
        //-----------------------------------------------

            $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------

            if($filter!=''){
                $query_sql ="";
                $query_sql.="
                    SELECT
                        COUNT(*) AS `cno`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter));
                if($filter2!==''){
                    $query_sql.="
                        UNION ALL
                            SELECT
                                COUNT(*) AS `cno`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                -- FILTER在此
                                {$filter2}
                    ";
                    if(trim($other_school_code)!==''){
                        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $query_sql.="AND `school_code`='{$sess_school_code}'";
                    }
                }
            }else{
                $query_sql ="";
                $query_sql.="
                    SELECT
                        COUNT(*) AS `cno`
                    FROM `mssr_book_class`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $query_sql.="
                    UNION ALL
                        SELECT
                            COUNT(*) AS `cno`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            -- FILTER在此
                            {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
            }
            //$sth=$conn_mssr->prepare($query_sql);
            //$sth->execute();
            //$db_results_cno=$sth->rowCount();
            //echo "<Pre>";
            //print_r($query_sql);
            //echo "</Pre>";
            //die();
            $db_results_cno=0;
            $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $db_results_cno+=(int)$db_result['cno'];
                }
            }

            if($db_results_cno===0){
                //page_nrs($title="明日星球,教師中心");
                //die();
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
            $psize=(int)10;
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

        $sinx  =(($pinx-1)*$psize);
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //SQL 查詢
    //---------------------------------------------------

        $arrys_result     =[];
        $tmp_total_results=[];

        if($db_results_cno!==0){
            if($filter!=''){
                $query_sql ="";
                $query_sql.="
                    SELECT
                        'mssr_book_library' AS `book_type`,
                        `book_sid`,
                        `book_isbn_10`,
                        `book_isbn_13`,
                        `book_library_code`,
                        '無' AS `book_no`,
                        `keyin_mdate`
                    FROM `mssr_book_library`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter2));
                $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                if(!empty($tmp_results)){
                    foreach($tmp_results as $tmp_result){
                        $tmp_total_results[]=$tmp_result;
                    }
                }
                if($filter2!==''){
                    $query_sql ="";
                    $query_sql.="
                        SELECT
                            'mssr_book_class' AS `book_type`,
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            '' AS `book_library_code`,
                            `book_no`,
                            `keyin_mdate`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            -- FILTER在此
                            {$filter2}
                    ";
                    if(trim($other_school_code)!==''){
                        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $query_sql.="AND `school_code`='{$sess_school_code}'";
                    }
                    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                    if(!empty($tmp_results)){
                        foreach($tmp_results as $tmp_result){
                            $tmp_total_results[]=$tmp_result;
                        }
                    }
                }
                $arrys_chunk =array_chunk($tmp_total_results,$psize);
                $arrys_result=$arrys_chunk[$pinx-1];
            }else{
                $has_search_cno=$psize*$pinx;
                $query_sql ="";
                $query_sql.="
                    SELECT
                        'mssr_book_class' AS `book_type`,
                        `book_sid`,
                        `book_isbn_10`,
                        `book_isbn_13`,
                        '' AS `book_library_code`,
                        `book_no`,
                        `keyin_mdate`
                    FROM `mssr_book_class`
                    WHERE 1=1
                        -- FILTER在此
                        {$filter}
                ";
                if(trim($other_school_code)!==''){
                    $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                }else{
                    $query_sql.="AND `school_code`='{$sess_school_code}'";
                }
                $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx,$psize),$arry_conn_mssr);
                if(!empty($tmp_results)){
                    foreach($tmp_results as $tmp_result){
                        $tmp_total_results[]=$tmp_result;
                    }
                }else{
                    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
                }
                if($has_search_cno!==($sinx+count($tmp_results))){
                    if($has_search_cno>$sinx+count($tmp_results)){
                        $sinx2=0;
                        $einx2=$psize-count($tmp_results);
                    }else{
                        $sinx2=$sinx-count($tmp_results);
                        $einx2=$psize;
                    }
                    $query_sql ="";
                    $query_sql.="
                        SELECT
                            'mssr_book_library' AS `book_type`,
                            `book_sid`,
                            `book_isbn_10`,
                            `book_isbn_13`,
                            `book_library_code`,
                            '無' AS `book_no`,
                            `keyin_mdate`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            -- FILTER在此
                            {$filter}
                    ";
                    if(trim($other_school_code)!==''){
                        $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
                    }else{
                        $query_sql.="AND `school_code`='{$sess_school_code}'";
                    }
                    $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx2,$einx2),$arry_conn_mssr);
                    if(!empty($tmp_results)){
                        foreach($tmp_results as $tmp_result){
                            $tmp_total_results[]=$tmp_result;
                        }
                    }
                }
                $arrys_result=$tmp_total_results;
            }
        }
//echo "<Pre>";
//print_r($arrys_result);
//echo "</Pre>";
//die();
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
    <!-- <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" /> -->
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../inc/code.css" media="all" />
    <script type="text/javascript" src="../inc/code.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
    <link rel="stylesheet" type="text/css" href="css/book_level.css" />

    <style>
        /* 容器微調 */
   /*      #container, #content, #teacher_datalist_tbl{
            width:760px;
        } */
    </style>
</Head>

<Body>
<form action="" method="get">
    <!-- 容器區塊 開始 -->
    <div id="container">

        <!-- 內容區塊 開始 -->
        <div id="content">
            <div class="form_div">
                <h1>登記書籍表單</h1>
            </div>
            <div id="about_qa"></div>
    <!--         <div id="container">
                
            </div> -->
    <!-- 
            <div class="form_div" id="isbn_input" >
                    <span>1.請填此書的ISBN(必填):</span><span class="star">*</span><br>
                    <input type="text"  id="isbn_text">
            </div>

            <div class="form_div" id="barcode_input">
                    <span>2.此本書有無條碼</span><span class="star">*</span>
                    <input type="radio" name="barcode" value="no_barcode" >無條碼
            </div>
            
            <div class="form_div">
                    <span>3.此本書為</span><span class="star">*</span><br>
                    <input type="radio" name="ch_book" value="ch_book" > 中文書<br>
                    <input type="radio" name="eng_book" value="eng_book" > 英文書<br>
                    <input type="radio" name="ch_eng_book" value="ch_eng_book" > 中英文混和書
            </div>
            <div class="form_div">
                    <span>4.是否有注音</span><span class="star">*</span><br>
                    <input type="radio" name="yes" value="yes" >有 <br>
                    <input type="radio" name="no" value="no" > 沒有<br>
            </div>
            <div class="form_div" id="major_topic">
               <span>3.請選擇適合此書的大主題</span><span class="star">*</span><br>
                    <input type="checkbox" name="major_topic[]" value="1"> 生活 <br>
                    <input type="checkbox" name="major_topic[]" value="2"> 科學<br> 
                    <input type="checkbox" name="major_topic[]" value="3"> 史地<br>           
                    <input type="checkbox" name="major_topic[]" value="4"> 文學<br>
                    <input type="checkbox" name="major_topic[]" value="5"> 藝術<br>    
                    <input type="checkbox" name="major_topic[]" value="6"> <span>其他 </span> <input type="text" name=""><br>
            </div>
            <div class="form_div" id="sub_topic">   
                    <span>6.請選擇適合此書的中主題</span><span class="star">*</span><br>
                    <input type="checkbox" name="character" value="character"> 品格 <br>
                    <input type="checkbox" name="health" value="health"> 健康<br> 
                    <input type="checkbox" name="financial " value="financial"> 理財<br>           
                    <input type="checkbox" name="energy" value="energy"> 能源<br>
                    <input type="checkbox" name="technology" value="technology"> 科技<br>
                    <input type="checkbox" name="mathematics" value="mathematics"> 數學<br>        
                    <input type="checkbox" name="biological" value="biological"> 生物<br>    
                    <input type="checkbox" name="earth_science" value="earth_science"> 地科<br>    
                    <input type="checkbox" name="geography" value="geography"> 地理<br>    
                    <input type="checkbox" name="history" value="history"> 歷史<br>    
                    <input type="checkbox" name="proverb" value="proverb"> 成語<br>
                    <input type="checkbox" name="chinese_studies" value="chinese_studies"> 國學<br>    
                    <input type="checkbox" name="novel" value="novel"> 小說<br>    
                    <input type="checkbox" name="prose" value="prose"> 散文<br>
                    <input type="checkbox" name="poetry" value="poetry"> 詩賦<br>
                    <input type="checkbox" name="visual_arts" value="visual_arts"> 視覺藝術<br>
                    <input type="checkbox" name="music" value="music"> 音樂<br>
                    <input type="checkbox" name="performing_arts" value="performing_arts"> 表演藝術<br>
                    <input type="checkbox" name="fairy_tales" value="fairy_tales">法律<br>
                    <input type="checkbox" name="other" value="other"><span>其他 </span> <input type="text" name=""><br>
            </div>
            <div class="form_div" id="minor_topic">
                    <span>7.請選擇適合此書的小主題 </span><span class="star">*</span><br>
                    <input type="checkbox" name="physiology" value="physiology"> 生理 <br>
                    <input type="checkbox" name="psychology" value="psychology"> 心理<br> 
                    <input type="checkbox" name="sports" value="sports"> 體育<br>           
                    <input type="checkbox" name="chemistry" value="chemistry"> 理化<br>
                    <input type="checkbox" name="computer_technology" value="computer_technology" > 電腦科技<br>
                    <input type="checkbox" name="art" value="art" > 航太科技<br>        
                    <input type="checkbox" name="animal" value="animal" > 動物<br>    
                    <input type="checkbox" name="plant" value="plant" > 植物<br>    
                    <input type="checkbox" name="astronomy" value="astronomy" > 天文<br>    
                    <input type="checkbox" name="atmospheric_science" value="atmospheric_science" > 大氣科學<br>    
                    <input type="checkbox" name="oceanography" value="oceanography" > 海洋學<br>
                    <input type="checkbox" name="world" value="world " > 世界<br>    
                    <input type="checkbox" name="taiwan" value="taiwan" > 台灣<br>    
                    <input type="checkbox" name="ancient_history" value="ancient_history" > 遠古史<br>
                    <input type="checkbox" name="modern_history" value="modern_history" > 近代史<br>
                    <input type="checkbox" name="myth" value="myth" > 神話<br>
                    <input type="checkbox" name="biography" value="biography" > 傳記<br>
                    <input type="checkbox" name="fairy_tales" value="fairy_tales" > 童話<br>
                    <input type="checkbox" name="fable" value="fable" > 寓言<br>
                    <input type="checkbox" name="calligraphy" value="calligraphy" > 書法<br>
                    <input type="checkbox" name="painting" value="painting" > 繪畫<br>
                    <input type="checkbox" name="photography" value="photography" > 攝影<br>
                    <input type="checkbox" name="dance" value="dance" > 舞蹈<br>
                    <input type="checkbox" name="drama" value="drama" > 戲劇<br>
                    <input type="checkbox" name="other" value="other" ><span>其他 </span> <input type="text" name="">
            </div>
            <div class="form_div">
                    <span>8.請填此書相關的標籤一: </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">   
                    <span>9.請填此書相關的標籤二: </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div ">   
                    <span>10.請填此書相關的標籤三: </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">
                    <span>11.請填此書相關的標籤四: </span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">
                    <span>12.請填此書相關的標籤五: </span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">   
                    <span>13.請填此書的頁數: </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">  
                    <span>14.請填此書的字數(必填): </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">
                    <span>15.請填此書的難度等級(填A-Z) (也可以填G-H)(必填): </span><span class="star">*</span><br>
                    <input type="text" name="" class="input_val">
            </div>
            <div class="form_div">
                <span>16.英文書的LEXILE分數（含AD, BR....）查不到寫0，中文書也寫0 (https://www.lexile.com)(必填) </span><span class="star">*</span><br>
                <input type="text" name="" class="input_val">
            </div> -->
        
            <input type="submit" name="送出" value="送出" id="submit" onclick="check_all_value(event);return false;">
        </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 容器區塊 結束 -->

    <!-- 快速切換區塊 開始 -->
</form>
</Body>
<script type="text/javascript">

// var major_topic_checkbox_stauts=false;
// var sub_topic_checkbox_stauts=false;
// var minor_topic_checkbox_stauts=false;
// var aaa;
// var checkbox=document.getElementById

//  // $("input[name='major_topic[]']").each( function () {
//  //        alert($(this).val());
//  //    });

// $( "#major_topic>input" ).change(function() {
//     var $input = $( this );
//     console.log($input.prop( "checked" )); 
//     if($input.prop( "checked" )==true){
//         return major_topic_checkbox_stauts=true;
//     }else{
//          return major_topic_checkbox_stauts=false;
//     }
//  });
// $( "#sub_topic>input" ).change(function() {
//     var $input = $( this );
//     console.log($input.prop( "checked" )); 
//     if($input.prop( "checked" )==true){
//         return sub_topic_checkbox_stauts=true;
//     }else{
//          return sub_topic_checkbox_stauts=false;
//     }
//  });
// $( "#minor_topic>input" ).change(function() {
//     var $input = $( this );
//     console.log($input.prop( "checked" )); 
//     if($input.prop( "checked" )==true){
//         return minor_topic_checkbox_stauts=true;
//     }else{
//          return minor_topic_checkbox_stauts=false;
//     }
//  });


// $("#isbn_text").keyup(function myfunction() {
//         $("#isbn_text").val();
//             console.log($("#isbn_text").val());
        
          
// });

// $("#b").keyup(function myfunction() {
//         $("#isbn_text").val();
//             console.log($("#isbn_text").val());
        
          
// });
    
// function submit_check(){

// var isbn_text = $('#isbn_text').val();
    
//  // if( $("#isbn_text").val()==""){ 
//  //              alert("isbn尚未填寫");
//  //              return false;
//  //    }else{
//  //             $("#isbn_input b").remove(":contains('尚未填寫')");
//  //    };
   
//         // var aaa= $( "#major_topic>input" );
    
//     console.log("第五題狀態:"+major_topic_checkbox_stauts);
//     if(major_topic_checkbox_stauts==false){
//         $("#sub_topic b").remove(":contains('尚未填寫')");
//         $("#major_topic .star").append(" <b>尚未填寫</b>");
        
//     }else{
//              $("#major_topic b").remove(":contains('尚未填寫')");
//     }

//     console.log("第六題狀態:"+sub_topic_checkbox_stauts);

//     if(sub_topic_checkbox_stauts==false){
//         $("#sub_topic b").remove(":contains('尚未填寫')");
//         $("#sub_topic .star").append(" <b>尚未填寫</b>");
            
//     }else{
//              $("#sub_topic b").remove(":contains('尚未填寫')");
//     }

//     console.log("第七題狀態:"+sub_topic_checkbox_stauts);

//     if(minor_topic_checkbox_stauts==false){
//         $("#minor_topic b").remove(":contains('尚未填寫')");
//         $("#minor_topic .star").append(" <b>尚未填寫</b>");
            
//     }else{
//              $("#minor_topic b").remove(":contains('尚未填寫')");
//     }

     

//     if(major_topic_checkbox_stauts==false || sub_topic_checkbox_stauts==false || minor_topic_checkbox_stauts==false) return false;



// }

// function about_qa(data_array){

//   var div=document.getElementById('container');
//     var topic_type=data_array['topic_type'];
//     console.log(topic_type);
//         for(var key in topic_type){

//             // console.log(key);
//             var type=topic_type[key];
//             switch (type) {

//                     case 'text':
                    
//                     break;

//                     case 'radio':
//                     var input = 
//                     break;

//                     case'checkbox':
//                      x="checkbox";
//                     break;

//                     default:
//                     x="沒有符合的條件";
//                     break;
//             }
//                          document.write(x);
                      
//         }

// }



//==============================
//data_array的內容
//===================================
//-----------
///取值///
//----------


function checkbox_val(event){
    // var element = event.target;
    // var input_name=element.getAttribute('name');
    var element = event.target;
    var input_id=element.getAttribute('id');
    var input_value = element.value;
    var input_name=element.getAttribute('name');
    console.log(input_name);


    

}

function text_val(event){

    var element = event.target;
    var input_name=element.getAttribute('name');
    var input_val=element.value;

 
        
}


function check_all_value(){
// var

     

}

function radio_value(event){

    var element = event.target;
    var input_id=element.getAttribute('id');
    var input_value = element.value;

       console.log(input_value);

}

//--------------
///產生input///
//-------------
function getInput(input){
    var tagName=input.tagName;
    var attribute=input.attribute;
    var el=document.createElement(tagName);

    if(attribute) {
        for(key in attribute) {
            el.setAttribute(key,attribute[key]);
        }
    }
    return el;
}



//------------
///產生題目///
//------------

function about_qa(data_array){

    for(var key in data_array){

           var topic_type=data_array[key]['topic_type'];
           var topic_id=data_array[key]['topic_id'];
           var topic_title=data_array[key]['topic_title'];
           var required_field=data_array[key]['required_field'];
           var topic_options=data_array[key]['topic_options'];
                            
           switch (topic_type) {

                   //input text類型

                    case '1':
                    $("#about_qa").append("<div class='"+qa_div+"'></div>");

                    var about_qa_div=$("#about_qa");
                    var qa_div = document.createElement('div');
                    qa_div.setAttribute("class", "qa_div");
                    qa_div.setAttribute("id", "qa_div"+topic_id);
                    var title_div = document.createElement("div");
                    title_div.setAttribute("class", "title_div");


                    if(required_field=="1"){
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"*"+"</span>";
                    }else{
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"</span>";
                    }
                    
                    
                    about_qa_div.appendChild(qa_div);
                    qa_div.appendChild(title_div); 

                    var qa_id=qa_div.getAttribute('id');

                    var input=getInput({
                        tagName: 'input',
                        attribute: {
                                    id: 'input_t'+topic_id,
                                    class: 'input',
                                    type: 'text',
                                    name: 'text_'+topic_id,
                                    onkeyup: "text_val(event)"
                                    
                        }

                    });
                    
                    qa_div.appendChild(title_div); 
                    qa_div.appendChild(input);
                    break;
                    //================
                    //input radio類型
                    //================

                    case '2':
                    var about_qa_div=document.getElementById('about_qa');
                    var qa_div = document.createElement('div');
                    qa_div.setAttribute("class", "qa_div");

                    var title_div = document.createElement("div");
                    title_div.setAttribute("class", "title_div");
                    if(required_field=="1"){
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"*"+"</span>";
                    }else{
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"</span>";
                    }

                    
                    about_qa_div.appendChild(qa_div);
                    qa_div.appendChild(title_div); 

                    for(var key in topic_options){
                            if(topic_options){
                                var options=topic_options[key];
                         // console.log(options)            
                                var input=getInput({
                                    tagName: 'input',
                                    attribute: {
                                                id: 'input_r'+topic_id+"_"+key,
                                                class: 'input',
                                                type: 'radio', 
                                                name: topic_id,
                                                value: parseInt(key)+1,
                                                onclick: "radio_value(event)"                                         
                                    }

                                });
                                var options_div = document.createElement('div');
                                options_div.setAttribute("class", "options_div");
                                var options_span=document.createElement("span");
                                options_span.innerHTML=options;
                                qa_div.appendChild(options_div);
                                options_div.appendChild(input);
                                options_div.appendChild(options_span);

                                
                            } 
                     }
                    break;

                    //input checkbox類型
                    case'3':
                    var about_qa_div=document.getElementById('about_qa');
                    var qa_div = document.createElement('div');
                    qa_div.setAttribute("class", "qa_div");

                    var title_div = document.createElement("div");
                    title_div.setAttribute("class", "title_div");
                    if(required_field=="1"){
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"*"+"</span>";
                    }else{
                        title_div.innerHTML="<span>"+topic_id+"."+topic_title+"</span>";
                    }   
                    
                    about_qa_div.appendChild(qa_div);
                    qa_div.appendChild(title_div);


                    for(var key in topic_options){
                            if(topic_options){
                                var options=topic_options[key];
          
                                var input=getInput({
                                    tagName: 'input',
                                    attribute: {
                                                id: 'input_c_'+topic_id+"_"+key,
                                                class: 'input',
                                                type: 'checkbox',
                                                name: 'about_topic'+topic_id+"[]",
                                                value: parseInt(key)+1,
                                                onclick: "checkbox_val(event)"
                                    
                                    }

                                });
                                var options_div = document.createElement('div');
                                options_div.setAttribute("class", "options_div");
                                var options_span=document.createElement("span");
                                options_span.innerHTML="<label for='input_c_"+topic_id+"_"+key+"'>"+options+"</label>";
                                qa_div.appendChild(options_div);
                                options_div.appendChild(input);
                                options_div.appendChild(options_span);

                                if(options=="其他"){

                                    var input_other=getInput({
                                        tagName: 'input',
                                        attribute: {
                                                id: 'input_o'+topic_id,
                                                class: 'input',
                                                type: 'text',
                                                name:  "text_"+topic_id,
                                                onkeyup:"text_val(event)"
                                                
                                        }

                                    });

                                options_div.appendChild(input_other);

                                }

                            } 
                     }
                    
                    break;

                    default:
                    x="沒有符合的條件";
                    break;
            }                        
                    
    }

}



//--------------
///畫面load進///
//-------------

function main(){


        var url = "./php/get_qa.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>"};
                   

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {
                    console.log(data);
                    data_array = JSON.parse(data);
                    console.log(data_array);
                    about_qa(data_array);

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });


} 
main();

</script>
</Html>
