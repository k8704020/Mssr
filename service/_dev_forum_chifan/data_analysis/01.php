<?php 
//-------------------------------------------------------
//mssr_forum
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------
        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/array/code'
       	);
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

	//---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------
            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

			//-----------------------------------------------
	        //下載資料庫
	        //-----------------------------------------------



//-------------------------------------------------------
//函式: date_week_array()
//用途: 週區段陣列
//日期: 2012年2月8日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_week_array($year,$month){
    //---------------------------------------------------
    //週區段陣列
    //---------------------------------------------------
    //$year     年
    //$month    月
    //
    //
    //本函式會傳回某年某月,每一週的起始與終止日的資訊
    //如果該月月初,非禮拜一,則要往前推至禮拜一
    //如果該月月尾,非禮拜日,則要往後推至禮拜日
    //
    //範例如下,假設2012年2月為我們的目標月份,則本函式推出
    //
    //第1週 --> 起始日: 2012-01-30,終止日 2012-02-05
    //第2週 --> 起始日: 2012-02-06,終止日 2012-02-12
    //第3週 --> 起始日: 2012-02-13,終止日 2012-02-19
    //第4週 --> 起始日: 2012-02-20,終止日 2012-02-26
    //第5週 --> 起始日: 2012-02-27,終止日 2012-03-04
    //---------------------------------------------------

        //參數檢驗
        if(!isset($year)||!is_numeric($year)){
            return array();
        }
        if(!isset($month)||!is_numeric($month)){
            return array();
        }

        //週區段參數陣列
        $arry=array(
            0=>array('f'=>6,'b'=>0), //禮拜日
            1=>array('f'=>0,'b'=>6), //禮拜一
            2=>array('f'=>1,'b'=>5), //禮拜二
            3=>array('f'=>2,'b'=>4), //禮拜三
            4=>array('f'=>3,'b'=>3), //禮拜四
            5=>array('f'=>4,'b'=>2), //禮拜五
            6=>array('f'=>5,'b'=>1)  //禮拜六
        );

        //變數設定
        $arry_week=array();
        $arry_week_first  =array();
        $arry_week_between=array();
        $arry_week_last   =array();

        //第一天,最末天
        $first_date=$year."-".$month."-"."01";
        $last_date ="";
        $last_day  =date("t",strtotime($first_date));
        $info      =date_parse($first_date);
        $last_date =$info["year"]."-".$info["month"]."-".$last_day;

        //-----------------------------------------------
        //推最各週,起始日,終止日
        //-----------------------------------------------

            //第一週
            $week      =date('w',strtotime($first_date));
            $sday      =$arry[$week]['f'];
            $eday      =$arry[$week]['b'];
            $sdate     =date("Y-m-d",strtotime($first_date)-($sday)*86400);
            $edate     =date("Y-m-d",strtotime($first_date)+($eday)*86400);
            $other_s   =date("Y-m-d",strtotime($edate)+(1)*86400);
            $arry_week_first[]=array('sdate'=>$sdate,'edate'=>$edate);

            //最末週
            $week      =date('w',strtotime($last_date));
            $sday      =$arry[$week]['f'];
            $eday      =$arry[$week]['b'];
            $sdate     =date("Y-m-d",strtotime($last_date)-($sday)*86400);
            $edate     =date("Y-m-d",strtotime($last_date)+($eday)*86400);
            $other_e   =date("Y-m-d",strtotime($sdate)-(1)*86400);
            $arry_week_last[]=array('sdate'=>$sdate,'edate'=>$edate);

            //其他週
            $st=strtotime($other_s);
            $et=strtotime($other_e);
            while($st<$et){
                $sdate=date("Y-m-d",$st);
                $edate=date("Y-m-d",$st+(86400*6));
                $arry_week_between[]=array('sdate'=>$sdate,'edate'=>$edate);
                $st+=(86400*7);
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
            $arry_week=array_merge($arry_week_first,$arry_week_between,$arry_week_last);
            return $arry_week;
    }

     $date4 = date_week_array(2015,4);
     $date5 = date_week_array(2015,5);
     


     $date4['0']['sdate'] = '2015-04-01';
     $date4['4']['edate'] = '2015-04-30';
     $date5['0']['sdate'] = '2015-05-01';

    $date =  array_merge($date4,$date5);
// echo '<pre>';
// print_r($date);
// echo '</pre>';
// die();
            //-----------------------------------------------
			//SQL-發表文章數量
			//-----------------------------------------------
     		$i =0;
     		foreach($date as $k=>$v){

     			$sdate = $v['sdate'];
     			$edate = $v['edate'];
     			
     			$sql="
						SELECT
 
							`mssr`.`mssr_forum_article`.`user_id`,
							`mssr`.`mssr_forum_article`.`article_id`,
							`mssr`.`mssr_forum_article`.`keyin_cdate`
						
						FROM `mssr`.`mssr_forum_article`

						INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`

		

						WHERE 1=1

						AND `mssr`.`mssr_forum_article`.`user_id` IN (
							SELECT `user`.`student`.`uid`
							FROM `user`.`student`
							WHERE 1=1
								AND `user`.`student`.`class_code`in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2')	
																
						)
								AND `mssr`.`mssr_forum_article`.keyin_cdate >= '$sdate'
								AND `mssr`.`mssr_forum_article`.keyin_cdate <= '$edate'
						
					";					
					
					$article_class_1[] =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$artilce_class_1_con[]=count($article_class_1[$i]);		
     		$i++;
		   

     		}
     		
     		$i =0;
     		foreach($date as $k=>$v){
			    $sdate = $v['sdate'];
     			$edate = $v['edate'];

			$sql="
						SELECT
						 
							`mssr`.`mssr_forum_article_reply`.`user_id`
							
						FROM `mssr`.`mssr_forum_article_reply`

							INNER JOIN `user`.`member` ON
							`mssr`.`mssr_forum_article_reply`.`user_id`=`user`.`member`.`uid`
							

						WHERE 1=1

							AND `mssr`.`mssr_forum_article_reply`.`user_id` IN (
								SELECT `user`.`student`.`uid`
								FROM `user`.`student`
								WHERE 1=1
									AND `user`.`student`.`class_code`in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2')	
								
							)
								AND `mssr`.`mssr_forum_article_reply`.keyin_cdate >= '$sdate'
								AND `mssr`.`mssr_forum_article_reply`.keyin_cdate <= '$edate'

					";						
						
	
					$reply_class_1[]     	=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
					$reply_class_1_con[] 	=count($reply_class_1[$i]);
			$i++;
// echo '<pre>';
//  	 print_r($reply_class_1_con);
//  	echo '</pre>';		

			}
     


?>            		


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<table border="1" align="center">
			<tr>
				<th>時間</th><th>發文</th><th>回文</th>
			</tr>
			
			<?php foreach($artilce_class_1_con as $k=>$v){ ?>
			<tr align="center">
			
				<td><?php echo $date[$k]['sdate'];?><br/>
					<?php echo $date[$k]['edate'];?>
				</td>
				<td><?php echo $v ?></td>
				<td><?php echo $reply_class_1_con[$k] ?></td>
			
			</tr>
			<?php } ?>

	</table>


</body>
</html>


 

 
 

  
  
  

