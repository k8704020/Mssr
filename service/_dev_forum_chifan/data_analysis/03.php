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
                                    and action_code not in('b1','p1','n0','p8', 'b14', 'b2', 'g5', 'n4', 'g26')
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
                                    and action_code not in('b1','p1','n0','p8', 'b14', 'b2', 'g5', 'n4', 'g26')
                                    order by  keyin_cdate  desc 
                                limit 1    
                            ),null)  as 'date'
                            
                        FROM `mssr`.`mssr_forum_article`

                            INNER JOIN `user`.`member` ON
                            `mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            
                             INNER JOIN `mssr`.`mssr_article_book_rev` ON
                            `mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

                        WHERE 1=1

                            AND `mssr`.`mssr_forum_article`.`user_id` IN (
                                SELECT `user`.`student`.`uid`
                                FROM `user`.`student`
                                WHERE 1=1
                                    AND `user`.`student`.`class_code` in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2') 
                                
                            )

                    ";                      

// echo '<pre>';
// print_r($sql);
// echo '</pre>';                 
                    $article_book_class_all=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
// echo '<pre>';
// print_r($sql);
// echo '</pre>';    

                    $sql="
                        SELECT
                            (
                                select 
                                        action_code  
                                from 
                                        mssr_action_forum_log
                                where 1 = 1
                                    and keyin_cdate < `mssr`.`mssr_forum_article`.`keyin_cdate`
                                    and action_from = `mssr`.`mssr_forum_article`.`user_id`
                                    and action_code not in('b1','p1','n0','p8', 'b14', 'b2', 'g5', 'n4', 'g26')
                                order by  keyin_cdate  desc 
                                limit 1    
                            ) as 'action_code'
                            
                        FROM `mssr`.`mssr_forum_article`

                            INNER JOIN `user`.`member` ON
                            `mssr`.`mssr_forum_article`.`user_id`=`user`.`member`.`uid`
                            
                             INNER JOIN `mssr`.`mssr_article_book_rev` ON
                            `mssr`.`mssr_forum_article`.`article_id`=`mssr`.`mssr_article_book_rev`.`article_id`

                        WHERE 1=1

                            AND `mssr`.`mssr_forum_article`.`user_id` IN (
                                SELECT `user`.`student`.`uid`
                                FROM `user`.`student`
                                WHERE 1=1
                                    AND `user`.`student`.`class_code` in('gcp_2014_2_5_5','gcp_2014_2_5_1','gcp_2014_2_5_2') 
                                
                            )

                    ";                      

               
                    $article_book_class=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                    $arr_count = array();
                    foreach($article_book_class as $k =>$v){
                        $code = $v['action_code'];
                        if($code!=""){
                            if(!array_key_exists($code,$arr_count)){
                                 $arr_count[$code] = 1;
                            }else{
                               $arr_count[$code] += 1;
                            }
                        }      
                            
                    } 

// echo '<pre>';
// print_r($article_book_class);
// echo '</pre>';

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <I><B><h2 style="color:blue">發文路徑分析</h2></B></I>
<HR>
<H3>
    1為接近最發文的動作<BR>
    排除：<BR>
    b1(在書的頁面，點擊發表文章按鈕)<BR>
    p1(點擊人的書櫃頁面裡面的書)<BR>
    n0(點擊導覽列(我的書櫃))<BR>
    p8(點擊人頁的書櫃頁籤)<BR>
    筆數<?php echo count($article_book_class_all);?><BR>
</H3>
<BR>

書頁發文
    <table border="1px" align="center">
      <tr>
          <th>uid</th>
          <th>article_id</th>
          <th>1</th>
      </tr>
    <?php foreach($article_book_class_all as $k=>$v){ ?>
      <tr align="center">
          <td><?php echo $v['user_id']; ?><br/>(<?php echo $v['keyin_cdate']; ?>)</td>
          <td><?php echo $v['article_id']; ?></td>
          <td><?php echo $v['action_code']; ?><br/><?php echo $v['date']; ?></td>
      </tr>
      <?php } ?>
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