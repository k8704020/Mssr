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

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/form/code'
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
    //area          回轉的頁面
    //type          指導類型
    //rec_sid       推薦識識別碼
    //user_id       使用者主索引
    //book_sid      書籍識別碼
    //anchor        錨點
    //date_filter   時間條件

        $get_chk=array(
            'area       ',
            'type       ',
            'rec_sid    ',
            'user_id    ',
            'book_sid   ',
            'anchor     '
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
    //area          回轉的頁面
    //type          指導類型
    //rec_sid       推薦識識別碼
    //user_id       使用者主索引
    //book_sid      書籍識別碼
    //anchor        錨點
    //date_filter   時間條件

        //GET
        $area    =trim($_GET[trim('area    ')]);
        $type    =trim($_GET[trim('type    ')]);
        $rec_sid =trim($_GET[trim('rec_sid ')]);
        $user_id =trim($_GET[trim('user_id ')]);
        $book_sid=trim($_GET[trim('book_sid')]);
        $anchor  =trim($_GET[trim('anchor  ')]);

        //date_filter   時間條件
        if(isset($_GET[trim('date_filter')])){
            $date_filter=trim($_GET[trim('date_filter')]);
            if(!in_array($date_filter,array("today","three_day","one_week","two_week","lose"))){
                $date_filter='';
            }
        }else{
            $date_filter='';
        }

        $scrolltop=(isset($_GET['scrolltop']))?(int)$_GET['scrolltop']:0;
        $semester_start=(isset($_GET['semester_start']))?trim($_GET['semester_start']):'';
        $semester_end=(isset($_GET['semester_end']))?trim($_GET['semester_end']):'';

        //SESSION
        $sess_user_id    =(int)$sess_login_info['uid'];
        $sess_permission =trim($sess_login_info['permission']);
        $sess_school_code=trim($sess_login_info['school_code']);
        $sess_class_code =trim($sess_login_info['arrys_class_code'][0]['class_code']);
        $sess_grade      =(int)$sess_login_info['arrys_class_code'][0]['grade'];
        $sess_classroom  =(int)$sess_login_info['arrys_class_code'][0]['classroom'];

        //分頁
        $psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        $pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        $psize=($psize===0)?10:$psize;
        $pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //area          回轉的頁面
    //type          指導類型
    //rec_sid       推薦識識別碼
    //user_id       使用者主索引
    //book_sid      書籍識別碼
    //anchor        錨點
    //date_filter   時間條件

        $arry_err=array();

        if($area===''){
           $arry_err[]='回轉的頁面,未輸入!';
        }

        if($type===''){
           $arry_err[]='指導類型,未輸入!';
        }else{
            $type=trim($type);
            if(!in_array($type,array('draw','text','record'))){
                $arry_err[]='指導類型,錯誤!';
            }
        }

        if($rec_sid===''){
           $arry_err[]='推薦識識別碼,未輸入!';
        }

        if($user_id===''){
           $arry_err[]='使用者主索引,未輸入!';
        }else{
            $user_id=(int)$user_id;
            if($user_id===0){
                $arry_err[]='使用者主索引,錯誤!';
            }
        }

        if($book_sid===''){
           $arry_err[]='書籍識別碼,未輸入!';
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
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //更新書店葵幣開放條件
        //-----------------------------------------------

            update_coin_open($db_type='mysql',$arry_conn_mssr,$APP_ROOT,$sess_user_id);

        //-----------------------------------------------
        //書店葵幣開放條件
        //-----------------------------------------------

            $query_sql="
                SELECT
                    `user_id`,
                    `auth`,
                    `keyin_mdate`
                FROM `mssr_auth_user`
                WHERE 1=1
                    AND `user_id`={$sess_user_id}
            ";
            //echo $query_sql;

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$query_sql,array(0,1),$arry_conn_mssr);
            $arry_result=$arrys_result[0];

            //匯入該名老師陣列資訊
            $arrys_this_teacher['auth'] =unserialize($arry_result['auth']);
            $bookstore_coin_open        =trim($arrys_this_teacher['auth']['coin_open']);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //area          回轉的頁面
        //type          指導類型
        //rec_sid       推薦識識別碼
        //user_id       使用者主索引
        //book_sid      書籍識別碼
        //anchor        錨點
        //date_filter   時間條件

            $type      =mysql_prep($type    );
            $rec_sid   =mysql_prep($rec_sid );
            $user_id   =mysql_prep($user_id );
            $book_sid  =mysql_prep($book_sid);
            $table_name="mssr_rec_book_{$type}_log";

            $sql="
                SELECT
                    `user_id`
                FROM `{$table_name}`
                WHERE 1=1
                    AND `user_id` = {$user_id }
                    AND `rec_sid` ='{$rec_sid }'
                    AND `book_sid`='{$book_sid}'
            ";
            //echo $sql.'<br/>';

            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(empty($arrys_result)){
                die();
            }

            if($type==='text'){
                $rec_text_info=get_rec_info($conn_mssr,$user_id,trim($book_sid),$rec_type='text',$array_filter=array("rec_content"),$order_by_filter='',$arry_limit=array(),$arry_conn_mssr);
                if(!empty($rec_text_info)){
                    //文字內容
                    $rs_rec_text_content=trim($rec_text_info[0]['rec_content']);
                    if(@unserialize($rs_rec_text_content)){
                        $arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
                        $arrys_rs_rec_text_content=array_map("base64_decode",$arrys_rs_rec_text_content);
                        $arrys_rs_rec_text_content=array_map("gzuncompress",$arrys_rs_rec_text_content);
                        $arrys_rs_rec_text_content=array_map("htmlspecialchars",$arrys_rs_rec_text_content);
                        $arrys_rs_rec_text_content=array_map("trim",$arrys_rs_rec_text_content);
                    }
                }
            }

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
    <link rel="stylesheet" type="text/css" href="../../../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../../../lib/js/string/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <link rel="stylesheet" type="text/css" href="../../../../css/def.css" media="all" />

    <style>
        /* 容器微調 */
        #container{
            width:600px;
        }
        body{
            overflow: hidden;
        }
    </style>
</Head>

<Body>

<!-- 容器區塊 開始 -->
<div id="container">

    <!-- 內容區塊 開始 -->
    <form id='Form1' name='Form1' method='post' onsubmit="return false;">
        <table cellpadding="0" cellspacing="0" border="0" width="700px" align="left"/>
            <tr class="fsize_18 font-weight1" align="center">
                <td colspan="2">
                    <?php if(isset($arrys_rec_type[$type])):?>
                        [<?php echo htmlspecialchars($arrys_rec_type[$type]);?>推薦指導]
                    <?php else:?>
                        [推薦指導]
                    <?php endif;?>
                </td>
            </tr>
            <tr class="fsize_18 font-weight1" align="left">
                <td colspan="2">
                <!-- 給予分數 -->
                    <div style="position:relative;left:5px;width:590px;float:left;">
                        給予分數<span class="fc_red0">(必填)</span>
                    </div>
                    <?php for($i=1;$i<=5;$i++):?>
                        <div style="left:-10px;position:relative;top:15px;float:left;margin:5px 20px;border:0px solid red;width:95px;">
                            <img src="../../../../img/user/user_rec/<?php echo $i;?>.png" width="62px" height="62px" border="0" alt="<?php echo $i;?>分" style="float:left;"/>
                            <?php if(file_exists("../../../../img/user/user_rec/cup{$i}.png")):?>
                                <img src="../../../../img/user/user_rec/cup<?php echo $i;?>.png" width="31px" height="31px" border="0" alt="" style="float:left;"/>
                            <?php endif;?>
                            <br/>
                            <div style="clear:both;"></div>
                            <span style="position:relative;left:20px;top:5px;">
                                <input type="radio" value="<?php echo $i;?>" id="comment_score<?php echo $i;?>" name="comment_score"/>
                            </span>
                        </div>
                    <?php endfor;?>
                </td>
            </tr>
            <tr class="fsize_18 font-weight1" align="left">
                <td width="280px">
                <!-- 給予或扣除錢幣 -->
                    <div class="mod_data_tbl_outline" style="width:290px;height:120px;position:relative;top:30px;left:20px;">
                        <?php if($bookstore_coin_open==='all_no'):?>
                            無法給予獎懲。
                        <?php endif;?>
                        <span style="position:relative;margin-top:10px;display:block;<?php if($bookstore_coin_open==='all_no')echo 'display:none;'?>">
                            <input type="radio" name="reward_punishment" att="0" checked/>無
                        </span>

                        <span style="position:relative;margin-top:15px;display:block;<?php if($bookstore_coin_open==='all_no')echo 'display:none;'?>">
                            <input type="radio" name="reward_punishment" att="1"/>給予額外獎勵
                            <!-- 給予獎勵 -->
                            <select id="comment_coin" name="comment_coin" class="form_select comment_coin" disabled>
                                <option value="50">+50葵幣
                                <option value="100" selected>+100葵幣(建議)
                                <option value="150">+150葵幣
                                <option value="200">+200葵幣
                                <option value="300">+300葵幣
                            </select>
                        </span>

                        <span style="position:relative;margin-top:10px;display:block;<?php if($bookstore_coin_open==='all_no')echo 'display:none;'?>">
                            <input type="radio" name="reward_punishment" att="2"/>給予額外懲罰
                            <!-- 給予懲罰 -->
                            <select id="comment_coin" name="comment_coin" class="form_select comment_coin" disabled>
                                <option value="-50">-50葵幣
                                <option value="-100" selected>-100葵幣(建議)
                                <option value="-150">-150葵幣
                                <option value="-200">-200葵幣
                                <option value="-300">-300葵幣
                            </select>
                        </span>
                    </div>
                </td>
                <td>
                <!-- 刪除資料 -->
                    <div class="mod_data_tbl_outline" style="width:300px;height:120px;position:relative;top:30px;left:30px;">
                        <span style="position:relative;margin-top:10px;display:block;">
                            <input type="radio" value="無" name="has_del_rec" checked/>無
                        </span>
                        <span style="position:relative;margin-top:15px;display:block;">
                            <input type="radio" value="有" name="has_del_rec"/>刪除推薦
                            <span class="fsize_12 fc_red0" style="position:relative;">● 自動扣除推薦所賺的獎勵</span>
                        </span>
                        <span class="fsize_12" style="position:relative;top:10px;left:20px;">
                            <input type="checkbox" id="black_book" name="black_book" value="1"
                            style="position:relative;top:2px;">
                            書籍加入黑名單
                            <span class="fsize_12 fc_red0" style="position:relative;">
                                ● 學生無法再登記<br>
                            </span>
                            <span class="fsize_12 fc_red0" style="position:relative;right:-140px;">
                                及推薦此本書
                            </span>
                        </span>
                    </div>
                </td>
            </tr>

            <?php if($type==='text'):?>
            <?php
            ?>
                <tr class="fsize_16 font-weight1" align="center">
                    <td colspan="2">
                    <div style="width:85%;border:0px solid red;position:relative;top:40px;right:30px;">可協助修正學生的文字推薦</div>
                        <div class="fsize_14" style="clear:left;width:180px;position:relative;top:30px;float:left;margin-top:15px;margin-left:40px;padding-top:5px;">
                            【最喜歡的一句話】
                            <textarea name="rec_content[]" rows="5"
                            wrap="hard" class="form_textarea" style="width:180px;margin-top:5px;"
                            ><?php echo htmlspecialchars($arrys_rs_rec_text_content[0]);?></textarea>
                        </div>
                        <div class="fsize_14" style="width:180px;position:relative;top:30px;float:left;margin-top:15px;margin-left:10px;padding-top:5px;">
                            【書本內容介紹】
                            <textarea name="rec_content[]" rows="5"
                            wrap="hard" class="form_textarea" style="width:180px;margin-top:5px"
                            ><?php echo htmlspecialchars($arrys_rs_rec_text_content[1]);?></textarea>
                        </div>
                        <div class="fsize_14" style="width:180px;position:relative;top:30px;float:left;margin-top:15px;margin-bottom:-5px;margin-left:10px;padding-top:5px;">
                            【書中所學到的事】
                            <textarea name="rec_content[]" rows="5"
                            wrap="hard" class="form_textarea" style="width:180px;margin-top:5px"
                            ><?php echo htmlspecialchars($arrys_rs_rec_text_content[2]);?></textarea>
                        </div>
                    </td>
                </tr>
            <?php endif;?>

            <tr class="fsize_18 font-weight1" align="center">
                <td colspan="2">
                <!-- 教師留言(30字以內) -->
                    <div style="width:590px;height:105px;position:relative;top:40px;right:25px;">
                        教師留言&nbsp;
                        <select id="comment_public" name="comment_public" style="height:25px;" class="form_select">
                            <option value="1" selected>不公開
                            <option value="2">公開&nbsp;(班上學生都可看到此留言)
                        </select><br/><br/>
                        <textarea id="comment_content" name="comment_content" cols="70" rows="2"
                        wrap="hard" class="form_textarea" style="width:580px"></textarea>
                    </div>
                </td>
            </tr>
            <tr class="fsize_18 font-weight1" align="center">
                <td colspan="2">
                    <div style="width:590px;position:relative;top:45px;right:25px;">
                        <!-- hidden -->
                        <input type="hidden" id="rec_sid" name="rec_sid" value="<?php echo trim($rec_sid);?>" size="10" maxlength="30" class="form_text" style="width:150px;">
                        <input type="hidden" id="comment_type" name="comment_type" value="<?php echo trim($type);?>" size="10" maxlength="30" class="form_text" style="width:150px;">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo (int)$user_id;?>" size="10" maxlength="30" class="form_text" style="width:150px;">
                        <input type="hidden" id="book_sid" name="book_sid" value="<?php echo trim($book_sid);?>" size="10" maxlength="30" class="form_text" style="width:150px;">
                        <input type="hidden" id="psize" name="psize" value="<?php echo addslashes($psize);?>">
                        <input type="hidden" id="pinx" name="pinx" value="<?php echo addslashes($pinx);?>">
                        <input type="hidden" id="area" name="area" value="<?php echo addslashes($area);?>">
                        <input type="hidden" id="anchor" name="anchor" value="<?php echo addslashes($anchor);?>">
                        <input type="hidden" id="scrolltop" name="scrolltop" value="<?php echo (int)($scrolltop);?>">
                        <input type="hidden" id="semester_start" name="semester_start" value="<?php echo addslashes($semester_start);?>">
                        <input type="hidden" id="semester_end" name="semester_end" value="<?php echo addslashes($semester_end);?>">
                        <input type="hidden" id="date_filter" name="date_filter" value="<?php echo addslashes($date_filter);?>">
                        <?php //if($date_filter!==''):?>
                            <!-- <input type="hidden" id="date_filter" name="date_filter" value="<?php echo addslashes($date_filter);?>"> -->
                        <?php //endif;?>

                        <input id="BtnS" type="button" value="送出" class="ibtn_gr6030" onmouseover="this.style.cursor='pointer'">
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <!-- 內容區塊 開始 -->

</div>
<!-- 容器區塊 結束 -->

</Body>
</Html>

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //變數
    var nl    ='\r\n';

    //給予分數(必填)
    var ocomment_scores     =document.getElementsByName('comment_score');

    var oreward_punishments =document.getElementsByName('reward_punishment');
    var ocomment_content    =document.getElementById('comment_content');
    var $comment_coins      =$('.comment_coin');
    var $type               ='<?php echo $type;?>';

    var oBtnS =document.getElementById('BtnS');     //送出
    var oForm1=document.getElementById('Form1');    //表單

    $(function(){
        //獎勵與懲罰設置
        for(var i=0;i<oreward_punishments.length;i++){
            oreward_punishment=oreward_punishments[i];
            oreward_punishment.onclick=function(){
                var att=parseInt(this.getAttribute('att'));
                switch(att){
                    case 0:
                        $comment_coins.attr('disabled',true);
                    break;
                    default:
                        $comment_coins.attr('disabled',true);
                        $comment_coins.eq(att-1).attr('disabled',false);
                    break;
                }
            }
        }
    });

    oBtnS.onclick=function(){
    //送出

        var arry_err=[];

        //是否給予分數
        var has_comment_score=false;
        for(var i=0;i<ocomment_scores.length;i++){
            ocomment_score=ocomment_scores[i];
            ochecked=ocomment_score.checked;
            if(ochecked){has_comment_score=true;}
        }
        if(!has_comment_score){
            arry_err.push('請給予分數!');
        }

        //if(trim(ocomment_content.value).length>100){
        //    arry_err.push('留言超過100字!');
        //}
        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要送出嗎?')){
                oForm1.action='detailedA.php'
                oForm1.submit();
                return true;
            }
            else{
                return false;
            }
        }
    }

</script>