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
        //追蹤的書籍 SQL
        //-----------------------------------------------

            $sql="
                SELECT `mssr_forum`.`mssr_forum_track_book`.`book_sid`
                FROM  `mssr_forum`.`mssr_forum_track_book`
                WHERE `mssr_forum`.`mssr_forum_track_book`.`user_id`={$user_id}
            ";
            $track_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //追蹤的小組 SQL
        //-----------------------------------------------

            $sql="
                SELECT `mssr_forum`.`mssr_forum_track_group`.`group_id`,
                       `mssr_forum`.`mssr_forum_group`.`group_name`
                FROM  `mssr_forum`.`mssr_forum_track_group`
                    INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                    `mssr_forum`.`mssr_forum_track_group`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                WHERE `mssr_forum`.`mssr_forum_track_group`.`user_id`={$user_id}
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
            ";
            $track_group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //追蹤的文章 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_track_article`.`group_id`,
                    `mssr_forum`.`mssr_forum_track_article`.`article_id`,
                    `mssr_forum`.`mssr_forum_track_article`.`keyin_cdate`,
                    `mssr_forum`.`mssr_forum_article_detail`.`article_title`
                FROM  `mssr_forum`.`mssr_forum_track_article`
                    INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
                    `mssr_forum`.`mssr_forum_track_article`.`article_id`=`mssr_forum`.`mssr_forum_article_detail`.`article_id`

                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                    `mssr_forum`.`mssr_forum_track_article`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                WHERE `mssr_forum`.`mssr_forum_track_article`.`user_id`={$user_id}
                    AND `mssr_forum`.`mssr_forum_article`.`article_state`=1
            ";
            $track_article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

        //-----------------------------------------------
        //朋友推薦給我的書 SQL
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`book_sid`
                FROM `mssr_forum`.`mssr_forum_user_request`
                    INNER JOIN `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev` ON
                    `mssr_forum`.`mssr_forum_user_request`.`request_id`=`mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`request_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_user_request`.`request_from` ={$user_id}
                    AND `mssr_forum`.`mssr_forum_user_request`.`request_state` IN (1)
                    AND `mssr_forum`.`mssr_forum_user_request`.`request_read` =1
                    AND `mssr_forum`.`mssr_forum_user_request_rec_us_book_rev`.`book_sid` <> ''
            ";
            $request_rec_us_book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>
<!-- 追蹤 -->
<div class="user_lefe_side_tab3 row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;">
        <div class="panel-group" id="accordion">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                            href="#collapseOne">
                            追蹤的書籍
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        if(!empty($track_book_results)){
                            foreach($track_book_results as $track_book_result):
                                $rs_book_sid=trim($track_book_result['book_sid']);
                                if($rs_book_sid!==''){
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(empty($arry_book_infos))continue;
                                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                    if(mb_strlen($rs_book_name)>25){
                                        $rs_book_name=mb_substr($rs_book_name,0,25)."..";
                                    }
                                    $rs_book_img    ='../img/default/book.png';
                                    if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                        $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                    }
                                }
                        ?>
                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                            <div class="thumbnail" style="height:150px;">
                                <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                    <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                    <div class="caption"><?php echo ($rs_book_name);?></div>
                                </a>
                            </div>
                        </div>
                        <?php endforeach;}?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                            href="#collapseThree">
                            收藏的小組
                        </a>
                    </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        if(!empty($track_group_results)){
                            foreach($track_group_results as $track_group_result):
                                $rs_group_id    =(int)$track_group_result['group_id'];
                                $rs_group_name  =trim($track_group_result['group_name']);
                                $rs_group_img   ='../img/default/group.jpg';

                                if($rs_group_id!==0&&isset($sess_school_code)&&trim($sess_school_code)!==''){
                                    $arry_blacklist_group_school=get_blacklist_group_school($sess_school_code,$rs_group_id,$arry_conn_mssr);
                                    if(!empty($arry_blacklist_group_school))continue;
                                }
                        ?>
                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                            <div class="thumbnail" style="height:150px;">
                                <a href="article.php?get_from=2&group_id=<?php echo addslashes($rs_group_id);?>">
                                    <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_group_img;?>" alt="Generic placeholder thumbnail">
                                    <div class="caption"><?php echo ($rs_group_name);?></div>
                                </a>
                            </div>
                        </div>
                        <?php endforeach;}?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                            href="#collapseTwo">
                            收藏的文章
                        </a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php if(!empty($track_article_results)){?>
                        <table class="user_lefe_side_tab1 table table-striped">
                            <thead><tr class="second_tr" align="left">
                                <td width='75%'><span>發表的內容</span></td>
                                <td><span>追蹤時間</span></td>
                            </tr></thead>
                            <tbody>
                            <?php
                                foreach($track_article_results as $track_article_result):
                                    $rs_group_id     =(int)$track_article_result['group_id'];
                                    $rs_article_id   =(int)$track_article_result['article_id'];
                                    $rs_article_title=trim($track_article_result['article_title']);
                                    $rs_keyin_cdate  =trim($track_article_result['keyin_cdate']);

                                    if($rs_group_id===0)$get_from=1;
                                    if($rs_group_id!==0)$get_from=2;
                            ?>
                            <tr align="left">
                                <td style="border:0px;">
                                    <a target="_blank" href="reply.php?get_from=<?php echo $get_from;?>&article_id=<?php echo $rs_article_id;?>">
                                        標題：<?php echo htmlspecialchars($rs_article_title);?>
                                    </a>
                                </td>
                                <td style="border:0px;">
                                    <?php echo htmlspecialchars($rs_keyin_cdate);?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                        <?php }?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion"
                            href="#collapseSix">
                            朋友推薦給我的書
                        </a>
                    </h4>
                </div>
                <div id="collapseSix" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <?php
                        if(!empty($request_rec_us_book_results)){
                            foreach($request_rec_us_book_results as $request_rec_us_book_result):
                                $rs_book_sid=trim($request_rec_us_book_result['book_sid']);
                                if($rs_book_sid!==''){
                                    $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                    if(empty($arry_book_infos))continue;
                                    $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                    if(mb_strlen($rs_book_name)>20){
                                        $rs_book_name=mb_substr($rs_book_name,0,20)."..";
                                    }
                                    $rs_book_img    ='../img/default/book.png';
                                    if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                        $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                    }
                                }
                        ?>
                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                            <div class="thumbnail" style="height:150px;">
                                <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                                    <img width="80" height="80" style="weight:80px;height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                                    <div class="caption"><?php echo ($rs_book_name);?></div>
                                </a>
                            </div>
                        </div>
                        <?php endforeach;}?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    ftp_close($ftp_conn);
?>