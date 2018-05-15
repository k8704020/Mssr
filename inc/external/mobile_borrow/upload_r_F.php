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
<Html lang="zh_TW">
<Head>
    <Title></Title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">
</head>

<body>

    <form name="Form1" id="Form1" action="upload_r_A.php" method="POST" enctype='multipart/form-data'>
        <input type="file" id="file" name="file">
        <input type="submit" value="錄音上傳">
    </form>

</body>
</Html>