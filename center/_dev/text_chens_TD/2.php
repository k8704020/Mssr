<?

//---------------------------------------------------
//設定與引用
//---------------------------------------------------
		
			//SESSION
			@session_start();
		
			//啟用BUFFER
			@ob_start();
		
			//外掛設定檔
			require_once(str_repeat("../",2)."/config/config.php");
		
			 //外掛函式檔
			$funcs=array(
						APP_ROOT.'inc/code',
						APP_ROOT.'lib/php/db/code',
						APP_ROOT.'lib/php/array/code'
						);
			func_load($funcs,true);
			
		
			//清除並停用BUFFER
			@ob_end_clean();
			
			//建立連線 user
			$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------		 
?>
<script>var onfocus_text_id="rec_text_ans1"</script>
<textarea cols=25 rows=12 style="font-size:20px;resize: none; "  id="rec_text_ans1" onfocus="onfocus_text_for('rec_text_ans1');"></textarea>
<textarea cols=25 rows=12 style="font-size:20px;resize: none; "  id="rec_text_ans2" onfocus="onfocus_text_for('rec_text_ans2');"></textarea>
<textarea cols=25 rows=12 style="font-size:20px;resize: none; "  id="rec_text_ans3" onfocus="onfocus_text_for('rec_text_ans3');"></textarea> 
<table width="750" border="0" >
	<tr>
		<td><img onClick="inputComma('，');" src="img/text_btn_1.png" /></td>
        <td><img onClick="inputComma('。');" src="img/text_btn_2.png" /></td>
        <td><img onClick="inputComma('；');" src="img/text_btn_3.png" /></td>
        <td><img onClick="inputComma('：');" src="img/text_btn_4.png" /></td>
        <td><img onClick="inputComma('！');" src="img/text_btn_5.png" /></td>
        <td><img onClick="inputComma('？');" src="img/text_btn_6.png" /></td>
        <td><img onClick="inputComma('、');" src="img/text_btn_7.png" /></td>
        <td><img onClick="inputComma('...');" src="img/text_btn_8.png" /></td>
        <td><img onClick="inputComma('「');" src="img/text_btn_9.png" /></td>
        <td><img onClick="inputComma('」');" src="img/text_btn_10.png" /></td>
        <td><img onClick="inputComma('“');" src="img/text_btn_11.png" /></td>
        <td><img onClick="inputComma('”');" src="img/text_btn_12.png" /></td>
   	 
	</tr>
</table>
<script>
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
</script>
