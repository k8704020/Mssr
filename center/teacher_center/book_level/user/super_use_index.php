<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();
        // $_SESSION['uid']=5029;

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');
        require_once(str_repeat("../",4)."/inc/get_black_book_info/code.php");

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code'
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

    $sess_user_id=$_SESSION['book_level_user_id'];
    $sess_permission=$_SESSION['book_level_permission'];
    $sess_name=$_SESSION['book_level_name'];
    
	//echo "<pre>";print_r($_SESSION);echo "</pre>";

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }




     if($sess_permission!="3"){

            echo $sess_permission,'<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------



    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------



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



    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //SESSION
       

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        

    //---------------------------------------------------
    //串接SQL
    //---------------------------------------------------

        //-----------------------------------------------
        //資料庫
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核借閱書學校關聯
        //-----------------------------------------------

            

        //---------------------------------------------------
        //SQL 筆數查詢
        //---------------------------------------------------

            
    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

   

    //---------------------------------------------------
    //SQL 查詢
    //---------------------------------------------------

//die();
    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
          $title="IDC學校";

        //-----------------------------------------------
        //教師中心路徑選單
        //-----------------------------------------------

           
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
    <!-- <link rel="stylesheet" type="text/css" href="../../../../inc/code.css" media="all" /> -->
    <script type="text/javascript" src="../../../../inc/code.js"></script>

    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/plugin/code.js"></script>

    <script type="text/javascript" src="../../../../lib/js/vaildate/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>

    <!-- 專屬 -->
    <link rel="stylesheet" type="text/css" href="../../inc/code.css" media="all" />
    <script type="text/javascript" src="../../inc/code.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="../../css/def.css" media="all" /> -->
    <!-- <link rel="stylesheet" type="text/css" href="../css/reading_book.css" /> -->

    <style>
        /* 容器微調 */
            #about_btn{
                width: 800px;
                margin: 0 auto;
            }
           .buttons{
                text-align: center;
                margin: 20px auto;
           }
    </style>
</Head>

<Body>
        <!-- 內容區塊 開始 -->
    <div id="about_btn">
            <div>
                <?php
                    require_once('../user/index.php');
                ?>
            </div>
            <div class="buttons">
                <input type="button" onclick="location.href='http://www.cot.org.tw/mssr/center/teacher_center/book_level/book_log/index.php'" name="" value="登記書籍等級">
                <input type="button" onclick="location.href='http://www.cot.org.tw/mssr/center/teacher_center/book_level/book_sticker/index.php'" name="" value="登記貼紙">
                <input type="button" onclick="location.href='http://www.cot.org.tw/mssr/center/teacher_center/book_level/about_book/administrator_level.php'" name="" value="書籍等級">
                <input type="button" onclick="location.href='http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/new_id_index.php'" name="" value="註冊新帳號">
            </div>
         <!-- 內容區塊 開始 -->
    </div>
    <!-- 快速切換區塊 開始 -->
</Body>
<script type="text/javascript">

    

//從資料庫尋找書籍顯示書籍資料



//978986317693
</script>
</Html>