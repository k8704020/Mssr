<?php
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //設定頁面語系
    header("Content-Type: text/html; charset=UTF-8");

    //設定文字內部編碼
    mb_internal_encoding("UTF-8");

    //設定台灣時區
    date_default_timezone_set('Asia/Taipei');
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

    <!--<Script Language="Javascript" Src=""></Script>-->
    <link rel="stylesheet" href="" />

    <style>
        #tm_left {
            width:780px;
            height:500px;
            margin:10px 0 10px 10px;
            float:left;
            background:#efefef;
        }
    </style>
</Head>

<Body>
<!-- index.php?sys_ename=user -->
    <div id="tm_left">
        <iframe id="IFC" name="IFC" src="index.php?sys_ename=book" frameborder="0"
        style="width:100%;height:100%;overflow:hidden;overflow-y:auto;"></iframe>
    </div>
</Body>
</Html>