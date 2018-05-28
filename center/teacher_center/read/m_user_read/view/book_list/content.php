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
        require_once(str_repeat("../",6).'config/config.php');

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
            $url=str_repeat("../",7).'index.php';
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_read');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id   使用者主索引

        $get_chk=array(
            'user_id'
        );
        $get_chk=array_map("trim",$get_chk);
        foreach($get_chk as $get){
            if(!isset($_GET[$get])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id   使用者主索引

        //GET
        $user_id =trim($_GET[trim('user_id ')]);

        //SESSION
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

      

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //user_id   使用者主索引

        $arry_err=array();

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
           $user_id=(int)$user_id;
           if($user_id===0){
              $arry_err[]='使用者主索引,不為整數!';
           }
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //書籍陣列
        //---------------------------------------------------

            $user_id=(int)$user_id;

            $query_sql="

                SELECT   book_borrow_log.`read_cno`,book_borrow_log.book_sid,book_borrow_log.borrow_sid,book_borrow_log.borrow_sdate
                FROM (
                    SELECT       
                            COUNT(`mssr_book_borrow_log`.`book_sid`) AS `read_cno`, 
                            `mssr_book_borrow_log`.`book_sid`, 
                            MAX(`mssr_book_borrow_log`.`borrow_sid`) as borrow_sid, 
                            MAX(`mssr_book_borrow_log`.`borrow_sdate`)  as borrow_sdate
                     FROM `mssr_book_borrow_log` 
                     WHERE 1=1 
                     AND `mssr_book_borrow_log`.`user_id`={$user_id} 
                     GROUP BY mssr_book_borrow_log.book_sid 

                ) AS  `book_borrow_log`



                ORDER BY book_borrow_log.borrow_sdate DESC


            ";
                //          SELECT
                //     COUNT(`mssr_book_borrow_log`.`book_sid`) AS `read_cno`,
                //     `mssr_book_borrow_log`.`book_sid`,
                //     `mssr_book_borrow_log`.`borrow_sid`,
                //     `mssr_book_borrow_log`.`borrow_sdate`
                // FROM `mssr_book_borrow_log`
                // WHERE 1=1
                //     AND `mssr_book_borrow_log`.`user_id`={$user_id}
                // GROUP BY `mssr_book_borrow_log`.`book_sid`
                // ORDER BY `mssr_book_borrow_log`.`borrow_sdate` DESC

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;  //資料總筆數
        $psize =20; //單頁筆數,預設20筆
        $pnos  =0;  //分頁筆數
        $pinx  =1;  //目前分頁索引,預設1
        $sinx  =0;  //值域起始值
        $einx  =0;  //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
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

        $numrow=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
        $numrow=count($numrow);

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,教師中心";

        if($numrow!==0){
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array($sinx-1,$psize),$arry_conn_mssr);
            page_hrs($title,$sess_school_code);
            die();
        }else{
            page_nrs($title,$sess_school_code);
            die();
        }
?>
<?php function page_hrs($title="",$sess_school_code) {?>
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
        global $arry_conn_mssr;

        //local
        global $numrow;
        global $psize;
        global $pnos;
        global $pinx;
        global $sinx;
        global $einx;

        global $arrys_result;
        global $config_arrys;
        global $conn_mssr;

        global $user_id;
        global $about_book;
        // global $book_pages;
        // global $book_words;

        $about_book=array();

        // $book_pages=array();
        // $book_words=array();


        


    //---------------------------------------------------
    //內部變數
    //---------------------------------------------------

        $fld_nos=4; //欄位個數
        $btn_nos=1; //功能按鈕個數

    //---------------------------------------------------
    //額外處理
    //---------------------------------------------------
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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
</Head>

<style type="text/css">
    td{
        padding: 5px;
    }
</style>

<Body>

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="350px" align="center" valign="top">
            <!-- 內容 -->
                <!-- 統計資料表格 開始 -->
                <div class="mod_data_tbl_outline" >
                    <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
                        <tr align="center" valign="middle" class="fsize_16">
                            <td align="center" width="470px">
                                <span class="fsize_16">
                                    <!-- <span class="fc_red1">●請注意! 此頁為新開頁面，</span> -->
                                    <input type="button" value="回上一頁" class="ibtn_gr9030" onclick="parent.window.location='../../index.php';" onmouseover="this.style.cursor='pointer'">
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 統計資料表格 結束 -->

                <!-- 資料表格 開始 -->
                <div class="mod_data_tbl_outline" style="margin-top:35px;">
                	<?php if($sess_school_code == 'idc') { ?>
						<table width="85%" style="padding-top: 20px; padding-left: 15px;">
							<tr>
								<th width="30px" style="background-color: #ffcccc;">
									<span style="font-size: 16px"></span>
								</th>
								<th>
									<span style="font-size: 16px">繪本 ∕ 初階</span>
								</th>
								<th width="30px" style="background-color: #ffe0cc;">
									<span style="font-size: 16px"></span>
								</th>
								<th>
									<span style="font-size: 16px">繪本 ∕ 進階</span>
								</th>
								<th width="30px" style="background-color: #ffff99;">
									<span style="font-size: 16px"></span>
								</th>
								<th>
									<span style="font-size: 16px">橋梁書 ∕ 初階</span>
								</th>
								<th width="30px" style="background-color: #adebad;">
									<span style="font-size: 16px"></span>
								</th>
								<th>
									<span style="font-size: 16px">橋梁書 ∕ 進階</span>
								</th>
								<th width="30px" style="background-color: #ccecff;">
									<span style="font-size: 16px"></span>
								</th>
								<th>
									<span style="font-size: 18px">文字書</span>
								</th>
							</tr>
						</table>
					<?php } ?>
                    <table id="mod_data_tbl" border="0" width="95%" cellpadding="5" cellspacing="0" style="margin-top:30px;" class="table_style4">
                        <tr align="center" valign="middle" class="fsize_18">
                           <!--  <th width="30px" height="40px">排序         </th> -->
                            <th width="30px" height="40px">登記次數     </th>
                            <!-- <?php if ($sess_school_code == 'idc') { ?>
                                <th width="90px" height="40px">等級</th>
                            <?php } ?> -->
                            <th width="120px" height="40px">書籍名稱    </th>
                            <?php if($sess_school_code==='idc'){ echo "<th width='30px' height='40px'>頁數</th>" ,"<th width='30px' height='40px'>字數</th>";}?>
                            
                            <th width="75px" height="40px">最後借閱日期</th>
                            <th width="60px" height="40px">閱讀進度</th>
                            <th width="60px" height="40px">閱讀難度</th>
                            <th width="60px" height="40px">喜愛程度</th>
                        </tr>
                        </tr>

                        <?php foreach($arrys_result as $inx=>$arry_result) :?>
                        <?php
                        //---------------------------------------------------
                        //接收欄位
                        //---------------------------------------------------

                            extract($arry_result, EXTR_PREFIX_ALL, "rs");

                        //---------------------------------------------------
                        //處理欄位
                        //---------------------------------------------------

                            $rs_read_cno    =(int)$rs_read_cno;
                            $rs_borrow_sdate=trim($rs_borrow_sdate);
                            
                            $time=date("m",strtotime($rs_borrow_sdate));
                            $real_time= date("Y-m-d",strtotime($rs_borrow_sdate));



                            if(preg_match("/^mbu/i",$rs_book_sid)){
                                $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                                if(!empty($get_book_info)){
                                    $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                    if($rs_book_verified===2)continue;
                                }else{continue;}
                            }

                        //---------------------------------------------------
                        //特殊處理
                        //---------------------------------------------------

                            //-----------------------------------------------
                            //查找,書籍資訊
                            //-----------------------------------------------

                                $rs_book_name='<span class="fc_red1">查無此書</span>';
                                $rs_book_type="";
                                $rs_book_code="";

                                $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array(),$arry_conn_mssr);
                                if(!empty($get_book_info)){

                                    //book_name     書籍名稱
                                    $rs_book_name=trim($get_book_info[0]['book_name']);
                                    if(mb_strlen($rs_book_name)>20){
                                        $rs_book_name=htmlspecialchars(mb_substr($rs_book_name,0,20)."..");
                                    }

                                    $rs_book_isbn_10=trim($get_book_info[0]['book_isbn_10']);
                                    $rs_book_isbn_13=trim($get_book_info[0]['book_isbn_13']);
                                    $rs_book_library_code=trim($get_book_info[0]['book_library_code']);
                                    switch(trim($rs_book_library_code)){
                                        case '':
                                            $rs_book_type='mssr_book_class';
                                            $rs_book_code=$rs_book_isbn_13;
                                        break;
                                        default:
                                            $rs_book_type='mssr_book_library';
                                            $rs_book_code=$rs_book_library_code;
                                        break;
                                    }

                                    //書籍捐贈者
                                    $rs_book_donor='無';
                                    if(isset($get_book_info[0]['book_donor'])){
                                        if($get_book_info[0]['book_donor']!==''){
                                            $rs_book_donor=trim($get_book_info[0]['book_donor']);
                                        }
                                    }
                                }

                            //-----------------------------------------------
                            //若是idc學校查找,書籍頁數字數
                            //-----------------------------------------------

                                if(isset($rs_book_sid)){
                                    $book_log_sql="

                                                    SELECT book_sid,pages,words,administrator_level
                                                    FROM mssr_idc_book_sticker_level_info
                                                    WHERE book_sid='{$rs_book_sid}'
                                                    ORDER BY keyin_cdate
                                                                      
                                                 ";

                                                
                                     $book_log_result=db_result($conn_type='pdo',$conn_mssr,$book_log_sql,array(),$arry_conn_mssr);

                                     if(!empty($book_log_result)){
                                        $book_pages=$book_log_result[0]['pages'];
                                        $book_words=$book_log_result[0]['words'];
                                        $administrator_level=$book_log_result[0]['administrator_level'];

										// if ($administrator_level == 1) {
										// 	$level = "<span style='color: red'>繪本 ∕ 初階</span>";
										// } elseif ($administrator_level == 2) {
										// 	$level = "<span style='color: #ff8000'>繪本 ∕ 進階</span>";
										// } elseif ($administrator_level == 3) {
										// 	$level = "<span style='color: #ffd700'>橋梁書 ∕ 初階</span>";
										// } elseif ($administrator_level == 4) {
										// 	$level = "<span style='color: #006600'>橋梁書 ∕ 進階</span>";
										// } elseif ($administrator_level == 5) {
										// 	$level = "<span style='color: blue'>文字書</span>";
										// } else {
										// 	$level = "";
										// }
                                     }else{

                                        $book_pages='';
                                        $book_words='';
                                        $level="";
                                        $administrator_level = "";


                                     }

                                



                                }
                                   

                                     // //--------------------------------
                                     // //等級判斷
                                     // //-------------------------------

                                     // $avg_level_sql="

                                     //                SELECT administrator_level
                                     //                FROM mssr_idc_book_sticker_level_info
                           
                                     //                WHERE book_sid='{$rs_book_sid}'
                                     //                ORDER BY keyin_cdate
                                                                      
                                     //             ";

                                                
                                     // $avg_level_result=db_result($conn_type='pdo',$conn_mssr,$avg_level_sql,array(),$arry_conn_mssr);

                                     // // print_r($book_log_result);

                                   

                            //-------------------------------------------
                            //查找, 閱讀資訊
                            //-------------------------------------------

                                $rs_borrow_sid=mysql_prep($rs_borrow_sid);

                                // print_r($rs_borrow_sid);
                                $get_book_read_opinion_log_info=get_book_read_opinion_log_info($conn_mssr,$rs_borrow_sid,$array_filter=array('opinion_answer'),$arry_conn_mssr);
                                $read_num_html='';  //閱讀程度
                                $diffcult_html='';  //難度程度
                                $like_num_html='';  //喜歡程度
                                if(!empty($get_book_read_opinion_log_info)){
                                    $rs_opinion_answer=$get_book_read_opinion_log_info[0]['opinion_answer'];
                                    if(unserialize($rs_opinion_answer)){
                                        $arrys_opinion_answer=unserialize($rs_opinion_answer);
                                        foreach($arrys_opinion_answer as $arry_opinion_answer){
                                            $topic_id=(int)$arry_opinion_answer['topic_id'];

                                            //閱讀程度
                                            if($topic_id===1){
                                                $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                                $read_num=(int)$opinion_answer;
                                                $sql="
                                                    SELECT `topic_options`
                                                    FROM `mssr_book_topic_log`
                                                    WHERE 1=1
                                                        AND `topic_id`={$topic_id}
                                                ";
                                                $read_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                $topic_options=trim($read_result[0]['topic_options']);
                                                if(unserialize($topic_options)){
                                                    $arrys_topic_options=unserialize($topic_options);
                                                    if(isset($arrys_topic_options[$read_num])){
                                                        $read_num_html=trim($arrys_topic_options[$read_num]);
                                                    }
                                                }
                                            }
                                            if($topic_id===2){
                                                $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                                $read_num=(int)$opinion_answer;
                                                $sql="
                                                    SELECT `topic_options`
                                                    FROM `mssr_book_topic_log`
                                                    WHERE 1=1
                                                        AND `topic_id`={$topic_id}
                                                ";
                                                $read_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                $topic_options=trim($read_result[0]['topic_options']);
                                                if(unserialize($topic_options)){
                                                    $arrys_topic_options=unserialize($topic_options);

                                                    // print_r($arrys_topic_options);
                                                    if(isset($arrys_topic_options[$read_num])){
                                                        if(trim($arrys_topic_options[$read_num])==='難度剛好'){
                                                            $diffcult_html="剛好";
                                                        }else{
                                                            $diffcult_html=trim($arrys_topic_options[$read_num]);
                                                        }
                                                    }
                                                }
                                            }
                                            if($topic_id===5){
                                                $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                                $like_num=(int)$opinion_answer;
                                                $sql="
                                                    SELECT `topic_options`
                                                    FROM `mssr_book_topic_log`
                                                    WHERE 1=1
                                                        AND `topic_id`={$topic_id}
                                                ";
                                                $like_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                $topic_options=trim($like_result[0]['topic_options']);
                                                if(unserialize($topic_options)){
                                                    $arrys_topic_options=unserialize($topic_options);
                                                    if(isset($arrys_topic_options[$like_num])){
                                                        $like_num_html=trim($arrys_topic_options[$like_num]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                        ?>
                        <tr class="fsize_16" style="background-color:<?php if($time%2===0){ echo "#eee";  }else{ echo  "#ECF5FF"; }?>;">

                          <!--   <td height="30px" align="center" valign="middle">
                                <?php if($pinx!==1):?>
                                    <?php echo $inx+1+(($pinx-1)*$psize);?>
                                <?php else:?>
                                    <?php echo $inx+1;?>
                                <?php endif;?>
                            </td> -->
                            <td height="30px" align="center" valign="middle">
                                <?php echo $rs_read_cno;?>

                            </td>
							<!-- <?php if ($sess_school_code == 'idc') { ?>
								<td height="30px" align="center" valign="middle">
									<?php echo $level; ?>
								</td>
							<?php } ?> -->
							<?php 
								if (isset($administrator_level)) {
									if ($administrator_level == 1) {
										$background_color = "#ffcccc";
									} elseif ($administrator_level == 2) {
										$background_color = "#ffe0cc";
									} elseif ($administrator_level == 3) {
										$background_color = "#ffff99";
									} elseif ($administrator_level == 4) {
										$background_color = "#adebad";
									} elseif ($administrator_level == 5) {
										$background_color = "#ccecff";
									} else {
                                        $background_color = "";
                                    }
								} else {
									$background_color = "";
								}
							 ?>
                            <td height="30px" style='text-align: left; background-color: <?php echo $background_color; ?>'>

                                <?php if(trim($rs_book_type)==='mssr_book_class'){

                                        
                                           $book_13=htmlspecialchars($rs_book_code);
                                           $book_10=htmlspecialchars($rs_book_isbn_10);
                                   
                                }else{
                                        $book_13=htmlspecialchars($rs_book_code);
                                }?>

                               <span onclick="alert_book_info(<?php echo $book_13,",",$book_10 ;?>);"   style='color: #8e4408;cursor: pointer;'>
                                <?php echo $rs_book_name;?></span>
                            </td>
                             <?php if($sess_school_code==='idc'){ echo   "<td height='35px' align='left' valign='middle'>".round($book_pages)."</td>" ,"<td height='35px'  align='left' valign='middle'>".round($book_words)."</td>";}?>
                           
 
                            <td height="30px" align="center" valign="middle">
                                <?php echo htmlspecialchars($real_time);?>
                            </td>
<!--                             <td height="35px" align="left" valign="middle">
                                <?php //if(trim($rs_book_type)==='mssr_book_class'):?>

                                        
                                        <span class="isbn_10" style="display:none;">
                                            13碼:<?php// echo htmlspecialchars($rs_book_code);?><br/>
                                            10碼:<?php //echo htmlspecialchars($rs_book_isbn_10);?>
                                        </span>
                                <?php //else:?>
                                    圖書館:<?php //echo htmlspecialchars($rs_book_code);?>
                                <?php //endif;?>
                            </td> -->
                          

                            <td height="30px" align="center" valign="middle">
                                <!-- <iframe src="data_detailed.php?inx=<?php echo $inx;?>&data_filter=read&borrow_sid=<?php echo addslashes($rs_borrow_sid);?>" frameborder="0"
                                style="width:110px;height:38px;overflow:hidden;overflow-y:auto"></iframe> -->
                                <?php echo htmlspecialchars($read_num_html);?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <!-- <iframe src="data_detailed.php?inx=<?php echo $inx;?>&data_filter=like&borrow_sid=<?php echo addslashes($rs_borrow_sid);?>" frameborder="0"
                                style="width:110px;height:38px;overflow:hidden;overflow-y:auto"></iframe> -->
                                <?php echo htmlspecialchars($diffcult_html);?>
                            </td>
                            <td height="30px" align="center" valign="middle">
                                <!-- <iframe src="data_detailed.php?inx=<?php echo $inx;?>&data_filter=rec&borrow_sid=<?php echo addslashes($rs_borrow_sid);?>" frameborder="0"
                                style="width:40px;height:38px;overflow:hidden;overflow-y:auto"></iframe> -->
                                <?php echo htmlspecialchars($like_num_html);?>
                            </td>
                        </tr>
                        <?php endforeach ;?>
                    </table>

                    <table border="0" width="100%">
                        <tr valign="middle">
                            <td align="left">
                                <!-- 分頁列 -->
                                <span id="page" style="position:relative;margin-top:10px;"></span>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- 資料表格 結束 -->
            <!-- 內容 -->
            </td>
        </tr>
    </table>
    <!-- 資料列表 結束 -->

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;
    var user_id=<?php echo $user_id;?>;
    var $isbn_10=$('.isbn_10');
   

                               


    window.onload=function(){

        //分頁列
        var cid         ="page";                        //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'content.php',
            'page_args' :{
                'user_id'   :user_id
            }
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    }

    function view_change(obj,type){
    //模式切換
        if(trim(type)==='展開'){
            obj.value='關閉';
            $isbn_10.show();
            $('#about_book_isbn').css(width,'200px');
        }else{
            obj.value='展開';
            $isbn_10.hide();
        }
    }

    function alert_book_info(book_13,book_10){

        alert("ISBN 13碼:"+book_13+"\n" +  "ISBN 10碼:"+book_10);


    }

</script>

</Body>
</Html>

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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
</Head>

<Body>
    <!-- 統計資料表格 開始 -->
    <div class="mod_data_tbl_outline" style="margin-top:35px;">
        <table id="mod_data_tbl" border="0" width="95%" height="40px" cellpadding="5" cellspacing="0" style="" class="font-weight1 font-family1 fc_green0">
            <tr align="center" valign="middle" class="fsize_16">
                <td align="center" width="470px">
                    <span class="fsize_16">
                                    <!-- <span class="fc_red1">●請注意! 此頁為新開頁面，</span> -->
                                    <input type="button" value="回上一頁" class="ibtn_gr9030" onclick="parent.parent.window.location='../../index.php';" onmouseover="this.style.cursor='pointer'">
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <!-- 統計資料表格 結束 -->

    <!-- 資料列表 開始 -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <!-- 在此設定寬高 -->
            <td width="100%" height="400px" align="center" valign="top">
                <!-- 內容 -->
                <table border="0" width="100%" cellpadding="5" cellspacing="0" style="position:relative;top:50px;" class="mod_data_tbl_outline">
                    <tr align="center" valign="middle">
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="400px" align="center" valign="middle" class="font-family1 fc_green0 fsize_18">
                            <img src="../../../../../../img/icon/fail.gif" style="vertical-align:middle;margin:2px;">
                            目前系統無資料，或查無資料!
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

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    window.onload=function(){

    }

</script>

</Body>
</Html>

<?php
//-------------------------------------------------------
//page_nrs 區塊 -- 結束
//-------------------------------------------------------
    $conn_mssr=NULL;
?>
<?php };?>