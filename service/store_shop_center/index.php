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
					APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code'
					);
		func_load($funcs,true);

        //清除並停用BUFFER
		
		//建立連線 user
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
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
		if($_GET['ps'] != 'tdd') die("們都沒有");
		
		
	
		
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
	<BR>
    <BR>
    <BR>
    <BR>
	<table class="table" id="ingngginin" style="width:1300px;">
    <tr>
    <td>ID</td>
    <td>圖片</td>
    <td>名稱</td>
    <td>資訊</td>
    <td>售價</td>
    <td>狀態</td>
    <td>註解</td>
    <td>存檔</td>
    </tr>
    </table>
    <BR>
    <BR>
    <BR>
    <BR>
    <iframe src="./uppage.php" style="width:1300px; position:fixed; top:0px; height:90px; background-color:#DDD";>
    </iframe>
    
</body>


    <script>
	var oholder_result  =document.getElementById('holder_result');
    var oForm1          =document.getElementById("Form1");      //表單
	var data_array;// 
	var table = window.document.getElementById("ingngginin") ;
	
	var max_id = 0;
	
	//---------------------------------------------------
    //送出處裡
    //---------------------------------------------------
	
	function onachange(value)
	{
		window.document.getElementById( value+"_tr").style.backgroundColor = "#FEE";	
		window.document.getElementById( value+"_btn").style.visibility = "";
		
		
	}
	function save(value)
	{
		window.document.getElementById( value+"_tr").style.backgroundColor = "#FEE";	
		window.document.getElementById( value+"_btn").style.visibility = "hidden";	
		array_i = window.document.getElementsByName(value);	
		
		console.log(array_i[0].value+"."+array_i[1].value+"."+array_i[2].value+"."+array_i[3].value);
		var url = "./ajax/set_shop_item_info.php";
		$.post(url, {
				item_id:value,
				item_name:array_i[0].value,
				item_info:array_i[1].value,
				item_coin:array_i[2].value,
				item_state:array_i[3].value,
				item_note:array_i[4].value
		}).success(function (data) 
		{
			if(data[0]!="{")
			{
				window.alert(data);	
				return false;
			}
			data_array = JSON.parse(data);
			if(data_array["error"]!="")
			{//發生錯誤
				window.document.getElementById( value+"_tr").style.backgroundColor = "#FCC";	
				window.document.getElementById( value+"_btn").style.visibility = "";	
				window.alert(data_array["error"]);	
			}else
			{
				window.document.getElementById( value+"_tr").style.backgroundColor = "#FFF";	
				window.document.getElementById( value+"_btn").style.visibility = "hidden";	
			}
		}).error(function(e){
		}).complete(function(e){
		});		
	}
	function set_all_info()
	{
		var tmp = "";
		
		for(var i= 0 ; i < data_array.length;i++)
		{
			if(Math.abs(max_id )< Math.abs(data_array[i]["item_id"]))max_id = data_array[i]["item_id"];
			
			tmp +="<tr id='"+data_array[i]["item_id"]+"_tr'>";
			//=============內容
			tmp += "<td>";
			tmp += data_array[i]["item_id"];
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<img src='../bookstore_v2/bookstore_courtyard/img/"+data_array[i]["item_id"]+".png' style='max-height:50px; max-width:50px;'>"; 
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<input onChange='onachange(this.name)' name='"+data_array[i]["item_id"]+"' type='text' value='"+data_array[i]["item_name"]+"'>";
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<input onChange='onachange(this.name)' name='"+data_array[i]["item_id"]+"' type='text' value='"+data_array[i]["item_info"]+"'>";
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<input onChange='onachange(this.name)' name='"+data_array[i]["item_id"]+"' type='text' value='"+data_array[i]["item_coin"]+"'>";
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<select  onChange='onachange(this.name)' name='"+data_array[i]["item_id"]+"' >"
    		if(data_array[i]["item_state"]== "上架")
			{
				tmp += "　<option value='上架'selected>O</option>";
				tmp += "　<option value='下架' >X</option>";
			}else if(data_array[i]["item_state"]== "下架")
			{
				tmp += "　<option value='上架'>O</option>";
				tmp += "　<option value='下架' selected>X</option>";
			}
   			tmp += "</select>";
			tmp += "</td>";
			
			tmp += "<td>";
			tmp += "<input onChange='onachange(this.name)' name='"+data_array[i]["item_id"]+"' type='text' value='"+data_array[i]["item_note"]+"'>";
			tmp += "</td>";		
			
			tmp += "<td>";
			tmp += "<a onClick='save(this.name)' id='"+data_array[i]["item_id"]+"_btn' name='"+data_array[i]["item_id"]+"' class='button' style='visibility:hidden'>存檔</a>";
			tmp += "</td>";
			//=============內容END
			tmp +="</tr>";
		}
		table.innerHTML += tmp;
		
	}
	
	
	
	
	function main()
	{
		var url = "./ajax/get_shop_item_info.php";
		$.post(url, {
				user_id:"",
				max_id:max_id
		}).success(function (data) 
		{
			data_array = JSON.parse(data);
			set_all_info();
		}).error(function(e){
		}).complete(function(e){
		});	
	}
	
	main();
	
	

    </script>
</Html>
    
    
    
    
    
     
    
    
    
    
    
    
    
    
    