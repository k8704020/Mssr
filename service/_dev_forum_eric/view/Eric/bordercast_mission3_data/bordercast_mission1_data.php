<?php
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",5).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",3).'pages/code.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
			APP_ROOT.'lib/php/db/code',
            APP_ROOT.'service/_dev_forum_eric/inc/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

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



		
		//-------------------------------------------------
		//抓取所有使用者
		$class_uid = array();
		$sql="
			SELECT 
				user.student.uid
				FROM user.student
					inner join user.class on user.class.class_code = user.student.class_code 
					inner join user.semester on user.class.semester_code = user.semester.semester_code 					
				where 1=1 
					AND user.class.class_code in ('ctg_2015_2_3_5_1','wxt_2015_2_3_2_2','wxt_2015_2_4_1_2','wxt_2015_2_4_2_2','gsw_2015_2_3_3_1','gnd_2015_2_3_2_2','wuh_2015_2_3_1_2','wuh_2015_2_4_1_2','ybs_2015_2_3_1_3','ybs_2015_2_3_2_3','ybs_2015_2_3_3_1','ybs_2015_2_4_1_3','dzu_2015_2_4_2_1','clc_2015_2_5_1_3','dhl_2015_2_4_1_2','dhl_2015_2_4_2_2','dhl_2015_2_4_3_2','itl_2015_2_3_1_2','itl_2015_2_3_2_2','itl_2015_2_4_1_2','itl_2015_2_4_2_2','itl_2015_2_5_1_2','itl_2015_2_5_2_2','dxi_2015_2_3_2_2','dxi_2015_2_3_3_2','dxi_2015_2_3_4_2','dxi_2015_2_3_5_2','dxi_2015_2_3_6_2','dxi_2015_2_3_7_2','dxi_2015_2_4_1_2','dxi_2015_2_4_7_2','lrb_2015_2_4_2_2'  )
			group by user.student.uid
		";
		/*
		user.semester.school_code in ('ctg','wxt','gsw','gnd','wuh','lqd','ybs','dzu','clc','dhl','itl','dxi','lrb')
		user.class.class_code in ('ctg_2015_2_3_5_1','wxt_2015_2_3_2_2','wxt_2015_2_4_1_2','wxt_2015_2_4_2_2','gsw_2015_2_3_3_1','gnd_2015_2_3_2_2','wuh_2015_2_3_1_2','wuh_2015_2_4_1_2','wuh_2015_2_5_1_2','lqd_2015_2_4_5_1','ybs_2015_2_3_1_3','ybs_2015_2_3_2_3','ybs_2015_2_3_3_1','ybs_2015_2_4_1_3','dzu_2015_2_4_2_1','clc_2015_2_5_1_3','dhl_2015_2_4_1_2','dhl_2015_2_4_2_2','dhl_2015_2_4_3_2','itl_2015_2_3_1_2','itl_2015_2_3_2_2','itl_2015_2_4_1_2','itl_2015_2_4_2_2','itl_2015_2_5_1_2','itl_2015_2_5_2_2','dxi_2015_2_3_2_2','dxi_2015_2_3_3_2','dxi_2015_2_3_4_2','dxi_2015_2_3_5_2','dxi_2015_2_3_6_2','dxi_2015_2_3_7_2','dxi_2015_2_4_1_2','dxi_2015_2_4_7_2','lrb_2015_2_4_2_2')
		
		*/
		$get_data_uid = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_data_uid as $key =>$get_data_uid){
			$class_uid[$key] = $get_data_uid['uid'];
		}
		$result1 = implode(",",$class_uid);
		
		echo "<table border=1>";
		echo "<tr style='background-color:yellow'>";
		echo "<td>個數</td>";
		echo "<td>使用者編號</td>";
		echo "<td>班級代號</td>";
		echo "</tr>";
		
		//-------------------------------------------------
		//找出發文的所有article_id
		//發文篇數
		$i = 0;
		$article_id = array();
		$user_id = array();
		$sql="			
			SELECT a.`user_id`,user.student.class_code,am.`article_id`
			FROM `mssr_forum`.`dev_article_group_mission_rev` AS am
				INNER JOIN `mssr_forum`.`mssr_forum_article` AS a ON ( am.`article_id` = a.`article_id` )
				INNER JOIN user.student ON (user.student.uid = a.`user_id`)
				INNER JOIN user.class ON (user.class.class_code = user.student.class_code) 
			WHERE 1=1
				AND `group_task_id` = 5 
				AND a.`article_from`= 3
				AND a.`user_id` in ($result1)
			group by am.`article_id`
		";
		$get_article_data = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		foreach($get_article_data as $key => $get_article_data){
			$article_id[$key] = $get_article_data['article_id'];
			$user_id[$key]    = $get_article_data['user_id'];
			$user_class[$key] = $get_article_data['class_code'];
			$join_number[$i]  = $get_article_data['user_id'];
			$i++;
			echo "<tr>";
				echo "<td> $i </td>";
				echo "<td> $user_id[$key] </td>";
				echo "<td> $user_class[$key] </td>";
			echo"</tr>";
		}
		$result2 = implode(",",$article_id);
		//echo "發文篇數：".$i;
		//echo "</br>";
		
		
		
		
		//-------------------------------------------------
		//找出有使用過「個人成就」的使用者
		$used_number = 0;
		$sql="
			SELECT count(mssr_forum.dev_member_score.u_id) as cno 
			FROM mssr_forum.dev_member_score
			WHERE 1=1
				AND mssr_forum.dev_member_score.u_id in ($result1)
			
		";
		$get_achievement_uid = db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
		if(isset($get_achievement_uid[0]['cno'])){
			$used_number = $get_achievement_uid[0]['cno'];
		}
		//-------------------------------------------------
		//

		
		//-------------------------------------------------
		echo "有使用過「個人成就」的使用者：".$used_number."人";
		
		





?>