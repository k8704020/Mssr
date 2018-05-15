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



    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
    //branch_id     分店主索引

        //GET
		$user_id=(isset($_GET[trim('user_id')]))?$_GET[trim('user_id')]:0;
        $branch_id=(isset($_GET[trim('branch_id')]))?$_GET[trim('branch_id')]:0;

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

        //-----------------------------------------------
        //主SQL撈取
        //-----------------------------------------------

            //$sql="
            //    SELECT
            //        `mssr_user_branch`.`user_id`,
            //        `mssr_user_branch`.`branch_id`,
            //        `mssr_user_branch`.`branch_rank`,
            //        `mssr_user_branch`.`branch_cs`,
            //        `mssr_user_branch`.`branch_nickname`,
            //        `mssr_user_branch`.`branch_state`,
            //
            //        `mssr_branch`.`branch_name`,
            //        `mssr_branch`.`branch_coordinate`
            //    FROM `mssr_user_branch`
            //        INNER JOIN `mssr_branch` ON
            //        `mssr_user_branch`.`branch_id`=`mssr_branch`.`branch_id`
            //    WHERE 1=1
            //        AND `mssr_user_branch`.`user_id`={$uid}
            //        AND `mssr_branch`.`branch_state`='啟用'
            //";
            //$arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            //if(!empty($arrys_result)){
            //    $arrys_branch=$arrys_result;
            //    $json_branch=json_encode($arrys_branch,true);
            //}else{
            //    die("DB_RESULT: QUERY FAIL!");
            //}

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
    <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>

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
                    <td width="200px" valign="middle" bgcolor="#edfed4" onmouseover="mouse_over(this);void(0);" onClick="change_page(1)" style="border:1px solid #ff0000;" >事件</td>
                    <td width="200px" valign="middle" bgcolor="#ffdabd" onmouseover="mouse_over(this);void(0);" onClick="change_page(2)" style="border:1px solid #ff0000;" >書籍</td>
                    <td width="200px" valign="middle" bgcolor="#c9dbfd" onmouseover="mouse_over(this);void(0);" onClick="change_page(3)" style="border:1px solid #ff0000;" >狀態</td>
                    <td width="200px" valign="middle" bgcolor="#caf3ff" onmouseover="mouse_over(this);void(0);" onClick="change_page(4)" style="border:1px solid #ff0000;" >營收</td>
                </tr>
            </table>
        </div>
        <!-- 導航區塊 結束 -->

        <!-- 內容區塊 開始 -->
            <!--版面一-->
            <div id="content_1">
                <table cellpadding="0" cellspacing="0" border="0" width="800px" height="330px" style="color:#000099;font-size:18pt;"/>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">執行中的任務</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="130px" bgcolor="#edfed4">
                            <iframe id="task_doing" src="./task/task_doing.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">任務歷史紀錄</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="130px" bgcolor="#edfed4">
                            <iframe id="task_log" name="task_log" src="./task/task_log.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                    </tr>
                </table>
            </div>
            <!--版面二-->
            <div id="content_2">
                <table cellpadding="0" cellspacing="0" border="0" width="800px" height="330px" style="color:#000099;font-size:18pt;"/>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">我的藏書</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="130px" bgcolor="#ffdabd">
                            <iframe id="task_doing" src="./book/my_book.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">已上架的書</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="130px" bgcolor="#ffdabd">
                            <iframe id="task_log" name="task_log" src="./book/has_publish.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                    </tr>
                </table>
            </div>
            <!--版面三-->
            <div id="content_3">
                <table cellpadding="0" cellspacing="0" border="0" width="800px" height="330px" style="color:#000099;font-size:18pt;"/>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">分店概況</td>
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">書店島發展度</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="300px" bgcolor="#c9dbfd">
                            <iframe id="task_doing" src="./branch_state/state.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                        <td valign="middle" height="300px" bgcolor="#c9dbfd">
                            <iframe id="task_doing" src="./branch_state/growing.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
                            style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
                        </td>
                    </tr>
                </table>
            </div>
            <!--版面四-->
            <div id="content_4">
                <table cellpadding="0" cellspacing="0" border="0" width="800px" height="330px" style="color:#000099;font-size:18pt;"/>
                    <tr align="center">
                        <td valign="middle" height="30px" style="border:2px solid #edfed4;">每月營業額</td>
                    </tr>
                    <tr align="center">
                        <td valign="middle" height="300px" bgcolor="#caf3ff">
                            <iframe id="task_doing" src="./revenue/revenue.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<? echo $user_id; ?>" frameborder="0"
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

    //---------------------------------------------------
    //物件
    //---------------------------------------------------

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
<Html>