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
        //書友 SQL
        //-----------------------------------------------

            $friend_results=get_forum_friend($user_id,$friend_id=0,$arry_conn_mssr);

        //-----------------------------------------------
        //個人大頭貼
        //-----------------------------------------------

            //FTP 路徑
            if(isset($file_server_enable)&&($file_server_enable)){
                //$ftp_root="public_html/mssr/info/user";
                //$ftp_path="{$ftp_root}/{$user_id}/forum/user_sticker";
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
                //    $user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg";
                //    $user_img_size=getimagesize($user_img);
                //}
                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg")){
                    $user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$user_id}/forum/user_sticker/1.jpg";
                    $user_img_size=getimagesize($user_img);
                }
            }else{
                if(file_exists("../../../info/user/{$user_id}/forum/user_sticker/1.jpg")){
                    $user_img="../../../info/user/{$user_id}/forum/user_sticker/1.jpg";
                    $user_img_size=getimagesize($user_img);
                }
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>
<div class="user_lefe_side_tab3 row">
    <?php
    if(!empty($friend_results)){
        foreach($friend_results as $inx=>$friend_result):
            $rs_friend_state=(int)$friend_result['friend_state'];
            $rs_user_id     =(int)$friend_result['user_id'];
            $rs_friend_id   =(int)$friend_result['friend_id'];
            if($rs_user_id!==$user_id || $rs_friend_id!==$user_id){
                $tmp_user_id=0;
                if($rs_user_id!==$user_id)$tmp_user_id=$rs_user_id;
                if($rs_friend_id!==$user_id)$tmp_user_id=$rs_friend_id;
                $sql="
                    SELECT
                        `name`,`sex`
                    FROM `user`.`member`
                    WHERE 1=1
                        AND `user`.`member`.`uid`={$tmp_user_id}
                ";
                $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                $rs_user_img  ='';
                $rs_user_name ='';
                $rs_user_sex  =1;
                if(!empty($db_results)){
                    $rs_user_name=trim($db_results[0]['name']);
                    $rs_user_sex =(int)$db_results[0]['sex'];
                    if($rs_user_sex===1)$rs_user_img='../img/default/user_boy.png';
                    if($rs_user_sex===2)$rs_user_img='../img/default/user_girl.png';
                }
            }
        if($rs_friend_state===1){
            if(isset($file_server_enable)&&($file_server_enable)){
                //$rs_user_img_ftp_path     ="{$ftp_root}/{$tmp_user_id}/forum/user_sticker";
                //$arry_rs_user_img_ftp_file=ftp_nlist($ftp_conn,$rs_user_img_ftp_path);
                //if(isset($arry_rs_user_img_ftp_file[0])){
                //    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                //}
                if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg")){
                    $rs_user_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                }
            }else{
                if(file_exists("../../../info/user/{$tmp_user_id}/forum/user_sticker/1.jpg")){
                    $rs_user_img="../../../info/user/{$tmp_user_id}/forum/user_sticker/1.jpg";
                }
            }
    ?>
    <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
        <div class="thumbnail" style="height:150px;">
            <a href="user.php?user_id=<?php echo $tmp_user_id;?>&tab=1">
                <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_user_img;?>" alt="Generic placeholder thumbnail">
                <div class="caption"><?php echo htmlspecialchars($rs_user_name);?></div>
            </a>
        </div>
    </div>
<?php }endforeach;}else{?>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
        <pre style="background-color:#ffffdd;">目前共有【<?php echo count($friend_results);?>】個書友</pre>
    </div>
<?php }?>
</div>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    //ftp_close($ftp_conn);
?>