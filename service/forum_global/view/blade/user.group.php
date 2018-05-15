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

            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
            $arry_conn_mssr_country=get_conn_country($user_id,$account);
            $conn_mssr_country=conn($db_type='mysql',$arry_conn_mssr_country);
            $conn_country_code=trim($arry_conn_mssr_country['db_country']);

        //-----------------------------------------------
        //小組 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum_global`.`mssr_forum_group`.`group_id`,
                    `mssr_forum_global`.`mssr_forum_group`.`group_name`,
                    `mssr_forum_global`.`mssr_forum_group`.`group_state`,

                    `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_country_code`,
                    `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_type`,
                    `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_state`
                FROM `mssr_forum_global`.`mssr_forum_group`
                    INNER JOIN `mssr_forum_global`.`mssr_forum_group_user_rev` ON
                    `mssr_forum_global`.`mssr_forum_group`.`group_id`=`mssr_forum_global`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_id`={$user_id}
                    AND `mssr_forum_global`.`mssr_forum_group_user_rev`.`user_country_code`='{$conn_country_code}'
                    AND `mssr_forum_global`.`mssr_forum_group`.`group_state`<>2
            ";
            //echo "<Pre>";print_r($sql);echo "</Pre>";
            $group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>
<div class="user_lefe_side_tab3 row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
        <pre style="background-color:#ffffdd;">目前共有【<?php echo count($group_results);?>】個小組&nbsp;</pre>
        <!-- <button type="button" class="btn btn-default btn-xs" onclick='location.href="forum.php?method=add_group";void(0);'>建立聊書小組</button> -->
    </div>
    <?php
    if(!empty($group_results)){
        foreach($group_results as $group_result):
            $rs_group_id    =(int)$group_result['group_id'];
            $rs_group_name  =trim($group_result['group_name']);
            $rs_group_img   ='../img/default/group.jpg';

            if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                if(!empty($arry_blacklist_group_school))continue;
            }

            $rs_group_state =(int)$group_result['group_state'];
            $rs_group_state_html='';
            switch($rs_group_state){
                case 1:
                    $rs_group_state_html='啟用';
                break;
                case 2:
                    $rs_group_state_html='停用';
                break;
                case 3:
                    $rs_group_state_html='連署中';
                break;
                default:
                    continue;
                break;
            }

            $rs_user_type   =(int)$group_result['user_type'];
            $rs_user_type_html='';
            switch($rs_user_type){
                case 1:
                    $rs_user_type_html='一般組員';
                break;
                case 2:
                    $rs_user_type_html='一般版主';
                break;
                case 3:
                    $rs_user_type_html='高級版主';
                break;
                default:
                    continue;
                break;
            }

            $rs_user_state  =(int)$group_result['user_state'];
            $rs_user_state_html='';
            switch($rs_user_state){
                case 1:
                    $rs_user_state_html='啟用';
                break;
                case 2:
                    $rs_user_state_html='停用';
                break;
                case 3:
                    $rs_user_state_html='申請中';
                break;
                default:
                    continue;
                break;
            }

            //小組大頭貼
            $rs_group_img   ='../img/default/group.jpg';
    ?>
    <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
        <div class="thumbnail" style="height:250px;">
            <?php //if($user_id===$sess_user_id && $sess_country_code===$conn_country_code):?>
                <a href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>" onclick="block_ui('網頁讀取中...');">
                    <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_group_img;?>" alt="Generic placeholder thumbnail">
                </a>
            <?php //else:?>
                <!-- <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_group_img;?>" alt="Generic placeholder thumbnail"> -->
            <?php //endif;?>
            <div class="caption">
                <?php echo htmlspecialchars($rs_group_name);?>
                <br>
                <?php if($rs_group_state!==3):?>
                    (<?php echo htmlspecialchars($rs_user_type_html);?>,<?php echo htmlspecialchars($rs_user_state_html);?>)
                <?php endif;?>
            </div>
        </div>
    </div>
    <?php endforeach;}?>
</div>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>