<?php 
//-------------------------------------------------------
//明日聊書
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

		//外掛頁面檔
		require_once(str_repeat("../",2).'pages/code.php');

		//外掛函式檔
		$funcs=array(
			APP_ROOT.'inc/code',
			APP_ROOT.'service/forum/inc/code'
		);
		func_load($funcs,true);

		//清除並停用BUFFER
		@ob_end_clean();

	//---------------------------------------------------
	//有無維護
	//---------------------------------------------------

	//---------------------------------------------------
	//有無登入,SESSION
	//---------------------------------------------------

		$arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
		if(empty($arrys_sess_login_info)){
			$msg="您沒有權限進入，請洽詢明日星球團隊人員!";
			$jscript_back="
				<script>
					alert('{$msg}');
					location.href='/ac/index.php';
				</script>
			";
			die($jscript_back);
		}

		if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
		if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
		if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
		if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
		if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
		if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
		if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
		if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
		if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
		if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
		if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
			$sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
			foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
				$sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
			}
		}

	//---------------------------------------------------
	//接收,設定參數
	//---------------------------------------------------

		$user_id=(isset($_GET['user_id']))?(int)$_GET['user_id']:0;
		$tab    =(isset($_GET['tab']))?(int)$_GET['tab']:1;

	//---------------------------------------------------
	//資料庫
	//---------------------------------------------------

		//-----------------------------------------------
		//連線物件
		//-----------------------------------------------

			//建立連線 mssr
			$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

			//建立連線 user
			$conn_user=conn($db_type='mysql',$arry_conn_user);

		//-----------------------------------------------
		//紀錄訊息 SQL
		//-----------------------------------------------

			$sql = "
				SELECT
					`mssr_forum`.`mssr_forum_report_message`.`article_type`,
					`mssr_forum`.`mssr_forum_report_message`.`article_id`,
					`mssr_forum`.`mssr_forum_report_message`.`message_status`,
					`mssr_forum`.`mssr_forum_report_message`.`keyin_cdate`,

					`mssr_forum`.`mssr_forum_article_detail`.`article_title`
				FROM `mssr_forum`.`mssr_forum_report_message`
					INNER JOIN `mssr_forum`.`mssr_forum_article_detail` ON
					`mssr_forum`.`mssr_forum_report_message`.`article_id` = `mssr_forum`.`mssr_forum_article_detail`.`article_id`
				WHERE 1=1
					AND `mssr_forum`.`mssr_forum_report_message`.`user_id` = $user_id
				ORDER BY `mssr_forum`.`mssr_forum_report_message`.`keyin_cdate` DESC
			";

			$message_results = db_result($conn_type='pdo', $conn_mssr, $sql, array(0, 10), $arry_conn_mssr);

	//---------------------------------------------------
	//資料,設定
	//---------------------------------------------------
 ?>
<div class="user_lefe_side_tab3 row">
<?php 
if (!empty($message_results)) {
 ?>
 	<div style="text-align: left; padding-left: 20px;">
 		<h4>文章被檢舉紀錄：</h4>
 	</div>
<?php 
	foreach($message_results as $message_result) {
		$article_type = trim($message_result['article_type']);
		$article_id = trim($message_result['article_id']);
		$message_status = trim($message_result['message_status']);
		$keyin_cdate = trim($message_result['keyin_cdate']);
		$article_title = trim($message_result['article_title']);

		if ($article_type == 1) {
			$content = "
				你所發表的文章【{$article_title}】被多數人檢舉，已將此文章移除並扣除發文所獲得的50點積分。
			";
		}

		if ($article_type == 2) {
			$content = "
				你在文章<a target='_blank' href='reply.php?get_from=1&article_id={$article_id}'>【{$article_title}】</a>中的回文被多數人檢舉，已將此回文移除並扣除50點發文點數作為懲罰。
			";
		}
?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="position:relative;margin-top:15px;">
		<div class="media">
			<div class="media-body">
				<h4 class="pull-left"><?php echo ($content);?></h4><br>
				<h4 class="pull-right"><?php echo ($keyin_cdate);?></h4>
			</div>
		</div>
	</div>
<?php 
	}
} else {
 ?>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<h2>你現在沒有任何紀錄。</h2>
	</div>
<?php 
}
 ?>
</div>
<?php 
	$conn_user=NULL;
	$conn_mssr=NULL;
?>