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
    //函式: add_action_branch_log()
    //用途: 新增分店動作log
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //user_id           動作執行者主索引
    //visit_to          被拜訪人主索引
    //book_sid          書籍識別碼
    //friend_ident      是否為朋友(1=是,0=不是)
    //branch_id         分店主索引
    //branch_state      分店狀態(啟用,停用)
    //branch_rank       分店等級
    //branch_lv         分店圈數
    //task_ident        分店有無可接任務(1=有,0=無)
    //task_state        分店有無進行中的任務(1=有,0=無)
    //cat_id            分店相關類別id
    //task_sdate        任務起始時間
    //go_url            動作完畢, 跳轉頁面
    //---------------------------------------------------

        var process_url     ='../code.php';

        var action_code     ='r01';
        var user_id         =1;
        var visit_to        =0;
        var book_sid        ='mbl0000000000000000000001';
        var friend_ident    =0;
        var branch_id       =0;
        var branch_state    ='啟用';
        var branch_rank     =1
        var branch_lv       =1
        var task_ident      =0;
        var task_state      =0;
        var cat_id          =2
        var task_sdate      ='';

        var go_url          ='';




        var oBtnS=document.getElementById('BtnS');

        oBtnS.onclick=function(){
            add_action_branch_log(
                process_url,
                action_code,
                user_id,
                visit_to,
                book_sid,
                friend_ident,
                branch_id,
                branch_state,
                branch_rank,
                branch_lv,
                task_ident,
                task_state,
                cat_id,
                task_sdate,
                go_url
            );
        }

</script>

</body>
</Html>