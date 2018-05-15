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
        require_once(str_repeat("../",4)."/inc/get_black_book_info/code.php");

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

        
        var_dump($sess_login_info);

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

        echo $sess_login_info['uid'];

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

    <!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
    <link rel="stylesheet" type="text/css" href="../css/book_level.css" />

    <style>
        /* 容器微調 */
   /*      #container, #content, #teacher_datalist_tbl{
            width:760px;
        } */
    </style>
</Head>

<Body>
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
                            <div class="options_div">
                                <span>2.等級 </span><span class="star">*</span><br>
                            </div>
                            <div>
                                <input type="text" name="level" class="input_val" id="level">
                            </div>
                    </div> 
            </div>
        
            <input type="submit" name="送出" value="送出" id="submit" onclick="level_info();">
        </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 快速切換區塊 開始 -->
</Body>
<script type="text/javascript">

  

//從資料庫尋找書籍顯示書籍資料
function find_book(){

    var isbn_val=$("#isbn").val();
    console.log(isbn_val);

    if(isbn_val){

            var url = "./ajax/get_book_info.php";
 
            var dataVal = {
                
                user_id:              <?php echo $sess_login_info['uid']?>,
                permission:           <?php echo $sess_login_info['permission']?>,
                school_code:          "<?php echo $sess_login_info['school_code']?>",
                responsibilities:     <?php echo $sess_responsibilities?>,
                class_code:           "<?php echo $sess_class_code?>",
                grade:                "<?php echo $sess_grade?>",
                classroom:            "<?php echo $sess_classroom?>",
                book_isbn:            isbn_val
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
                        console.log(data_array);
                        if(data!="[]"){

                                var book_sid= data_array[0]['book_sid'];
                                var book_isbn_10=data_array[0]['book_isbn_10'];
                                var book_isbn_13=data_array[0]['book_isbn_13'];
                                var book_name=data_array[0]['book_name'];
                                var book_library_code=data_array[0]['book_library_code'];
                                var msg=data_array[0]['msg'];

                                if(book_sid==="")book_sid=0;
                                if(book_isbn_10==="")book_isbn_10=0;
                                if(book_isbn_13==="")book_isbn_13=0;
                                if(book_name==="")book_name=0;
                                if(book_library_code==="")book_library_code=0;
                                if(msg!=""){
                                    
                                    alert("書有多筆資料,資料庫有問題");

                                                                
                                    
                                }else{
                                   
                                    $('#isbn_option').append("<span id='ps'> 書名:"+book_name+"</span>");
                                    
                                    $('#isbn_option').append('<input type="hidden" id="book_sid"  name="book_sid" value="'+book_sid+'">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_10" name="book_isbn_10" value="'+book_isbn_10+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_isbn_13" name="book_isbn_13" value="'+book_isbn_13+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_name" name="book_name" value="'+book_name+' ">');
                                    $('#isbn_option').append('<input type="hidden" id="book_library_code" name="book_library_code" value="'+book_library_code+'">');
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



    //資料輸入至資料庫
    function level_info(){

        var book_sid=$("#book_sid").val();
        var book_isbn_10=$("#book_isbn_10").val();
        var book_isbn_13=$("#book_isbn_13").val();
        var book_library_code=$("#book_library_code").val();
        var level=$('#level').val();


      var url = "./ajax/insert_level.php";
      var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            book_sid:             book_sid,
            book_isbn_10:         book_isbn_10,
            book_isbn_13:         book_isbn_13,
            book_library_code:    book_library_code,
            level:                level
          
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,  
                   success: function(data) {
                    window.location.href="finish.php";
                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });





    }


//978986317693
</script>
</Html>