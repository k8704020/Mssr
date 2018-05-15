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

                //-----------------------------------------------
                //SQL-發表文章數量(群)
                //-----------------------------------------------

                    $sql="
                        SELECT
 
                            `mssr`.`mssr_forum_article`.`user_id`,
                            `mssr`.`mssr_forum_article`.`article_id`,  
                            `mssr`.`mssr_forum_article`.`keyin_cdate`,

                            ifnull((
                                select 
                                        action_code  
                                from 
                                        mssr_action_forum_log
                                where 1 = 1
                                    and keyin_cdate < `mssr`.`mssr_forum_article`.`keyin_cdate`
                                    and action_from = `mssr`.`mssr_forum_article`.`user_id`
                                    and action_code not in('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')
                                order by  keyin_cdate  desc 
                                limit 1    
                            ),null)  as 'action_code',
                            
                             ifnull((
                                select 
                                        keyin_cdate 
                                from 
                                        mssr_action_forum_log
                                where 1 = 1
                                    and keyin_cdate < `mssr`.`mssr_forum_article`.`keyin_cdate`
                                    and action_from = `mssr`.`mssr_forum_article`.`user_id`
                                    and action_code not in('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')
                                    order by  keyin_cdate  desc 
                                limit 1    
                            ),null)  as 'date'
                            
                        FROM `mssr`.`mssr_forum_article`

                            INNER JOIN `user`.`member` ON
                            `mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            
                             INNER JOIN `mssr`.`mssr_article_forum_rev` ON
                            `mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_forum_rev`.`article_id`

                        WHERE 1=1

                            AND `mssr`.`mssr_forum_article`.`user_id` IN (
                                SELECT `user`.`student`.`uid`
                                FROM `user`.`student`
                                WHERE 1=1
                                    AND `user`.`student`.`class_code` in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2')                
                            )
                    ";                      
                        
                
                    $article_group_class_all =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<I><B><h2 style="color:blue">發文路徑分析</h2></B></I>
<HR>
<H3>
    1為接近最發文的動作<BR>
    排除：<BR>
    g22(在群的一般討論區頁面裡，點擊發表文章按鈕)<BR>
    g2(群的頁面，點擊大家來聊書頁籤(一般討論區))<BR>
    p6(點擊人的聊書群頁面裡的其中一個聊書群)<BR>
    p10(點擊人頁的聊書小組頁籤)<BR>
    p2(點擊導覽列(我的聊書小組))<BR>
    g11(在群的頁面，點擊群首頁頁籤)<BR>
    g5在群的興趣書單頁面，點擊裡面的書<BR>
    ('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4')
    總比數<?php echo count($article_group_class_all);?>
    </H3>
<BR>

群頁發文
    <table border="1px" align="center">
      <tr>
          <th>uid</th>
          <th>article_id</th>
          <th>1</th>
      </tr>
    <?php 
        $arr_count = array();
        foreach($article_group_class_all as $k=>$v){ 
    ?>
      <tr align="center">
          <td><?php echo $v['user_id']; ?><br/>(<?php echo $v['keyin_cdate']; ?>)</td>
          <td><?php echo $v['article_id']; ?></td>
          <td>
          <?php if($v['action_code']=='g26'){
                   
                    $date = $v['date'];
                    $uid = $v['user_id'];
                    $sql = "
                            select 
                                    action_code  
                            from 
                                    mssr_action_forum_log
                            where 1 = 1
                                and keyin_cdate <  '$date'
                                and action_from =  $uid
                                and action_code not in('g22', 'g2', 'p6', 'p10', 'n2', 'g11', 'g5', 'n4','g26','g3')
                            order by  keyin_cdate  desc 
                            limit 1
                    ";
                     $article_group_class_ac =db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                     echo $article_group_class_ac[0]['action_code'];
                     $arr_count[] =  $article_group_class_ac[0]['action_code'];
                      
          }else{
                     $arr_count[] = $v['action_code'];
                     echo $v['action_code'];

          } ?><br/><?php echo $v['date']; ?>
          </td>
      </tr>
      <?php }  
        $arr_count =  array_count_values($arr_count);
      ?>
    </table>

    <P> <HR><P>
    <table width="500" border="1" cellpadding="5" cellspacing="2">
        <thead>
            <tr color=blue>
                
                <th scope="col">action_code</th>
                <th scope="col">次數</th>
                
            </tr>
        </thead>
        <tbody>  
    <?php foreach($arr_count as $k=>$v){ ?>
            <tr align=center>
                <td class=""><?php echo $k ?></td>
                <td class=""><?php echo $v ?></td>
            <tr>
    <?php } ?>       
</body>
</html>
                  