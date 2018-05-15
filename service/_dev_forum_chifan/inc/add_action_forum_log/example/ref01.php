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

    <script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../code.js"></script>
    <link rel="stylesheet" href=""/>
</head>

<body>

    <input type="button" id="BtnS" name="BtnS" value="轉頁面" tabindex="1">

<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    //---------------------------------------------------
    //函式: add_action_forum_log()
    //用途: 新增聊書動作log
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //action_from       動作來源使用者主索引
    //user_id_1         動作指向使用者主索引1
    //user_id_2         動作指向使用者主索引2
    //book_sid_1        書籍識別碼1
    //book_sid_2        書籍識別碼2
    //forum_id_1        社團主索引1
    //forum_id_2        社團主索引2
    //article_id        文章主索引
    //reply_id          回覆主索引
    //go_url            跳轉頁面
    //---------------------------------------------------

        var process_url     ='../code.php';
        var action_code     ='i0';
        var action_from     =1;

        var user_id_1       =2;
        var user_id_2       =3;
        var book_sid_1      ='mbl0000000000000000000001';
        var book_sid_2      ='mbl0000000000000000000002';
        var forum_id_1      =1;
        var forum_id_2      =2;

        var article_id      =1;
        var reply_id        =2;
        var go_url          ='ref03.php';

        var oBtnS=document.getElementById('BtnS');

        oBtnS.onclick=function(){
            add_action_forum_log(
                process_url,
                action_code,
                action_from,
                user_id_1,
                user_id_2,
                book_sid_1,
                book_sid_2,
                forum_id_1,
                forum_id_2,
                article_id,
                reply_id,
                go_url
            );
        }

</script>

</body>
</Html>