<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code'
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

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
//$_GET['book_sid']='mbc1209320140616022036701';


        //SESSION
		$user_id    =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission =(isset($_SESSION['permission']))?trim($_SESSION['permission']):'0';

        //GET
        $book_sid   =(isset($_GET['book_sid']))?trim($_GET['book_sid']):'';
		$chas   =(isset($_GET['chas']))?trim($_GET['chas']):1;
	
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

		if($permission =='0'||$user_id=='0' || $book_sid=='') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

	//---------------------------------------------------
	//SQL
	//---------------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //網頁標題
        $title="明日星球,書店";

        //已上傳圖片設置
        $upload_cno_limit=3;
        $upload_cno=0;
		$pic_hav= array();
        $root=str_repeat("../",5)."info/user";
        $path="{$root}/{$user_id}/book/{$book_sid}/draw/bimg";
	
		$user = $arry_ftp1_info['account']; 
		$pass = $arry_ftp1_info['password']; 
		$host = $arry_ftp1_info['host']; 
		$hostname = "ftp://".$user .":".$pass."@".$host."/public_html/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/"; 
		$path = "http://".$host."/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/";
        for($i=1;$i<=$upload_cno_limit;$i++){
            $img_path="{$hostname}upload_".$i.".jpg?t=".time();
            if(file_exists($img_path)){
                $upload_cno++;
            }
        }
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <link rel="stylesheet" type="text/css" href="../../../../../inc/code.css" media="all" />

    <!-- 專屬 -->
    <link rel="stylesheet" href="css/code.css">

    <style>
		.bar_1
		{
			text-align:center;
			border:1px solid;
			
			box-shadow: -2px -2px 2px #995;
			font-size:28px;
			width:600px;
			height:120px;
		
			position: absolute;
			top:340px;
			left:80px;
			transition: 0.1s;
		}
		.bar_2
		{
			text-align:center;
			border:1px solid;
			color:#AAA;
			
			box-shadow: 4px 4px 2px #440;
			font-size:20px;
			text-shadow:#666
			width:600px;
			height:120px;
		
			position: absolute;
			top:340px;
			left:80px;
			transition: 0.1s;
		}
		.bar_2_s
		{
			text-align:center;
			border:4px solid #0AF;
			color:#AAA;
			
			box-shadow: 4px 4px 2px #44a0;
			font-size:20px;
			text-shadow:#666
			width:600px;
			height:120px;
		
			position: absolute;
			top:340px;
			left:80px;
			transition: 0.1s;
		}
    </style>
</head>

<body >
	<!--==================================================
    html內容
    ====================================================== -->
    <div style="background-color:#F9F2D7; position:absolute; width:676px; height:67px; top:344px; left:22px;" class="bar_1"></div>
    <div style="background-color:#F9F2D7; position:absolute; width:676px; height:331px; top:12px; left:22px;"class="bar_1" ></div>
    
    <div  width='0px' height='0px' style="position:absolute; color:#AA6633; top:289px; left:12px; width: 157px; font-size:18px; text-align:right; height: 41px;">↑你可以點選列表<BR>並上傳最多3張圖</div>
    <form name="Form1" id="Form1" action="" method="post" enctype="multipart/form-data" onsubmit="return false">
        <!-- 3張選擇圖示 -->
        <div id="p_btn_1" onClick="click_p(1)" style="position:absolute; width:110px; height:70px; top:27px; left:41px;" class="bar_2"><img id="p_btn_1_png" src = "<?PHP  if(file_exists("{$hostname}/upload_1.jpg")){echo "{$path}/upload_1.jpg?t=".time(); }else{echo "img/noimg.png?s";} ?>" style="position:absolute; width:110px; height:70px; top:0px; left:0px;"></div>
        <div id="p_btn_2" onClick="click_p(2)" style="position:absolute; width:110px; height:70px; top:120px; left:41px;"class="bar_2"><img id="p_btn_1_png"  src = "<?PHP  if(file_exists("{$hostname}/upload_2.jpg")){echo "{$path}/upload_2.jpg?t=".time(); }else{echo "img/noimg.png?s";} ?>" style="position:absolute; width:110px; height:70px; top:0px; left:0px;"></div>
        <div id="p_btn_3" onClick="click_p(3)" style="position:absolute; width:110px; height:70px; top:213px; left:41px;"class="bar_2"><img id="p_btn_1_png"  src = "<?PHP  if(file_exists("{$hostname}/upload_3.jpg")){echo "{$path}/upload_3.jpg?t=".time(); }else{echo "img/noimg.png?s";} ?>" style="position:absolute; width:110px; height:70px; top:0px; left:0px;"></div>
        <!-- 大圖示 -->
        <div  style=" position:absolute; width:350px; height:300px; top:27px; left:175px;"class="bar_2"><img id="main_img" src="" style=" position:absolute; position:absolute; width:500px; height:300px; top:0px; left:0px;">
        <!-- 上傳 -->
        </div> <span id="BtnU" class='btn btn-success' style="position:absolute; top:356px; left:560px;">上傳</span>
        <input id="file" name="file" type="file" class="form_text" style='width:108px; position:absolute; left:40px; top:356px; font-size:22px'/>
        <input type="hidden" id="book_sid" name="book_sid" value="<?php echo addslashes($book_sid);?>">
        <input type="hidden" id="auth_coin_open" name="auth_coin_open" value="">
        <input id="chas" type="hidden" id="chas" name="chas" value="<?php echo addslashes($chas);?>">
        <div class='holder' id='holder_result' style="position:absolute; top:349px; left:129px;">
                <div id="box_h" width='0px' height='0px' style="position:absolute;color:#AA6633; top:12px; left:-1px; width: 424px;  font-size:24px; text-align:right;">←先點選[選擇檔案]選擇上傳圖片&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <img width='0px' height='0px' style="position:absolute; top:0px; left:29px;" id="result_image" class='img_container' title=''/>
        </div>
            
	</form>

<!-- 拖曳區塊 (隱藏) -->
<div class='holder' id="holder" style='display:none;'>
    <div id='holder_helper'>
        <h2 id="holder_helper_title">請拖曳圖片至此!</h2>
    </div>
    <img id="source_image" class='img_container' title='source_image' alt='source_image'/>
</div>

<script type="text/javascript" src="../../../../../lib/jquery/basic/code.js"></script>
<script type="text/javascript" src="../../../../../lib/jquery/ui/code.js"></script>
<script type="text/javascript" src="../../../../../lib/js/string/code.js"></script>
<script type="text/javascript" src="../../../../../lib/js/array/code.js"></script>
<script type="text/javascript" src="../../../../../lib/js/fso/code.js"></script>
<script type="text/javascript" src="../../../../../inc/code.js"></script>
<script type="text/javascript" src="inc/code.js"></script>
<script type="text/javascript">
//-------------------------------------------------------
//遮罩物件
//-------------------------------------------------------
	function cover(text,type,proc,proc2)
	{
		echo(proc2);
		window.parent.cover(text,type);
		if(type == 2 && proc != null)
		{
			delayExecute(proc);
		}
		else if(type == 3 && proc2 != null)
		{
			delayExecute(proc,proc2);
		}	
	}
	cover("讀取中");
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc,proc2) {
		var x = 100;
		
		var hnd = window.setInterval(function () {
			if(window.parent.parent.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.parent.parent.cover_click = -1;
				window.parent.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				cover("上傳中");
				proc();
				//cover("");
			}
			else if(window.parent.parent.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.parent.parent.cover_click = -1;
				window.parent.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
			else if(window.parent.parent.parent.cover_click ==2 )
			{//點選取消的狀況
				window.parent.parent.parent.cover_click = -1;
				window.parent.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				proc2();
				echo("COVER點選取消");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}
//-------------------------------------------------------
//初始化
//-------------------------------------------------------
	var array_img_obj=new Array();
	
	<?php
		$user = $arry_ftp1_info['account']; 
		$pass = $arry_ftp1_info['password']; 
		$host = $arry_ftp1_info['host']; 
		$hostname = "ftp://".$user .":".$pass."@".$host."/public_html/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/"; 
        $com_src = "http://".$host."/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/"; 
		for($i=1;$i<=$upload_cno_limit;$i++){
            $img_path="{$hostname}upload_".$i.".jpg";
            if(file_exists($img_path)){
    ?>
				array_img_obj[<?php echo $i;?>] = 
				{
					"src" : "<?php echo $com_src?>"+"upload_"+<?php echo $i;?>+".jpg?t=<? echo time();?>"
					
				}
        
    <?php
			}else
			{
	?>
				array_img_obj[<?php echo $i;?>] = 
				{
					"src" : "img/noimg2.png"
				}
        
    <?php				
			}
        }
    ?>
	
	
    var oholder_result  =document.getElementById('holder_result');
    var oForm1          =document.getElementById("Form1");      //表單
    var ofile           =document.getElementById('file');       //檔案,按鈕
    var oBtnU           =document.getElementById("BtnU");       //上傳,按鈕

	

    //綁定壓縮比例
    var quality         =parseInt(7);
    var output_format   ="jpg";

    var arry_img=[];
    var arry_img_obj=[];
    <?php
		$user = $arry_ftp1_info['account']; 
		$pass = $arry_ftp1_info['password']; 
		$host = $arry_ftp1_info['host']; 
		$hostname = "ftp://".$user .":".$pass."@".$host."/public_html/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/"; 
        $com_src = "http://".$host."/mssr/info/user/{$user_id}/book/{$book_sid}/draw/bimg/"; 
		for($i=1;$i<=$upload_cno_limit;$i++):
            $img_path="{$hostname}upload_".$i.".jpg";
            if(file_exists($img_path)):
    ?>
                arry_img_obj[<?php echo $i;?>]=new Image();
                arry_img_obj[<?php echo $i;?>].src="<?php echo $com_src?>"+"upload_"+<?php echo $i;?>+".jpg";
                arry_img.push(<?php echo $i;?>);
    <?php
            endif;
        endfor;
    ?>
    //----------------------------------------------------
    //事件
    //----------------------------------------------------

        try{
            
        }catch(e){
        }

        oBtnU.onclick=function(){
        //上傳

            var arry_type=[
                'jpg',
                'jpeg'
            ]

            var file=trim(ofile.value);
            var info=pathinfo(file);
			
            var filename =info['filename'];
            var extension=info['extension'];

            if(trim(file)==''){
                cover('請選擇欲上傳的圖片!',1);
                return false;
            }

            if(!in_array(extension.toLowerCase(),arry_type,false)){
                cover('請選擇JPG圖片檔案!',1);
                return false;
            }

			cover('你確定要上傳<BR>並<a style="color:#CC0000;">覆蓋第'+window.document.getElementById("chas").value+'張圖片</a>嗎?',2,function(){go_uploadA();})
           
        }
	//---------------------------------------------------
    //送出處裡
    //---------------------------------------------------
	function go_uploadA()
	{
				window.parent.parent.parent.set_action_bookstore_log(window.parent.parent.parent.user_id,'e17',window.parent.parent.parent.action_on);
		oForm1.action="uploadA.php";
        oForm1.submit();
	}
	
    $(function() {
    //---------------------------------------------------
    //按鈕事件處理
    //---------------------------------------------------

        ofile.onchange=function(e){
            e.preventDefault();
            var file = this.files[0];
            reader = new FileReader();
            reader.onload = function(event) {
                var oimg = document.getElementById("source_image");
                    oimg.src = event.target.result;
                    oimg.onload = function(){
                        //進行縮圖
                        encode();
                    }
            };
            if(file.type==="image/png"){
                output_format="png";
            }
            reader.readAsDataURL(file);
            return true;
        }
    });
	//---------------------------------------------------
    //點選圖片事件處裡
    //---------------------------------------------------
	function click_p(value)
	{
		for(var i = 1; i <= 3; i++)
		{
			window.document.getElementById("p_btn_"+i).className = "bar_2";		
		}	
		window.document.getElementById("p_btn_"+value).className = "bar_2_s";		
		
		window.document.getElementById("chas").value = value;	
		window.document.getElementById("main_img").src = array_img_obj[value].src;

	}
	
	
    //---------------------------------------------------
    //壓縮事件處理
    //---------------------------------------------------

        function encode(){
            var source_image=document.getElementById('source_image');
            var result_image=document.getElementById('result_image');
            if (source_image.src==="") {
                cover("請選擇欲上傳的圖片!",1);
                return false;
            }

            //清空, 重新匯出
            result_image.src='';
            result_image.src=jic.compress(source_image,quality,output_format).src;

            result_image.onload = function(){
                var image_width =$(result_image).width(),
                image_height    =$(result_image).height();
                result_image.style.width  ="100px";
                result_image.style.height ="70px";
                result_image.style.display="block";
            	window.document.getElementById("box_h").innerHTML = "確認圖片後再點選[上傳]→"
			}
        }
	function main()
	{
		
		window.document.getElementById("auth_coin_open").value = window.parent.parent.parent.auth_coin_open;
		click_p(<?PHP echo $chas;?>);
		cover();
	}
	
	
	
	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	main();

</script>
</body>
</html>