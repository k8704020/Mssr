<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,商品編輯功能
//

//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION

        //啟用BUFFER
        //外掛設定檔
        require_once(str_repeat("../",2).'config/config.php');

         //外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code'
					);
		func_load($funcs,true);

        //清除並停用BUFFER
		
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
        
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
	
	
	
?>
<!DOCTYPE HTML>
<Html>
<Head>
	<Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <!-- 掛載 -->
    <link rel="stylesheet" href="../../lib/framework/bootstrap/css/code.css">
    <script type="text/javascript" src="../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
	<script type="text/javascript" src="../../lib/framework/bootstrap/js/code.js"></script>

    <style>
      	.button {
		   border-top: 1px solid #96d1f8;
		   background: #65a9d7;
		   background: -webkit-gradient(linear, left top, left bottom, from(#3e779d), to(#65a9d7));
		   background: -webkit-linear-gradient(top, #3e779d, #65a9d7);
		   background: -moz-linear-gradient(top, #3e779d, #65a9d7);
		   background: -ms-linear-gradient(top, #3e779d, #65a9d7);
		   background: -o-linear-gradient(top, #3e779d, #65a9d7);
		   padding: 9px 18px;
		   -webkit-border-radius: 6px;
		   -moz-border-radius: 6px;
		   border-radius: 6px;
		   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
		   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
		   box-shadow: rgba(0,0,0,1) 0 1px 0;
		   text-shadow: rgba(0,0,0,.4) 0 1px 0;
		   color: white;
		   font-size: 11px;
		   font-family: Georgia, serif;
		   text-decoration: none;
		   vertical-align: middle;
		   }
		.button:hover {
		   border-top-color: #28597a;
		   background: #28597a;
		   color: #ccc;
		   }
		.button:active {
		   border-top-color: #1b435e;
		   background: #1b435e;
		   }
	</style>

      
</Head>
<body>
	<form name="Form1" id="Form1" action="" method="post" enctype="multipart/form-data" onsubmit="return false">
        <table class="table" id="DWDWDfFFCS" style="width:1300px; position:fixed; top:0px; background-color:#DDD">
        <tr>
            <td>--</td>
            <td>圖片</td>
            <td>名稱</td>
            <td>資訊</td>
            <td>售價</td>
            <td>狀態</td>
            <td>註解</td>
            <td>存檔</td>
        </tr>
        
        <tr>
            <td>新增</td>
            <td><input id="file" name="file" type="file" class="form_text"/></td>
            <td><input id="item_name" type="text" name="item_name"></td>
            <td><input id="item_info" type="text" name="item_info"></td>
            <td><input id="item_coin" type="text" name="item_coin"></td>
            <td><select id="item_state" name="item_state">
            	<option value='上架' selected>O</option>
                <option value='下架'>x</option>
            </select></td>
            <td><input id="item_note" type="text" name="item_note"></td>
            <td><a class="button" onClick='new_()'>上傳</a></td>
        </tr>
        </table>
    </form>
</body>


    <script>
	var oholder_result  =document.getElementById('holder_result');
    var oForm1          =document.getElementById("Form1");      //表單
	var data_array;// 
	var table = window.document.getElementById("ingngginin") ;
	
	//---------------------------------------------------
    //送出處裡
    //---------------------------------------------------
	
	function new_()
	{
		var style = /\.(png)$/i;  //允許的檔案格式
		if (!style.test(window.document.getElementById("file").value))
		{ 
			window.alert("圖片格式錯誤"); 
			return false;
		}
		if (window.document.getElementById("item_name").value=="")
		{ 
			window.alert("名稱尚未輸入"); 
			return false;
		}
		if (!(window.document.getElementById("item_coin").value < 9999999999 && 0 < window.document.getElementById("item_coin").value))
		{ 
			window.alert("金錢不正確"); 
			return false;
		}
		
		
		oForm1.action="uploadA.php";
        oForm1.submit();
	}

    </script>
</Html>
    
    
    
    
    
     
    
    
    
    
    
    
    
    
    