<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 推薦框 -> 文字推薦
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
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
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

        $user_id        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:'0';
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");

    //---------------------------------------------------
    //接收,設定參數
    //---------------------------------------------------
        //GET
        $uid          =(isset($_GET['uid']))?(int)$_GET['uid']:$user_id;
        $book_sid    =(isset($_GET['book_sid']))?mysql_prep($_GET['book_sid']):0;
        $book_name         =(isset($_GET['book_name']))?mysql_prep($_GET['book_name']):"？";

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

    <!-- 掛載 -->
    <script type="text/javascript" src="../../../../lib/jquery/basic/func/jquery_1.9.0.min/code.js"></script>
    <link rel="stylesheet" href="../../css/btn.css">

    <style type="text/css">
	.tillt {
		font-size: 36px;
		font-family: Verdana, Geneva, sans-serif;
		font-weight: bold;
		color: #000;
	}
	.u9ipup {
		text-align: center;
	}
	.tillt_2 {
		font-weight: bold;
		font-size: 24px;
		color: #000;
	}
	.tillt_1 {
		font-size: 24px;
		color: #000;
	}
	#noEngTxt{
		display: none;
		font-size: 16px;
		font-weight: 600;
		color: #aaa;
		right: 50px;
		top: 5px;
		position: absolute;
		letter-spacing: 2px;

	}
	.titleImg{
		position: relative;
		left: 335px;

	}

	</style>
	</style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<div style="position:absolute; top:1px; left:129px;">
        <table width="750" border="0" height="412">
            <tr>
                <td width="380" height="408">
                    <table width="100%" border="0" height="100%" style=" position:relative; left:-15px;">
                        <tr>
                            <td align="center" class="tillt" ><img style="position:absolute; top:-3px; left:0px;" src="img/tittle_2.png" /> &nbsp;</td>
                        </tr>

                        <tr>
                        	 <!-- 最喜歡的一句話的textarea -->
                            <td  valign="top" class="tillt_2"><img src="img/text3.png" /><BR>
                            <textarea cols=25 rows=3 style="font-size:20px;resize: none;" onfocus="set_rec_on_edit(true);onfocus_text_for('rec_text_ans1');" onkeypress= "echo('AAA');" onChange="set_rec_on_edit(true);noEnglsh(this,event);" onkeydown="window.setTimeout( function(){ set_rec_on_edit(true);noEnglsh(this,event);}, 1)" id="rec_text_ans1"></textarea>
                            </td>
                        </tr>
                        <tr>
                        	 <!-- 書本內容介紹的textarea -->
                            <td valign="top" class="tillt_2"><img src="img/text4.png" /><BR>
                            <textarea cols=25 rows=6 style="font-size:20px;resize: none;" onfocus="set_rec_on_edit(true);onfocus_text_for('rec_text_ans2');"  onChange="set_rec_on_edit(true);noEnglsh(this,event);" onkeydown="window.setTimeout( function(){set_rec_on_edit(true);noEnglsh(this,event);}, 1)" id="rec_text_ans2"></textarea>
                            </td>
                        </tr>
                    </table>


                </td>
                <td width="360">
                    <table width="100%" border="0" height="100%" style=" position:relative; top:-5px; left:-5px;">
                        <tr>
                            <td  height="30" >&nbsp;</td>
                        </tr>
                        <tr>
                         <!-- 在書中所學到的事情的textarea -->
                            <td  class="tillt_2" ><img src="img/text2.png" style="position: absolute; left:-10px;top:40px;"/><BR><textarea cols=25 rows=12 style="font-size:20px;resize: none;" onChange="set_rec_on_edit(true);noEnglsh(this,event);" onfocus="window.setTimeout( function(){ set_rec_on_edit(true);onfocus_text_for('rec_text_ans3');}, 1)" onkeydown="set_rec_on_edit(true);noEnglsh(this,event);" id="rec_text_ans3"></textarea></td>
                        </tr>


                    </table>

                </td>
            </tr>
        </table>
        <table class="inputComma" width="700" border="0" style=" position:relative; top:0px; left:-3px;display:none;">
            <tr>
                <td><img onClick="inputComma('，');" src="img/text_btn_1.png" style="cursor: pointer;"/></td>
                <td><img onClick="inputComma('。');" src="img/text_btn_2.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('；');" src="img/text_btn_3.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('：');" src="img/text_btn_4.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('！');" src="img/text_btn_5.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('？');" src="img/text_btn_6.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('、');" src="img/text_btn_7.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('…');" src="img/text_btn_8.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('「');" src="img/text_btn_9.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('」');" src="img/text_btn_10.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('“');" src="img/text_btn_11.png" style="cursor: pointer;" /></td>
                <td><img onClick="inputComma('”');" src="img/text_btn_12.png"  style="cursor: pointer;"/></td>

            </tr>
        </table>
    </div>
    <!--展開後可輸入可打字的textarea-->
    <div class="rec_text_ans_full" img="" to="" style="height:385px;width:100%;position:absolute;top:0;left:0;display:none;">
        <table class="rec_text_ans_title_full" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f3e4bc;"/>
            <tr>
            	<td >
                	<img class="rec_text_ans_img_full titleImg" src=""/>	
                	
                	<span id="noEngTxt">
            		不可以填寫英文
            		</span>
            	</td>
            	
            
            </tr>
        </table>
        <textarea cols=1 rows=1
        
        style="font-size:20px;resize: none; width:100%;height:385px;background-image: url('');"
        onfocus="onfocus_text_for('rec_text_ans_full');"
        onChange="noEnglsh(this,event);"
        onkeydown="window.setTimeout( function(){noEnglsh(this,event);}, 1)"
        id="rec_text_ans_full"></textarea>
	<!-- 確定按鈕 -->
        <a id="set_btn" class="btn_yes"  onClick="rec_text_ans_full_save();" style="position:absolute; right:50px; bottom: -85px; cursor:pointer; display:;"></a>
    </div>
    <img id="save_btn_img" src="../../img/UI_savef.png" style="position:absolute; top:286px; left:879px; cursor: no-drop; opacity:0.4" border="0">
     <!-- 儲存按鈕 -->
    <a id="save_btn"  class="btn_save" onClick="go_save()" style="position:absolute; top:286px; left:879px; cursor:pointer; display:none"></a>

    <script>
	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
     var book_sid = '<? echo $book_sid;?>';
	 var book_name = '<? echo $book_name;?>';
	 var auth_coin_open =  window.parent.parent.auth_coin_open;
	 var uid = '<? echo $uid;?>';
	 var reason = "xxxxxxx";
	 var rank = -1;
	 var time = 0;
	 var time_lock = false ;
	 var auth_rec_en_input = window.parent.parent.auth_rec_en_input;
	 if(auth_rec_en_input == "yes")auth_rec_en_input=false;
	 else auth_rec_en_input=true;


	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------

    //將滑鼠右鍵|貼上事件取消
    // document.oncontextmenu = function(){
    //     window.event.returnValue=false;
    // }


    //防止ctrl+v

    // $(document).keydown(function(event) {
    //     if (event.ctrlKey==true && (event.which == '118' || event.which == '86')) {
    //         event.preventDefault();
    //      }
    // });




      
 $(document)
        .on("click","#rec_text_ans1",function() {
            parent_hide();
            $('.rec_text_ans_full').show();
            $('#rec_text_ans_full').show();
            $('.inputComma').show();
            $('#rec_text_ans_full')[0].focus();
            $('.rec_text_ans_full').attr("to","#rec_text_ans1");
            $('.rec_text_ans_img_full')[0].src='img/text3.png';
            $('#rec_text_ans_full')[0].value=$(this)[0].value;
            var textarea_val=$('#rec_text_ans_full')[0].value;
            handleTextarea(textarea_val);


        })
        .on("click","#rec_text_ans2",function() {
            parent_hide();
            $('.rec_text_ans_full').show();
            $('#rec_text_ans_full').show();
            $('.inputComma').show();
            $('#rec_text_ans_full')[0].focus();
            $('.rec_text_ans_full').attr("to","#rec_text_ans2");
            $('.rec_text_ans_img_full')[0].src='img/text4.png';
            $('#rec_text_ans_full')[0].value=$(this)[0].value;
            var textarea_val=$('#rec_text_ans_full')[0].value;
            handleTextarea(textarea_val);
        })
        .on("click","#rec_text_ans3",function() {
            parent_hide();
            $('.rec_text_ans_full').show();
            $('#rec_text_ans_full').show();
            $('.inputComma').show();
            $('#rec_text_ans_full')[0].focus();
            $('.rec_text_ans_full').attr("to","#rec_text_ans3");
            $('.rec_text_ans_img_full')[0].src='img/text2.png';
            $('#rec_text_ans_full')[0].value=$(this)[0].value;
            var textarea_val=$('#rec_text_ans_full')[0].value;
            handleTextarea(textarea_val);

            

        });

        
	//單次只可複製100字,且可顯示100字內容.若老師有限制不能打英文.則也不可貼上英文

    function handleTextarea(textarea_val){

        var keydown = $("#rec_text_ans_full").on(keydown);
        var click = $("#rec_text_ans_full").on(click);
            
        var cursorPosition = '';
        var initText = '';
        var lastText = '';

        if(auth_rec_en_input)
        {
            $("#noEngTxt").show();
            
            //抓滑鼠位置
            function setBeforeAfterText(val){

                    $("#rec_text_ans_full").mouseup(function(e){

                            var textareaValue=e.target.value; ;
                             
                            cursorPosition = $(this).prop("selectionStart");
                            // console.log("滑鼠位置:"+cursorPosition);
                            initText = textareaValue.substr(0,cursorPosition);
                            lastText = textareaValue.substr(cursorPosition);
                            // console.log("initText: "+initText);
                            // console.log("lastText: "+lastText);
                    });

            }

            setBeforeAfterText(keydown);

            setBeforeAfterText(click);

            //打字不能輸入英文

            function checkPastedText(textareaValue){
               
                var checkEng=/[a-zA-Z]/g;
                var newText= textareaValue.replace(checkEng,"");
                $("#rec_text_ans_full").val(newText);


            }

            //拖曳不能有英文
            $("#rec_text_ans_full").mouseout(function(e){
                         
                var textareaValue=e.target.value; ;
                checkPastedText(textareaValue);

                      
             });

            //打字不能輸入英文

            $("#rec_text_ans_full").keyup(function(e){
                         
                var textareaValue=e.target.value; ;
                checkPastedText(textareaValue);

                // console.log("新的文字:"+newText);

                // console.log("textarea的值:"+textareaValue);

                      
             });

            //貼上不可貼上英文

            $("#rec_text_ans_full").on("paste", function(e){
                

                var pastedText = e.originalEvent.clipboardData.getData('text');
                // console.log("貼上的東西:"+ pastedText);

                var pastedTextLength=pastedText.length;
                // console.log("貼上的東西長度:"+ pastedTextLength);

                var newPastedText = pastedText.substr(0,100); 
                var checkEng=/[a-zA-Z]/g;
                var checkPastedText= newPastedText.replace(checkEng,"");
                pasteText = checkPastedText;
                cover("貼上成功<br>最多貼上100字，多餘的字刪除",1)

                // console.log("擷取一百字:"+newPastedText);
                // console.log("扣掉英文剩餘:"+checkPastedText);
                
                setTimeout(function(){
                        pasteValue()
                }, 100);

             });
             function pasteValue(){

                     $("#rec_text_ans_full").val(initText + pasteText + lastText);
             }

        }
        else{

             function setBeforeAfterText(val){

                    $("#rec_text_ans_full").mouseup(function(e){

                            var textareaValue=e.target.value; ;
                             
                            cursorPosition = $(this).prop("selectionStart");
                            // console.log("滑鼠位置:"+cursorPosition);
                            initText = textareaValue.substr(0,cursorPosition);
                            lastText = textareaValue.substr(cursorPosition);
                            // console.log("initText: "+initText);
                            // console.log("lastText: "+lastText);
                    });

            }

            setBeforeAfterText(keydown);

            setBeforeAfterText(click);
             //貼上不可貼上英文

            $("#rec_text_ans_full").on("paste", function(e){
                

                var pastedText = e.originalEvent.clipboardData.getData('text');
                // console.log("貼上的東西:"+ pastedText);

                var pastedTextLength=pastedText.length;
                // console.log("貼上的東西長度:"+ pastedTextLength);

                var newPastedText = pastedText.substr(0,100); 
                pasteText = newPastedText;
                cover("貼上成功<br>最多貼上100字，多餘的字刪除",1)

                // console.log("擷取一百字:"+newPastedText);
                // console.log("扣掉英文剩餘:"+checkPastedText);
                
                setTimeout(function(){
                        pasteValue()
                }, 100);

            } );
             function pasteValue(){
                $("#rec_text_ans_full").val(initText + pasteText + lastText);
            }

        }
            
    }

     



    function rec_text_ans_full_save(){
        $('.rec_text_ans_full').hide();
        $('#rec_text_ans_full').hide();
        $('.inputComma').hide();
        parent_show();
        var rec_text_name=$('.rec_text_ans_full').attr('to');
        $(rec_text_name)[0].value=$('#rec_text_ans_full')[0].value;
        $("#rec_text_ans_full").unbind("keyup");
        $("#rec_text_ans_full").unbind("mouseout");

        return false;
    }

    function parent_hide(){
        $(parent.document.getElementById('top_btn')).hide();
        $('#save_btn').hide();
        $('#save_btn_img').hide();
    }
    function parent_show(){
        $(parent.document.getElementById('top_btn')).show();
        $('#save_btn').show();
        $('#save_btn_img').show();
    }

	//cover
	function cover(text,type,proc)
	{

		window.parent.cover(text,type);
		if(type == 2)
		{
			delayExecute(proc);
		}
	}
	/*cover 啟用器的用法
	 cover("這嘎");
	 cover("這嘎",1);
	 cover("這嘎",2,function(){echo("哈哈");});
	*/
	//cover 點選器
	function delayExecute(proc) {
		var x = 100;
		var hnd = window.setInterval(function () {
			if(window.parent.parent.cover_click ==1 )
			{//點選確定的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選確定");
				proc();
				cover("");
			}
			else if(window.parent.parent.cover_click ==0 )
			{//點選取消的狀況
				window.parent.parent.cover_click = -1;
				window.parent.parent.cover_level = 0;
				window.clearInterval(hnd);
				echo("COVER點選取消");
				cover("");
			}
		}, x);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}

	function onfocus_text_for(name)
	{
		onfocus_text_id=name;
	}




	function inputComma(btn) {
		var obj = document.getElementById(onfocus_text_id);
	   // var obj = $('rec_text_ans1')[0];

		//var obj = btn.form.text1;
		if (document.selection) //for ie
		{
			obj.focus();
			var sel = document.selection.createRange();
			sel.text = btn;
		} else //for firefox
		{
			var prefix = obj.value.substring(0, obj.selectionStart);
			var suffix = obj.value.substring(obj.selectionEnd);
			obj.value = prefix + btn + suffix;
			obj.selectionEnd = obj.selectionStart;
			obj.selectionStart += 1;
		}
		isSaved = false;
		isModify = true;
		wordCount(obj);
	}

	function noEnglsh(aaa,e)
	{
		if(auth_rec_en_input)
		{
			
			$("#noEngTxt").show();
			aaa.value = aaa.value.replace(/[a-zA-Z]/g,"");

			var keynum
			var keychar
			var numcheck

			if(window.event) // IE
			  {
			  keynum = e.keyCode
			  }
			else if(e.which) // Netscape/Firefox/Opera
			  {
			  keynum = e.which
			  }
			keychar = String.fromCharCode(keynum)
			numcheck = /\d/
			return !numcheck.test(keychar)
		}
	}

	// function noNumbers(e)
	// {
	// 	if(auth_rec_en_input)
	// 	{
	// 		var keynum
	// 		var keychar
	// 		var numcheck

	// 		if(window.event) // IE
	// 		  {
	// 		  keynum = e.keyCode
	// 		  }
	// 		else if(e.which) // Netscape/Firefox/Opera
	// 		  {
	// 		  keynum = e.which
	// 		  }
	// 		keychar = String.fromCharCode(keynum)
	// 		numcheck = /\b\w+\b/
	// 		return !numcheck.test(keychar)
	// 	}
	// }

	function go_save(fun,value,state)
	{
		echo("go_save:文字存檔判定");


		//判定條件   選項是否填寫
		if(document.getElementById('rec_text_ans1').value.length==0 && document.getElementById('rec_text_ans2').value.length==0 && document.getElementById('rec_text_ans3').value.length==0 )
		{
			echo("文字推薦 條件未達成");
			cover("你沒有填寫任何東西喔",1);
			return false;
		}
		time_lock = false;
		echo("go_save:通過 進入存檔階段");
		set_rec_text(fun,value,state);
	}

	//存檔
	function set_rec_text(fun,value,state)
	{
		echo("set_rec_star:初始開始:儲存文字資料>"+book_sid+"_"+uid);
		cover("儲存文字資料中");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e18',window.parent.parent.action_on);
		var url = "./ajax/set_rec_text.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,
					time:time,
					auth_coin_open:auth_coin_open,
					rec_text_ans1:document.getElementById('rec_text_ans1').value,
					rec_text_ans2:document.getElementById('rec_text_ans2').value,
					rec_text_ans3:document.getElementById('rec_text_ans3').value,
					user_permission:'<? echo $permission;?>'

			}).success(function (data)
			{

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",1);
					echo("AJAX:success:set_rec_star():儲存文字資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
				echo("AJAX:success:set_rec_star():儲存文字資料:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
					if(fun!= null)
					{
						if(data_array["text"]!="")cover(data_array["text"],1);
						if(data_array["coin"]>0)window.parent.parent.set_coin(data_array["coin"]);
						fun(value,state);

					}else
					{
						window.document.getElementById("save_btn").style.display = "none";
						window.parent.save_lock = 0;
						cover("存檔完畢",1);
						if(data_array["text"]!="")cover(data_array["text"],1);
						if(data_array["coin"]>0)window.parent.parent.set_coin(data_array["coin"]);
						if(data_array["exp_score"]>0)window.parent.parent.set_score_exp(data_array["exp_score"]);
					}
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:set_rec_star():儲存文字資料:");
			});
	}

	//計時器裝置=================
	//事件 :  操作時變更的設定
		function set_rec_on_edit(value)
		{
			time_lock = value;
			window.parent.save_lock = 1;
			window.document.getElementById("save_btn").style.display = "block";
		}

		//事件 :  設置計時器
		function timedCount()
		{
			if(time_lock)time++;
			echo(time);
			rec_edit_setTimeout=setTimeout("timedCount()",1000);
		}
		timedCount();
	//=========MAIN=============
	function main()
	{
		echo("Main:初始開始:讀取文字資料>"+book_sid+"_"+uid);
		cover("讀取文字資料");
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e12',window.parent.parent.action_on);

		var url = "./ajax/get_rec_text.php";
		$.post(url, {
					user_id:uid,
					book_sid:book_sid,

					user_permission:'<? echo $permission;?>'
			}).success(function (data)
			{
                

				if(data[0]!="{")
				{
					cover("資料庫好像有點問題呢，請再試試看<BR>或通知系統人員",2,function(){main();});
					echo("AJAX:success:main():讀取文字資料:資料庫發生問題");
					return false;
				}

				data_array = JSON.parse(data);
                

				echo("AJAX:success:main():讀取文字資料:已讀出:"+data);
				if(data_array["error"]!="")
				{
					cover(data_array["error"]);
					return false;
				}
				if(data_array["echo"]!="")
				{
					cover(data_array["echo"],1);

				}else
				{
                   
					window.document.getElementById("rec_text_ans1").value = data_array["rec_text_content_1"];
					window.document.getElementById("rec_text_ans2").value = data_array["rec_text_content_2"];
					window.document.getElementById("rec_text_ans3").value = data_array["rec_text_content_3"];

     // 
					// window.parent.set_content(data_array["rec_text_content"],data_array["rec_text_score"],data_array["keyin_cdate"]);
     //                console.log(data_array["rec_text_content"]);
     //                console.log(data_array["keyin_cdate"]);
                       
                     for(var key in data_array){
                        if(typeof(data_array[key]) == 'object' ){
                        
                             window.parent.set_content(data_array[key]["rec_text_content"],data_array[key]["rec_text_score"],data_array[key]["keyin_cdate"],key);
                           

                        }

                    }
					cover("");     


					// console.log(window.document.getElementById("rec_text_ans3").value );
					// checkCopyLimit();
				}

			}).error(function(e){
				cover("");
				window.alert("連線失敗請重新讀取/存檔");
			}).complete(function(e){
				echo("AJAX:complete:main():讀取文字資料:");
			});
	}

	 //---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
$.ajaxSetup({
		timeout: 15*1000
	});
       main();

    </script>
</Html>














