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
        require_once(str_repeat("../",0).'PHPWord.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/plugin/func/tcpdf/tcpdf',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code'
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
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_rec');
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
        $user_id        =trim($_GET[trim('user_id ')]);
        $view           =(isset($_GET[trim('view')]))?$_GET[trim('view')]:'';
        $arry_book_sid  =(isset($_GET[trim('book_sid')]))?$_GET[trim('book_sid')]:array();

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

            $user_id=(int)$user_id;

            $query_sql="
                SELECT
                    *
                FROM(
                    SELECT
                        `book_sid`,
                        `keyin_cdate`,
                        `keyin_mdate`
                    FROM `mssr_rec_book_cno`
                    WHERE 1=1
                        AND `user_id`={$user_id}
                        AND `rec_state`=1
                ) AS `sqry`
                WHERE 1=1
                #GROUP BY `sqry`.`book_sid`
                ORDER BY `sqry`.`keyin_mdate` DESC
            ";
            $db_results=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(),$arry_conn_mssr);
            $db_results_cno=count($db_results);

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

        $user_id=(int)$user_id;

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

        $ftp_root="public_html/mssr/info/user/".(int)$user_id."/book";
        $http_path="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$user_id."/book/";

        //連接 | 登入 FTP
        $ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
        $ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

        //設定被動模式
        ftp_pasv($ftp_conn,TRUE);

        //開暫存目錄
        if(!is_dir("tmp1")){
            mk_dir("tmp1",$mode=0777,$recursive=true,$fso_enc);
        }

    //---------------------------------------------------
    //網頁內容
    //---------------------------------------------------

        $phpword = new PHPWord();
        $sectionStyle = array(
            //'orientation' => 'landscape'
            'orientation' => null
        );
        $section = $phpword->createSection($sectionStyle);


        $styletable    = array('borderColor'=>'000000', 'borderSize'=>1);
        $stylefirstrow = array('borderColor'=>'000000', 'borderSize'=>1);
        $phpword->addTableStyle('myTable', $styletable, $stylefirstrow);
        $table = $section->addTable('myTable');

        foreach($arrys_result as $arrys_inx=>$arry_result):
        //---------------------------------------------------
        //接收欄位
        //---------------------------------------------------

            extract($arry_result, EXTR_PREFIX_ALL, "rs");

        //---------------------------------------------------
        //處理欄位
        //---------------------------------------------------

            $rs_book_sid=trim($rs_book_sid);
            if(in_array($rs_book_sid,$arry_book_sid)){
                continue;
            }

        //---------------------------------------------------
        //特殊處理
        //---------------------------------------------------

            //推薦識別碼陣列
            $arrys_rec_sid=array();

            //-----------------------------------------------
            //查找, 使用者資訊
            //-----------------------------------------------

                $get_user_info=get_user_info($conn_user,$user_id,$array_filter=array("name"),$arry_conn_user);
                if(!empty($get_user_info)){
                    $rs_user_name=trim($get_user_info[0]['name']);
                }

            //-----------------------------------------------
            //查找, 書名
            //-----------------------------------------------

                $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_name'),$arry_conn_mssr);
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
                $rec_draw_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='draw',$array_filter=array("rec_sid"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_draw_info)){
                    foreach($rec_draw_info as $inx=>$arry){
                        //匯入推薦識別碼陣列
                        array_push($arrys_rec_sid,trim($arry['rec_sid']));
                    }
                    //圖片識別碼
                    $rs_rec_draw_sid=trim($rec_draw_info[0]['rec_sid']);

                    //$root         =str_repeat("../",6)."info/user/".(int)$user_id."/book";

                    //手繪
                    $draw_path      ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/1.jpg";
                    //$draw_path_enc  =mb_convert_encoding($draw_path,$fso_enc,$page_enc);
                    $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);
                    $http_draw_path =$http_path.trim($rs_book_sid)."/draw/bimg/1.jpg";

                    //上傳
                    //$up_load_draw_path_1    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_1.jpg";
                    //$up_load_draw_path_1_enc=mb_convert_encoding($up_load_draw_path_1,$fso_enc,$page_enc);
                    //$up_load_draw_path_2    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_2.jpg";
                    //$up_load_draw_path_2_enc=mb_convert_encoding($up_load_draw_path_2,$fso_enc,$page_enc);
                    //$up_load_draw_path_3    ="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_3.jpg";
                    //$up_load_draw_path_3_enc=mb_convert_encoding($up_load_draw_path_3,$fso_enc,$page_enc);
                    //$arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                    //$arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                    //$arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);

                    //$draw_path      ="{$root}/".trim($rs_book_sid)."/draw/bimg/1.jpg";
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
                $rec_star_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='star',$array_filter=array("rec_sid","rec_rank","rec_reason"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
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
                $rec_text_info=get_rec_info($conn_mssr,$user_id,trim($rs_book_sid),$rec_type='text',$array_filter=array("rec_sid","rec_content","rec_state"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
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

                if($rs_rec_draw_sid!=='' && !empty($arry_ftp_file_draw_path)&& !empty($arry_ftp_file_draw_path[0])){
                    try{
                        ftp_get($ftp_conn, "tmp1/{$rs_book_sid}.jpeg", $arry_ftp_file_draw_path[0], FTP_BINARY);
                        imagejpeg(imagecreatefromstring(file_get_contents("tmp1/{$rs_book_sid}.jpeg")),"tmp1/{$rs_book_sid}.jpeg");
                    }catch(Exception $e) {
                        continue;
                    }
                }

                $table->addRow();
                $c1=$table->addCell(4500);
                $c2=$table->addCell(4500);

                $c1->addText("學生名稱: {$rs_user_name}");
                $c1->addText("書籍名稱: {$rs_book_name}");
                $c2->addText("建立時間: {$rs_keyin_cdate}");
                $c2->addText("最後更新時間: {$rs_keyin_mdate}");

                $table->addRow();
                $c3=$table->addCell(4500);
                $c4=$table->addCell(4500);

                $imageStyle = array('width'=>205, 'height'=>190, 'align'=>'center');
                $c3->addImage("tmp1/{$rs_book_sid}.jpeg", $imageStyle);

                $c4->addText("評價 : {$rs_rec_star_rank}");
                if($has_star){
                    $c4->addText("理由 :{$arrys_rs_rec_star_reason}");
                }else{
                    $c4->addText("理由 :{$rs_rec_star_reason}");
                }

                $c4->addText("");

                $c4->addText("【最喜歡的一句話】");
                if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[0]!=='')){
                    $arrys_rs_rec_text_content[0]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));
                    $c4->addText("$arrys_rs_rec_text_content[0]");
                    $c4->addText("");
                }else{
                    $c4->addText("");
                }
                $c4->addText("【書本內容介紹】");
                if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[1]!=='')){
                    $arrys_rs_rec_text_content[1]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));
                    $c4->addText("$arrys_rs_rec_text_content[1]");
                    $c4->addText("");
                }else{
                    $c4->addText("");
                }
                $c4->addText("【書中所學到的事】");
                if($rs_rec_text_state==='顯示'&&trim($arrys_rs_rec_text_content[2]!=='')){
                    $arrys_rs_rec_text_content[2]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));
                    $c4->addText("$arrys_rs_rec_text_content[2]");
                    $c4->addText("");
                }else{
                    $c4->addText("");
                }

    endforeach;

    //Save File
    header('Content-Type: application/vnd.ms-word');
    header("Content-Disposition: attachment;filename={$rs_user_name}.docx");
    header('Cache-Control: max-age=0');
    $objWriter = PHPWord_IOFactory::createWriter($phpword, 'Word2007');
    $objWriter->save('php://output');
?>