<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 說明表單
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    ///---------------------------------------------------
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
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>說明文件</title>
    <script src="../js/select_thing.js" type="text/javascript"></script>
   <link rel="stylesheet" href="../css/btn.css">
</head>

<body>
    <div style="position:absolute; top:0px; left:0px; width:1000px; height:480px; background-color:#000000; opacity:0.8;" onClick=""></div>
    <table  cellpadding="0" cellspacing="0" border="0" style="position:absolute;">
        <tr>
            <td  onClick="clichk_btn(1)" style="cursor: pointer;">
                <img id="back_1" src="img/btn_back_on.png">
              <div align="center" style="position:relative;  font-size:28px; top:-60px;">賺錢法1</div>
              
          </td>
            <td onClick="clichk_btn(2)" style="cursor: pointer;">
                <img id="back_2" src="img/btn_back.png">
                <div align="center" style="position:relative;  font-size:28px; top:-60px;">賺錢法2</div>
                
            </td>
           <!-- <td onClick="clichk_btn(3)>
                <img id="back_3" src="img/btn_back.png"">
                <div align="center" style="position:relative;  font-size:28px; top:-60px;">媽?!我在這</div>
            </td>
            <td onClick="clichk_btn(4)>
                <img id="back_4" src="img/btn_back.png"">
                <div align="center" style="position:relative;  font-size:28px; top:-60px;">媽?!我在這</div>
            </td>
            -->
      </tr>
    </table>
    
    <div id= "commot" style="position:absolute; top:67px; left:8px">
        <img src="img/coin_help_back.png" >
        <img id= "commot_pic" src="img/coin_help_1.png" style="position:absolute; top:22px; left:36px">
    </div>
    <div id ="sound" style="position : absolute;	top: 0px; left: 0px;"></div>
<a id="out"  class=" btn_close" onClick="out()" style="position:absolute; top:318px; left:884px; cursor:pointer;"></a>
<img src="img/sound_btn.png" id="sound_id" onClick="play_sound(1)" style="position:absolute; top:40px; left:800px"/>
<script>
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	function out()
	{
		window.parent.set_page("");	
	}
	
	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
	function play_sound(value)
	{
				window.document.getElementById('sound').innerHTML = '<embed id="sound" height="0" width="0" src="sound/'+value+'.MP3">';
	
	}
	function clichk_btn(value)
	{
		window.document.getElementById('commot_pic').src = "img/coin_help_"+value+".png";
		window.document.getElementById('sound_id').onclick = function(){
   play_sound(value);
};
		for(var i = 1 ; i <= 2 ; i++)
		{
			window.document.getElementById('back_'+i).src = "img/btn_back.png";
		}
		window.document.getElementById('back_'+value).src = "img/btn_back_on.png";
	}
	
	function close_()
	{
		window.parent.document.getElementById('help_page').style.display ="none";
	}
	cover("");
	</script>
    
</body>
</html>