<?php
    $online_state='online';
    //$online_state='local';
?>
<?php if($online_state==='online'):?>
    <?php
    //-------------------------------------------------------
    //教師中心
    //-------------------------------------------------------

        //---------------------------------------------------
        //設定與引用
        //---------------------------------------------------

            //SESSION
            @session_start();

            ////啟用BUFFER
            //@ob_start();

            $APP_ROOT=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

            //外掛設定檔
            require_once("{$APP_ROOT}/ta/require/user_db_con.php");    //連接user資料庫
            require_once("{$APP_ROOT}/ta/require/ta_db_con.php");      //連接ta 資料庫
            require_once("{$APP_ROOT}/ta/require/login.php");          //登入判斷
            require_once("{$APP_ROOT}/ta/require/logout.php");         //登出之下

            //外掛函式檔
            //$funcs=array(
            //
            //);
            //func_load($funcs,true);

            ////清除並停用BUFFER
            //@ob_end_clean();

        //---------------------------------------------------
        //權限,與判斷
        //---------------------------------------------------

            ////權限辨認(身分開放權限)
            //$sth1 = "SELECT * FROM ta_permission WHERE permission_id =:permission_id AND sub_function='tools' AND enable='1'";
            //$sth = $dbt->prepare($sth1);
            //$sth->execute(array(":permission_id"=>$_SESSION["permission_id"])) or exit(var_dump($sth->errorInfo()));
            //$toolsIden=$sth->rowCount();
            //
            //if($toolsIden != "1" || $toolsIden != "30" || $toolsIden != "2230"){
            //
            //}else{
            //    echo "<script>alert('很抱歉，你的權限無法進入本區，請先登入!');location.replace('/ta/index.php');</script>";
            //}

    //SESSION 名稱 (僅供參考)
    //echo "unique_id: ".$_SESSION["uid"]."<br>";       //UID
    //echo "nickname: ".$_SESSION["name"]."<br>";       //名稱
    //echo "identity: ".$_SESSION["identity"]."<br>";   //身分--> 教師為"T"
    //echo "school:" .$_SESSION["school"]."<br>";       //學校名
    //echo "sem_year：".$_SESSION["sem_year"]."<br>";   //學年
    //echo "sem_term：".$_SESSION["sem_term"]."<br>";   //學期
    //echo "grade：".$_SESSION["grade"]."<br>";         //年級
    //echo "class：".$_SESSION["class"]."<br>";	        //班級
    ?>
    <!DOCTYPE html>
    <html lang="zh-tw">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>夫子學院-學習歷程-明日書店</title>
        <link rel="stylesheet" href="/ta/css/temp.css" media="screen"/>
        <link rel="stylesheet" href="css/mssr.css" media="screen"/>
    </head>

    <body style="margin:0;">
        <div id="top-navigation-wrap">
            <?php require_once("{$APP_ROOT}/ta/require/LP_list.php");?>
        </div>
        <div style="position:relative;height:810px;border:0px solid #f00;">
            <iframe id="IFC" name="IFC" src="/mssr/center/teacher_center/main/m_main/index.php" frameborder="0"
            style="width:100%;height:800px;overflow:hidden;overflow-y:auto;"></iframe>
        </div>
    </body>
    </html>
<?php else:?>
    <?php
    ?>
    <!DOCTYPE html>
    <html lang="zh-tw">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>夫子學院-學習歷程-明日書店</title>
        <link rel="stylesheet" href="../../css/temp.css" media="screen"/>
        <link rel="stylesheet" href="css/mssr.css" media="screen"/>
    </head>

    <body style="margin:0;">
        <div style="position:relative;height:810px;border:0px solid #f00;">
            <iframe id="IFC" name="IFC" src="/new_cl_ncu/mssr/center/teacher_center/main/m_main/index.php" frameborder="0"
            style="width:100%;height:800px;overflow:hidden;overflow-y:auto;"></iframe>
        </div>
    </body>
    </html>
<?php endif;?>
