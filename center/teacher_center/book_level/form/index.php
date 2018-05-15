<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
    //     @session_start();
    //     // $_SESSION['uid']=5029;

    //     //啟用BUFFER
    //     @ob_start();

    //     //外掛設定檔
    //     require_once(str_repeat("../",3).'config/config.php');
    //     require_once(str_repeat("../",3)."/inc/get_black_book_info/code.php");

    //     //外掛函式檔
    //     $funcs=array(
    //                 APP_ROOT.'inc/code',
    //                 APP_ROOT.'center/teacher_center/inc/code',

    //                 APP_ROOT.'lib/php/db/code'
    //                 );
    //     func_load($funcs,true);

    //     //清除並停用BUFFER
    //     @ob_end_clean();

    // //---------------------------------------------------
    // //有無維護
    // //---------------------------------------------------

    //     if($config_arrys['is_offline']['center']['teacher_center']){
    //         $url=str_repeat("../",5).'index.php';
    //         header("Location: {$url}");
    //         die();
    //     }

    // //---------------------------------------------------
    // //有無登入
    // //---------------------------------------------------

    //     $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
    //     if(empty($arrys_login_info)){
    //         die();
    //     }

    // //---------------------------------------------------
    // //重複登入
    // //---------------------------------------------------

    //     if(in_array('read_the_registration_code',$config_arrys['user_area'])){
    //     //清空閱讀登記條碼版登入資訊

    //         $_SESSION['config']['user_tbl']=array();
    //         $_SESSION['config']['user_type']='';
    //         $_SESSION['config']['user_lv']=0;
    //         if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
    //             foreach($_SESSION['config']['user_area'] as $inx=>$area){
    //                 if(trim($area)==='read_the_registration_code'){
    //                     unset($_SESSION['config']['user_area'][$inx]);
    //                 }
    //             }
    //         }
    //     }

    // //---------------------------------------------------
    // //SESSION
    // //---------------------------------------------------

    //     $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();
    //     unset($_SESSION['tc']['t|dt']['add_book_tip']);

    // //---------------------------------------------------
    // //權限,與判斷
    // //---------------------------------------------------

    //     if(!empty($sess_login_info)){
    //         if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
    //             $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
    //             $jscript_back="
    //                 <script>
    //                     alert('{$msg}');
    //                     history.back(-1);
    //                 </script>
    //             ";
    //             die($jscript_back);
    //         }
    //     }else{
    //         //權限指標
    //         $auth_flag=false;
    //         foreach($arrys_login_info as $inx=>$arry_login_info){
    //             if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
    //         }
    //         if(!$auth_flag){
    //             $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
    //             $jscript_back="
    //                 <script>
    //                     alert('{$msg}');
    //                     history.back(-1);
    //                 </script>
    //             ";
    //             die($jscript_back);
    //         }
    //     }

    // //---------------------------------------------------
    // //管理者判斷
    // //---------------------------------------------------

    //     if(!empty($sess_login_info)){
    //         $is_admin=is_admin(trim($sess_login_info['permission']));
    //         if($is_admin){
    //             $sess_login_info['responsibilities']=99;
    //         }
    //     }

    // //---------------------------------------------------
    // //系統權限判斷
    // //---------------------------------------------------
    // //1     校長
    // //3     主任
    // //5     帶班老師
    // //12    行政老師
    // //14    主任帶一個班
    // //16    主任帶多個班
    // //22    老師帶多個班
    // //99    管理者

    //     if(!empty($sess_login_info)){
    //         $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_book');
    //     }

    // //---------------------------------------------------
    // //接收參數
    // //---------------------------------------------------

    //     //SESSION
    //     $filter      ='';   //查詢條件式
    //     $query_fields='';   //查詢欄位,顯示用

    //     if(isset($_SESSION['m_book']['filter'])){
    //         $filter=$_SESSION['m_book']['filter'];
    //     }
    //     if(isset($_SESSION['m_book']['query_fields'])){
    //         $query_fields=$_SESSION['m_book']['query_fields'];
    //     }
    //     if(isset($_SESSION['m_book'])&&(isset($_SESSION['m_book']['adscription']))){
    //         $sess_adscription=trim($_SESSION['m_book']['adscription']);
    //     }else{
    //         $sess_adscription='school';
    //     }

    //     if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
    //     if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
    //     if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
    //     if(isset($sess_login_info['responsibilities'])){
    //         $sess_responsibilities=(int)$sess_login_info['responsibilities'];
    //         if(in_array($auth_sys_check_lv,array(5,14,16,22,99))){
    //             $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
    //             $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
    //             $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
    //         }
    //     }

    // //---------------------------------------------------
    // //設定參數
    // //---------------------------------------------------

    //     $sess_school_code=mysql_prep($sess_school_code);

    // //---------------------------------------------------
    // //串接SQL
    // //---------------------------------------------------

    //     //-----------------------------------------------
    //     //資料庫
    //     //-----------------------------------------------

    //         //建立連線 user
    //         $conn_user=conn($db_type='mysql',$arry_conn_user);

    //         //建立連線 mssr
    //         $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

    //     //-----------------------------------------------
    //     //檢核借閱書學校關聯
    //     //-----------------------------------------------

    //         $other_school_code=book_borrow_school_rev($db_type='mysql',$arry_conn_mssr,$sess_school_code);

    //     //---------------------------------------------------
    //     //SQL 筆數查詢
    //     //---------------------------------------------------

    //         if($filter!=''){
    //             $query_sql ="";
    //             $query_sql.="
    //                 SELECT
    //                     COUNT(*) AS `cno`
    //                 FROM `mssr_book_library`
    //                 WHERE 1=1
    //                     -- FILTER在此
    //                     {$filter}
    //             ";
    //             if(trim($other_school_code)!==''){
    //                 $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //             }else{
    //                 $query_sql.="AND `school_code`='{$sess_school_code}'";
    //             }
    //             $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter));
    //             if($filter2!==''){
    //                 $query_sql.="
    //                     UNION ALL
    //                         SELECT
    //                             COUNT(*) AS `cno`
    //                         FROM `mssr_book_class`
    //                         WHERE 1=1
    //                             -- FILTER在此
    //                             {$filter2}
    //                 ";
    //                 if(trim($other_school_code)!==''){
    //                     $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //                 }else{
    //                     $query_sql.="AND `school_code`='{$sess_school_code}'";
    //                 }
    //             }
    //         }else{
    //             $query_sql ="";
    //             $query_sql.="
    //                 SELECT
    //                     COUNT(*) AS `cno`
    //                 FROM `mssr_book_class`
    //                 WHERE 1=1
    //                     -- FILTER在此
    //                     {$filter}
    //             ";
    //             if(trim($other_school_code)!==''){
    //                 $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //             }else{
    //                 $query_sql.="AND `school_code`='{$sess_school_code}'";
    //             }
    //             $query_sql.="
    //                 UNION ALL
    //                     SELECT
    //                         COUNT(*) AS `cno`
    //                     FROM `mssr_book_library`
    //                     WHERE 1=1
    //                         -- FILTER在此
    //                         {$filter}
    //             ";
    //             if(trim($other_school_code)!==''){
    //                 $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //             }else{
    //                 $query_sql.="AND `school_code`='{$sess_school_code}'";
    //             }
    //         }
    //         //$sth=$conn_mssr->prepare($query_sql);
    //         //$sth->execute();
    //         //$db_results_cno=$sth->rowCount();
    //         //echo "<Pre>";
    //         //print_r($query_sql);
    //         //echo "</Pre>";
    //         //die();
    //         $db_results_cno=0;
    //         $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
    //         if(!empty($db_results)){
    //             foreach($db_results as $db_result){
    //                 $db_results_cno+=(int)$db_result['cno'];
    //             }
    //         }

    //         if($db_results_cno===0){
    //             //page_nrs($title="明日星球,教師中心");
    //             //die();
    //         }

    // //---------------------------------------------------
    // //分頁處理
    // //---------------------------------------------------

    //     $numrow=0;  //資料總筆數
    //     $psize =10; //單頁筆數,預設10筆
    //     $pnos  =0;  //分頁筆數
    //     $pinx  =1;  //目前分頁索引,預設1
    //     $sinx  =0;  //值域起始值
    //     $einx  =0;  //值域終止值

    //     if(isset($_GET['psize'])){
    //         $psize=(int)10;
    //         if($psize===0){
    //             $psize=10;
    //         }
    //     }
    //     if(isset($_GET['pinx'])){
    //         $pinx=(int)$_GET['pinx'];
    //         if($pinx===0){
    //             $pinx=1;
    //         }
    //     }

    //     $numrow=$db_results_cno;

    //     $pnos  =ceil($numrow/$psize);
    //     $pinx  =($pinx>$pnos)?$pnos:$pinx;

    //     $sinx  =(($pinx-1)*$psize);
    //     $einx  =(($pinx)*$psize);
    //     $einx  =($einx>$numrow)?$numrow:$einx;
    //     //echo $numrow."<br/>";

    // //---------------------------------------------------
    // //SQL 查詢
    // //---------------------------------------------------

    //     $arrys_result     =[];
    //     $tmp_total_results=[];

    //     if($db_results_cno!==0){
    //         if($filter!=''){
    //             $query_sql ="";
    //             $query_sql.="
    //                 SELECT
    //                     'mssr_book_library' AS `book_type`,
    //                     `book_sid`,
    //                     `book_isbn_10`,
    //                     `book_isbn_13`,
    //                     `book_library_code`,
    //                     '無' AS `book_no`,
    //                     `keyin_mdate`
    //                 FROM `mssr_book_library`
    //                 WHERE 1=1
    //                     -- FILTER在此
    //                     {$filter}
    //             ";
    //             if(trim($other_school_code)!==''){
    //                 $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //             }else{
    //                 $query_sql.="AND `school_code`='{$sess_school_code}'";
    //             }
    //             $filter2=trim(preg_replace("/AND `book_library_code` = .*/",'',$filter2));
    //             $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
    //             if(!empty($tmp_results)){
    //                 foreach($tmp_results as $tmp_result){
    //                     $tmp_total_results[]=$tmp_result;
    //                 }
    //             }
    //             if($filter2!==''){
    //                 $query_sql ="";
    //                 $query_sql.="
    //                     SELECT
    //                         'mssr_book_class' AS `book_type`,
    //                         `book_sid`,
    //                         `book_isbn_10`,
    //                         `book_isbn_13`,
    //                         '' AS `book_library_code`,
    //                         `book_no`,
    //                         `keyin_mdate`
    //                     FROM `mssr_book_class`
    //                     WHERE 1=1
    //                         -- FILTER在此
    //                         {$filter2}
    //                 ";
    //                 if(trim($other_school_code)!==''){
    //                     $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //                 }else{
    //                     $query_sql.="AND `school_code`='{$sess_school_code}'";
    //                 }
    //                 $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
    //                 if(!empty($tmp_results)){
    //                     foreach($tmp_results as $tmp_result){
    //                         $tmp_total_results[]=$tmp_result;
    //                     }
    //                 }
    //             }
    //             $arrys_chunk =array_chunk($tmp_total_results,$psize);
    //             $arrys_result=$arrys_chunk[$pinx-1];
    //         }else{
    //             $has_search_cno=$psize*$pinx;
    //             $query_sql ="";
    //             $query_sql.="
    //                 SELECT
    //                     'mssr_book_class' AS `book_type`,
    //                     `book_sid`,
    //                     `book_isbn_10`,
    //                     `book_isbn_13`,
    //                     '' AS `book_library_code`,
    //                     `book_no`,
    //                     `keyin_mdate`
    //                 FROM `mssr_book_class`
    //                 WHERE 1=1
    //                     -- FILTER在此
    //                     {$filter}
    //             ";
    //             if(trim($other_school_code)!==''){
    //                 $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //             }else{
    //                 $query_sql.="AND `school_code`='{$sess_school_code}'";
    //             }
    //             $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx,$psize),$arry_conn_mssr);
    //             if(!empty($tmp_results)){
    //                 foreach($tmp_results as $tmp_result){
    //                     $tmp_total_results[]=$tmp_result;
    //                 }
    //             }else{
    //                 $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
    //             }
    //             if($has_search_cno!==($sinx+count($tmp_results))){
    //                 if($has_search_cno>$sinx+count($tmp_results)){
    //                     $sinx2=0;
    //                     $einx2=$psize-count($tmp_results);
    //                 }else{
    //                     $sinx2=$sinx-count($tmp_results);
    //                     $einx2=$psize;
    //                 }
    //                 $query_sql ="";
    //                 $query_sql.="
    //                     SELECT
    //                         'mssr_book_library' AS `book_type`,
    //                         `book_sid`,
    //                         `book_isbn_10`,
    //                         `book_isbn_13`,
    //                         `book_library_code`,
    //                         '無' AS `book_no`,
    //                         `keyin_mdate`
    //                     FROM `mssr_book_library`
    //                     WHERE 1=1
    //                         -- FILTER在此
    //                         {$filter}
    //                 ";
    //                 if(trim($other_school_code)!==''){
    //                     $query_sql.="AND `school_code` IN ('{$sess_school_code}',{$other_school_code})";
    //                 }else{
    //                     $query_sql.="AND `school_code`='{$sess_school_code}'";
    //                 }
    //                 $tmp_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx2,$einx2),$arry_conn_mssr);
    //                 if(!empty($tmp_results)){
    //                     foreach($tmp_results as $tmp_result){
    //                         $tmp_total_results[]=$tmp_result;
    //                     }
    //                 }
    //             }
    //             $arrys_result=$tmp_total_results;
    //         }
    //     }
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

            // $auth_sys_name_arry=auth_sys_name_arry();
            // $FOLDER=explode('/',dirname($_SERVER['PHP_SELF']));
            // $sys_ename=$FOLDER[count($FOLDER)-2];
            // $mod_ename=$FOLDER[count($FOLDER)-1];
            // $sys_cname='';  //系統名稱
            // $mod_cname='';  //模組名稱

            // foreach($auth_sys_name_arry as $key=>$val){
            //     if($key==$sys_ename){
            //         $sys_cname=$val;
            //     }elseif($key==$mod_ename){
            //         $mod_cname=$val;
            //     }
            // }

            // if((trim($sys_cname)=='')||(trim($mod_cname)=='')){
            //     $err ='teacher_center_path err!';

            //     if(1==2){//除錯用
            //         echo "<pre>";
            //         print_r($err);
            //         echo "</pre>";
            //         die();
            //     }
            // }

            //連結路徑
            // $sys_url ="";
            // $sys_page=str_repeat("../",2)."index.php";
            // $sys_arg =array(
            //     'sys_ename'  =>addslashes($sys_ename)
            // );
            // $sys_arg=http_build_query($sys_arg);
            // $sys_url=$sys_page."?".$sys_arg;
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
<form action="php/insert_book_level.php" method="get">
        <!-- 內容區塊 開始 -->
    <div id="content">
            <div class="form_div">
                <h1>登記書籍表單</h1>
            </div>
            <div id="about_qa">
         
                    <div class="qa_div" id="isbn_input" >
                            <div class="title_div">
                                <span>1.請填此書的ISBN或圖書館編號:</span><span class="star"  >*</span>
                            </div>
                            <div class="options_div" id="isbn_option">
                                <input type="text" name="isbn" id="isbn" onblur="find_book()">
                            </div>
                    </div>


                    
                    <div class="qa_div">
                            <div class="title_div">
                                <span>2.此本書為</span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="radio" id="ch_book" name="language" value="1" > <label for="ch_book">中文書</label>
                                <input type="radio" id="eng_book" name="language" value="2" > <label for="eng_book">英文書</label>
                                <input type="radio" id="ch_eng_book" name="language" value="3" > <label for="ch_eng_book">中英文混和書</label>
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>3.是否有注音</span><span class="star">*</span>
                            </div>
                             <div class="options_div">
                                <input type="radio"  name="bopomofo" id="yes" value="1" ><label for="yes">有</label>
                                <input type="radio"  name="bopomofo" id="no" value="2" > <label for="no">無</label>
                            </div>
                    </div>
                    <div class="qa_div" id="major_topic">
                        <div class="title_div">
                            <span>4.請選擇適合此書的大主題</span><span class="star">*</span>
                        </div>
                        <div class="options_div">
                            <input type="checkbox" id="" name="major_topic[]" value="1"> <label for="yes">生活</label>
                            <input type="checkbox" id="" name="major_topic[]" value="2"> <label for="yes">科學</label>  
                            <input type="checkbox" id="" name="major_topic[]" value="3"> <label for="yes">史地</label>           
                            <input type="checkbox" id="" name="major_topic[]" value="4"> <label for="yes">文學</label> 
                            <input type="checkbox" id="" name="major_topic[]" value="5"> <label for="yes">藝術</label>     
                            <input type="checkbox" id="other1" name="major_topic[]" value="6"> <label for="other1">其他</label><input type="text" name="other1" id="other_input1"><br>
                        </div>
                    </div>
                    <div class="qa_div" id="sub_topic">
                        <div class="title_div">  
                            <span>5.請選擇適合此書的中主題</span><span class="star">*</span>
                        </div>
                        <div class="options_div">
                            <input type="checkbox" id="character" name="sub_topic[]" value="1"> <label for="character">品格</label>
                            <input type="checkbox" id="health" name="sub_topic[]" value="2"><label for="health"> 健康</label>
                            <input type="checkbox" id="financial" name="sub_topic[] " value="3"><label for="financial">理財</label>          
                            <input type="checkbox" id="energy" name="sub_topic[]" value="4"> <label for="energy">能源</label>
                            <input type="checkbox" id="technology" name="sub_topic[]" value="5"> <label for="technology">科技</label>
                            <input type="checkbox" id="mathematics" name="sub_topic[]" value="6"> <label for="mathematics">數學</label>       
                            <input type="checkbox" id="biological" name="sub_topic[]" value="7"> <label for="biological">生物</label>   
                            <input type="checkbox" id="earth_science" name="sub_topic[]" value="8"> <label for="earth_science">地科</label>  
                            <input type="checkbox" id="geography" name="sub_topic[]" value="9"> <label for="geography">地理</label>   
                            <input type="checkbox" id="history" name="sub_topic[]" value="10"> <label for="energy">歷史</label>   
                            <input type="checkbox" id="proverb" name="sub_topic[]" value="11"> <label for="proverb">成語</label>
                            <input type="checkbox" id="chinese_studies" name="sub_topic[]" value="12"> <label for="chinese_studies">國學</label> 
                            <input type="checkbox" id="novel" name="sub_topic[]" value="13"><label for="novel">小說</label>
                            <input type="checkbox" id="prose" name="sub_topic[]" value="14"> <label for="prose">散文</label>
                            <input type="checkbox" id="poetry" name="sub_topic[]" value="15"> <label for="poetry">詩賦</label>
                            <input type="checkbox" id="visual_arts" name="sub_topic[]" value="16"> <label for="visual_arts">視覺藝術</label>
                            <input type="checkbox" id="music" name="sub_topic[]" value="17"> <label for="music">音樂</label>
                            <input type="checkbox" id="performing_arts" name="sub_topic[]" value="18"> <label for="performing_arts">表演藝術</label>
                            <input type="checkbox" id="fairy_tales" name="sub_topic[]" value="19"> <label for="fairy_tales"> 法律</label>
                            <input type="checkbox" id="other2" name="sub_topic[]" value="20"><label for="other2">其他 </label><input type="text" name="other2" id="other_input2"br>
                        </div>
                    </div>
                    <div class="qa_div" id="minor_topic">
                            <div class="title_div"> 
                                <span>6.請選擇適合此書的小主題 </span><span class="star">*</span><br>
                            </div>
                            <div class="options_div">
                                <input type="checkbox" id="physiology" name="minor_topic[]" value="1"><label for="physiology"> 生理 </label>
                                <input type="checkbox" id="psychology" name="minor_topic[]" value="2"><label for="psychology">  心理 </label>
                                <input type="checkbox" id="sports" name="minor_topic[]" value="3"> <label for="sports">體育</label>          
                                <input type="checkbox" id="chemistry" name="minor_topic[]" value="4"> <label for="chemistry">理化</label>
                                <input type="checkbox" id="computer_technology" name="minor_topic[]" value="5" ><label for="computer_technology"> 電腦科技</label>
                                <input type="checkbox" id="art" name="minor_topic[]" value="6" ><label for="art"> 航太科技</label>       
                                <input type="checkbox" id="animal" name="minor_topic[]" value="7" ><label for="animal">動物 </label>   
                                <input type="checkbox" id="plant" name="minor_topic[]" value="8" ><label for="plant"> 植物 </label>    
                                <input type="checkbox" id="astronomy" name="minor_topic[]" value="9" ><label for="astronomy"> 天文 </label>  
                                <input type="checkbox" id="atmospheric_science" name="minor_topic[]" value="10" ><label for="atmospheric_science"> 大氣科學</label>    
                                <input type="checkbox" id="oceanography" name="minor_topic[]" value="11" ><label for="oceanography">海洋學</label>
                                <input type="checkbox" id="world" name="minor_topic[]" value="12 " > <label for="world"> 世界</label>    
                                <input type="checkbox" id="taiwan" name="minor_topic[]" value="13" > <label for="taiwan"> 台灣 </label>    
                                <input type="checkbox" id="ancient_history" name="minor_topic[]" value="14" > <label for="ancient_history">遠古史</label>
                                <input type="checkbox" id="modern_history" name="minor_topic[]" value="15" ><label for="modern_history">  近代史</label>
                                <input type="checkbox" id="myth" name="minor_topic[]" value="16" > <label for="myth"> 神話</label>
                                <input type="checkbox" id="biography" name="minor_topic[]" value="17" > <label for="biography"> 傳記</label>
                                <input type="checkbox" id="fairy_tales" name="minor_topic[]" value="18" > <label for="fairy_tales"> 童話</label>
                                <input type="checkbox" id="fable" name="minor_topic[]" value="19" > <label for="fable"> 寓言</label>
                                <input type="checkbox" id="calligraphy" name="minor_topic[]" value="20" > <label for="calligraphy"> 書法</label>
                                <input type="checkbox" id="painting" name="minor_topic[]" value="21" > <label for="painting"> 繪畫</label>
                                <input type="checkbox" id="photography" name="minor_topic[]" value="22" > <label for="photography"> 攝影</label>
                                <input type="checkbox" id="dance" name="minor_topic[]" value="23" > <label for="dance">舞蹈</label>
                                <input type="checkbox" id="drama" name="minor_topic[]" value="24" > <label for="drama">戲劇</label>
                                <input type="checkbox" id="other3" name="minor_topic[]" value="25" ><label for="other3">其他 </label><input type="text" name="other3" id="other_input3">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div"> 
                                <span>7.請填此書相關的標籤一: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag1" class="input_val" id="tag1">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">   
                                <span>8.請填此書相關的標籤二: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag2" class="input_val" id="tag2">
                            </div>
                    </div>
                    <div class="qa_div "> 
                            <div class="title_div">  
                                <span>9.請填此書相關的標籤三: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag3" class="input_val" id="tag3">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>10.請填此書相關的標籤四: </span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag4" class="input_val" id="tag4">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">
                                <span>11.請填此書相關的標籤五: </span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="tag5" class="input_val" id="tag5">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="title_div">   
                                <span>12.請填此書的頁數: </span><span class="star">*</span>
                            </div>
                            <div class="options_div">
                                <input type="text" name="pages" class="input_val" id="pages">
                            </div>
                    </div>
                    <div class="qa_div"> 
                            <div class="title_div">   
                                <span>13.請填此書的字數(必填): </span><span class="star">*</span>
                            </div>
                             <div class="options_div">
                                <input type="text" name="words" class="input_val"  id="words" >
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="options_div">
                                <span>14.請填此書的難度等級(填A-Z) (也可以填G-H)(必填): </span><span class="star">*</span>
                            </div>
                            <div>
                                <input type="text" name="hard" class="input_val" id="hard">
                            </div>
                    </div>
                    <div class="qa_div">
                            <div class="options_div">
                                <span>15.英文書的LEXILE分數（含AD, BR....）查不到寫0，中文書也寫0 (https://www.lexile.com)(必填) </span><span class="star">*</span><br>
                            </div>
                            <div>
                                <input type="text" name="level" class="input_val" id="level">
                            </div>
                    </div> 
            </div>
        
            <input type="submit" name="送出" value="送出" id="submit" onclick="check_all_value(); return false;">
        </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 快速切換區塊 開始 -->
</form>
</Body>
<script type="text/javascript">

//從資料庫尋找書籍顯示書籍資料
function find_book(){

    var isbn_val=$("#isbn").val();
    if(isbn_val){

            var url = "./php/get_book_info.php";
            var dataVal = {
                
                user_id:              <?php echo $sess_login_info['uid']?>,
                permission:           <?php echo $sess_login_info['permission']?>,
                school_code:          "<?php echo $sess_login_info['school_code']?>",
                responsibilities:     <?php echo $sess_responsibilities?>,
                class_code:           "<?php echo $sess_class_code?>",
                grade:                "<?php echo $sess_grade?>",
                classroom:            "<?php echo $sess_classroom?>",
                book_isbn:            $('#isbn').val()
            };
                       

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
                        if(data!="[]"){

                                var book_sid= data_array[0]['book_sid'];
                                var book_isbn_10=data_array[0]['book_isbn_10'];
                                var book_isbn_13=data_array[0]['book_isbn_13'];
                                var book_name=data_array[0]['book_name'];
                                var book_library_code=data_array[0]['book_library_code'];
                                var have_log=data_array[0]['have_log'];

                                if(book_isbn_10==="")book_isbn_10=0;
                                if(book_isbn_13==="")book_isbn_13=0;
                                if(book_name==="")book_name=0;
                                if(book_library_code==="")book_library_code=0;
                                if(have_log==="yes"){
                                    $('#isbn').css("display","none");
                                    $('#isbn_input .title_div').html("<h1 style='color:red;'></h1>");
                                    $('#isbn_input .title_div h1').append("此書你登記過了");
                                }else{
                                    $('#isbn').css("display","none");
                                    $('#isbn_input .title_div').html("<h1> 書名:</h1>");
                                    $('#isbn_input .title_div h1').append(book_name);
                                    $('#isbn_option').append('<input type="text" id="book_sid"  name="book_sid" style="display:none;" value="'+book_sid+'">');
                                    $('#isbn_option').append('<input type="text" id="book_isbn_10" name="book_isbn_10"  value="'+book_isbn_10+' ">');
                                    $('#isbn_option').append('<input type="text" id="book_isbn_13" name="book_isbn_13"  value="'+book_isbn_13+' ">');
                                    $('#isbn_option').append('<input type="text" id="book_name" name="book_name"  value="'+book_name+' ">');
                                    $('#isbn_option').append('<input type="text" id="book_library_code" name="book_library_code" value="'+book_library_code+'">');
                                }
                               

                        }else{
                            alert("沒有此isbn的書籍,請先登記書籍");
                        }         
                       
                       },
                       error: function(jqXHR) {
                        alert("發生錯誤: " + jqXHR.status);
                      }

            });

    }
}


//確認那些值沒有填寫

function check_all_value(){

        

        var isbn_val=$("#isbn").val();
        var tag1=$("#tag1").val();
        var tag2=$("#tag2").val();
        var tag3=$("#tag3").val();
        var pages=$("#pages").val();
        var words=$("#words").val();
        var hard=$("#hard").val();
        var level=$("#level").val();
        var major_topic=$("input[name='major_topic[]']:checked").length;
        var sub_topic=$("input[name='sub_topic[]']:checked").length;
        var minor_topic=$("input[name='minor_topic[]']:checked").length;
        
        if(isbn_val===""){
            alert("請填第1題此書的ISBN");
        }else if(language===undefined){
            alert("請填第2題填此書的語言");
        }else if(bopomofo===undefined){
            alert("請填第3題填此書有無注音");
        }else if(major_topic===0){
                alert("請勾選第4題");
        }else if(sub_topic===0){
                alert("請勾選第5題");
        }else if(minor_topic===0){
                alert("請勾選第6題");
        }else if(tag1===""){
             alert("請填第7題填此書相關的標籤一");
        }else if(tag2===""){
             alert("請填第8題填此書相關的標籤二");
        }else if(tag3===""){
            alert("請填第9題填此書相關的標籤三");
        }else if(pages===""){
            alert("請填第12題填此書的頁數");
        }else if(words===""){
            alert("請填第13題填此書的字數");
        }else if(hard===""){
            alert("請填第14題填此書的難度");
        }else if(level===""){
            alert("請填第15題填此英文書LEXILE分數");

        }








    }


//978986317693
</script>
</Html>