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
        //page_info SQL
        //-----------------------------------------------

            $sql="
                SELECT `user`.`member_school`.`school_code`
                FROM `user`.`member_school`
                WHERE 1=1
                    AND `user`.`member_school`.`uid`={$user_id}
                    AND `user`.`member_school`.`end`='0000-00-00'
                LIMIT 1

                #    SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
                #    FROM `user`.`class`
                #        INNER JOIN `user`.`class_name` ON
                #        `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                #        INNER JOIN `user`.`student` ON
                #        `user`.`class`.`class_code`=`user`.`student`.`class_code`
                #        INNER JOIN `user`.`semester` ON
                #        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                #        INNER JOIN `user`.`school` ON
                #        `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                #    WHERE 1=1
                #        AND `user`.`student`.`uid`={$user_id}
                #        AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
                #        AND `user`.`student`.`start`<=NOW()
                #        AND `user`.`student`.`end`  >=NOW()
                #    GROUP BY `user`.`class`.`class_code`
                #
                #UNION
                #
                #    SELECT `user`.`school`.`school_code`, `user`.`school`.`school_name`, `user`.`class`.`grade`, `user`.`class_name`.`class_name`
                #    FROM `user`.`class`
                #        INNER JOIN `user`.`class_name` ON
                #        `user`.`class`.`class_category`=`user`.`class_name`.`class_category`
                #        INNER JOIN `user`.`teacher` ON
                #        `user`.`class`.`class_code`=`user`.`teacher`.`class_code`
                #        INNER JOIN `user`.`semester` ON
                #        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                #        INNER JOIN `user`.`school` ON
                #        `user`.`semester`.`school_code`=`user`.`school`.`school_code`
                #    WHERE 1=1
                #        AND `user`.`teacher`.`uid`={$user_id}
                #        AND `user`.`class`.`classroom` =`user`.`class_name`.`classroom`
                #        AND `user`.`teacher`.`start`<=NOW()
                #        AND `user`.`teacher`.`end`  >=NOW()
                #    GROUP BY `user`.`class`.`class_code`
            ";
            $arry_user_school_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            $user_school_code='';
            if(!empty($arry_user_school_results))$user_school_code=trim($arry_user_school_results[0]['school_code']);

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

            $arry_book_category_rev_cno=array();
            $book_category_rev_results =array();
            $lists_book_category_rev='';
            if(!empty($book_results)&&(isset($user_school_code))&&(trim($user_school_code)!=='')){
                $sql="
                    SELECT `mssr`.`mssr_book_category`.`cat_name`
                    FROM `mssr`.`mssr_book_category`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                ";
                $book_category_rev_cno_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_cno_results)){
                    foreach($book_category_rev_cno_results as $arry_val){
                        $arry_book_category_rev_cno[trim($arry_val['cat_name'])]=0;
                    }
                }

                $arry_book_category_rev=array();
                foreach($book_results as $book_result){
                    $rs_book_sid=trim($book_result['book_sid']);
                    $arry_book_category_rev[]=$rs_book_sid;
                    $lists_book_category_rev=implode("','",$arry_book_category_rev);
                }

                $sql="
                    SELECT `mssr`.`mssr_book_category`.`cat_name`
                    FROM `mssr`.`mssr_book_category`
                        INNER JOIN `mssr`.`mssr_book_category_rev` ON
                        `mssr`.`mssr_book_category`.`cat_code`=`mssr`.`mssr_book_category_rev`.`cat_code`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`cat2_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat3_id`    =1
                        AND `mssr`.`mssr_book_category`.`cat_state`  ='啟用'
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category_rev`.`book_sid` IN ('{$lists_book_category_rev}')
                ";
                $book_category_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_results)){
                    foreach($book_category_rev_results as $book_category_rev_result){
                        $rs_cat_name=trim($book_category_rev_result['cat_name']);
                        if(array_key_exists($rs_cat_name,$arry_book_category_rev_cno)){
                            $arry_book_category_rev_cno[$rs_cat_name]=(int)($arry_book_category_rev_cno[$rs_cat_name]+1);
                        }
                    }
                    $arry_book_category_rev_cno['未分類']=(int)(count($book_results)-count($book_category_rev_results));
                }

                $category_rev_cno_val_0=0;
                foreach($arry_book_category_rev_cno as $val){
                    if((int)$val===0)$category_rev_cno_val_0++;
                }
                if($category_rev_cno_val_0===count($arry_book_category_rev_cno))$arry_book_category_rev_cno=array();
            }

        //-----------------------------------------------
        //書籍類別
        //-----------------------------------------------

            $book_category_results=array();
            if((isset($user_school_code))&&(trim($user_school_code)!=='')){
                $sql="
                    SELECT
                        `mssr`.`mssr_book_category`.`cat_name`,
                        `mssr`.`mssr_book_category`.`cat_code`
                    FROM `mssr`.`mssr_book_category`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category`.`cat1_id`<>1
                        AND `mssr`.`mssr_book_category`.`cat2_id`=1
                        AND `mssr`.`mssr_book_category`.`cat3_id`=1
                ";
                $book_category_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            }

        //-----------------------------------------------
        //書籍類別關聯
        //-----------------------------------------------

            $arrys_book_category_rev=array();
            $json_book_category_rev=json_encode($arrys_book_category_rev,true);
            if((isset($user_school_code))&&(trim($user_school_code)!=='')&&(trim($lists_book_category_rev)!=='')){

                //硬標籤
                $sql="
                    SELECT
                        `mssr`.`mssr_book_category`.`cat_name`,
                        `mssr`.`mssr_book_category`.`cat_code`,
                        `mssr`.`mssr_book_category_rev`.`book_sid`
                    FROM `mssr`.`mssr_book_category`
                        INNER JOIN `mssr`.`mssr_book_category_rev` ON
                        `mssr`.`mssr_book_category`.`cat_code`=`mssr`.`mssr_book_category_rev`.`cat_code`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category`.`cat1_id`<>1

                        AND `mssr`.`mssr_book_category_rev`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category_rev`.`book_sid` IN ('{$lists_book_category_rev}')
                ";
                $book_category_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_results)){
                    foreach($book_category_rev_results as $book_category_rev_result){
                        $rs_cat_name=trim($book_category_rev_result['cat_name']);
                        $rs_book_sid=trim($book_category_rev_result['book_sid']);
                        $arrys_book_category_rev[$rs_book_sid]['school'][]=$rs_cat_name;
                    }
                }

                //軟標籤
                $sql="
                    SELECT
                        `mssr`.`mssr_book_category`.`cat_name`,
                        `mssr`.`mssr_book_category`.`cat_code`,
                        `mssr`.`mssr_book_category_user_rev`.`book_sid`
                    FROM `mssr`.`mssr_book_category`
                        INNER JOIN `mssr`.`mssr_book_category_user_rev` ON
                        `mssr`.`mssr_book_category`.`cat_code`=`mssr`.`mssr_book_category_user_rev`.`cat_code`
                    WHERE 1=1
                        AND `mssr`.`mssr_book_category`.`school_code`='{$user_school_code}'
                        AND `mssr`.`mssr_book_category`.`cat1_id`<>1

                        AND `mssr`.`mssr_book_category_user_rev`.`create_by`={$user_id}
                        AND `mssr`.`mssr_book_category_user_rev`.`book_sid` IN ('{$lists_book_category_rev}')
                ";
                $book_category_rev_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                if(!empty($book_category_rev_results)){
                    foreach($book_category_rev_results as $book_category_rev_result){
                        $rs_cat_name=trim($book_category_rev_result['cat_name']);
                        $rs_book_sid=trim($book_category_rev_result['book_sid']);
                        $arrys_book_category_rev[$rs_book_sid]['user'][]=$rs_cat_name;
                    }
                }

                $json_book_category_rev=json_encode($arrys_book_category_rev,true);
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------
?>

<!-- 書櫃 -->
<div class="user_lefe_side_tab2 row">
    <?php if(empty($arry_book_category_rev_cno)):?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;">
            <pre style="background-color:#ffffdd;">目前書櫃共有【<?php echo count($book_results);?>】本書<?php if($user_id===$sess_user_id):?>&nbsp;<button type="button" class="btn btn-default btn-xs" onclick='location.href="/mssr/service/code.php?mode=read_the_registration";void(0);'>進行閱讀登記</button><?php endif;?></pre>
        </div>
    <?php endif;?>
    <?php if(!empty($arry_book_category_rev_cno)):?>
        <!-- 大解析度 -->
        <!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 hidden-xs" style="text-align:center;margin-bottom:15px;">
            <pre style="background-color:#ffffdd;">目前書櫃共有【<?php echo count($book_results);?>】本書<?php if($user_id===$sess_user_id):?>&nbsp;<button type="button" class="btn btn-default btn-xs" onclick='location.href="/mssr/service/code.php?mode=read_the_registration";void(0);'>進行閱讀登記</button><?php endif;?></pre>
        </div> -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs"
        style="width:97%;text-align:center;margin-bottom:15px;background-color:#ffffdd;border:1px solid #cccccc;border-radius:3px;left:15px;">
            <div class="row">
                <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="text-align:center;">
                    <table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" style="position:relative;margin-top:5px;">
                        <tr align="center" valign="middle" height="35">
                            <td colspan="2">目前書櫃共有【<?php echo count($book_results);?>】本書<?php if($user_id===$sess_user_id):?>&nbsp;<button type="button" class="btn btn-default btn-xs" onclick='location.href="/mssr/service/code.php?mode=read_the_registration";void(0);'>進行閱讀登記</button><?php endif;?></td>
                        </tr>
                    </table>
                    <table align="center" cellpadding="0" cellspacing="0" border="0" width="70%" height="100%" style="position:relative;left:10%;">
                        <tr align="left" valign="middle">
                            <td width="50%">生活：<?php echo (int)$arry_book_category_rev_cno[trim('生活')];?>本(<?php echo round((int)($arry_book_category_rev_cno[trim('生活')])/(count($book_results)-(int)$arry_book_category_rev_cno[trim('未分類')])*100);?>%)</td>
                            <td>文學：<?php echo (int)$arry_book_category_rev_cno[trim('文學')];?>本(<?php echo round((int)($arry_book_category_rev_cno[trim('文學')])/(count($book_results)-(int)$arry_book_category_rev_cno[trim('未分類')])*100);?>%)</td>
                        </tr>
                        <tr align="left" valign="middle">
                            <td>科學：<?php echo (int)$arry_book_category_rev_cno[trim('科學')];?>本(<?php echo round((int)($arry_book_category_rev_cno[trim('科學')])/(count($book_results)-(int)$arry_book_category_rev_cno[trim('未分類')])*100);?>%)</td>
                            <td>藝術：<?php echo (int)$arry_book_category_rev_cno[trim('藝術')];?>本(<?php echo round((int)($arry_book_category_rev_cno[trim('藝術')])/(count($book_results)-(int)$arry_book_category_rev_cno[trim('未分類')])*100);?>%)</td>
                        </tr>
                        <tr align="left" valign="middle">
                            <td>社會：<?php echo (int)$arry_book_category_rev_cno[trim('社會')];?>本(<?php echo round((int)($arry_book_category_rev_cno[trim('社會')])/(count($book_results)-(int)$arry_book_category_rev_cno[trim('未分類')])*100);?>%)</td>
                            <td>未分類：<?php echo (int)$arry_book_category_rev_cno[trim('未分類')];?>本</td>
                        </tr>
                    </table>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4" style="text-align:center;">
                    <div class="flot_pie" style="width:170px;height:110px;"></div>
                </div>
            </div>
        </div>

        <!-- 小解析度 -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 visible-xs" style="text-align:center;">
            <pre style="background-color:#ffffdd;">目前書櫃共有【<?php echo count($book_results);?>】本書<?php if($user_id===$sess_user_id):?>&nbsp;<button type="button" class="btn btn-default btn-xs" onclick='location.href="/mssr/service/code.php?mode=read_the_registration";void(0);'>進行閱讀登記</button><?php endif;?></pre>
        </div>
    <?php endif;?>
    <!-- <div class="col-sm-2 col-md-2 col-lg-2 hidden-xs" style="text-align:center;">
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;margin-top:5px;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('全部');void(0);">
            <img src="../img/default/all.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>全部</div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('文學');void(0);">
            <img src="../img/default/literature.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>文學</div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('生活');void(0);">
            <img src="../img/default/life.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>生活</div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('科學');void(0);">
            <img src="../img/default/science.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>科學</div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('藝術');void(0);">
            <img src="../img/default/art.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>藝術</div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="margin:15px 0;"
        onmouseover="this.style.backgroundColor='#e1e1e1';this.style.cursor='pointer';"
        onmouseout="this.style.backgroundColor='#fff';"
        onclick="book_categroy_rev_filter('社會');void(0);">
            <img src="../img/default/society.png" width="62" height="100" border="0" alt="資料夾"/>
            <div>社會</div>
        </div>
    </div> -->
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
                <?php if(!empty($book_category_results)&&($sess_user_id===$user_id)):?>
                    <div>
                        <select class="form-control input-sm" name="cat_code" style="width:99%;margin-bottom:5px;"
                        onchange="edit_book_category_user_rev(this,'<?php echo addslashes($rs_book_sid);?>');void(0);">
                            <option class="disabled" disabled="disabled" selected>分類</option>
                            <?php foreach($book_category_results as $book_category_result):?>
                            <?php
                                $rs_cat_name=trim($book_category_result['cat_name']);
                                $rs_cat_code=trim($book_category_result['cat_code']);
                            ?>
                                <option value="<?php echo trim($rs_cat_code);?>"><?php echo htmlspecialchars($rs_cat_name);?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                <?php endif;?>
                <a href="article.php?get_from=1&book_sid=<?php echo addslashes($rs_book_sid);?>">
                    <img height="80" style="height:80px;" src="<?php echo $rs_book_img;?>" alt="Generic placeholder thumbnail">
                    <?php if(!empty($arrys_book_category_rev)&&isset($arrys_book_category_rev[$rs_book_sid])&&!empty($arrys_book_category_rev[$rs_book_sid])):
                            foreach($arrys_book_category_rev[$rs_book_sid] as $key=>$arry_book_category_rev):
                                $tmp_array_cat_name=[];
                                foreach($arry_book_category_rev as $rs_cat_name):
                                    $rs_cat_name=mb_substr(trim($rs_cat_name),0,1);
                                    if(in_array($rs_cat_name,$tmp_array_cat_name))continue;
                                    $tmp_array_cat_name[]=$rs_cat_name;
                    ?>
                        <?php if(trim($key)==='school'):?>
                            <div style="float:left;background-color:#000;color:#fff;font-weight:bold;border-radius:3px;margin:0 1px;">
                                <?php echo htmlspecialchars($rs_cat_name);?>
                            </div>
                        <?php else:?>
                            <div style="float:left;background-color:#fc0;color:#fff;font-weight:bold;border-radius:3px;margin:0 1px;">
                                <?php echo htmlspecialchars($rs_cat_name);?>
                            </div>
                        <?php endif;?>
                    <?php endforeach;endforeach;else:?>
                        <div style="float:left;">&nbsp;</div>
                    <?php endif;?>
                    <div style="clear:both;"></div>
                    <div class="caption" style="width:100%;"><?php echo htmlspecialchars($rs_book_name);?></div>
                </a>
            </div>
        </div>
        <?php endforeach;}?>
    </div>
</div>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //書籍類型圓餅圖
    try{
        $.plot($(".flot_pie"),category_dataset,{
            series:{pie:{show:true}},
            //legend:{show:false},//grid: {hoverable:true}
        });
    }catch(e){}

</script>
<?php
    $conn_user=NULL;
    $conn_mssr=NULL;
    @ftp_close($ftp_conn);
?>