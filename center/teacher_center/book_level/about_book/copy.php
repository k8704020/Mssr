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
<?php
          //-----------------------------------------------
        //處理
        //-----------------------------------------------

        $array_output=array();
        $topic_options=array();
        $array_book_sid=array();


        //======================
        //第二題題目選項(書的語言)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                     WHERE topic_id ='2'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic2_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第三題題目選項(是否有注音)
        //======================
        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='3'

                ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic3_options']=unserialize($value['topic_options']);
            }
            
            // print_r($topic_options[$key]['topic3_options'][0]);
         }

        //======================
        //第四題題目選項(大主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='4'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic4_options']=unserialize($value['topic_options']);
            }
            
           
         }

        //======================
        //第五題題目選項(中主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='5'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic5_options']=unserialize($value['topic_options']);
            }
            
           
        }
        //======================
        //第六題題目選項(小主題)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='6'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic6_options']=unserialize($value['topic_options']);
            }
            
           
        }

        //======================
        //第十四題題目選項(難度等級)
        //======================

        $qa_sql ="
                    SELECT topic_id ,topic_options
                    FROM `mssr_idc_reading_log_topic`
                    WHERE topic_id ='14'

                 ";

        $qa_result=db_result($conn_type='pdo',$conn_mssr,$qa_sql,array(),$arry_conn_mssr);

        if(!empty($qa_result)){
            foreach ($qa_result as $key => $value) {
                $topic_options[$key]['topic14_options']=unserialize($value['topic_options']);
                
            }
            
           
         }



//----------------------------------------------------------

        //======================
        //表單登記的書
        //======================


        $book_sql="
              SELECT 
                    book_sid,
                    book_isbn_10,
                    book_isbn_13,
                    book_library_code
                                      
               FROM `mssr_idc_reading_log_spreadsheet` 
               GROUP BY  `book_sid` 
               ORDER BY book_sid DESC 
                        
        ";

        $book_sql_result=db_result($conn_type='pdo',$conn_mssr,$book_sql,array(),$arry_conn_mssr);


        foreach ($book_sql_result as $key => $value) {
            // echo "key";
            // print_r($key);
            // echo "<br>";

                  $array_output[$key]['book_sid']          =trim($value['book_sid']);
                  $array_output[$key]['book_isbn_13']        =trim($value['book_isbn_13']);
                  $array_output[$key]['book_isbn_10']        =trim($value['book_isbn_10']);
                  $array_output[$key]['book_library_code']   =trim($value['book_library_code']);
               
                  //===================
                  //尋找書名
                  //===================
                   $sql="
                        SELECT 
                     
                             IFNULL(`book_name`,0) as `book_name` 
                             
                        FROM `mssr_book_class`
                        WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='test'

                        union 
                        
                        SELECT 
                        
                       
                            IFNULL(`book_name`,0) as `book_name`
                        
                             
                        FROM `mssr_book_library`
                         WHERE `book_sid` = '{$array_output[$key]['book_sid']  }'
                        AND school_code='test'


                        
                    ";

                    $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);


                    if(!empty($result)){
                              
                             $array_output[$key]['book_name'] =trim($result[0]['book_name']);                    
                                    
                     }

                  $log_sql="

                        SELECT 
                                user_id,
                                book_language,
                                bopomofo, 
                                major_topic,
                                sub_topic,
                                minor_topic,
                                tag1,
                                tag2,
                                tag3,
                                tag4,
                                tag5,
                                pages,
                                words,
                                hard_level,
                                one_person_level,
                                lexile_level
                                      
                        FROM `mssr_idc_reading_log_spreadsheet`
                        WHERE `book_sid`='{$value['book_sid']}'             
                        order by book_sid desc

                    ";


                    $log_result=db_result($conn_type='pdo',$conn_mssr,$log_sql,array(),$arry_conn_mssr);

                    // print_r($log_result);




                    if(!empty($log_result)){
                            
                            foreach ($log_result as $index_key => $array_log) {
                                      //   echo "<br>";
                                      //  echo "index_key:";
                                      // print_r($array_log);
                                      //   echo "<br>";
                                    $array_output[$key]['book_language'][$index_key] =trim($array_log['book_language']);
                                    $array_output[$key]['bopomofo'][$index_key] =trim($array_log['bopomofo']);
                                    $array_output[$key]['tag1'][$index_key] =trim($array_log['tag1']);
                                    $array_output[$key]['tag2'][$index_key]=trim($array_log['tag2']);
                                    $array_output[$key]['tag3'][$index_key]=trim($array_log['tag3']);
                                    $array_output[$key]['tag4'][$index_key]=trim($array_log['tag4']);
                                    $array_output[$key]['tag5'][$index_key]=trim($array_log['tag5']);
                                    $array_output[$key]['pages'][$index_key]=trim($array_log['pages']);
                                    $array_output[$key]['words'][$index_key]=trim($array_log['words']);
                                     $array_output[$key]['one_person_level'][$index_key] =trim($array_log['one_person_level']);
                                    $array_output[$key]['lexile_level'][$index_key] =trim($array_log['lexile_level']);
                                       
                                        // ======================
                                        // 尋找誰寫出這筆資料 
                                        // ======================
                                         
                                    $user_sql="
                                                SELECT name
                                                FROM  `member`  
                                                WHERE  uid='{$array_log['user_id']}'   
                                                " ; 
                                    $user_result=db_result($conn_type='pdo',$conn_user,$user_sql,array(),$arry_conn_user);
                                    if(!empty($user_result)){

                                            $array_output[$key]['name'][$index_key]=trim($user_result[0]['name']);
                                                                       
                                    }else{

                                            $array_output[$key]['name'][$index_key]="0";
                                                

                                    }


                                    // // //======================
                                    // // //尋找貼紙編號及貼紙顏色
                                    // // //======================

                                    $sticker_sql="
                                                SELECT `sticker_color`,`sticker_number` 
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                                    ";

                                    $sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);

                                    if(!empty($sticker_result)){
                                          

                                                $array_output[$key]['sticker_number']=trim($sticker_result[0]['sticker_number']);


                                                $sticker_color_sql="
                                                    SELECT color
                                                    FROM  `mssr_idc_book_sticker_color`
                                                    WHERE  color_id='{$sticker_result[0]['sticker_color']}'   
                                                " ; 

                                                $sticker_color_result=db_result($conn_type='pdo',$conn_mssr,$sticker_color_sql,array(),$arry_conn_mssr);
                                                  
                                                if(!empty($sticker_color_result)){

                                                        $array_output[$key]['sticker_color'] =trim($sticker_color_result[0]['color']);
                                                }else{
                                                    $array_output[$key]['sticker_color'] ="0";
                                                }
                                    }else{
                                            $array_output[$key]['sticker_color']="0";
                                            $array_output[$key]['sticker_number']="0";
                                    }


                                    // //======================
                                    // //書本的語言
                                    // //======================
                                    $array_output[$key]['book_language'][$index_key]=$topic_options[0]['topic2_options'][$array_log['book_language']];

                                    // //======================
                                    // //是否有注音
                                    // //======================
                                    $array_output[$key]['bopomofo'][$index_key]=$topic_options[0]['topic3_options'][$array_log['bopomofo']];
                                   
                                    //======================
                                    //書本的大主題
                                    //======================
                                    if($array_log['major_topic']){
                                        $major_topic=explode(",",$array_log['major_topic']);
                                        
                                        foreach ($major_topic as $index => $array_major) {

                                            $major_topic[$index]=$topic_options[0]['topic4_options'][$array_major];
                                            $array_output[$key]['major_topic'][$index_key]=$major_topic;

                                        }
                                    }

                                    //======================
                                    //書本的中主題
                                    //======================
                                    if($array_log['sub_topic']){
                                        $sub_topic=explode(",",$array_log['sub_topic']);
                                        
                                        foreach ($sub_topic as $index => $array_major) {

                                            $sub_topic[$index]=$topic_options[0]['topic5_options'][$array_major];
                                            $array_output[$key]['sub_topic'][$index_key]=$sub_topic;

                                        }
                                    }
                                    // //======================
                                    // //書本的小主題
                                    // //======================
                  
                                    if($array_log['minor_topic']){
                                            $minor_topic=explode(",",$array_log['minor_topic']);
                                            foreach ($minor_topic as $index => $array_minor) {

                                                $minor_topic[$index]=$topic_options[0]['topic6_options'][$array_minor];
                                                $array_output[$key]['minor_topic'][$index_key]=$minor_topic;

                                            }
                                     }
                                    // //======================
                                    // //單人等級
                                    // //======================
                                    if($array_log['hard_level']){
                                        $hard_level=explode("-",$array_log['hard_level']);
                                        foreach ($hard_level as $index => $array_hard) {

                                            $hard_level[$index]=$topic_options[0]['topic14_options'][$array_hard];
                                            $array_output[$key]['hard_level'][$index_key]=$hard_level;

                                        }
                                    }

                                    // //======================
                                    // //平均等級
                                    // //======================

                                    if($array_output[$key]['book_sid']){

                                                    $avg_sql="

                                                        SELECT *
                                                        FROM  `mssr_idc_book_sticker_level_info` 
                                                        WHERE `book_sid`='{$array_output[$key]['book_sid']}'           
                                                        
                                                        
                                                    ";
                                                    
                                                    
                                                    $avg_result=db_result($conn_type='pdo',$conn_mssr,$avg_sql,array(),$arry_conn_mssr);

                                                    if($avg_result[0]['need_read_again']==="0"){

                                                                $array_output[$key]['avg_level']="{$avg_result[0]['avg_level']}";

                                                                $level_sql="

                                                                     SELECT one_person_level
                                                                     FROM `mssr_idc_reading_log_spreadsheet` 
                                                                     WHERE `book_sid`='{$array_output[$key]['book_sid']}'           
                                                                
                                                                ";

                                                                $level_result=db_result($conn_type='pdo',$conn_mssr,$level_sql,array(),$arry_conn_mssr);

                                                                

                                                                if(count($level_result)<2){

                                                                    $array_output[$key]['about_level']="{$level_result[0]['one_person_level']}";

                                                                }else{
                                                                    $one=$level_result[0]['one_person_level'];
                                                                    $two=$level_result[1]['one_person_level'];

                                                                    if($one>$two){
                                                                         $about_level=$one-$two;
                                                                    }else if($two>$one){
                                                                         $about_level=$two-$one;
                                                                    }else{
                                                                        $about_level=$one;


                                                                    }

                                                                    $array_output[$key]['about_level']="{$about_level}";
                                                                   




                                                                }


                                                    }else{



                                                         $avg_sql="

                                                             SELECT avg_level                                  
                                                             FROM `mssr_idc_read_again_info_log` 
                                                             WHERE `book_sid`='{$array_output[$key]['book_sid']}'
                                                             AND  need_read_again='1'         
                                                        
                                                        
                                                        ";
                                                        

                                                        $avg_result=db_result($conn_type='pdo',$conn_mssr,$avg_sql,array(),$arry_conn_mssr);

                                                        

                                                        if(!empty($avg_result)){

                                                             $array_output[$key]['avg_level']=$avg_result[0]['avg_level'];
                                                        }

                                                       

                                                        $avg_sql1="

                                                             SELECT avg_level                                  
                                                             FROM `mssr_idc_read_again_info_log` 
                                                             WHERE `book_sid`='{$array_output[$key]['book_sid']}'
                                                             AND  need_read_again='2'         
                                                        
                                                        
                                                        ";

                                                        

                                                        $avg_result1=db_result($conn_type='pdo',$conn_mssr,$avg_sql1,array(),$arry_conn_mssr);

                                                        

                                                        if(!empty($avg_result1)){
                                                            $array_output[$key]['avg_level_1']=$avg_result1[0]['avg_level'];
                                                        }

                                                        

                                                    }

                                    }

                                    

                                    // //======================
                                    // //等級小於6或大於6
                                    // //======================


                                    // // //======================
                                    // // //尋找是否已閱
                                    // // //======================

                                    $read_sql="
                                                SELECT `status`,`need_read_again`
                                                FROM `mssr_idc_book_sticker_level_info`
                                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                                    ";

                                    $result=db_result($conn_type='pdo',$conn_mssr,$read_sql,array(),$arry_conn_mssr);

                                    if(!empty($result)){
                                          
                                            $array_output[$key]['status']=trim($result[0]['status']);
                                            $array_output[$key]['need_read_again'] =trim($result[0]['need_read_again']);
                                                
                                    }else{
                                            $array_output[$key]['status']="0";
                                            $array_output[$key]['need_read_again']="0";
                                    }



                            }
                              
                                
                    }

                
           
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
    <link rel="stylesheet" type="text/css" href="../css/reading_book.css" />

    <style>
    *{
        font-size: 13px;
        font-family: 微軟正黑體;
    }
    #pt_table,#level_table_over6,#level_table_less6,#read_again_table,#topic_table,#tag_table,#pages_table,#read_table{

        text-align: center;
        margin: 30px auto;
        display: none;

    }
    #pt_table table{
        text-align: center;
        margin: 30px auto;

    }


</style>
</Head>

<Body>
        <!-- 內容區塊 開始 -->
    <div id="content">
            <div class="about_title_div">
                <h1 id="title">書籍相關總覽</h1>
            </div>
            <div class="buttons">
                
                
                 <a href="more_six.php"><input type="button" id="level_6" value="等級大於6書表"></a>
                 <a href="less_six.php"><input type="button" id="level_0" value="等級小於6書表"></a>
                 <a href="need_read_again.php"><input type="button" id="read_again" value="需重看書籍表"></a>
                 <a href="pt_work.php"><input type="button" id="pt" value="工讀生工作表" ></a>
                 <a href="read.php"><input type="button" id="read" value="已閱表"></a>
                 <a href="topic.php"><input type="button" id="topic" value="書本主題表"></a>
                 <a href="tag.php"><input type="button" id="tag" value="書本標籤表"></a>
                 <a href="pages.php"><input type="button" id="pages" value="書本頁數表" ></a>

            </div>
            
            <div id="level_table_over6">
                <table id="level" border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                            <tr>
                                <td>已閱</td>
                                <td>書名</td> 
                                <td>isbn10碼</td>
                                <td>isbn13碼</td>
                                <td>圖書館編號</td>
                                <td>貼紙顏色</td>
                                <td>貼紙編號</td>
                                <td>難度等級</td>
                                <td>等級平均</td>
                                <td>學生姓名</td>
                                <td>需要重看</td>
                                
                            </tr>   
                       
                <?php foreach ($array_output as $key => $value) { ?>
                        <?php if($value['status']<="2"&&$value['need_read_again']==='0'&& $value['about_level']>6){?>
                            <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                        <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">
                                        <td class="read_checkbox" rowspan="2" style="height:40px;">
                                            <input type="checkbox" id="read_checkbox_<?php echo $key?>">
                                        </td>
                                        <td rowspan="2" id="book_name" style="height: 40px; width: 250px;" title="<?php echo trim($value['book_name']) ?> ">           
                                            <?php 
                                                $str=substr(trim($value['book_name']),0,45); 
                                                if(mb_strlen($str, "utf-8")>8){
                                                echo  $str,'....';
                                                } else{
                                                    echo $str;
                                                }
                                            ?>
                                        </td>
                                        <td rowspan="2" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                        <td rowspan="2" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                        <td rowspan="2" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                        <td rowspan="2" id="sticker_color" style="height: 40px;">
                                             <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                  }else{
                                                    echo $value['sticker_color'];
                                                   } ?>
                                            
                                        </td>
                                        <td rowspan="2" id="sticker_number" style="height:40px;"> 

                                                <?php if($value['sticker_number']==="0"){
                                                    echo  $value['sticker_number']="";
                                                  }else{
                                                    echo $value['sticker_number'];
                                                   } ?>
                                                
                                        </td>
                                        <td style="height:20px;">
                                            <?php
                                              if(!empty($value['hard_level'][0])){
                                                echo $value['hard_level'][0][0]."-".$value['hard_level'][0][1];
                                               }
                                            ?>
                                            
                                        </td>
                                        <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                            
                                            <div class="avg">
                                                <span class="avg_level" id="avg_level_<?php echo $key?>"><?php echo $value['avg_level'];?></span>
                                                <input type="text" value="<?php echo $value['avg_level'];?>" class="input_text" id="change_text_<?php echo $key?>" style="display: none;width: 30px;" size="3" maxlength="3"  >
                                                <input type="button" value="修改"  id="change_btn_<?php echo $key?>">
                                            </div>

                                        </td>
                                           
                                        <td style="height:20px;"> 
                                            <?php
                                              if(!empty($value['name'][0])){
                                                    echo $value['name'][0];
                                            }?>
                                                
                                        </td>
                                       
                                        <td class="read_again_checkbox" rowspan="2" style="height: 40px;">  <input type="checkbox" id="read_again_checkbox_<?php echo $key?>"></td>
                                
                            </tr>
                            <tr class="data_content" id="data_two_<?php echo $key?>">
                                        <td style="height: 20px;"> 
                                            <?php
                                             if(!empty($value['hard_level'][1])){
                                             echo $value['hard_level'][1][0]."-".$value['hard_level'][1][1]; }
                                            ?>
                                                
                                        </td>
                                        <td style="height: 20px;"> 
                                            <?php
                                              if(!empty($value['name'][1])){
                                                echo $value['name'][1];
                                            }?>
                                            
                                        </td>
                            </tr>

                        <?php }?>
                <?php } ?>
                       
                </table>
            
            </div>
            <div id="level_table_less6">
                <table id="level" border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                            <tr>
                                <td>已閱</td>
                                <td>書名</td> 
                                <td>isbn10碼</td>
                                <td>isbn13碼</td>
                                <td>圖書館編號</td>
                                <td>貼紙顏色</td>
                                <td>貼紙編號</td>
                                <td>難度等級</td>
                                <td>等級平均</td>
                                <td>學生姓名</td>
                                <td>需要重看</td>
                                
                            </tr>   
                       
                <?php foreach ($array_output as $key => $value) { ?>
                        <?php if($value['status']<="2"&&$value['need_read_again']==='0'&& $value['about_level']<=6){?>
                            <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                        <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">
                                        <td class="read_checkbox" rowspan="2" style="height:40px;">
                                            <input type="checkbox" id="read_checkbox_<?php echo $key?>">
                                        </td>
                                        <td rowspan="2" id="book_name" width="250" style="height: 40px;" title="<?php echo trim($value['book_name'])?>">
                                            <?php 
                                                $str=substr(trim($value['book_name']),0,45); 
                                                if(mb_strlen($str, "utf-8")>8){
                                                echo  $str,'....';
                                                } else{
                                                    echo $str;
                                                }
                                            ?>
                                            
                                        </td>
                                        <td rowspan="2" id="isbn_10" width="80" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                        <td rowspan="2" id="isbn_13" width="80" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                        <td rowspan="2" id="book_library_code" width="80" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                        <td rowspan="2" id="sticker_color" width="40" style="height: 40px;">
                                             <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                  }else{
                                                    echo $value['sticker_color'];
                                                   } ?>
                                            
                                        </td>
                                        <td rowspan="2" id="sticker_number" width="40" style="height:40px;"> 

                                                <?php if($value['sticker_number']==="0"){
                                                    echo  $value['sticker_number']="";
                                                  }else{
                                                    echo $value['sticker_number'];
                                                   } ?>
                                                
                                        </td>
                                        <td style="height:20px;">
                                            <?php
                                              if(!empty($value['hard_level'][0])){
                                                echo $value['hard_level'][0][0]."-".$value['hard_level'][0][1];
                                               }
                                            ?>
                                            
                                        </td>
                                        <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                            
                                            <div class="avg">
                                                <span class="avg_level" id="avg_level_<?php echo $key?>"><?php echo $value['avg_level'];?></span>
                                                <input type="text" value="<?php echo $value['avg_level'];?>" class="input_text" id="change_text_<?php echo $key?>" style="display: none;width: 30px;" size="3" maxlength="3"  >
                                                <input type="button" value="修改"  id="change_btn_<?php echo $key?>">
                                            </div>

                                        </td>
                                           
                                        <td style="height:20px;"> 
                                            <?php
                                              if(!empty($value['name'][0])){
                                                    echo $value['name'][0];
                                            }?>
                                                
                                        </td>
                                       
                                        <td class="read_again_checkbox" rowspan="2" style="height: 40px;">  <input type="checkbox" id="read_again_checkbox_<?php echo $key?>"></td>
                                
                            </tr>
                            <tr class="data_content" id="data_two_<?php echo $key?>">
                                        <td style="height: 20px;"> 
                                            <?php
                                             if(!empty($value['hard_level'][1])){
                                             echo $value['hard_level'][1][0]."-".$value['hard_level'][1][1]; }
                                            ?>
                                                
                                        </td>
                                        <td style="height: 20px;"> 
                                            <?php
                                              if(!empty($value['name'][1])){
                                                echo $value['name'][1];
                                            }?>
                                            
                                        </td>
                            </tr>

                        <?php }?>
                <?php } ?>
                       
                </table>
            
            </div>
            <!--需要重看的表-->
            <div id="read_again_table">
                    <table border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                             <tr>
                               
                                <td>書名</td> 
                                <td>isbn10碼</td>
                                <td>isbn13碼</td>
                                <td>圖書館編號</td>
                                <td>貼紙顏色</td>
                                <td>貼紙編號</td>
                                <td>難度等級</td>
                                <td>等級平均</td>
                                <td>學生姓名</td>
                                <td>最後等級</td>
                                
                            </tr>   
                            <?php foreach ($array_output as $key => $value) { ?>
                                <?php if($value['status']<"3"&&$value['need_read_again']==='1'){?>

                                    <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                                <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">

                                                <td rowspan="4" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?>">         
                                                    <?php 
                                                        $str=substr(trim($value['book_name']),0,30); 
                                                        if(mb_strlen($str, "utf-8")>8){
                                                        echo  $str,'...';
                                                        } else{
                                                            echo $str;
                                                        }
                                                    ?>
                                                </td>
                                                <td rowspan="4" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                                <td rowspan="4" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                                <td rowspan="4" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                                <td rowspan="4" id="sticker_color" style="height: 40px;">
                                                     <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                          }else{
                                                            echo $value['sticker_color'];
                                                           } ?>
                                                    
                                                </td>
                                                <td rowspan="4" id="sticker_number" style="height:40px;"> 

                                                        <?php if($value['sticker_number']==="0"){
                                                            echo  $value['sticker_number']="";
                                                          }else{
                                                            echo $value['sticker_number'];
                                                           } ?>
                                                        
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['hard_level'][0])){
                                                        echo $value['hard_level'][0][0]."-".$value['hard_level'][0][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                                    
                                                    <div class="avg">
                                                        <span class="avg_level" id="avg_level_<?php echo $key?>"><?php echo $value['avg_level'];?></span>
                                                       
                                                    </div>

                                                </td>
                                                   
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][0])){
                                                            echo $value['name'][0];
                                                    }?>
                                                        
                                                </td>
                                               
                                                <td class="read_again_checkbox" rowspan="4" style="height: 40px;"> 

                                                    <div class="save">
                                                   <!--      <?php if(!empty($value['avg_level'])){?>
                                                            <input type="checkbox" value="<?php echo $value['avg_level'];?>" class="input_ckb" id="change_1_<?php echo $key?>" width: 30px;" size="3" maxlength="3"><?php echo $value['avg_level'];?>
                                                        <?php }?>
                                                        <?php if(!empty($value['avg_level_1'])){?>
                                                            <input type="checkbox" value="<?php echo $value['avg_level_1'];?>" class="input_ckb" id="change_2_<?php echo $key?>" width: 30px;" size="3" maxlength="3"><?php echo $value['avg_level_1'];?>
                                                        <?php }?> -->
                                                        <input type="text"  class="input_text" id="text_<?php echo $key?>" width: 30px;" size="3" maxlength="3"  >

                                                        <input type="button" value="儲存" id="save_btn_<?php echo $key?>">  
                                                    </div>

                                                </td>
                                        
                                    </tr>
                                    <tr class="data_content" id="data_two_<?php echo $key?>">
                                                <td style="height: 20px;"> 
                                                    <?php
                                                     if(!empty($value['hard_level'][1])){
                                                     echo $value['hard_level'][1][0]."-".$value['hard_level'][1][1]; }
                                                    ?>
                                                        
                                                </td>
                                                <td style="height: 20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                        echo $value['name'][1];
                                                    }?>
                                                    
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_three_<?php echo $key?>">
                                        
                                                <td style="height: 20px;"> 
                                                    <?php
                                                     if(!empty($value['hard_level'][2])){
                                                     echo $value['hard_level'][2][0]."-".$value['hard_level'][2][1]; }
                                                    ?>
                                                        
                                                </td>
                                                <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                                    
                                                    <div class="avg">
                                                        <span class="avg_level" id="avg_level_1_<?php echo $key?>"><?php if(!empty($value['avg_level_1'])){echo $value['avg_level_1'];}?></span>
                                                       
                                                    </div>

                                                </td>
                                                <td style="height: 20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][2])){
                                                        echo $value['name'][2];
                                                    }?>
                                                    
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_four_<?php echo $key?>">
                                                <td style="height: 20px;"> 
                                                    <?php
                                                     if(!empty($value['hard_level'][3])){
                                                     echo $value['hard_level'][3][0]."-".$value['hard_level'][3][1]; }
                                                    ?>
                                                        
                                                </td>
                                                <td style="height: 20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][3])){
                                                        echo $value['name'][3];
                                                    }?>
                                                    
                                                </td>
                                    </tr>



                                <?php }?>
                            <?php } ?>
                       
                </table>
            
            </div>
            <!--工讀生的表-->
            <div id="pt_table">
                 <div style="text-align: center;">
                    <label for="bookdate">起始日期：</label>
                    <input type="date" id="date_start" >
                    <label for="bookdate">結束日期：</label>
                    <input type="date" id="date_end">
                    <input type="button" name="查詢" value="查詢" onclick="search()">
                </div>
                <table  class="table_about_pt" border="1" cellpadding="5"  >
                        <tr>
                            <td>
                                工讀生姓名
                            </td>
                            <td>
                                登記過幾本書
                            </td>
                        </tr>

                </table>
            </div>
            <!--已閱的表-->
            <div id="read_table">
               <table border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                             <tr>
                               
                                <td>書名</td> 
                                <td>isbn10碼</td>
                                <td>isbn13碼</td>
                                <td>圖書館編號</td>
                                <td>貼紙顏色</td>
                                <td>貼紙編號</td>
                                <td>等級平均</td>
 
                                
                            </tr>   
                            <?php foreach ($array_output as $key => $value) { ?>
                                <?php if($value['status']==="3"){?>

                                    <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                                <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">

                                                <td rowspan="4" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?>">         
                                                    <?php 
                                                        $str=substr(trim($value['book_name']),0,45); 
                                                        if(mb_strlen($str, "utf-8")>8){
                                                        echo  $str,'....';
                                                        } else{
                                                            echo $str;
                                                        }
                                                    ?>
                                                </td>
                                                <td rowspan="4" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                                <td rowspan="4" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                                <td rowspan="4" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                                <td rowspan="4" id="sticker_color" style="height: 40px;">
                                                     <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                          }else{
                                                            echo $value['sticker_color'];
                                                           } ?>
                                                    
                                                </td>
                                                <td rowspan="4" id="sticker_number" style="height:40px;"> 

                                                        <?php if($value['sticker_number']==="0"){
                                                            echo  $value['sticker_number']="";
                                                          }else{
                                                            echo $value['sticker_number'];
                                                           } ?>
                                                        
                                                </td>
                                              
                                                <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                                    
                                                    <div class="avg">
                                                        <span class="avg_level" id="avg_level_<?php echo $key?>"><?php echo $value['avg_level'];?></span> 
                                                    </div>

                                                </td>
                                                   
                                                
                                        
                                    </tr>
                                    <tr class="data_content" id="data_two_<?php echo $key?>">
     

                                    </tr>
                                    <tr class="data_content" id="data_three_<?php echo $key?>">
                                        
                                      
                                                <td rowspan="2"  id="change_<?php echo $key?>" style="height: 40px;">    
                                                    
                                                    <div class="avg">
                                                        <span class="avg_level" id="avg_level_1_<?php echo $key?>"><?php if(!empty($value['avg_level_1'])){echo $value['avg_level_1'];}?></span>
                                                       
                                                    </div>

                                                </td>
                                       
                                    </tr>
                                    <tr class="data_content" id="data_four_<?php echo $key?>">


                                    </tr>



                                <?php }?>
                            <?php } ?>
                       
                </table>
            
            </div>
            <!--書題主題的表-->
            <div id="topic_table">
                <table border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                        <tr>
                            <td>書名</td> 
                            <td>isbn10碼</td>
                            <td>isbn13碼</td>
                            <td>圖書館編號</td>
                            <td>貼紙顏色</td>
                            <td>貼紙編號</td>
                            <td>大主題</td>
                            <td>中主題</td>
                            <td>小主題</td>
                            <td>學生姓名</td>
                        </tr>
            <?php foreach ($array_output as $key => $value) { ?>
                               

                                    <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                                <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">

                                                <td rowspan="4" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?>">         
                                                    <?php 
                                                        $str=substr(trim($value['book_name']),0,45); 
                                                        if(mb_strlen($str, "utf-8")>8){
                                                        echo  $str,'....';
                                                        } else{
                                                            echo $str;
                                                        }
                                                    ?>
                                                </td>
                                                <td rowspan="4" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                                <td rowspan="4" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                                <td rowspan="4" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                                <td rowspan="4" id="sticker_color" style="height: 40px;">
                                                     <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                          }else{
                                                            echo $value['sticker_color'];
                                                           } ?>
                                                    
                                                </td>
                                                <td rowspan="4" id="sticker_number" style="height:40px;"> 

                                                        <?php if($value['sticker_number']==="0"){
                                                            echo  $value['sticker_number']="";
                                                          }else{
                                                            echo $value['sticker_number'];
                                                           } ?>
                                                        
                                                </td>
                                                <td> 
                                                    <?php
                                                      if(!empty($value['major_topic'][0])){
                                                        foreach ($value['major_topic'][0]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                    }?>
                                                        
                                                </td>
                                                <td>
                                                     <?php
                                                      if(!empty($value['sub_topic'][0])){
                                                        foreach ($value['sub_topic'][0]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                          
                                                    }?>
                                                </td>
                                                <td>
                                                    <?php
                                                      if(!empty($value['minor_topic'][0])){
                                                        foreach ($value['minor_topic'][0]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                  
                                                    }?>
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                            echo $value['name'][1];
                                                    }?>
                                                        
                                                </td>
                                               
         
                                        
                                    </tr>
                                    <tr class="data_content" id="data_two_<?php echo $key?>">
                                         <td> 
                                                    <?php
                                                      if(!empty($value['major_topic'][1])){
                                                        foreach ($value['major_topic'][1]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                    }?>
                                                        
                                                </td>
                                                <td>
                                                     <?php
                                                      if(!empty($value['sub_topic'][1])){
                                                        foreach ($value['sub_topic'][1]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                          
                                                    }?>
                                                </td>
                                                <td>
                                                    <?php
                                                      if(!empty($value['minor_topic'][1])){
                                                        foreach ($value['minor_topic'][1]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                  
                                                    }?>
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                            echo $value['name'][1];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_three_<?php echo $key?>">
                                        
                                                <td> 
                                                    <?php
                                                      if(!empty($value['major_topic'][2])){
                                                        foreach ($value['major_topic'][2]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                    }?>
                                                        
                                                </td>
                                                <td>
                                                     <?php
                                                      if(!empty($value['sub_topic'][2])){
                                                        foreach ($value['sub_topic'][2]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                          
                                                    }?>
                                                </td>
                                                <td>
                                                    <?php
                                                      if(!empty($value['minor_topic'][2])){
                                                        foreach ($value['minor_topic'][2]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                  
                                                    }?>
                                                </td>
                                                        <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][2])){
                                                            echo $value['name'][2];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_four_<?php echo $key?>">
                                                         <td> 
                                                    <?php
                                                      if(!empty($value['major_topic'][3])){
                                                        foreach ($value['major_topic'][3]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                    }?>
                                                        
                                                </td>
                                                <td>
                                                     <?php
                                                      if(!empty($value['sub_topic'][3])){
                                                        foreach ($value['sub_topic'][3]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                          
                                                    }?>
                                                </td>
                                                <td>
                                                    <?php
                                                      if(!empty($value['minor_topic'][3])){
                                                        foreach ($value['minor_topic'][3]as $key => $val) {
                                                            echo $key+1,".",$val;

                                                        };
                                                  
                                                    }?>
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][3])){
                                                            echo $value['name'][3];
                                                    }?>
                                                        
                                                </td>
                                    </tr>



                         
                            <?php } ?>


                    
                </table>
        
            </div>
            <!--書題標籤的表-->
            <div id="tag_table">
                <table border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                        <tr>
                            <td>書名</td> 
                            <td>isbn10碼</td>
                            <td>isbn13碼</td>
                            <td>圖書館編號</td>
                            <td>貼紙顏色</td>
                            <td>貼紙編號</td>
                            <td>標籤1</td>
                            <td>標籤2</td>
                            <td>標籤3</td>
                            <td>標籤4</td>
                            <td>標籤5</td>
                            <td>學生姓名</td>
                        </tr>
                  <?php foreach ($array_output as $key => $value) { ?>
                               

                                    <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                                <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">

                                                <td rowspan="4" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?>">         
                                                    <?php 
                                                        $str=substr(trim($value['book_name']),0,7); 
                                                        if(mb_strlen($str, "utf-8")>8){
                                                        echo  $str,'...';
                                                        } else{
                                                            echo $str;
                                                        }
                                                    ?>
                                                </td>
                                                <td rowspan="4" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                                <td rowspan="4" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                                <td rowspan="4" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                                <td rowspan="4" id="sticker_color" style="height: 40px;">
                                                     <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                          }else{
                                                            echo $value['sticker_color'];
                                                           } ?>
                                                    
                                                </td>
                                                <td rowspan="4" id="sticker_number" style="height:40px;"> 

                                                        <?php if($value['sticker_number']==="0"){
                                                            echo  $value['sticker_number']="";
                                                          }else{
                                                            echo $value['sticker_number'];
                                                           } ?>
                                                        
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag1'][0])){
                                                        echo $value['tag1'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag2'][0])){
                                                        echo $value['tag2'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag3'][0])){
                                                        echo $value['tag3'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag4'][0])){
                                                        echo $value['tag4'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['tag5'][0])){
                                                            echo $value['tag5'][0];
                                                    }?>
                                                        
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                            echo $value['name'][1];
                                                    }?>
                                                        
                                                </td>
                                               
         
                                        
                                    </tr>
                                    <tr class="data_content" id="data_two_<?php echo $key?>">
                                         <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag1'][1])){
                                                        echo $value['tag1'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag2'][1])){
                                                        echo $value['tag2'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag3'][1])){
                                                        echo $value['tag3'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag4'][1])){
                                                        echo $value['tag4'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag5'][1])){
                                                        echo $value['tag5'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                            echo $value['name'][1];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_three_<?php echo $key?>">
                                        
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag1'][2])){
                                                        echo $value['tag1'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag2'][2])){
                                                        echo $value['tag2'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag3'][2])){
                                                        echo $value['tag3'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag4'][2])){
                                                        echo $value['tag4'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag5'][2])){
                                                        echo $value['tag5'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][2])){
                                                            echo $value['name'][2];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_four_<?php echo $key?>">
                                                           <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag1'][3])){
                                                        echo $value['tag1'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag2'][3])){
                                                        echo $value['tag2'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag3'][3])){
                                                        echo $value['tag3'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag4'][3])){
                                                        echo $value['tag4'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['tag5'][3])){
                                                        echo $value['tag5'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][3])){
                                                            echo $value['name'][3];
                                                    }?>
                                                        
                                                </td>
                                    </tr>



                            <?php } ?>
                </table>
        
            </div>
            <!--書籍頁數的表-->
            <div id="pages_table">
                <table border="1" cellpadding="5" style="border:2px #FFB326 solid;text-align:center;margin: 20px auto;">
                        <tr>
                            <td>書名</td> 
                            <td>isbn10碼</td>
                            <td>isbn13碼</td>
                            <td>圖書館編號</td>
                            <td>貼紙顏色</td>
                            <td>貼紙編號</td>
                            <td>書的語言</td>
                            <td>注音</td>
                            <td>頁數</td>
                            <td>字數</td>
                            <td>學生姓名</td>
                        </tr>
                        <?php foreach ($array_output as $key => $value) { ?>
                               

                                    <tr class="data_content" id="data_one_<?php echo $key?>"> 
                                                <input type="hidden" class="book_sid" value="<?php echo $value['book_sid']?>" name="<?php echo $value['book_sid']?>">

                                                <td rowspan="4" id="book_name" style="height: 40px;" title="<?php echo trim($value['book_name']) ?>">         
                                                    <?php 
                                                        $str=substr(trim($value['book_name']),0,45); 
                                                        if(mb_strlen($str, "utf-8")>8){
                                                        echo  $str,'....';
                                                        } else{
                                                            echo $str;
                                                        }
                                                    ?>
                                                </td>
                                                <td rowspan="4" id="isbn_10" style="height: 40px;"><?php echo $value['book_isbn_10']?></td>
                                                <td rowspan="4" id="isbn_13" style="height: 40px;"><?php echo $value['book_isbn_13']?></td>
                                                <td rowspan="4" id="book_library_code" style="height: 40px;"><?php echo $value['book_library_code']?></td>
                                                <td rowspan="4" id="sticker_color" style="height: 40px;">
                                                     <?php if($value['sticker_color']==="0"){echo  $value['sticker_color']="";
                                                          }else{
                                                            echo $value['sticker_color'];
                                                           } ?>
                                                    
                                                </td>
                                                <td rowspan="4" id="sticker_number" style="height:40px;"> 

                                                        <?php if($value['sticker_number']==="0"){
                                                            echo  $value['sticker_number']="";
                                                          }else{
                                                            echo $value['sticker_number'];
                                                           } ?>
                                                        
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['book_language'][0])){
                                                        echo $value['book_language'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['bopomofo'][0])){
                                                        echo $value['bopomofo'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['pages'][0])){
                                                        echo $value['pages'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['words'][0])){
                                                        echo $value['words'][0];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][0])){
                                                            echo $value['name'][0];
                                                    }?>
                                                        
                                                </td>
                                               
                                            
                                        
                                    </tr>
                                    <tr class="data_content" id="data_two_<?php echo $key?>">
                                         <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['book_language'][1])){
                                                        echo $value['book_language'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['bopomofo'][1])){
                                                        echo $value['bopomofo'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['pages'][1])){
                                                        echo $value['pages'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['words'][1])){
                                                        echo $value['words'][1];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][1])){
                                                            echo $value['name'][1];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_three_<?php echo $key?>">
                                        
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['book_language'][2])){
                                                        echo $value['book_language'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['bopomofo'][2])){
                                                        echo $value['bopomofo'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['pages'][2])){
                                                        echo $value['pages'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['words'][2])){
                                                        echo $value['words'][2];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][2])){
                                                            echo $value['name'][2];
                                                    }?>
                                                        
                                                </td>
                                    </tr>
                                    <tr class="data_content" id="data_four_<?php echo $key?>">
                                                           <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['book_language'][3])){
                                                        echo $value['book_language'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['bopomofo'][3])){
                                                        echo $value['bopomofo'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['pages'][3])){
                                                        echo $value['pages'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;">
                                                    <?php
                                                      if(!empty($value['words'][3])){
                                                        echo $value['words'][3];
                                                       }
                                                    ?>
                                                    
                                                </td>
                                                <td style="height:20px;"> 
                                                    <?php
                                                      if(!empty($value['name'][3])){
                                                            echo $value['name'][3];
                                                    }?>
                                                        
                                                </td>
                                    </tr>



                            
                            <?php } ?>
                </table>
        
            </div>
            

</Body>
<script type="text/javascript">

//點擊上方按鈕表單切換
$( "#level_6" ).click(function() {

        $("#title").html('等級大於6書表');
        $( "#level_table_over6" ).css("display", "block");
        $( "#level_table_less6" ).css("display", "none");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "none");

        

});
$( "#level_0" ).click(function() {
        $("#title").html('等級小於6書表');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "block");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "none");
       
        

});
$( "#read_again" ).click(function() {
         $("#title").html('需要重看的書表');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "none");
        $( "#read_again_table" ).css("display", "block");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "none");

       
        
});
$( "#pt" ).click(function() {
        $("#title").html('工讀生登記書本狀況');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "none");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "block");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "none");
        

});
$( "#topic" ).click(function() {
        $("#title").html('書本主題表');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "nene");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "block");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "none");
        
        

});
$( "#tag" ).click(function() {
        $("#title").html('書本標籤的表');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "nene");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "block");
        $( "#pages_table" ).css("display", "none");
       
        

});
$( "#pages" ).click(function() {
        $("#title").html('書本頁數字數表');
        $( "#level_table_over6" ).css("display", "none");
        $( "#level_table_less6" ).css("display", "none");
        $( "#read_again_table" ).css("display", "none");
        $( "#pt_table" ).css("display", "none");
        $( "#topic_table" ).css("display", "none");
        $( "#tag_table" ).css("display", "none");
        $( "#pages_table" ).css("display", "block");
        

});



//若按下修改鍵

$(".avg input:button").click(function(){

        var $this = $(this);
        var btn_id=$this.attr("id");
        var val=$this.val();
        var td_id=$(this).parent().attr("id");
        
        console.log(val);
        if(val=="修改"){
            $this.attr("value", "儲存");
            $(this).siblings('.input_text').css("display","block");

        }else{
           
            $this.attr("value", "修改");
            var input_id=$(this).siblings('.input_text').css("display","none");
            var input_text_val=$(this).siblings('.input_text').val();
            var span_id=$(this).siblings('.avg_level').attr("id");
            var book_sid=$(this).parent().parent().siblings('.book_sid').val();

            
            $("#"+span_id).text(input_text_val);
            change_level(input_text_val,book_sid);
            
        };

       
});



//修改等級

function change_level(input_text_val,book_sid){
        var url = "ajax/update_avg_level.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",
            avg_level:            input_text_val,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//已閱按鈕被按下後
$(".read_checkbox input:checkbox").change(function(){
    var $this = $(this);   
    if($this.is(":checked")){ 
        var checkbox_id=$this.attr("id");
        var book_sid=$this.parent().siblings('.book_sid').val();
        var www=$this.parent().parent('.data_content');
        var www_id=www.attr("id");
        console.log(www_id);
        var str=www_id.substr(9);
        console.log(www_id.substr(9));
        var hhh=$this.parent().parent('#'+www_id).siblings('#data_two_'+str);
 
        var hhh_id=hhh.attr("id");
        console.log(hhh_id);
        $("#"+www_id).remove();
        $("#"+hhh_id).remove();
        var status="true";
        read(book_sid,status);
    }

       
});


//已閱

function read(book_sid,status){

      var url = "./ajax/update_read.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",
            status:               status,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}


//重看按鈕被按下後
$(".read_again_checkbox input:checkbox").change(function(){
    var $this = $(this);   
    if($this.is(":checked")){ 
        var checkbox_id=$this.attr("id");
        var book_sid=$this.parent().siblings('.book_sid').val();
        var www=$this.parent().parent('.data_content');
        var www_id=www.attr("id");
        console.log(www_id);
        var str=www_id.substr(9);
        console.log(www_id.substr(9));
        var hhh=$this.parent().parent('#'+www_id).siblings('#data_two_'+str);
 
        var hhh_id=hhh.attr("id");
        console.log(hhh_id);
        $("#"+www_id).remove();
        $("#"+hhh_id).remove();
        var status="true";
        read_again(book_sid,status);

    }



       
});


//重看按鈕

function read_again(book_sid,status){

    console.log(book_sid);

      var url = "./ajax/update_read_again.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",

            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//最終等級按鈕被按下後
$(".save input:button").click(function(){
   
        var $this = $(this);
        var btn_id=$this.attr("id");
        
        var txt_id=$(this).siblings('.input_text').attr("id");
        var txt_val=$("#"+txt_id).val();
        var book_sid=$(this).parent().parent().siblings('.book_sid').val();

        // last_avg(book_sid,txt_val);

        var tr_one=$this.parent().parent().parent('.data_content');
        var tr_one_id=tr_one.attr("id");
        var str=tr_one_id.substr(-1);

        var tr_two=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_two_'+str);
        var tr_two_id=tr_two.attr("id");
        var tr_three=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_three_'+str);
        var tr_three_id=tr_three.attr("id");

        var tr_four=$this.parent().parent().parent('#'+tr_one_id).siblings('#data_four_'+str);
        var tr_four_id=tr_four.attr("id");


        $("#"+tr_one_id).remove();
        $("#"+tr_two_id).remove();
        $("#"+tr_three_id).remove();
        $("#"+tr_four_id).remove();

        last_avg(book_sid,txt_val);
       
       
});

//最終等級
function last_avg(book_sid,txt_val){

  

      var url = "./ajax/update_last_avg.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",
            txt_val:              txt_val,
            book_sid:             book_sid  
        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}
//找尋時間內工讀生登記本數
function search(){
        var date_start=$("#date_start").val();
        var date_end=$("#date_end").val();
        var url = "./ajax/search_pt_data.php";
        var dataVal = {
            user_id:              <?php echo $sess_login_info['uid']?>,
            permission:           <?php echo $sess_login_info['permission']?>,
            school_code:          "<?php echo $sess_login_info['school_code']?>",
            responsibilities:     <?php echo $sess_responsibilities?>,
            class_code:           "<?php echo $sess_class_code?>",
            grade:                "<?php echo $sess_grade?>",
            classroom:            "<?php echo $sess_classroom?>",
            date_start:           date_start,
            date_end:             date_end 

        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                    data_array = JSON.parse(data);
                  

                        for(var key in data_array){
                            var name= data_array[key]['name'];
                            var count=data_array[key]['count'];
                            $('.table_about_pt tbody').append('<tr class="data"><td>'+name+'</td><td>'+count+'</td></tr>');
                        }


                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}

//--------------
///畫面load進///
//-------------

// function main(){


//         var url = "./php/get_reading_log_info.php";
//         var dataVal = {
//             user_id:              <?php echo $sess_login_info['uid']?>,
//             permission:           <?php echo $sess_login_info['permission']?>,
//             school_code:          "<?php echo $sess_login_info['school_code']?>",
//             responsibilities:     <?php echo $sess_responsibilities?>,
//             class_code:           "<?php echo $sess_class_code?>",
//             grade:                "<?php echo $sess_grade?>",
//             classroom:            "<?php echo $sess_classroom?>"};
                   

//         $.ajax({
//                    url: url,
//                    type: "POST",
//                    datatype: "json",
//                    data: dataVal,
//                    // contentType: "application/json; charset=utf-8",
//                    async: false,
//                    success: function(data) {
  
//                     data_array = JSON.parse(data);
//                     // console.log(data_array);


//                     data_array.forEach(function(element,index){

//                         var tr=document.createElement("tr");
//                         tr.setAttribute("id", "data"+index);
//                         // document.getElementById("level").appendChild(tr);

//                         $("#data"+index).append("<td rowspan='2' class='checkbox_"+index+1+"'><input type='checkbox' id='read_checkbox_"+index+1+"'></td>");                        
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_name+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_isbn_13+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.book_library_code+"</td>");
//                         if(element.sticker_color=="undefined"){  element.sticker_color="";}
//                         $("#data"+index).append("<td rowspan='2'>"+element.sticker_color+"</td>");
//                         $("#data"+index).append("<td rowspan='2'>"+element.sticker_number+"</td>");
//                         // $("#data").append("<td>"+hard+"</td>");
//                         // $("#data").append("<td>"+element.sticker_number+"</td>");
//                         // element.book_language.forEach(function(language){
//                         //     console.log( "book_language:",element.book_language,"index:",index)

//                        // console.log(element.hard_level);
//                         element.hard_level.forEach(function(hard, i){
                              
//                               $("#data"+i).append("<td id=one_"+i+">"+hard+"</td>");                       
//                         });

//                         // for(var key in element.hard_level){
//                         //     var kk=element.hard_level[key];

//                         //      $("#data").append("<td id=one_'"+index+">"+kk+"</td>");
//                         //   // $("#data1").append("<td id=two_'"+index+"''>"+kk+"</td>");
//                         // }

//                          // $("#data").append("<td rowspan='2'>"+element.avg_level+"</td>");
//                          // $("#data").append("<td rowspan='2'>"+element.avg_level+"</td>");


//                         //  element.name.forEach(function(name){
//                         //   console.log( "name:",name);
//                         //   $("#data").append("<td id=one_'"+index+"''>"+name+"</td>");
//                         //   $("#data1").append("<td id=two_'"+index+"''>"+name+"</td>");
//                         //   // language.forEach(function(hard){
//                         //   //    console.log( "hard:",hard);
//                         //   //    // $("#data").append("<td>"+hard+"</td>");
//                         //   // });                           
//                         // });
//                     });

// // 
//                     // for(var key in data_array){

//                     //     var book_name=data_array[key]['book_name'];
// //                     //     var book_isbn_13=data_array[key]['book_isbn_13'];
// //                     //     var book_isbn_10=data_array[key]['book_isbn_10'];
// //                     //     var book_library_code=data_array[key]['book_library_code'];
// //                     //     var book_language=data_array[key]['book_language'];

                       

// //                     //     for(var index_key in book_language){

// //                     //         console.log(book_language);
// //                     //         var language=book_language[index_key]['book_language'];
                    

// //                     //     }
                     


// //                     // }
                   
                   

// //                    },
// //                    error: function(jqXHR) {
// //                     alert("發生錯誤: " + jqXHR.status);
// //                   }

// //         });


// } 
// main();




//978986317693
</script>
</Html>