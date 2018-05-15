<?php
//-------------------------------------------------------
//明日聊書
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
        require_once(str_repeat("../",2).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",2).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum_global/inc/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入,SESSION
    //---------------------------------------------------

        if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
            $sess_country_code    =trim($_SESSION['mssr_forum_global']['country_code']);
            $arry_conn_user       ="arry_conn_user_{$sess_country_code}";
            $arry_conn_user       =$$arry_conn_user;
            $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
            if(empty($arrys_sess_login_info)){
                $msg="請先登入!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        location.href='login.php';
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            $msg="請先登入!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='login.php';
                </script>
            ";
            die($jscript_back);
        }

        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
        if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
        if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
        if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
        if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
        if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
        if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
        if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
        if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
        if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
        if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
            $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
            foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
            }
        }

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------

        $user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:(int)$sess_user_id;
        $account=(isset($_GET['account']))?trim($_GET['account']):trim($sess_account);
        $tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            $arry_conn_mssr=get_conn_country($user_id,$account);
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
            $conn_country_code=trim($conn_host_country_code[$arry_conn_mssr['db_host']]);

        //-----------------------------------------------
        //page_info SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //書櫃 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_log`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id`={$user_id}
                GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
                         `mssr`.`mssr_book_borrow_log`.`book_sid`
                ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
            ";
            $book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //書櫃類型 SQL
        //-----------------------------------------------

        //-----------------------------------------------
        //書籍類別
        //-----------------------------------------------

        //-----------------------------------------------
        //書籍類別關聯
        //-----------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>

<!-- 書櫃 -->
<div class="user_lefe_side_tab2 row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-sm-12 col-md-12 col-lg-12 hidden" style="text-align:center;">
            <pre style="background-color:#ffffdd;"></pre>
        </div>
        <?php
        if(!empty($book_results)){
            foreach($book_results as $book_result):
                $rs_book_sid=trim($book_result['book_sid']);
                if($rs_book_sid!==''){
                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                    if(empty($arry_book_infos))continue;
                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                    if(mb_strlen($rs_book_name)>10){
                        $rs_book_name=mb_substr($rs_book_name,0,10)."..";
                    }
                    if(trim($rs_book_name)==='')continue;
                    $rs_book_img    ='../img/default/book.png';
                    if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                        $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                    }
                    if(preg_match("/^mbu/i",$rs_book_sid)){
                        $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                        if(!empty($get_book_info)){
                            $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                            if($rs_book_verified===2)continue;
                        }else{continue;}
                    }
                }
        ?>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2 book_thumbnail_col" book_sid='<?php echo addslashes($rs_book_sid);?>'>
            <div class="thumbnail" style="border:1px solid #e1e1e1;margin:10px 0;overflow:hidden;"
            onmouseover="this.style.backgroundColor='#e1e1e1';"
            onmouseout="this.style.backgroundColor='#fff';"
            >
                <!-- <a href="#"> -->
                    <img height="80" style="height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                    <div style="clear:both;"></div>
                    <div class="caption" style="width:100%;"><?php echo htmlspecialchars($rs_book_name);?></div>
                <!-- </a> -->
            </div>
        </div>
        <?php endforeach;}?>
    </div>
</div>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------
</script>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    @ftp_close($ftp_conn);
?>