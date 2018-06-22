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
    $sess_user_id=$_SESSION['book_level_user_id'];
    $sess_permission=$_SESSION['book_level_permission'];
    $sess_name=$_SESSION['book_level_name'];
    

    if(!isset($sess_user_id)&&!isset($sess_permission)&&!isset($sess_name)){


        echo '<span style="font-size:40px; color:red;">請先登入!!</span>';

        header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');


        die();


        
     }

     if($sess_permission!="3"){

            echo '<span style="font-size:40px; color:red;">你沒有權限進入!!</span>';

            die();
    }
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
    .about_sign,.success_sign{
        background-color: #fff;
        margin: 50px auto;
        border: dashed 1px #aaa;
        width: 400px;
        height: 500px;
        
    }
    .success_sign{
      text-align: center;
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

/*    .footer{
      display: none;
    }
*/






</style>
</Head>

<Body>

    <div class="content" id="user_area">
    
       
        <div>
                <?php
                    require_once('../user/index.php');
                    if($sess_permission==="3"){require_once('../user/super_use_index.php');};
                    
                ?>
          </div>

      
        <div class="container">

             
              <div class="about_sign">
                <h1 class="title">註冊</h1>
                  <div class="input_container">
                        <label for="username">姓名:</label>
                        <input type="text" id="username" required="required"/>   
                      
                  </div>
                    <div class="input_container">
                        <label for="sex">性別:</label>
                        <input type="radio" name="sex" value="1" id="girl"/>男
                        <input type="radio" name="sex" value="2" id="boy"/>女
                  </div>
                   <div class="input_container">
                        <label for="permission">身分:</label>
                        <input type="radio" name="permission"  value="1" id="pt"/>工讀生
                        <input type="radio" name="permission"  value="2" id="idc_t"/>idc老師
                        <input type="radio" name="permission"  value="3" id="super"/>管理員
                  </div>
                  <div class="input_container" id="about_account">
                        <label for="new_account">帳號:</label>
                        <input type="text" id="new_account"  onblur="checkNewId()" />
                        
                        
                  </div>
                  <div class="input_container">
                        <label for="new_password">密碼:</label>
                        <input type="password" id="new_password" required="required"/>
                       
                        
                  </div>
                
                <div class="button_container">
                    <input type="button" value="送出" id="sign_submit" name="" onclick="newId();">
                  </div>
                  
             
              </div>
              <div class="success_sign">
                  <span>新增成功</span>
                  <div class="footer"><a href="#">繼續註冊新帳號</a></div>
              
                  
              </div>
        </div>
    </div>

</Body>
<script type="text/javascript">




function checkNewId(){

     var new_account =$("#new_account").val();
     var url = "http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/ajax/check_sign_up.php";
     var dataVal = {
        new_account:          new_account
       

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
                    $('#ps').remove();

                if(msg!=''){

                    $("#about_account").append("<span id='ps' style='color:red;' >帳號已存在</span>");
                }


               },
               error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
              }

    });

}

function newId(){

    var name=$("#username").val();
    var sex=$('input[name=sex]:checked').val();
    var permission=$('input[name=permission]:checked').val();
    var new_account =$("#new_account").val();
    var new_password=$("#new_password").val();
    console.log(name);
    console.log(sex);
    console.log(permission);
    console.log(new_account);
     console.log(new_password);
    var url = "http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/ajax/insert_new_id.php";
    var dataVal = {
     
        name:                 name,
        sex:                  sex,
        permission:           permission,
        new_account:          new_account,
        new_password:         new_password 

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


                if(data_array.length===0){
                   $(".about_sign").css('display','none');
                   $(".success_sign").css('display','block');

                }
               


               },
               error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
              }

    });

}





$(document).ready(function(){

   var sess_user_id='<?php if(isset($_SESSION['book_level_user_id']))echo $_SESSION['book_level_user_id'] ;?>';
       $('.footer').on('click', function() {
     
      $('.about_sign').css("display","block");
      $('.success_sign').css("display","none");
      $('#username').val("");
      $('#new_account').val("");
      $('#new_password').val("");
     $('input[name=sex]').attr('checked',false);
     $('input[name=permission]').attr('checked',false);
    });




});




</script>
</Html>