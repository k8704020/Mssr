<?php
//-------------------------------------------------------
//推薦顯示頁面
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION

        //啟用BUFFER
        @ob_start();
		$conn_mssr;
		$conn_user;
		$arry_conn_mssr;
		$arry_conn_user;
        $DOCUMENT_ROOT=str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['DOCUMENT_ROOT']);

        //外掛設定檔
        require_once("{$DOCUMENT_ROOT}/mssr/config/config.php");
		require_once("{$DOCUMENT_ROOT}/mssr/inc/get_book_info/code.php");

         //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);


        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------


        //建立連線 user
        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
 		$conn_user=conn($db_type='mysql',$arry_conn_user);
	//---------------------------------------------------
    //END
    //---------------------------------------------------
	$data = array();

	$data["rec_stat_cno"]=0;
	$data["rec_draw_cno"]=0;
	$data["rec_text_cno"]=0;
	$data["rec_record_cno"]=0;
	$data["reason"]="";
	$data["rank"]="";
	$data["draw_scr"]="";
	$data["text1"]="";
	$data["text2"]="";
	$data["text3"]="";
	$data["record_scr"]=0;
	$data["start_time"]=0;
	$data["edit_time"]=0;

	$user_id = $_GET["uid"];
	$book_sid = $_GET["book_sid"] ;

	$get_book_info=get_book_info($conn='',$book_sid,$array_select=array("book_name"),$arry_conn_mssr);
	$data["book_name"] = $get_book_info[0]["book_name"];

	//判斷權限是否為管理員.若為管理員則統一顯示"王大明"學生的作品

	$sql = "
		   SELECT permission
 		   FROM `member`
 		   WHERE uid= $user_id

 		   ";


 	$return = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);

 	$permission =$return [0]['permission'];

 	$student_id='';


 	if($permission=="super"){


 		$student_id= '1238';


 	}else{

		$sql1= "SELECT uid_sub
		 		   FROM `kinship`
		 		   WHERE uid_main= $user_id
				  ";
		$return = db_result($conn_type='pdo',$conn_user,$sql1,$arry_limit=array(0,1),$arry_conn_user);


		$student_id=$return[0]['uid_sub'];


 	}


	
	

	$sql = "SELECT rec_stat_cno, rec_draw_cno,rec_text_cno,rec_record_cno,keyin_cdate,keyin_mdate
			FROM  `mssr_rec_book_cno`
			WHERE  user_id =$student_id
			AND    book_sid ='{$book_sid}'
			";
	$result = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);

	//搜尋個別推薦資料
	if(count($result))
	{
		//時間
		$data["start_time"]=$result[0]["keyin_cdate"];
		$data["edit_time"]=$result[0]["keyin_mdate"];

		//評星
		if($result[0]["rec_stat_cno"])
		{
			$sql_star = "SELECT rec_reason,rec_rank
						 FROM   `mssr_rec_book_star_log`
						 WHERE  user_id =$student_id
						 AND    book_sid ='{$book_sid}'
						 ORDER BY  keyin_cdate DESC ";
			$result_star = db_result($conn_type='pdo',$conn_mssr,$sql_star,$arry_limit=array(0,1),$arry_conn_mssr);

			for($i=1;$i<=$result_star[0]["rec_rank"];$i++)
			{
				$data["rank"]=$data["rank"]."★";
			}
			$string = array
			(
				"內容很有趣",
				"封面畫插圖很好看",
				"內容輕鬆好讀",
				"內容很感人",
				"喜歡故事人物",
				"可以學到很多知識",
				"印象深刻"
			);
			for($i = 0; $i < sizeof($string);$i++)
			{
				if($result_star[0]["rec_reason"][$i]=="o")$data["reason"]=$data["reason"].$string[$i]."　";
			}

			$data["rec_stat_cno"]=1;
		}

		//繪圖
		if($result[0]["rec_draw_cno"])
		{

			/* set the FTP hostname */
			$user = $arry_ftp1_info['account'];
			$pass = $arry_ftp1_info['password'];
			$host = $arry_ftp1_info['host'];
			$hostname = "ftp://".$user .":".$pass."@".$host."/public_html/mssr/info/user/{$student_id}/book/{$book_sid}/draw/";

			$filename = sprintf('http://'.$host."/mssr/info/user/%s/book/%s/draw/",$student_id,$book_sid);


			if(file_exists($hostname.'bimg/1.jpg')) $data["draw_scr"]=$filename.'bimg/1.jpg';
			else if(file_exists($hostname.'bimg/1.jpg'))$data["draw_scr"]=$filename.'bimg/upload_1.jpg';
			else if(file_exists($hostname.'bimg/1.jpg'))$data["draw_scr"]=$filename.'bimg/upload_2.jpg';
			else if(file_exists($hostname.'bimg/1.jpg'))$data["draw_scr"]=$filename.'bimg/upload_3.jpg';


			$data["rec_draw_cno"]=1;
		}

		//文字
		if($result[0]["rec_text_cno"])
		{
			$sql_text = "SELECT rec_content
						 FROM  `mssr_rec_book_text_log`
						 WHERE  user_id =$student_id
						 AND    book_sid ='{$book_sid}'
						 ORDER BY  keyin_cdate DESC ";
			$result_text = db_result($conn_type='pdo',$conn_mssr,$sql_text,$arry_limit=array(0,1),$arry_conn_mssr);
			$rs_rec_text_content=trim($result_text[0]["rec_content"]);
			if(@unserialize($rs_rec_text_content)){
				$arrys_rs_rec_text_content=@unserialize($rs_rec_text_content);
				$data["text1"]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[0])));
				$data["text2"]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[1])));
				$data["text3"]=htmlspecialchars(gzuncompress(base64_decode($arrys_rs_rec_text_content[2])));
			}
			$data["rec_text_cno"]=1;
		}

		//錄音
		if($result[0]["rec_record_cno"])
		{

			/* set the FTP hostname */
			$user = $arry_ftp1_info['account'];
			$pass = $arry_ftp1_info['password'];
			$host = $arry_ftp1_info['host'];

			$filename = sprintf('http://'.$host."/mssr/info/user/%s/book/%s/record/1.mp3",$student_id,$book_sid);



			$data["record_scr"]=$filename;
			$data["rec_record_cno"]=1;
		}
	}





?>
<style type="text/css">
td {
	background-color: #DDD;
	font-weight: bold;
	color: #8E4408;
font-size: 18px;
}
table{
	background-color: #FFF;
}
body{
	color: #8E4408;

}
</style>


<body>
<table id="1" width="900px" align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top:25px;">
  <tr>
        <td>
        	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                    	<table width="100%"  border="0" cellpadding="0" cellspacing="5" >
                            <tr>
                                <td>
                                    <span style="position:relative;left:10px;top:-3px;">書籍名稱 : <? echo $data["book_name"];?></span>
                                    <br/>
                                    <span style="position:relative;left:10px;">建立時間 : <? echo 	$data["start_time"];?></span>
                                    <span style="position:relative;left:100px;">更新時間 : <? echo 	$data["edit_time"];?></span>
                    			</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
  </tr>
    <tr>
        <td>
        	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                <tr>
                    <td>
                    	<table width="100%" height="400px" border="0" cellpadding="0" cellspacing="5"  style="left:5px;">
                            <tr style="left:5px;">
                                <td style="left:5px;">
                   				  <div id="teg_2" align="center"><img src="<?php echo $data["draw_scr"];?>" width="355px"  align="center" height="283px" border="0" alt="尚未畫圖"/></div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td>
                        <table width="100%" height="400px"  border="0" cellpadding="0" cellspacing="5" >
                            <tr>
                                <td>
                                    <span style="position:relative;left:10px;top:-3px;">評價 : <? echo $data["rank"];?></span></br>
                                    <span style="position:relative;left:10px;top:-3px;">理由 : <? echo $data["reason"];?></span></br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                  <div id="teg_4" align="center">
                                    <textarea cols="40" rows="10" wrap="hard" style="width:460px;height:260px;display:block;border:0;font-weight: bold;color: #8E4408;font-size: 18px;	">
											【最喜歡的一句話】
											<?php if($data["text1"]!=''):?>
											<?php echo $data["text1"];?>
											<?php endif;?>


											【書本內容介紹】
											<?php if($data["text2"]!=''):?>
											<?php echo $data["text2"];?>
											<?php endif;?>


											【書中所學到的事】
											<?php if($data["text3"]!=''):?>
											<?php echo $data["text3"];?>
											<?php endif;?>
                                    </textarea></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="teg_5" align="center"><? if($data["rec_record_cno"] != 0){?><span style="position:relative;padding:5px;top:5px;">
                                    <audio id="audio" controls="controls">
                                                        <source id="record1" src="<?php echo $data["record_scr"];?>"/>
                                                        <source id="record2" src="<?php echo $data["record_scr"];?>"/>
                                            無法播放，建議使用 chrome 瀏覽器獲得更佳體驗。
                                    </audio>
                                                </span><? }else{echo "尚未錄音";}?></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>




</table>
</body>