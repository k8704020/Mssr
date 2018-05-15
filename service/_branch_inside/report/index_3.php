<?php
//-------------------------------------------------------
//明日星球,分店
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'service/branch/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
        $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
        $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
        $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
        $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
        $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //branch_id     分店主索引

        //GET
		$user_id=(isset($_GET[trim('user_id')]))?(int)$_GET[trim('user_id')]:$sess_uid;
        $branch_id=(isset($_GET[trim('branch_id')]))?$_GET[trim('branch_id')]:0;

		$user_id = $_GET["user_id"] ;
		$branch_id = $_GET["branch_id"] ;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //branch_id     分店主索引

        $arry_err=array();

        if($branch_id===''){
           $arry_err[]='分店主索引,未輸入!';
        }else{
            $branch_id=(int)$branch_id;
            if($branch_id===0){
                $arry_err[]='分店主索引,錯誤!';
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
    //資料庫
    //---------------------------------------------------

        //建立連線 user
        $conn_user=conn($db_type='mysql',$arry_conn_user);

        //建立連線 mssr
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //---------------------------------------------------
        //撈取, 學校資訊
        //---------------------------------------------------

            if((trim($sess_school_code)==='')&&(trim($sess_class_code)!=='')){
                $sess_class_code=mysql_prep($sess_class_code);
                $sql="
                    SELECT
                        `class`.`grade`,
                        `semester`.`school_code`
                    FROM `class`
                        INNER JOIN `semester` ON
                        `class`.`semester_code`=`semester`.`semester_code`
                    WHERE 1=1
                        AND `class`.`class_code`='{$sess_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $sess_school_code=trim($arrys_result[0]['school_code']);
                    if($sess_grade===0){
                        $sess_grade=(int)$arrys_result[0]['grade'];
                    }
                }
            }

        //---------------------------------------------------
        //撈取分店類型相關
        //---------------------------------------------------

            $sess_school_code   =mysql_prep($sess_school_code);

            //初始化, 階層陣列
            $arrys_category_info=array();
            $json_category_info =json_encode($arrys_category_info,true);

            $sql="
                SELECT
                    *
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `mssr_book_category`.`cat1_id`<>1
                    AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                    AND `mssr_book_category`.`cat_state`  ='啟用'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
            //回填各階層相關陣列
                foreach($arrys_result as $inx=>$arry_result){
                    $cat1_id=(int)$arry_result['cat1_id'];
                    $cat2_id=(int)$arry_result['cat2_id'];
                    $cat3_id=(int)$arry_result['cat3_id'];

                    if(($cat2_id===1)&&($cat3_id===1)){
                        $lv_flag=1;
                        $arrys_category_info['lv1'][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id===1)){
                        $lv_flag=2;
                        $arrys_category_info['lv2'][$cat1_id][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id!==1)){
                        $lv_flag=3;
                        $arrys_category_info['lv3'][$cat2_id][]=$arry_result;
                    }else{
                        $lv_flag=0;
                        die("發生嚴重錯誤，請洽詢明日星球團隊人員!");
                    }
                }
                $json_category_info =json_encode($arrys_category_info,true);
            }
//echo "<Pre>";
//print_r($json_category_info);
//echo "</Pre>";
//die();

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            $sql="
                SELECT
                    `mssr_branch`.`branch_name`
                FROM `mssr_user_branch`
                    INNER JOIN `mssr_branch` ON
                    `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
                WHERE 1=1
                    AND `mssr_user_branch`.`user_id`={$user_id}
                    AND `mssr_branch`.`branch_state`='啟用'
                    AND `mssr_branch`.`branch_id`   ={$branch_id}
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
            if(!empty($arrys_result)){
                $branch_name=trim($arrys_result[0]['branch_name']);
            }else{
                die("DB_RESULT: QUERY FAIL!");
            }

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,分店";
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
    <link rel="stylesheet" type="text/css" href="../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>

    <script type="text/javascript" src="../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../lib/js/array/code.js"></script>

    <script type="text/javascript" src="../../branch/inc/add_action_branch_log/code.js"></script>

    <!-- 專屬 -->


    <style>
        /* 微調 */
        body{
            overflow:hidden;
            position:relative;
            margin:0px;
            padding:0px;
            margin:0 auto;

            z-index:1;

            border:0px solid #ff0000;
        }

        #container{
            position:relative;
            margin:0px;
            padding:0px;
            margin:0 auto;

            width:1000px;
            height:480px;

            z-index:2;

            background:#ffffff;
            border:0px solid #ff0000;
        }


        #navbar{
            position:relative;
            margin:0px;
            padding:0px;
            margin:0 auto;

            width:1000px;
            height:50px;

            z-index:3;

            border:0px solid #ff0000;
        }

        #content{
            position:relative;
            margin:0px;
            padding:0px;
            margin:0 auto;

            width:1000px;
            height:480px;

            z-index:3;

            border:0px solid #ff0000;
        }

    </style>
</Head>

<Body>

    <!-- 容器區塊 開始 -->
    <div id="container">

        <!-- 標題區塊 開始 -->

        <!-- 標題區塊 結束 -->

        <!-- 導航區塊 開始 -->
        <div id="navbar">
            <table cellpadding="0" cellspacing="0" border="0" width="800px" height="50px" style="color:#000099;font-size:14pt;"/>
                <tr align="center">
                    <td width="200px" valign="middle" bgcolor="#edfed4" onmouseover="mouse_over(this);void(0);" onclick="change_page(1)">任務             </td>
                    <td width="200px" valign="middle" bgcolor="#ffdabd" onmouseover="mouse_over(this);void(0);" onclick="change_page(2)">書籍推薦         </td>
                    <td width="200px" valign="middle" bgcolor="#c9dbfd" onmouseover="mouse_over(this);void(0);" style="border:1px solid #ff0000;">分店概況</td>
                    <td width="200px" valign="middle" bgcolor="#caf3ff" onmouseover="mouse_over(this);void(0);" onclick="change_page(4)">營收報表         </td>
                </tr>
            </table>
        </div>
        <!-- 導航區塊 結束 -->

        <!-- 內容區塊 開始 -->
        <div id="content_3">
            <table cellpadding="0" cellspacing="0" border="0" width="800px" height="330px" style="color:#000099;font-size:18pt;"/>
                <tr align="center">
                    <td valign="middle" height="30px" style="border:2px solid #edfed4;">分店概況</td>
                    <td valign="middle" height="30px" style="border:2px solid #edfed4;"><div id="title">--</div></td>
                </tr>
                <tr align="center">
                    <td valign="middle" height="300px" bgcolor="#c9dbfd">
                        <iframe src="./branch_state/state.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                        style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                    </td>
                    <td valign="middle" height="300px" bgcolor="#c9dbfd">
                        <iframe id="state" name="state"  frameborder="0"
                        style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                    </td>
                </tr>
            </table>
        </div>
        <!-- 內容區塊 結束 -->

    </div>
    <!-- 容器區塊 結束 -->

</Body>
<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    //---------------------------------------------------
    //參數
    //---------------------------------------------------

        var branch_name='<?php echo $branch_name;?>';

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

        var json_category_info=<?php echo $json_category_info;?>;

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function mouse_over(obj){
        //初始化, 滑鼠移入函式
            obj.style.cursor='pointer';
            return false;
        }

		function change_page(value)
		{//轉換內頁函式
			window.parent.add_debug("開啟報表分頁:"+value);
			var page_path='index_'+value+'.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>';
            var action_code='';

            switch(parseInt(value)){
                case 1:
                    action_code='rep02';
                break;
                case 2:
                    action_code='rep03';
                break;
                case 3:
                    action_code='rep06';
                break;
                case 4:
                    action_code='rep07';
                break;
            }

            //action_log
            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===branch_name){
                    //action_log
                    add_action_branch_log(
                        '../../branch/inc/add_action_branch_log/code.php',
                        action_code,
                        <?php echo $sess_uid;?>,
                        <?php echo $user_id;?>,
                        '',
                        0,
                        <?php echo $branch_id;?>,
                        '',
                        0,
                        0,
                        0,
                        0,
                        cat_id,
                        '',
                        page_path
                    );
                }
            }

            var lv2_in_flag=false;
            for(key2 in json_category_info['lv2']){
                var key2=parseInt(key2);
                for(key3 in json_category_info['lv2'][key2]){
                    var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                    var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                    if(cat_name===branch_name){
                        //action_log
                        add_action_branch_log(
                            '../../branch/inc/add_action_branch_log/code.php',
                            action_code,
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            '',
                            0,
                            <?php echo $branch_id;?>,
                            '',
                            0,
                            0,
                            0,
                            0,
                            key2,
                            '',
                            ''
                        );

                        if(!lv2_in_flag){
                            add_action_branch_log(
                                '../../branch/inc/add_action_branch_log/code.php',
                                action_code,
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                '',
                                0,
                                <?php echo $branch_id;?>,
                                '',
                                0,
                                0,
                                0,
                                0,
                                cat_id,
                                '',
                                page_path
                            );
                            lv2_in_flag=true;
                        }
                    }
                }
            }
		}

    //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------

        $(function(){

            //初始化, 禁止滑鼠滾動事件
            $(document).on("mousewheel DOMMouseScroll", function(e){
                e.preventDefault();
                return false;
            });
        });

</script>
</Html>