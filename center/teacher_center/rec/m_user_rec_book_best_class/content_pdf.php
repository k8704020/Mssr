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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/plugin/func/tcpdf/tcpdf'
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //user_id   使用者主索引

    
    
        $post_chk=array(
            'user_id_book_sid'
        );
        $post_chk=array_map("trim",$post_chk);
        foreach($post_chk as $post){
            if(!isset($_POST[$post])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //user_id   使用者主索引

    
        //GET
        $view           =(isset($_GET[trim('view')]))?$_GET[trim('view')]:'';
		
		
		//POST
		$user_id_book_sid           =(isset($_POST[trim('user_id_book_sid')]))?$_POST[trim('user_id_book_sid')]:array();
		
		foreach ($user_id_book_sid as $key => $value) {
			$tmp_array = array();
			$tmp_array = explode('_',$value);
			$array_data[$key]['user_id'] = $tmp_array[0];
			$array_data[$key]['book_sid'] = $tmp_array[1];
		}
		
		
		
		
        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,22))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

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

        //---------------------------------------------------
        //更新上架條件
        //---------------------------------------------------

            $update_open_publish=update_open_publish($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$sess_user_id);

        //---------------------------------------------------
        //SQL條件
        //---------------------------------------------------
        $db_results = array();
		$db_results_cno = 0;
    	foreach ($array_data as $key => $val) {
			
			$query_sql="
                SELECT
                    *
                FROM(
                    SELECT
                    	`user_id`,
                        `book_sid`,
                        `keyin_cdate`,
                        `keyin_mdate`
                    FROM `mssr_rec_book_cno`
                    WHERE 1=1
                        AND `user_id`=".$val['user_id']."
                        AND `book_sid` = '".$val['book_sid']."'
                        AND `rec_state`=1
                ) AS `sqry`
                WHERE 1=1
                #GROUP BY `sqry`.`book_sid`
                ORDER BY `sqry`.`keyin_mdate` DESC
            ";
			//echo "<pre>";print_r($query_sql);echo "</pre>";
			$db_results_tmp=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
        	$db_results_cno_tmp=count($db_results_tmp);
			if($db_results_cno_tmp > 0){
				$db_results = array_merge($db_results,$db_results_tmp);
				$db_results_cno = $db_results_cno +$db_results_cno_tmp;
			}
			
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

        $numrow=$db_results_cno;

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


        //推薦類型陣列
        $arrys_rec_type=array(
            trim('draw  ')=>trim('繪圖'),
            trim('text  ')=>trim('文字'),
            trim('record')=>trim('錄音')
        );

        $arry_record_info=array();

        if($numrow===0){
            die();
        }else{
            $arrys_chunk =array_chunk($db_results,$psize);
            $arrys_result=$arrys_chunk[$pinx-1];
            if($view==='all')$arrys_result=$db_results;
        }

        global $arry_ftp1_info;

        

        //連接 | 登入 FTP
        $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
        $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

        //設定被動模式
        ftp_pasv($ftp_conn,TRUE);

    //---------------------------------------------------
    //網頁內容
    //---------------------------------------------------

        $body ='';
        $body.='
            <!DOCTYPE HTML>
            <Html>
            <Head>
                <Title>明日星球,教師中心</Title>
                <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
                <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">

                <!-- 通用 -->
                <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
                <script type="text/javascript" src="../../../../../../inc/code.js"></script>

                <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
                <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>
                <script type="text/javascript" src="../../../../../../lib/jquery/ui/code.js"></script>

                <script type="text/javascript" src="../../../../../../lib/js/vaildate/code.js"></script>
                <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
                <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>
                <script type="text/javascript" src="../../../../../../lib/js/table/code.js"></script>

                <!-- 專屬 -->
                <link rel="stylesheet" href="../../../../inc/rec/audio_player/code.css">
                <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
                <script type="text/javascript" src="../../../../inc/code.js"></script>

                <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />
                <style type="text/css">
                    @media print{
                        p{
                            page-break-before:always;
                        }
                    }
                </style>
            </Head>

            <Body>
        ';
        $body.='
            <!-- 資料列表 開始 -->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr>
                    <!-- 在此設定寬高 -->
                    <td width="100%" height="350px" align="center" valign="top">
                    <!-- 內容 -->
                        <!-- 資料表格 開始 -->
                        <div class="mod_data_tbl_outline" style="margin-top:35px;">
        ';
		
        foreach($arrys_result as $arrys_inx=>$arry_result):
        //---------------------------------------------------
        //接收欄位
        //---------------------------------------------------

            extract($arry_result, EXTR_PREFIX_ALL, "rs");

        //---------------------------------------------------
        //特殊處理
        //---------------------------------------------------

            //推薦識別碼陣列
            $arrys_rec_sid=array();

            //-----------------------------------------------
            //查找, 使用者資訊
            //-----------------------------------------------

                $get_user_info=get_user_info($conn_user,$arry_result['user_id'],$array_filter=array("name"),$arry_conn_user);
                if(!empty($get_user_info)){
                    $rs_user_name=trim($get_user_info[0]['name']);
                }

            //-----------------------------------------------
            //查找, 書名
            //-----------------------------------------------

                $get_book_info=get_book_info($conn_mssr,trim($arry_result['book_sid']),$array_filter=array('book_name'),$arry_conn_mssr);
                $rs_book_name='查無書名!';
                if(!empty($get_book_info)){
                    $rs_book_name=trim($get_book_info[0]['book_name']);
                    if(mb_strlen($rs_book_name)>15){
                        $rs_book_name=mb_substr($rs_book_name,0,15)."..";
                    }
                }

            //-----------------------------------------------
            //查找, 圖片
            //-----------------------------------------------

                $has_draw     =false;
                $rec_draw_info=get_rec_info($conn_mssr,$arry_result['user_id'],trim($arry_result['book_sid']),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_draw_info)){
                    foreach($rec_draw_info as $inx=>$arry){
                        //匯入推薦識別碼陣列
                        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                    }
                    //圖片識別碼
                    $rs_rec_draw_sid=trim($rec_draw_info[0]['rec_sid']);

                    //$root         =str_repeat("../",6)."info/user/".(int)$arry_result['user_id']."/book";

                    //手繪
                    $ftp_root="public_html/mssr/info/user/".(int)$arry_result['user_id']."/book";
                    $draw_path      ="{$ftp_root}/".trim($arry_result['book_sid'])."/draw/bimg/1.jpg";
                    //$draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                    $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);
					$http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$arry_result['user_id']."/book/";
                    $http_draw_path =$http_path.trim($arry_result['book_sid'])."/draw/bimg/1.jpg";

                    //上傳
                    //$up_load_draw_path_1    ="{$ftp_root}/".trim($arry_result['book_sid'])."/draw/bimg/upload_1.jpg";
                    //$up_load_draw_path_1_enc=mb_convert_encoding($up_load_draw_path_1,$fso_enc,$page_enc);
                    //$up_load_draw_path_2    ="{$ftp_root}/".trim($arry_result['book_sid'])."/draw/bimg/upload_2.jpg";
                    //$up_load_draw_path_2_enc=mb_convert_encoding($up_load_draw_path_2,$fso_enc,$page_enc);
                    //$up_load_draw_path_3    ="{$ftp_root}/".trim($arry_result['book_sid'])."/draw/bimg/upload_3.jpg";
                    //$up_load_draw_path_3_enc=mb_convert_encoding($up_load_draw_path_3,$fso_enc,$page_enc);
                    //$arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                    //$arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                    //$arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);

                    //$draw_path      ="{$root}/".trim($arry_result['book_sid'])."/draw/bimg/1.jpg";
                    //$draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                    if((!empty($arry_ftp_file_draw_path))){
                        $has_draw=true;
                    }
                }

            //-----------------------------------------------
            //查找, 星評資訊
            //-----------------------------------------------

                $has_star=false;
                $rs_rec_star_rank='尚未選擇星等 !';
                $rs_rec_star_reason='尚未選擇評星理由 !';
                $rec_star_info=get_rec_info($conn_mssr,$arry_result['user_id'],trim($arry_result['book_sid']),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_star_info)){
                    foreach($rec_star_info as $inx=>$arry){
                        //匯入推薦識別碼陣列
                        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                    }
                    //星評識別碼
                    $rs_rec_star_sid=trim($rec_star_info[0]['rec_sid']);

                    //評星評價
                    $rs_rec_star_rank=str_repeat('★',(int)$rec_star_info[0]['rec_rank']);

                    //評星理由
                    $arrys_rs_rec_star_reason=array();
                    $rs_rec_star_reason=trim($rec_star_info[0]['rec_reason']);
                    foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                        if($rs_rec_star_reason[$inx1]==='o'){
                            //匯入評星理由
                            array_push($arrys_rs_rec_star_reason,$val1);
                        }
                    }
                    $arrys_rs_rec_star_reason=implode(" , ",$arrys_rs_rec_star_reason);

                    $has_star=true;
                }

            //-----------------------------------------------
            //查找, 文字資訊
            //-----------------------------------------------

                $has_text   =false;
                $rs_rec_text_state='';
                $arrys_rs_rec_text_content=array('','','');
                $rec_text_info=get_rec_info($conn_mssr,$arry_result['user_id'],trim($arry_result['book_sid']),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_text_info)){
                    foreach($rec_text_info as $inx=>$arry){
                        //匯入推薦識別碼陣列
                        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                    }
                    //文字識別碼
                    $rs_rec_text_sid=trim($rec_text_info[0]['rec_sid']);

                    //文字內容
                    $rs_rec_text_content=trim($rec_text_info[0]['rec_content']);
                    if(@unserialize($rs_rec_text_content)){
                        $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                    }

                    //文字推薦狀態
                    $rs_rec_text_state=trim($rec_text_info[0]['rec_state']);

                    if($rs_rec_text_state==='顯示'){
                        $has_text=true;
                    }
                }

            //-----------------------------------------------
            //查找, 錄音資訊
            //-----------------------------------------------

                $has_record     =false;
                $rec_record_info=get_rec_info($conn_mssr,$arry_result['user_id'],trim($arry_result['book_sid']),$rec_type='record',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_record_info)){
                    foreach($rec_record_info as $inx=>$arry){
                        //匯入推薦識別碼陣列
                        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                    }
                    //錄音識別碼
                    $rs_rec_record_sid=trim($rec_record_info[0]['rec_sid']);

                    $root               =str_repeat("../",6)."info/user/".(int)$arry_result['user_id']."/book";

                    $record_path_mp3    ="{$root}/".trim($arry_result['book_sid'])."/record/1.mp3";
                    $record_path_mp3_enc=mb_convert_encoding($record_path_mp3,$fso_enc,$page_enc);

                    $record_path_wav    ="{$root}/".trim($arry_result['book_sid'])."/record/1.wav";
                    $record_path_wav_enc=mb_convert_encoding($record_path_wav,$fso_enc,$page_enc);

                    if((file_exists($record_path_mp3_enc))||(file_exists($record_path_wav_enc))){
                        $has_record =true;
                        $arry_record_info[$arry_result['book_sid']]=$arry_result['user_id'];
                    }
                }

            //-----------------------------------------------
            //查找, 書本推薦內容總調查計數表資訊
            //-----------------------------------------------

                $get_rec_book_cno_info=get_rec_book_cno_info($conn_mssr,$arry_result['user_id'],trim($arry_result['book_sid']),$array_filter=array('has_publish'),$arry_conn_mssr);
                $has_publish=trim($get_rec_book_cno_info[0]['has_publish']);

            //-----------------------------------------------
            //設定, 表格背景顏色
            //-----------------------------------------------

                $tbl_bg_color="bg_gray0";
                $tbl_fc_color="fc_brown0";
                if($update_open_publish===2){
                    if($has_publish==='可'){
                        $tbl_bg_color="bg_gray1";
                        $tbl_fc_color="fc_white0";
                    }
                }

            //-----------------------------------------------
            //查找, 提取老師對推薦內容評論表資訊
            //-----------------------------------------------

                $has_rec_comment=false;
                $arry_has_rec_comment=array();
                $get_rec_comment_log_info=get_rec_comment_log_info($conn_mssr,$sess_user_id,$arrys_rec_sid,$array_filter=array('comment_type','comment_score','has_del_rec','comment_coin','comment_content','keyin_cdate'),$arry_limit=array(),$arry_conn_mssr);
                if(!empty($get_rec_comment_log_info)){
                    foreach($get_rec_comment_log_info as $inx=>$arry_rec_comment){
                        $comment_type=trim($arry_rec_comment['comment_type']);
                        $arry_has_rec_comment[]=$comment_type;
                    }
                    $has_rec_comment=true;
                }

        $body.="
            <br/>
            <table id=\"{$arrys_inx}\" width=\"530px\" align=\"center\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:25px;\" class=\"mod_data_tbl_outline\">
                <tr valign=\"top\">
                    <td align=\"center\" colspan=\"2\">
                        <!-- 基本資訊 -->
                        <table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"525px\" height=\"60px\" style=\"padding:3px;\">
                            <tr align=\"center\" valign=\"middle\" class=\"{$tbl_bg_color} fsize_18 {$tbl_fc_color}\">
                                <td align=\"center\">
                                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"98%\" class=\"{$tbl_bg_color} fsize_18 {$tbl_fc_color}\">
                                        <tr>
                                            <td align=\"left\" width=\"225px\">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                學生名稱: {$rs_user_name}
                                            </td>
                                            <td>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                建立時間: {$rs_keyin_cdate}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align=\"left\">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                書籍名稱: {$rs_book_name}
                                            </td>
                                            <td>
                                                &nbsp;
                                                最後更新時間: {$rs_keyin_mdate}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr valign=\"top\">
                    <td align=\"center\" width=\"45%\" height=\"190px\">
                        <!-- 畫圖資訊 -->
                        <br/><br/>
        ";
                        if(!empty($arry_ftp_file_draw_path)){
                            $body.="
                                <img src=\"{$http_draw_path}\" width=\"205px\" height=\"190px\" border=\"0\" alt=\"畫圖資訊\">
                            ";
                        }else{
                            $body.="
                                <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
                                <span class=\"fsize_18 font-weight1 {$tbl_fc_color}\">尚未畫圖 !</span>
                            ";
                        }
        $body.="
                    </td>
                    <td align=\"center\" width=\"55%\" height=\"190px\">
                        <!-- 評星資訊 -->
                        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"70px\" style=\"padding:3px;\">
                            <tr align=\"left\" valign=\"middle\" class=\"{$tbl_bg_color}\">
                                <td>
                                    <br/>
                                    <span class=\"fsize_18 font-weight1 {$tbl_fc_color}\" style=\"position:relative;padding:5px;display:block;\">
                                        評價 : {$rs_rec_star_rank}
                                    </span><br/>
                                    <span class=\"fsize_18 font-weight1 {$tbl_fc_color}\" style=\"position:relative;padding:5px;display:block;\">
                                        理由 :
        ";
                                            if($has_star){
                                                $body.="{$arrys_rs_rec_star_reason}";
                                            }else{
                                                $body.="{$rs_rec_star_reason}";
                                            }
        $body.="
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <br/><br/>

                        <!-- 文字資訊 -->
                        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"230px\" style=\"padding:3px;\">
                            <tr align=\"left\" valign=\"top\" class=\"{$tbl_bg_color}\">
                                <td>
                                    <br/>
                                    【最喜歡的一句話】
                                    <br/>
        ";
        if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[0]!=='')){
            $arrys_rs_rec_text_content[0]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));
            $body.="{$arrys_rs_rec_text_content[0]}";
        }
        $body.="
                                    <br/><br/>
                                    【書本內容介紹】
                                    <br/>
        ";
        if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[1]!=='')){
            $arrys_rs_rec_text_content[1]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));
            $body.="{$arrys_rs_rec_text_content[1]}";
        }
        $body.="
                                    <br/><br/>
                                    【書中所學到的事】
                                    <br/>
        ";
        if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[2]!=='')){
            $arrys_rs_rec_text_content[2]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));
            $body.="{$arrys_rs_rec_text_content[2]}";
        }
        $body.="
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br/><br/>
        ";
        if(($arrys_inx+1)%3===0&&$arrys_inx!==0){
            $body.="<p style='display:block;'></p>";
        }

        endforeach;
        $body.='
                        </div>
                        <!-- 資料表格 結束 -->
                    <!-- 內容 -->
                    </td>
                </tr>
            </table>
            <!-- 資料列表 結束 -->
        ';
        $body.='
            </Body>
            </Html>
        ';

        //呼叫pdf
        //echo "<pre>";print_r($body);echo "</pre>";
        pdf_set();
?>


<?php
//-------------------------------------------------------
//pdf設置
//-------------------------------------------------------

    function pdf_set(){

        global $page_enc;
        global $body;

        //新增pdf類別
        $opdf=new TCPDF('R',PDF_UNIT,'A4',true,$page_enc,false);

        //不要頁首
        $opdf->setPrintHeader(false);

        //不要頁尾
        $opdf->setPrintFooter(true);

        //設定自動分頁
        $opdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //設定語言相關字串
        //$opdf->setLanguageArray($l);

        //產生字型子集（有用到的字才放到文件中）
        $opdf->setFontSubsetting(true);

        //設定字型
        $opdf->SetFont('cid0jp', '', 8, '', true);

        //新增頁面
        $opdf->AddPage();

        //寫入
        $opdf->writeHTML($body,$ln=0,$fill=0,$reseth=false,$cell=false,$align='L');

        //輸出
        $opdf->Output('single_rec.pdf', 'I');

        die();
    }
?>