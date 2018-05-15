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

        //外掛頁面檔
        require_once(str_repeat("../",2).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/forum/inc/code'
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

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_sess_login_info)){
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='/ac/index.php';
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

        $user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:0;
        $tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

        //-----------------------------------------------
        //小組 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_group`.`group_id`,
                    `mssr_forum`.`mssr_forum_group`.`group_name`,
                    `mssr_forum`.`mssr_forum_group`.`group_state`,

                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_type`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`
                FROM `mssr_forum`.`mssr_forum_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                    `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`={$user_id}
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`<>2
            ";
            $group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>
<div class="user_lefe_side_tab3 row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
        <pre style="background-color:#ffffdd;">目前共有【<?php echo count($group_results);?>】個小組&nbsp;<button type="button" class="btn btn-default btn-xs" onclick='location.href="forum.php?method=add_group";void(0);'>建立聊書小組</button></pre>
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

            //連署進度
            if($rs_group_state===3){
                $create_cno    =0;
                $create_success=0;
                $create_percent=0;
                $sql="
                    SELECT
                        `mssr_forum`.`mssr_forum_user_request`.`request_state`
                    FROM `mssr_forum`.`mssr_forum_group`
                        INNER JOIN `mssr_forum`.`mssr_forum_user_request_create_group_rev` ON
                        `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`

                        INNER JOIN `mssr_forum`.`mssr_forum_user_request` ON
                        `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`request_id`=`mssr_forum`.`mssr_forum_user_request`.`request_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_user_request_create_group_rev`.`group_id`={$rs_group_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                $create_cno=2;
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_request_state=(int)$db_result['request_state'];
                        if($rs_request_state===1)$create_success++;
                    }
                    $create_percent=ceil(($create_success/$create_cno)*100);
                }
            }

            //小組大頭貼
            $rs_group_img   ='../img/default/group.jpg';

            ////FTP 路徑
            //$ftp_root="public_html/mssr/info/forum";
            //$ftp_path="{$ftp_root}/group/{$rs_group_id}/group_sticker";
            //
            ////連接 | 登入 FTP
            //$ftp_conn  =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
            //$ftp_login =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
            //
            ////設定被動模式
            //ftp_pasv($ftp_conn,TRUE);
            //
            ////獲取檔案目錄
            //$arry_ftp_file=ftp_nlist($ftp_conn,$ftp_path);
            //
            //if(!empty($arry_ftp_file)){
            //    $rs_group_img="http://".$arry_ftp1_info['host']."/mssr/info/forum/group/{$rs_group_id}/group_sticker/1.jpg";
            //}
    ?>
    <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
        <div class="thumbnail" style="height:250px;">
            <a href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>">
                <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_group_img;?>" alt="Generic placeholder thumbnail">
            </a>
            <div class="caption">
                <?php if($rs_group_state===3):?>
                    <div class="progress progress-striped active" style="position:relative;margin-bottom:5px;">
                        <div class="progress-bar progress-bar-success" role="progressbar"
                            style="width: <?php echo $create_percent;?>%;">
                            <span class=""><?php echo $create_percent;?>%</span>
                        </div>
                    </div>
                <?php endif;?>
                <?php echo htmlspecialchars($rs_group_name);?>
                <?php if($rs_group_state===3):?>
                    (<?php echo htmlspecialchars($rs_group_state_html);?>)
                <?php endif;?>
                <br>
                <?php if($rs_group_state!==3):?>
                    <?php //echo htmlspecialchars($user_name);?>
                    <!-- <br> -->
                    (<?php echo htmlspecialchars($rs_user_type_html);?>,<?php echo htmlspecialchars($rs_user_state_html);?>)
                <?php endif;?>
                <?php if($rs_group_state===3):?>
                    <button type="button" class="btn btn-default btn-xs"  style="position:relative;margin-top:5px;"
                    onclick='location.href="forum.php?method=view_create_group_info&group_id=<?php echo $rs_group_id;?>";void(0);'>
                        查看連署詳情
                    </button>
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