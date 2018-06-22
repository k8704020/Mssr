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
    //SESSION
    //---------------------------------------------------
    // if(isset($_SESSION['book_level_user_id'])&&isset($_SESSION['book_level_permission'])&&isset($_SESSION['book_level_name'])){
    //         echo '<span class="user_btn">',$_SESSION['book_level_name'],',您好</span>' ,'<span><a href="#" class="user_btn" onclick="logout()" >登出</a></span>';
                
    // }else{
    //                  echo '<a href="#" class="user_btn" >會員登入/註冊</a>';
    //        }


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
    //SQL 查詢
    //---------------------------------------------------

 
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
    <link rel="stylesheet" type="text/css" href="../css/book_level.css" />

    <style>

    #user_area{
        
        font-family: 微軟正黑體;
        font-weight: 600;
        position: relative;
    }
/*    .content {
        
        width:1000px;
        height:1000px;
        background-color: #ccc;
        border:1px solid #000;
        margin: 0 auto;
    }*/

    .user{
        width:900px;
    }
    .container{

        
    }
    .user{
        text-align: right;
    }
    .title{
        
        text-align: center;


    }
    .title span{
        font-size: 45px;
        font-family: 微軟正黑體; 
    }
    .about_login {
        background-color: #fff;
        margin: 50px auto;
        border: dashed 1px #aaa;
        width: 400px;
        height: 500px;
        display: none;
    }


    .input_container{
      
        text-align: left;
        padding-left: 110px;
        margin: 20px auto;

    }

    label{
        display: block;
        max-width: 100%;
    }
    .button_container{
        text-align: center;
       
        padding-top: 5px;
    }
    #insert_new_id{
        margin-left: 80px;
        margin-top: 10px;

        
    }
/*    .footer{
      display: none;
    }
*/






</style>
</Head>

<Body>

    <div class="content" id="user_area">
        <div class="user">
                <?php
                if(isset($_SESSION['book_level_user_id'])&&isset($_SESSION['book_level_permission'])&&isset($_SESSION['book_level_name'])){
              
                      echo '<span class="user_btn">',$_SESSION['book_level_name'],',您好</span>' ,'<span><a href="#" class="user_btn" onclick="logout()" >登出</a></span>';
              
                
                }else{
                     echo '<a href="#" class="user_btn" id="log_sign" >會員登入</a>';
                }

            ?>
        

        </div>
        <div class="container">

              <div class="about_login">
                <h1 class="title">登入</h1>
                
                  <div class="input_container">
                    <label for="account">帳號:</label>
                    <input type="text" id="account" required="required" />
                    
                    
                  </div>
                  <div class="input_container">
                    <label for="password">密碼:</label>
                    <input type="password" id="password" required="required"/>

                  </div>
                  
                  <div class="button_container">
                    <input type="button" value="送出" id="login_submit" name="" onclick="check();">
                  </div>
                  
        
              </div>

             
        </div>
    </div>

</Body>
<script type="text/javascript">



function check(){

    var account =$("#account").val();
    var password=$("#password").val();

    var url = "http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/ajax/check_login_in.php";
        var dataVal = {

            account:              account,
            password:             password 

        };

        $.ajax({
                   url: url,
                   type: "POST",
                   datatype: "json",
                   data: dataVal,
                   // contentType: "application/json; charset=utf-8",
                   async: false,
                   success: function(data) {

                    data_array = JSON.parse(data);
                    console.log(data_array);
                  
                    
                    var msg=data_array['msg'];
                    
                    if(msg!=''){
                        alert("帳號或密碼有輸入錯誤");
                    }else{

                      var user_id=data_array[0]['book_level_user_id'];
                      var permission=data_array[0]['book_level_permission'];
                      var name=data_array[0]['book_level_name'];
                        $('.about_login').css('display','none');
                        $('.user_btn').remove();
                        $('.user').append('<span class="user_btn">'+name+',您好</span>' ,'<span><a href="#" onclick="logout()" class="user_btn" >登出</a></span>');
                       
                         if(permission==="1"){

                            window.location="http://www.cot.org.tw/mssr/center/teacher_center/book_level/book_log/index.php";
                           

                         }else if(permission==="2"){
                          
                            window.location="http://www.cot.org.tw/mssr/center/teacher_center/book_level/book_sticker/index.php";


                         }else{

                            window.location="http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/super_use_index.php";

                         }


                    }



                   },
                   error: function(jqXHR) {
                    alert("發生錯誤: " + jqXHR.status);
                  }

        });

}


function logout(){

    $('.user_btn').remove();
    $('.user').append('<a href="#" class="user_btn" id="log_sign" >會員登入/註冊</a>');

    var url = "http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/ajax/log_out.php";

    $.ajax({
               url: url,
               type: "POST",
               datatype: "json",
               // data: dataVal,
               // contentType: "application/json; charset=utf-8",
               async: false,
               success: function(data) {

                 window.location.href = 'http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php';
               },
               error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
              }

    });

}










$(document).ready(function(){

   var sess_user_id='<?php if(isset($_SESSION['book_level_user_id']))echo $_SESSION['book_level_user_id'] ;?>';

  if(sess_user_id===''){

      $('.about_login').css("display","block");

  }

    $('#log_sign').on('click', function() {
        $('.about_login').css("display","block");


    });


    // $('.footer1').on('click', function() {
     
    //   $('.about_sign').css("display","none");
    //   $('.about_login').css("display","block");
    //   $('.success_sign').css("display","none");
    //   $('#account').val("");
    //   $('#password').val("");
    // });



});




</script>
</Html>