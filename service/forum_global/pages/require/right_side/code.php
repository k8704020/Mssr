<?php
//-------------------------------------------------------
//函式: right_side()
//用途: 側邊欄資訊
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------
    //判斷 main     => isset && !empty(main) && is_array(main)
    //判斷 sun_main => isset && !empty(sun_main) && is_array(sun_main)

        ////外掛設定檔
        //require_once(str_repeat("../",5).'config/config.php');
        //require_once(str_repeat("../",5).'inc/code.php');
        //$conn_user=conn($db_type='mysql',$arry_conn_user);
        //$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
        //$user_id=5032;
        //$user_id=694;
        //$user_id=12169;

        //$oright_side=new right_side($user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
        //echo "<Pre>";
        //print_r(get_class_methods($oright_side));
        //echo "</Pre>";

        ////側欄-人(登入者看到自己)
        //$arry_member_self=$oright_side->member_self($user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
        //echo "<Pre>";
        //print_r($arry_member_self);
        //echo "</Pre>";

        ////側欄-人(登入者看到其他人)
        //$arry_member_other=$oright_side->member_other($user_id,$member_id=5031,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
        //echo "<Pre>";
        //print_r($arry_member_other);
        //echo "</Pre>";

        ////側欄-書
        //$arry_book=$oright_side->book($user_id,$book_sid='mbl1201310091653566182403',$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
        //echo "<Pre>";
        //print_r($arry_book);
        //echo "</Pre>";

        ////側欄-群
        //$arry_group=$oright_side->group($user_id,$group_id=1,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
        //echo "<Pre>";
        //print_r($arry_group);
        //echo "</Pre>";

    class right_side{
    //---------------------------------------------------
    //函式: right_side()
    //用途: 側邊欄資訊
    //---------------------------------------------------
    //$user_id              登入者主索引
    //$conn_user            user 資料庫連線物件
    //$conn_mssr            mssr 資料庫連線物件
    //$arry_conn_user       user 資料庫連線資訊陣列
    //$arry_conn_mssr       mssr 資料庫連線資訊陣列
    //---------------------------------------------------

        //公開成員
        public $member1;

        //私有成員(可繼承)
        Protected $member2;

        //私有成員(不可繼承)
        Private $member3;

        //引入 session 變數
        protected $sess_school_code;

        //公開成員函式
        public function group($user_id,$school_code,$group_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr){
        //側欄-群

            $arry_right_side_msg=array();
            $arry_msg1=array();
            $arry_msg2=array();
            $arry_msg3=array();
            $user_id=(int)$user_id;
            $group_id=(int)$group_id;

            //補檢驗
            if($group_id===0){
                $err_msg='GROUP_ID IS INVALID';
                $this->err_report($err_msg);
            }

            //撈出(登入者)全部好友
            $arry_good_friend_info=$this->get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //撈出(登入者)全部好友的所有朋友
            $arry_good_friend_friend_info=$this->get_arry_good_friend_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //訊息1
                //撈出這個小組的所有成員
                $arrys_group=array();
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                    FROM `mssr_forum`.`mssr_forum_group_user_rev`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`  = {$group_id}
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_user_id=(int)$db_result['user_id'];
                        $arrys_group[]=$rs_user_id;
                    }
                }
                shuffle($arrys_group);

                //判斷登入者是否有加入這個小組
                $is_join_group=$this->is_join_group($user_id,$group_id,$conn_mssr,$arry_conn_mssr);
                $arry_msg1=array();
                if($is_join_group){
                //是，顯示5個小組成員(不包含好友以及好友的朋友優先顯示)
                    $arry_msg1['main']=array();
                    foreach($arrys_group as $rs_user_id){
                        if(!in_array($rs_user_id,$arry_good_friend_info)&&!in_array($rs_user_id,$arry_good_friend_friend_info)){
                            if(count($arry_msg1['main'])<5){
                                $arry_msg1['main'][]=$rs_user_id;
                            }
                        }
                    }
                    $i=0;
                    while(count($arry_msg1['main'])<5){
                        if(isset($arrys_group[$i])){
                            if(!in_array($arrys_group[$i],$arry_msg1['main'])){
                                $arry_msg1['main'][]=$arrys_group[$i];
                            }
                        }else{
                            break;
                        }
                        $i++;
                    }
                }else{
                //否，顯示5個小組成員(好友以及好友的朋友優先顯示)，及誘導加入小組的字眼
                    $arry_msg1['main']=array();
                    foreach($arrys_group as $rs_user_id){
                        if(in_array($rs_user_id,$arry_good_friend_info)||in_array($rs_user_id,$arry_good_friend_friend_info)){
                            if(count($arry_msg1['main'])<5){
                                $arry_msg1['main'][]=$rs_user_id;
                            }
                        }
                    }
                    $i=0;
                    while(count($arry_msg1['main'])<5){
                        if(isset($arrys_group[$i])){
                            if(!in_array($arrys_group[$i],$arry_msg1['main'])){
                                $arry_msg1['main'][]=$arrys_group[$i];
                            }
                        }else{
                            break;
                        }
                        $i++;
                    }
                }

                //輔助, 並個別顯示這五個人有借閱過哪些其他的書，且不包含該小組的書籍清單，以及自己不能借閱過(取一筆)
                $user_book_borrow_info='';
                $rs_user_id_list='';
                $arry_user_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),$day_lin='',$conn_mssr,$arry_conn_mssr);
                if(!empty($arry_user_book_borrow_info))$user_book_borrow_info=implode("','",$arry_user_book_borrow_info['book_sid']);
                if(!empty($arry_msg1['main'])){
                    $rs_user_id_list=implode(",",$arry_msg1['main']);
                    $sql="
                        SELECT `book_sid`
                        FROM `mssr_book_borrow_semester`
                        WHERE 1=1
                            AND `user_id` IN ({$rs_user_id_list})
                            AND `book_sid` NOT IN (
                                SELECT `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`
                                FROM  `mssr_forum`.`mssr_forum_group_booklist`
                                WHERE `mssr_forum`.`mssr_forum_group_booklist`.`group_id`={$group_id}
                            )
                    ";
                    if($user_book_borrow_info!=='')$sql.="AND `book_sid` NOT IN ('{$user_book_borrow_info}')";
                    $sql.="
                        GROUP BY `book_sid`
                        LIMIT 5
                    ";
                    $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                    if(!empty($db_results)){
                        foreach($db_results as $inx=>$db_result){
                            $rs_book_sid=trim($db_result['book_sid']);
                            if(isset($arry_msg1['main'][$inx])){
                                $rs_book_name='';
                                $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                                $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                                $arry_msg1['submain'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                            }
                        }
                    }
                }
                if(!empty($arry_msg1['main'])){
                    foreach($arry_msg1['main'] as $inx=>$rs_user_id){
                        $rs_user_name='';
                        $rs_user_name=@trim($this->get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                        $arry_msg1['main'][$inx]="<a href='user.php?user_id={$rs_user_id}&tab=1'>".$rs_user_name."</a>";
                    }
                }else{
                    $arry_msg1['main']=array();
                    $arry_msg1['submain']=array();
                }

            //訊息2
                //撈出這個聊書小組的書籍清單，以討論次數排序(取前20本)
                $arry_book=array();
                $sql="
                        SELECT `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`,
                               `mssr_forum`.`mssr_forum_group_booklist`.`keyin_cdate`
                        FROM `mssr_forum`.`mssr_forum_group_booklist`
                            INNER JOIN `mssr_forum`.`mssr_forum_article_book_rev` ON
                            `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`=`mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group_booklist`.`group_id`={$group_id}
                    UNION ALL
                        SELECT `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`,
                               `mssr_forum`.`mssr_forum_group_booklist`.`keyin_cdate`
                        FROM `mssr_forum`.`mssr_forum_group_booklist`
                            INNER JOIN `mssr_forum`.`mssr_forum_reply_book_rev` ON
                            `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`=`mssr_forum`.`mssr_forum_reply_book_rev`.`book_sid`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group_booklist`.`group_id`={$group_id}
                    ORDER BY `keyin_cdate` DESC
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,100),$arry_conn_mssr);
                if(!empty($db_results)){
                    $arrys_book=array();
                    foreach($db_results as $db_result){
                        $rs_book_sid=trim($db_result['book_sid']);
                        $arrys_book[]=$rs_book_sid;
                    }
                    if(!empty($arrys_book)){
                        $arrys_book=array_count_values($arrys_book);
                        arsort($arrys_book);
                        $cno=0;
                        foreach($arrys_book as $val){
                            if($cno>=20){
                                unset($arrys_book[$val]);
                            }
                            $cno++;
                        }
                        $arry_book=array_keys($arrys_book);
                    }

                    //隨機顯示五本書
                    shuffle($arry_book);
                    $cno=0;
                    foreach($arry_book as $val){
                        if($cno<5)$arry_msg2['main'][]=$val;
                    }

                    //輔助, 並顯示討論次數
                    if(!empty($arry_msg2['main'])){
                        foreacH($arry_msg2['main'] as $inx=>$rs_book_sid){
                            $rs_book_sid=trim($rs_book_sid);
                            $arry_msg2['submain'][$inx]=$arrys_book[$rs_book_sid];
                        }
                    }

                    if(!empty($arry_msg2['main'])){
                        foreach($arry_msg2['main'] as $inx=>$val){
                            $val=trim($val);
                            $rs_book_name='';
                            $rs_book_name=@trim(get_book_info($conn_mssr,$val,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                            $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                            $arry_msg2['main'][$inx]="<a href='article.php?get_from=1&book_sid={$val}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";;
                        }
                    }
                }else{
                    $arry_msg2['main']=array();
                    $arry_msg2['submain']=array();
                }

            //訊息3
                $merge_good_friend_info='';
                $arry_merge_good_friend_info=($arry_good_friend_info+$arry_good_friend_friend_info);
                $merge_good_friend_info=implode(",",$arry_merge_good_friend_info);

                //撈出這個小組的成員(限定好友以及好友的朋友)
                //撈出他們參加的其他聊書小組
                //按重複數高→低排序聊書小組，且自己沒有參與過
                //(群組B)
                $arry_group_b=array();
                $arry_group_b_count=array();

                $filter_1='';
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                    FROM `mssr_forum`.`mssr_forum_group_user_rev`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`   = {$user_id}
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $arry_filter_1[]=(int)$db_result['group_id'];
                    }
                    $filter_1=implode(",",$arry_filter_1);
                }
                if($merge_good_friend_info!==''){
                    $filter_2='';
                    $sql="
                        SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                        FROM `mssr_forum`.`mssr_forum_group_user_rev`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`  IN ({$merge_good_friend_info})
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`  = {$group_id}
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                        GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                    ";
                    $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                    if(!empty($db_results)){
                        foreach($db_results as $db_result){
                            $arry_filter_2[]=(int)$db_result['user_id'];
                        }
                        $filter_2=implode(",",$arry_filter_2);
                    }
                    $sql="
                        SELECT
                            `mssr_forum`.`mssr_forum_group`.`group_id`,
                            `mssr_forum`.`mssr_forum_group`.`group_name`
                        FROM `mssr_forum`.`mssr_forum_group`
                            INNER JOIN `mssr_forum`.`mssr_forum_group_user_rev` ON
                            `mssr_forum`.`mssr_forum_group`.`group_id`=`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`<>{$group_id}
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                    ";
                    if(isset($filter_1)&&$filter_1!==''){
                        $sql.="
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id` NOT IN ($filter_1)
                        ";
                    }
                    if(isset($filter_2)&&$filter_2!==''){
                        $sql.="
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id` IN ($filter_2)
                        ";
                    }
                    $sql.="
                        GROUP BY `mssr_forum`.`mssr_forum_group`.`group_id`
                        LIMIT 5
                    ";
                    $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                    if(!empty($db_results)){
                        foreach($db_results as $db_result){
                            $rs_group_id=(int)$db_result['group_id'];
                            $rs_group_name=trim($db_result['group_name']);
                            $arry_group_b_tmp[]=$rs_group_id;
                        }
                    }
                    if(!empty($arry_group_b_tmp)){
                        $arry_group_b_count=array_count_values($arry_group_b_tmp);
                        arsort($arry_group_b_count);
                        $arry_group_b=array_chunk(array_keys($arry_group_b_count),5)[0];
                    }
                }

                //撈出這個小組的成員(不包含好友以及好友的朋友)
                //撈出他們參加的其他聊書小組
                //按重複數高→低排序聊書小組，且自己沒有參與過
                //(群組A)
                $arry_group_a=array();
                $arry_group_a_count=array();
                $sql="
                    SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                    FROM `mssr_forum`.`mssr_forum_group_user_rev`
                        INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                        `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                    WHERE 1=1
                        AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`<>{$group_id}
                ";
                if(isset($filter_1)&&$filter_1!==''){
                    $sql.="
                        AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id` NOT IN ($filter_1)
                    ";
                }
                if($merge_good_friend_info!==''){
                    if(isset($filter_2)&&$filter_2!==''){
                        $sql.="
                            AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id` IN ($filter_2)
                        ";
                    }
                }
                $sql.="
                    LIMIT 5
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_group_id=(int)$db_result['group_id'];
                        $arry_group_a_tmp[]=$rs_group_id;
                    }
                    if(!empty($arry_group_a_tmp)){
                        $arry_group_a_count=array_count_values($arry_group_a_tmp);
                        arsort($arry_group_a_count);
                        $arry_group_a=array_chunk(array_keys($arry_group_a_count),5)[0];
                    }
                }

                //撈取5個群組B裡的聊書小組，則不足則由群組A裡的聊書小組補滿
                $arry_group=array();
                if(!empty($arry_group_b) || !empty($arry_group_a)){
                    if(!empty($arry_group_b))$arry_group=array_chunk($arry_group_b,5)[0];
                    $i=0;
                    while(count($arry_group)<5){
                        if(!isset($arry_group_a[$i]))break;
                        if(count($arry_group)<5){
                            $arry_group[]=$arry_group_a[$i];
                        }
                        $i++;
                    }
                    if(!empty($arry_group)){
                        $arry_group=array_unique($arry_group);
                        foreach($arry_group as $inx=>$rs_group_id){
                            if($rs_group_id!==0&&isset($school_code)&&trim($school_code)!==''){
                                $arry_blacklist_group_school=$this->get_blacklist_group_school($school_code,$rs_group_id,$conn_mssr,$arry_conn_mssr);
                                if(!empty($arry_blacklist_group_school))continue;
                            }
                            $rs_group_name='';
                            $rs_group_name=@trim($this->get_group_info($conn_mssr,$rs_group_id,$array_filter=array('group_name'),$arry_conn_mssr)[0]['group_name']);
                            $rs_group_name_substr=mb_substr($rs_group_name,0,8)."..";
                            $arry_msg3['main'][]="<a href='article.php?get_from=2&group_id={$rs_group_id}' title='{$rs_group_name}'>".$rs_group_name_substr."</a>";
                        }
                    }
                }else{
                    $arry_msg3['main']=array();
                }

                //輔助, 並個別顯示那些好友參與過該聊書小組(取一筆)
                //輔助, 再個別顯示(幾位好友數-1)參與過該聊書小組
                if(!empty($arry_group)&&!empty($arry_good_friend_info)){
                    $good_friend_info=implode(",",$arry_good_friend_info);
                    foreach($arry_group as $inx=>$rs_group_id){
                        $join_cno=0;
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                            FROM `mssr_forum`.`mssr_forum_group_user_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id` IN ({$good_friend_info})
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`  = {$rs_group_id}
                                AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                            GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                        ";
                        $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        shuffle($db_results);
                        if(!empty($db_results)){
                            $rs_user_id=(int)$db_results[0]['user_id'];
                            $rs_user_name='';
                            $rs_user_name=@trim($this->get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                            $arry_msg3['submain'][$inx]['user_id']="<a href='user.php?user_id={$rs_user_id}&tab=1'>".$rs_user_name."</a>";
                        }
                        $join_cno=count($db_results);
                        if((int)($join_cno)>=1){
                            $join_cno=(int)($join_cno-1);
                            $arry_msg3['submain'][$inx]['join_cno']=(int)($join_cno);
                        }else{
                            $arry_msg3['submain']=array();
                        }
                    }
                }else{
                    $arry_msg3['submain']=array();
                }

            //訊息彙整
            $arry_right_side_msg[]=$arry_msg1;
            $arry_right_side_msg[]=$arry_msg2;
            $arry_right_side_msg[]=$arry_msg3;

            //關閉連線
            $conn_user=NULL;
            $conn_mssr=NULL;

            //回傳
            return $arry_right_side_msg;
        }
        public function book($user_id,$school_code,$book_sid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr){
        //側欄-書

            $arry_right_side_msg=array();
            $arry_msg1=array();
            $arry_msg2=array();
            $arry_msg3=array();
            $user_id=(int)$user_id;
            $book_sid=addslashes(trim($book_sid));
            $day_line=date("Y-m-d", strtotime("-180day"));

            //補檢驗
            if(!is_string($book_sid)||($book_sid==='')){
                $err_msg='BOOK_SID IS INVALID';
                $this->err_report($err_msg);
            }

            //撈出(登入者)全部好友
            $arry_good_friend_info=$this->get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //撈出(登入者)全部好友的所有朋友
            $arry_good_friend_friend_info=$this->get_arry_good_friend_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //撈出好友以及好友的朋友在半年內借閱過此書籍的人
            $arry_merge_good_friend_info=($arry_good_friend_info+$arry_good_friend_friend_info);
            $merge_good_friend_info=implode(",",$arry_merge_good_friend_info);
            $arry_has_borrow_good_friend_info=array();
            if(!empty($arry_merge_good_friend_info)){
                $sql="
                    SELECT
                        `user_id`,
                        `borrow_sdate`
                    FROM `mssr_book_borrow_log`
                    WHERE 1=1
                        AND `user_id` IN ({$merge_good_friend_info  })
                        AND `book_sid` = '{$book_sid                }'
                    GROUP BY `user_id`, `book_sid`
                    ORDER BY `borrow_sdate` DESC
                    LIMIT 1
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_user_id=(int)$db_result['user_id'];
                        $rs_borrow_sdate=strtotime(trim($db_result['borrow_sdate']));
                        $arry_has_borrow_good_friend_info[$rs_borrow_sdate]=$rs_user_id;
                    }
                }
            }

            //---- 例外, 若比對無任何好友借閱過此書籍, 則撈出前五人借閱過此書籍即可 ----//
            if(empty($arry_has_borrow_good_friend_info)||count($arry_has_borrow_good_friend_info)<5){
                $sql="
                    SELECT
                        `user_id`,
                        `borrow_sdate`
                    FROM `mssr_book_borrow_log`
                    WHERE 1=1
                        AND `book_sid` = '{$book_sid     }'
                    GROUP BY `user_id`, `book_sid`
                    ORDER BY `borrow_sdate` DESC
                    LIMIT 5
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_user_id=(int)$db_result['user_id'];
                        $rs_borrow_sdate=strtotime(trim($db_result['borrow_sdate']));
                        $arry_has_borrow_good_friend_info[$rs_borrow_sdate]=$rs_user_id;
                    }
                }
            }
            //---- 例外, 若比對無任何好友借閱過此書籍, 則撈出前五人借閱過此書籍即可 ----//

            krsort($arry_has_borrow_good_friend_info);
            $arry_has_borrow_good_friend_info=array_values($arry_has_borrow_good_friend_info);

            //訊息1
                //顯示最近看過的前五個人
                foreach($arry_has_borrow_good_friend_info as $inx=>$rs_friend_id){
                    $rs_friend_id=(int)$rs_friend_id;
                    if($inx<5&&$rs_friend_id!==$user_id){
                        $rs_user_name='';
                        $rs_user_name=@trim($this->get_user_info($conn_user,$rs_friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                        $arry_msg1['main'][]="<a href='user.php?user_id={$rs_friend_id}&tab=1'>".$rs_user_name."</a>";

                        //輔助, 並個別顯示這五個人對書籍的進貨評價(為什麼會選擇這本書)
                        $sql="
                            SELECT
                                `opinion_answer`
                            FROM `mssr_book_read_opinion_log`
                            WHERE 1=1
                                AND `user_id`  =  {$rs_friend_id }
                                AND `book_sid` = '{$book_sid     }'
                            ORDER BY `borrow_sdate` DESC
                            LIMIT 1
                        ";
                        $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            $rs_opinion_answer=trim($db_results[0]['opinion_answer']);
                            if(unserialize($rs_opinion_answer)){
                                $arrys_opinion_answer=unserialize($rs_opinion_answer);
                                foreach($arrys_opinion_answer as $inx=>$arry_opinion_answer){
                                    $topic_id=(int)$arry_opinion_answer['topic_id'];
                                    //你為甚麼會選擇讀這本書
                                    if($topic_id===3){
                                        $opinion_answer=(int)$arry_opinion_answer['opinion_answer'][0];
                                        $like_num=(int)$opinion_answer;
                                        $sql="
                                            SELECT `topic_options`
                                            FROM `mssr_book_topic_log`
                                            WHERE 1=1
                                                AND `topic_id`={$topic_id}
                                        ";
                                        $like_result=$this->db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                        $topic_options=trim($like_result[0]['topic_options']);
                                        if(unserialize($topic_options)){
                                            $arrys_topic_options=unserialize($topic_options);
                                            if(isset($arrys_topic_options[$like_num])){
                                                $like_num_html=trim($arrys_topic_options[$like_num]);
                                                $arry_msg1['submain'][]=$like_num_html;
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            $arry_msg1['submain'][]='';
                        }
                    }
                }
                if(empty($arry_msg1)){
                    $arry_msg1['main']=array();
                    $arry_msg1['submain']=array();
                }

                //訊息2
                    if(!empty($arry_has_borrow_good_friend_info)){
                        //篩選最近看過的前10個人(優先篩選好友)
                        $arry_has_borrow_ten_good_friend_info=array_chunk($arry_has_borrow_good_friend_info,10)[0];

                        //撈出這10個人近半年內借閱過的其他書籍，且登入者不能借閱過
                        $arry_user_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),$day_line,$conn_mssr,$arry_conn_mssr);
                        $arry_ten_good_friend_book_borrow_info=$this->get_arry_book_borrow_info($arry_has_borrow_ten_good_friend_info,$day_line,$conn_mssr,$arry_conn_mssr);
                        $arry_good_friend_book_borrow_info_tmp=array();
                        $arry_good_friend_book_borrow_info=array();
                        foreach($arry_ten_good_friend_book_borrow_info['book_sid'] as $rs_book_sid){
                            if(!in_array($rs_book_sid,$arry_user_book_borrow_info['book_sid'])){
                                $arry_good_friend_book_borrow_info_tmp['book_sid'][]=$rs_book_sid;
                            }
                        }
                        if(empty($arry_good_friend_book_borrow_info_tmp)){
                            $arry_msg2['main']=array();
                            $arry_msg2['submain']=array();
                        }else{
                            $arry_good_friend_book_borrow_info_tmp['cno']=array_count_values($arry_good_friend_book_borrow_info_tmp['book_sid']);

                            //顯示重複數最高的前5本
                            arsort($arry_good_friend_book_borrow_info_tmp['cno']);
                            foreach($arry_good_friend_book_borrow_info_tmp['cno'] as $rs_book_sid=>$rs_cno){
                                if(count($arry_good_friend_book_borrow_info)<5)$arry_good_friend_book_borrow_info[]=$rs_book_sid;
                            }
                            foreach($arry_good_friend_book_borrow_info as $inx=>$rs_book_sid){
                                $rs_book_sid=trim($rs_book_sid);
                                $rs_book_name='';
                                $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                                $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                                $arry_msg2['main'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                                $borrow_cno=0;

                                //輔助, 並個別顯示哪些好友以及好友的朋友借閱過該本書(取一筆)
                                //輔助, 再個別顯示(幾位好友以及好友的朋友數-1)借閱過該本書
                                //shuffle($arry_merge_good_friend_info);
                                if(!empty($arry_merge_good_friend_info)){
                                    $cno=0;
                                    foreach($arry_merge_good_friend_info as $rs_friend_id){
                                        $rs_friend_id=(int)$rs_friend_id;
                                        $is_borrow=$this->is_borrow($rs_friend_id,$rs_book_sid,$conn_mssr,$arry_conn_mssr);
                                        if($is_borrow){
                                            $borrow_cno++;
                                            $rs_user_name='';
                                            $rs_user_name=@trim($this->get_user_info($conn_user,$rs_friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                            $arry_msg2['submain'][$inx]['user_id']="<a href='user.php?user_id={$rs_friend_id}&tab=1'>".$rs_user_name."</a>";
                                        }else{
                                            if(!isset($arry_msg2['submain'][$inx]['user_id']))$arry_msg2['submain'][$inx]['user_id']='';
                                        }
                                        if($cno===count($arry_merge_good_friend_info)-1)break;
                                        $cno++;
                                    }
                                }else{
                                    $arry_msg2['submain'][$inx]['user_id']='';
                                }
                                if((int)($borrow_cno)>=1)$borrow_cno=(int)($borrow_cno-1);
                                $arry_msg2['submain'][$inx]['borrow_cno']=(int)($borrow_cno);
                            }
                        }
                    }else{
                        $arry_msg2['main']=array();
                        $arry_msg2['submain']=array();
                    }

                //訊息3
                    //撈出好友以及好友的朋友加入了那些小組
                    $arry_group_info=array();
                    $arry_merge_good_friend_group_info=array();
                    if(!empty($arry_merge_good_friend_info)){
                        $arry_merge_good_friend_group_info=$this->get_arry_user_group_info($arry_merge_good_friend_info,$day_line='',$conn_mssr,$arry_conn_mssr);
                    }

                    //篩選出那些小組也討論這本書，取隨機5個小組(若不足則挑選其他的小組-只要有討論過這本書)
                    if(!empty($arry_merge_good_friend_group_info['cno'])&&!empty($arry_merge_good_friend_group_info['group_id'])){
                        $merge_good_friend_group_info='';
                        $merge_good_friend_group_info=implode(",",$arry_merge_good_friend_group_info['group_id']);
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group_booklist`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_group_booklist`
                                INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                                `mssr_forum`.`mssr_forum_group_booklist`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_booklist`.`group_id` IN ({$merge_good_friend_group_info})
                                AND `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`  = '{$book_sid}'
                                AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                        ";
                        $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                        if(!empty($db_results)){
                            foreach($db_results as $db_result){
                                $rs_group_id=(int)$db_result['group_id'];
                                $arry_group_info[]=$rs_group_id;
                            }
                        }
                        if(count($arry_group_info)<5){
                            $sql="
                                SELECT `mssr_forum`.`mssr_forum_group_booklist`.`group_id`
                                FROM `mssr_forum`.`mssr_forum_group_booklist`
                                    INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                                    `mssr_forum`.`mssr_forum_group_booklist`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`  = '{$book_sid}'
                                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                                ORDER BY RAND()
                            ";
                            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
                            foreach($db_results as $db_result){
                                $rs_group_id=(int)$db_result['group_id'];
                                if(!in_array($rs_group_id,$arry_group_info))$arry_group_info[]=$rs_group_id;
                            }
                        }
                    }else{
                        $sql="
                            SELECT `mssr_forum`.`mssr_forum_group_booklist`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_group_booklist`
                                INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                                `mssr_forum`.`mssr_forum_group_booklist`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`  = '{$book_sid}'
                                AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                            ORDER BY RAND()
                        ";
                        $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,array(0,5),$arry_conn_mssr);
                        foreach($db_results as $db_result){
                            $rs_group_id=(int)$db_result['group_id'];
                            if(!in_array($rs_group_id,$arry_group_info))$arry_group_info[]=$rs_group_id;
                        }
                    }

                    //顯示這五個小組
                    if(!empty($arry_group_info)){
                        $arry_user_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),$day_line='',$conn_mssr,$arry_conn_mssr);
                        foreach($arry_group_info as $inx=>$rs_group_id){
                            if($rs_group_id!==0&&isset($school_code)&&trim($school_code)!==''){
                                $arry_blacklist_group_school=$this->get_blacklist_group_school($school_code,$rs_group_id,$conn_mssr,$arry_conn_mssr);
                                if(!empty($arry_blacklist_group_school))continue;
                            }
                            if($inx<5){
                                $rs_group_name='';
                                $rs_group_name=@trim($this->get_group_info($conn_mssr,$rs_group_id,$array_filter=array('group_name'),$arry_conn_mssr)[0]['group_name']);
                                $arry_msg3['main'][]="<a href='article.php?get_from=2&group_id={$rs_group_id}'>".$rs_group_name."</a>";

                                //輔助, 並個別顯示小組內還有討論哪些書籍且自己不能借閱過(取一本書)
                                $sql="
                                    SELECT `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`
                                    FROM `mssr_forum`.`mssr_forum_group_booklist`
                                    WHERE 1=1
                                        AND `mssr_forum`.`mssr_forum_group_booklist`.`group_id`  =  {$rs_group_id}
                                        AND `mssr_forum`.`mssr_forum_group_booklist`.`book_sid`  <>'{$book_sid   }'
                                    LIMIT 1
                                ";
                                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                                if(!empty($db_results)){
                                    $rs_book_sid=trim($db_results[0]['book_sid']);
                                    if(!in_array($rs_book_sid,$arry_user_book_borrow_info['book_sid'])){
                                        $rs_book_name='';
                                        $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                                        $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                                        $arry_msg3['submain'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                                    }
                                }
                            }
                        }
                    }else{
                        $arry_msg3['main']=array();
                        $arry_msg3['submain']=array();
                    }

            //訊息彙整
            $arry_right_side_msg[]=$arry_msg1;
            $arry_right_side_msg[]=$arry_msg2;
            $arry_right_side_msg[]=$arry_msg3;

            //關閉連線
            $conn_user=NULL;
            $conn_mssr=NULL;

            //回傳
            return $arry_right_side_msg;
        }
        public function member_other($user_id,$school_code,$member_id=0,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr){
        //側欄-人(登入者看到其他人)

            $arry_right_side_msg=array();
            $arry_msg1=array();
            $arry_msg2=array();
            $arry_msg3=array();
            $user_id=(int)$user_id;
            $member_id=(int)$member_id;

            //補檢驗
            if($member_id===0){
                $err_msg='MEMBER_ID IS INVALID';
                $this->err_report($err_msg);
            }

            //撈出(其他人)的所有好友
            $arry_member_good_friend_info=$this->get_arry_good_friend_info($member_id,$conn_mssr,$arry_conn_mssr);

            //撈出(登入者)全部好友
            $arry_user_good_friend_info=$this->get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //訊息1
                //比對是否有相同的好友(比對完隨機排列)，不足則補滿到五筆
                $arry_intersect_good_friend_info=array_intersect($arry_member_good_friend_info,$arry_user_good_friend_info);

                //---- 例外, 若比對無任何好友, 回傳空陣列 ----//
                if(empty($arry_intersect_good_friend_info)){
                    $arry_msg1['main']=array();
                    $arry_msg1['submain']=array();
                    //$arry_msg1=array();
                //---- 例外, 若比對無任何好友, 回傳空陣列 ----//
                }else{
                    //$i=0;
                    //while(count($arry_intersect_good_friend_info)<5){
                    //    if(!isset($arry_member_good_friend_info[$i]))break;
                    //    if(!in_array($arry_member_good_friend_info[$i],$arry_intersect_good_friend_info))$arry_intersect_good_friend_info[]=$arry_member_good_friend_info[$i];
                    //    $i++;
                    //}
                    shuffle($arry_intersect_good_friend_info);

                    //撈出相同好友借閱過書籍
                    //[主要]比對是否借閱過相同的書，若有則先顯示，顯示到有五筆為止
                    $arry_user_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),$day_line='',$conn_mssr,$arry_conn_mssr);
                    $arry_msg1['main']=array();
                    $arry_msg1['submain']=array();
                    $tmp_friend_id=array();
                    foreach($arry_intersect_good_friend_info as $friend_id){
                        $friend_id=(int)$friend_id;
                        if($friend_id!==$user_id){
                            $arry_good_friend_book_borrow_info=$this->get_arry_book_borrow_info(array($friend_id),$day_line='',$conn_mssr,$arry_conn_mssr);
                            $arry_intersect_book_borrow_info=array_values(array_intersect($arry_user_book_borrow_info['book_sid'],$arry_good_friend_book_borrow_info['book_sid']));
                            if(!empty($arry_intersect_book_borrow_info)&&count($arry_msg1['main'])<5){
                                $rs_user_name='';
                                $rs_user_name=@trim($this->get_user_info($conn_user,$friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                $arry_msg1['main'][]="<a href='user.php?user_id={$friend_id}&tab=1'>".$rs_user_name."</a>";
                                $tmp_friend_id[]=$friend_id;
                            }

                            //輔助, 並顯示借閱過相同的書籍名稱
                            if(!empty($arry_intersect_book_borrow_info)&&count($arry_msg1['submain'])<5){
                                $rs_book_sid=addslashes(trim($arry_intersect_book_borrow_info[0]));
                                $rs_book_name='';
                                $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                                $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                                $arry_msg1['submain'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                            }
                        }
                    }
                    foreach($arry_intersect_good_friend_info as $friend_id){
                        $friend_id=(int)$friend_id;
                        if(count($arry_msg1['main'])<5&&$friend_id!==$user_id){
                            if(!in_array($friend_id,$tmp_friend_id)){
                                $rs_user_name='';
                                $rs_user_name=@trim($this->get_user_info($conn_user,$friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                $arry_msg1['main'][]="<a href='user.php?user_id={$friend_id}&tab=1'>".$rs_user_name."</a>";
                                $arry_msg1['submain'][]='';
                            }
                        }
                    }
                }

            //訊息2
                //撈出(登入者)半年內閱讀過的書籍
                $day_line=date("Y-m-d", strtotime("-180day"));
                $arry_user_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),$day_line,$conn_mssr,$arry_conn_mssr);

                //撈出(其他人)半年內閱讀過的書籍
                $arry_member_book_borrow_info=$this->get_arry_book_borrow_info(array($member_id),$day_line,$conn_mssr,$arry_conn_mssr);

                //比對是否有相同的書籍
                $arry_intersect_book_borrow_info=array_intersect($arry_user_book_borrow_info['book_sid'],$arry_member_book_borrow_info['book_sid']);

                ////若沒有則隨機顯示五本書
                //if(count($arry_intersect_book_borrow_info)<5){
                //    shuffle($arry_user_book_borrow_info['book_sid']);
                //    foreach($arry_user_book_borrow_info['book_sid'] as $rs_book_sid){
                //        if(count($arry_intersect_book_borrow_info)<5)$arry_intersect_book_borrow_info[]=$rs_book_sid;
                //    }
                //}
                if(!empty($arry_intersect_book_borrow_info)){
                    $cno=0;
                    foreach(array_values($arry_intersect_book_borrow_info) as $rs_book_sid){
                        if($cno<5){
                            $rs_book_sid=trim($rs_book_sid);
                            $rs_book_name='';
                            $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                            $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                            $arry_msg2['main'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                            $borrow_cno=0;

                            //輔助, 並個別顯示有另外哪些(登入者)的好友半年內也讀這本書(取一筆好友)
                            //輔助, 再個別顯示(幾位好友-1)借閱過該本書
                            shuffle($arry_user_good_friend_info);
                            foreach($arry_user_good_friend_info as $rs_friend_id){
                                $rs_friend_id=(int)$rs_friend_id;
                                $is_borrow=$this->is_borrow($rs_friend_id,$rs_book_sid,$conn_mssr,$arry_conn_mssr);
                                if($is_borrow){
                                    $borrow_cno++;
                                    $rs_user_name='';
                                    $rs_user_name=@trim($this->get_user_info($conn_user,$rs_friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                    $arry_msg2['submain'][$cno]['user_id']="<a href='user.php?user_id={$rs_friend_id}&tab=1'>".$rs_user_name."</a>";
                                }else{
                                    $arry_msg2['submain'][$cno]['user_id']='';
                                }
                                break;
                            }
                            if((int)($borrow_cno)>1)$borrow_cno=(int)($borrow_cno-1);
                            $arry_msg2['submain'][$cno]['borrow_cno']=(int)($borrow_cno);
                            $cno++;
                        }
                    }
                }else{
                    $arry_msg2['main']=array();
                    $arry_msg2['submain']=array();
                }

            //訊息3
                //撈出(登入者)參與的聊書小組
                $arry_user_group_info=$this->get_arry_user_group_info(array($user_id),$day_line='',$conn_mssr,$arry_conn_mssr);

                //撈出(其他人)參與的聊書小組
                $arry_member_group_info=$this->get_arry_user_group_info(array($member_id),$day_line='',$conn_mssr,$arry_conn_mssr);

                //比對是否有相同的聊書小組
                $arry_intersect_group_info=array_intersect($arry_user_group_info['group_id'],$arry_member_group_info['group_id']);

                //若有則隨機顯示五筆聊書小組
                if(!empty($arry_intersect_group_info)){
                    $cno=0;
                    foreach($arry_intersect_group_info as $rs_group_id){
                        if($cno<5){
                            $rs_group_id=(int)$rs_group_id;
                            $rs_group_name='';
                            $rs_group_name=@trim($this->get_group_info($conn_mssr,$rs_group_id,$array_filter=array('group_name'),$arry_conn_mssr)[0]['group_name']);
                            $rs_group_name_substr=mb_substr($rs_group_name,0,8)."..";
                            $arry_msg3['main'][]="<a href='article.php?get_from=2&group_id={$rs_group_id}' title='{$rs_group_name}'>".trim($rs_group_name_substr)."</a>";

                            //輔助, 並個別顯示有另外那些(登入者)的好友也參與該聊書小組(取一筆好友)
                            shuffle($arry_user_good_friend_info);
                            foreach($arry_user_good_friend_info as $friend_id){
                                $friend_id=(int)$friend_id;
                                $rs_group_id=(int)$rs_group_id;
                                $is_join_group=$this->is_join_group($friend_id,$rs_group_id,$conn_mssr,$arry_conn_mssr);
                                if($is_join_group && $friend_id!==$member_id){
                                    $rs_user_name='';
                                    $rs_user_name=@trim($this->get_user_info($conn_user,$friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                    $arry_msg3['submain'][]="<a href='user.php?user_id={$rs_friend_id}&tab=1'>".$rs_user_name."</a>";
                                    break;
                                }
                            }
                            $cno++;
                        }
                    }
                }else{
                    $arry_msg3['main']=array();
                    $arry_msg3['submain']=array();
                }

            //訊息彙整
            $arry_right_side_msg[]=$arry_msg1;
            $arry_right_side_msg[]=$arry_msg2;
            $arry_right_side_msg[]=$arry_msg3;

            //關閉連線
            $conn_user=NULL;
            $conn_mssr=NULL;

            //回傳
            return $arry_right_side_msg;
        }
        public function member_self($user_id,$school_code,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr){
        //側欄-人(登入者看到自己)

            $arry_right_side_msg=array();
            $arry_msg1=array();
            $arry_msg2=array();
            $arry_msg3=array();
            $user_id=(int)$user_id;

            //撈出(登入者)全部好友的所有朋友
            $arry_good_friend_friend_info=$this->get_arry_good_friend_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //撈出(登入者)全部好友
            $arry_good_friend_info=$this->get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr);

            //訊息1
                //並排除(登入者)的好友
                $arry_diff_good_friend_friend_info=array_diff($arry_good_friend_friend_info,$arry_good_friend_info);

                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//
                if(empty($arry_diff_good_friend_friend_info)){
                    $arry_diff_good_friend_friend_info=$this->ar_user($user_id,$ar_cno=15,$conn_user,$arry_conn_user);
                }
                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//

                //挑選出重複數較高的前15人
                $array_count_values=array_count_values($arry_diff_good_friend_friend_info);
                arsort($array_count_values);
                $arry_filter_good_friend_friend_info=array_chunk(array_keys($array_count_values),15)[0];

                //顯示隨機5個人
                shuffle($arry_filter_good_friend_friend_info);
                $arry_filter_good_friend_friend_info=array_chunk($arry_filter_good_friend_friend_info,5)[0];
                $arry_my_book_borrow_info=$this->get_arry_book_borrow_info(array($user_id),date("Y-m-d", strtotime("-360day")),$conn_mssr,$arry_conn_mssr);
                foreach($arry_filter_good_friend_friend_info as $rs_user_id){
                    $rs_user_id=(int)$rs_user_id;
                    if($rs_user_id!==$user_id){
                        $rs_user_name='';
                        $rs_user_name=@trim($this->get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                        $arry_msg1['main'][]="<a href='user.php?user_id={$rs_user_id}&tab=1'>".$rs_user_name."</a>";

                        //輔助, 與登入者共同借閱過的書籍, 挑選重覆數較高的前一筆書(半學期)
                        $day_line=date("Y-m-d", strtotime("-180day"));
                        $arry_book_borrow_info=$this->get_arry_book_borrow_info(array($rs_user_id),$day_line,$conn_mssr,$arry_conn_mssr);
                        arsort($arry_book_borrow_info['cno']);
                        if(!empty($arry_book_borrow_info['cno'])&&!empty($arry_my_book_borrow_info['cno'])){
                            $arry_book_borrow_info['cno']=array_intersect_key($arry_book_borrow_info['cno'],$arry_my_book_borrow_info['cno']);
                            foreach($arry_book_borrow_info['cno'] as $rs_book_sid=>$rs_cno){
                                $rs_book_sid=addslashes(trim($rs_book_sid));
                                $rs_book_name='';
                                $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                                $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                                $arry_msg1['submain'][]="<br><a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                                break;
                            }
                        }
                    }
                }

            //訊息2
                //撈出好友在半年內借閱過的書籍且(登入者)不能看過
                $day_line=date("Y-m-d", strtotime("-180day"));
                $arry_book_borrow_info=array();

                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//
                if(empty($arry_good_friend_info)){
                    $arry_good_friend_info=$this->ar_user($user_id,$ar_cno=15,$conn_user,$arry_conn_user);
                }
                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//

                $arry_book_borrow_info1=$this->get_arry_book_borrow_info($arry_good_friend_info,$day_line,$conn_mssr,$arry_conn_mssr);
                $arry_book_borrow_info2=$this->get_arry_book_borrow_info(array($user_id),$day_line,$conn_mssr,$arry_conn_mssr);
                $arry_diff_book_borrow_info=array_diff($arry_book_borrow_info1['book_sid'],$arry_book_borrow_info2['book_sid']);
                foreach($arry_book_borrow_info1 as $key=>$arry_info){
                    if($key==='cno')foreach($arry_info as $rs_book_sid=>$rs_cno){
                        if(in_array($rs_book_sid,$arry_diff_book_borrow_info))$arry_book_borrow_info['cno'][$rs_book_sid]=$rs_cno;
                    }
                    if($key==='book_sid')foreach($arry_info as $rs_book_sid){
                        if(in_array($rs_book_sid,$arry_diff_book_borrow_info))$arry_book_borrow_info['book_sid'][]=$rs_book_sid;
                    }
                }

                //挑選出重複數較高的前10本書籍
                if(!empty($arry_book_borrow_info)){
                    arsort($arry_book_borrow_info['cno']);
                    $arry_book_borrow_info=array_chunk(array_keys($arry_book_borrow_info['cno']),10)[0];
                }else{
                    $arry_book_borrow_info=$this->ar_book($user_id,10,$conn_mssr,$arry_conn_mssr);
                }

                //隨機顯示其中5本書籍
                shuffle($arry_book_borrow_info);
                $arry_book_borrow_info=array_chunk($arry_book_borrow_info,5)[0];
                $arry_total_friend=array();
                $arry_total_friend_tmp=($arry_good_friend_friend_info+$arry_good_friend_info);
                foreach($arry_total_friend_tmp as $rs_user_id)if(!in_array($rs_user_id,$arry_total_friend))$arry_total_friend[]=$rs_user_id;
                shuffle($arry_total_friend);
                foreach($arry_book_borrow_info as $inx=>$rs_book_sid){
                    $rs_book_sid =trim($rs_book_sid);
                    if($rs_book_sid==='')continue;
                    $rs_book_name='';
                    $rs_book_name=@trim(get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr)[0]['book_name']);
                    $rs_book_name_substr=mb_substr($rs_book_name,0,8)."..";
                    $arry_msg2['main'][]="<a href='article.php?get_from=1&book_sid={$rs_book_sid}' title='{$rs_book_name}'>".$rs_book_name_substr."</a>";
                    $borrow_cno=0;

                    //輔助, 並個別顯示哪些好友以及好友的朋友借閱過該本書(取一筆)
                    //輔助, 再個別顯示(幾位好友以及好友的朋友數-1)借閱過該本書
                    shuffle($arry_total_friend);
                    $total_friend=implode(",",$arry_total_friend);
                    $sql="
                        SELECT `mssr`.`mssr_book_borrow_log`.`user_id`
                        FROM `mssr`.`mssr_book_borrow_log`
                        WHERE 1=1
                            AND `mssr`.`mssr_book_borrow_log`.`user_id`  IN ( {$total_friend } )
                            AND `mssr`.`mssr_book_borrow_log`.`book_sid`  = ('{$rs_book_sid  }')
                        GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`, `mssr`.`mssr_book_borrow_log`.`book_sid`
                    ";
                    $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
                    if(!empty($db_results)){
                        $borrow_cno=count($db_results);
                        shuffle($db_results);
                        foreach($db_results as $db_result){
                            $rs_user_id=(int)$db_result['user_id'];
                            $rs_user_name='';
                            $rs_user_name=@trim($this->get_user_info($conn_user,$rs_user_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                            $arry_msg2['submain'][$inx]['user_id']="<a href='user.php?user_id={$rs_user_id}&tab=1'>".$rs_user_name."</a>";
                            break;
                        }
                        if((int)($borrow_cno)>1)$borrow_cno=(int)($borrow_cno-1);
                        $arry_msg2['submain'][$inx]['borrow_cno']=(int)($borrow_cno);
                    }
                }

            //訊息3
                //撈出好友加入的聊書小組，並且(登入者)不加入過
                $arry_user_group_info=array();

                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//
                if(empty($arry_good_friend_info)){
                    $arry_good_friend_info=$this->ar_user($user_id,$ar_cno=15,$conn_user,$arry_conn_user);
                }
                //---- 例外, 若登入者無任何好友, 推薦班上人員15人 ----//

                $arry_user_group_info1=$this->get_arry_user_group_info($arry_good_friend_info,$day_line='',$conn_mssr,$arry_conn_mssr);
                $arry_user_group_info2=$this->get_arry_user_group_info(array($user_id),$day_line='',$conn_mssr,$arry_conn_mssr);
                $arry_diff_user_group_info=array_diff($arry_user_group_info1['group_id'],$arry_user_group_info2['group_id']);
                foreach($arry_user_group_info1 as $key=>$arry_info){
                    if($key==='cno')foreach($arry_info as $rs_group_id=>$rs_cno){
                        if(in_array($rs_group_id,$arry_diff_user_group_info))$arry_user_group_info['cno'][$rs_group_id]=$rs_cno;
                    }
                    if($key==='group_id')foreach($arry_info as $rs_group_id){
                        if(in_array($rs_group_id,$arry_diff_user_group_info))$arry_user_group_info['group_id'][]=$rs_group_id;
                    }
                }

                //---- 例外, 若無任何小組, 推薦隨機5組小組 ----//
                if(empty($arry_user_group_info)){
                    $arry_user_group_info_tmp=$this->ar_group($user_id,$ar_cno=5,$conn_mssr,$arry_conn_mssr);
                    foreach($arry_user_group_info_tmp as $rs_group_id){
                        $arry_user_group_info['cno'][$rs_group_id]=1;
                    }
                    $arry_user_group_info['group_id']=$arry_user_group_info_tmp;
                }
                //---- 例外, 若無任何小組, 推薦隨機5組小組 ----//

                //挑選出重複數較高的聊書小組前5組並顯示
                if(!empty($arry_user_group_info['cno'])&&!empty($arry_user_group_info['group_id'])){
                    arsort($arry_user_group_info['cno']);
                    $arry_user_group_info=array_chunk(array_keys($arry_user_group_info['cno']),5)[0];
                    shuffle($arry_user_group_info);
                    foreach($arry_user_group_info as $inx=>$rs_group_id){
                        $rs_group_name='';
                        $rs_group_name=@trim($this->get_group_info($conn_mssr,$rs_group_id,$array_filter=array('group_name'),$arry_conn_mssr)[0]['group_name']);
                        $rs_group_name_substr=mb_substr($rs_group_name,0,8)."..";
                        $arry_msg3['main'][]="<a href='article.php?get_from=2&group_id={$rs_group_id}' title='{$rs_group_name}'>".$rs_group_name_substr."</a>";

                        //輔助, 並顯示哪位好友加入(按加入時間排序, 取一筆)
                        foreach($arry_good_friend_info as $friend_id){
                            $friend_id=(int)$friend_id;
                            $rs_group_id=(int)$rs_group_id;
                            $is_join_group=$this->is_join_group($friend_id,$rs_group_id,$conn_mssr,$arry_conn_mssr);
                            if($is_join_group){
                                $rs_user_name='';
                                $rs_user_name=@trim($this->get_user_info($conn_user,$friend_id,$array_filter=array('name'),$arry_conn_user)[0]['name']);
                                $arry_msg3['submain'][]="<a href='user.php?user_id={$friend_id}&tab=1'>".$rs_user_name."</a>";
                                break;
                            }
                        }
                    }
                }else{
                    $arry_msg3['main']=array();
                    $arry_msg3['submain']=array();
                }

            //訊息彙整
            $arry_right_side_msg[]=$arry_msg1;
            $arry_right_side_msg[]=$arry_msg2;
            $arry_right_side_msg[]=$arry_msg3;

            //關閉連線
            $conn_user=NULL;
            $conn_mssr=NULL;

            //回傳
            return $arry_right_side_msg;
        }

        //私有成員函式(可繼承)
        Protected function ar_book($user_id,$ar_cno,$conn_mssr,$arry_conn_mssr){
        //挑選隨機書籍

            $user_id=(int)$user_id;
            $ar_cno=(int)$ar_cno;
            $arry_book=array();
            $sql="
                SELECT
                    `mssr`.`mssr_book_borrow_semester`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_semester`
                WHERE 1=1
                #ORDER BY RAND()
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,$ar_cno),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result){
                    if($inx<$ar_cno)$arry_book[]=trim($db_result['book_sid']);
                }
            }
            return $arry_book;
        }
        Protected function ar_group($user_id,$ar_cno,$conn_mssr,$arry_conn_mssr){
        //挑選隨機小組

            $user_id=(int)$user_id;
            $ar_cno=(int)$ar_cno;
            $arry_group=array();
            $sql="
                SELECT `mssr_forum`.`mssr_forum_group`.`group_id`
                FROM `mssr_forum`.`mssr_forum_group`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                ORDER BY RAND()
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,$ar_cno),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result){
                    if($inx<$ar_cno)$arry_group[]=(int)$db_result['group_id'];
                }
            }
            return $arry_group;
        }
        Protected function ar_user($user_id,$ar_cno,$conn_user,$arry_conn_user){
        //挑選班上隨機人員

            $user_id  =(int)$user_id;
            $ar_cno   =(int)$ar_cno;
            $curdate  =date("Y-m-d");
            $arry_user=array();
            $sql="
                SELECT
                    `user`.`student`.`uid`
                FROM `user`.`student`
                WHERE 1=1
                    AND (
                        `user`.`student`.`class_code`=(
                            SELECT
                                `user`.`student`.`class_code`
                            FROM `user`.`student`
                            WHERE 1=1
                                AND `user`.`student`.`uid`={$user_id}
                                AND '{$curdate}' BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                            LIMIT 1
                        )
                    )
                    AND '{$curdate}' BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,$ar_cno),$arry_conn_user);
            if(!empty($db_results)){
                foreach($db_results as $inx=>$db_result){
                    if($inx<$ar_cno)$arry_user[]=(int)$db_result['uid'];
                }
            }else{
                $sql="
                    SELECT
                        `user`.`student`.`uid`
                    FROM `user`.`student`
                    WHERE 1=1
                        AND `user`.`student`.`start` >='{$curdate}'
                        AND `user`.`student`.`end`   <='{$curdate}'
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,$ar_cno),$arry_conn_user);
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        if($inx<$ar_cno)$arry_user[]=(int)$db_result['uid'];
                    }
                }
            }
            return $arry_user;
        }
        Protected function is_join_group($user_id,$group_id,$conn_mssr,$arry_conn_mssr){
        //是否加入了小組

            $user_id=(int)$user_id;
            $group_id=(int)$group_id;
            $sql="
                SELECT `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`
                FROM `mssr_forum`.`mssr_forum_group_user_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                    `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id`  = ({$user_id })
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`group_id` = ({$group_id})
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state` =1
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
                ORDER BY `mssr_forum`.`mssr_forum_group_user_rev`.`keyin_cdate` ASC
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
            if(!empty($db_results)){
                return true;
            }else{
                return false;
            }
        }
        Protected function is_borrow($user_id,$book_sid,$conn_mssr,$arry_conn_mssr){
        //是否借閱過書籍

            $user_id=(int)$user_id;
            $book_sid=addslashes(trim($book_sid));
            $sql="
                SELECT `mssr`.`mssr_book_borrow_log`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_log`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_log`.`user_id`  IN ( {$user_id } )
                    AND `mssr`.`mssr_book_borrow_log`.`book_sid` IN ('{$book_sid}')
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1),$arry_conn_mssr);
            if(!empty($db_results)){
                return true;
            }else{
                return false;
            }
        }
        Protected function get_arry_user_group_info($user_id_list,$day_line='',$conn_mssr,$arry_conn_mssr){
        //加入的聊書小組

            $arry_user_group_info['cno']=array();
            $arry_user_group_info['group_id']=array();
            $user_id_list=addslashes(trim(implode(",",$user_id_list)));
            $day_line=addslashes(trim($day_line));
            $sql="
                SELECT
                    COUNT(`mssr_forum`.`mssr_forum_group_user_rev`.`group_id`) AS `group_cno`,
                    `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`
                FROM `mssr_forum`.`mssr_forum_group_user_rev`
                    INNER JOIN `mssr_forum`.`mssr_forum_group` ON
                    `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`=`mssr_forum`.`mssr_forum_group`.`group_id`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_id` IN ({$user_id_list})
                    AND `mssr_forum`.`mssr_forum_group_user_rev`.`user_state`=1
                    AND `mssr_forum`.`mssr_forum_group`.`group_state`=1
            ";
            $sql.=" GROUP BY `mssr_forum`.`mssr_forum_group_user_rev`.`group_id`";
            $sql.=" ORDER BY `mssr_forum`.`mssr_forum_group_user_rev`.`keyin_cdate` DESC";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_group_cno=(int)$db_result['group_cno'];
                    $rs_group_id=(int)$db_result['group_id'];
                    $arry_user_group_info['cno'][$rs_group_id]=$rs_group_cno;
                    $arry_user_group_info['group_id'][]=$rs_group_id;
                }
            }
            return $arry_user_group_info;
        }
        Protected function get_arry_book_borrow_info($user_id_list,$day_line='',$conn_mssr,$arry_conn_mssr){
        //借閱過的書籍

            $arry_book_borrow_info['cno']=array();
            $arry_book_borrow_info['book_sid']=array();
            $user_id_list=addslashes(trim(implode(",",$user_id_list)));
            $day_line=addslashes(trim($day_line));
            $sql="
                SELECT
                    COUNT(`mssr`.`mssr_book_borrow_semester`.`book_sid`) AS `borrow_cno`,
                    `mssr`.`mssr_book_borrow_semester`.`book_sid`
                FROM `mssr`.`mssr_book_borrow_semester`
                WHERE 1=1
                    AND `mssr`.`mssr_book_borrow_semester`.`user_id` IN ({$user_id_list})
            ";
            if($day_line!=='')$sql.=" AND `mssr`.`mssr_book_borrow_semester`.`borrow_sdate`>='{$day_line} 00:00:00'";
            $sql.=" GROUP BY `mssr`.`mssr_book_borrow_semester`.`book_sid`";
            $sql.=" ORDER BY `mssr`.`mssr_book_borrow_semester`.`borrow_sdate` DESC";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_borrow_cno=(int)$db_result['borrow_cno'];
                    $rs_book_sid  =trim($db_result['book_sid']);
                    $arry_book_borrow_info['cno'][$rs_book_sid]=$rs_borrow_cno;
                    $arry_book_borrow_info['book_sid'][]=$rs_book_sid;
                }
            }
            return $arry_book_borrow_info;
        }
        Protected function get_arry_good_friend_friend_info($user_id,$conn_mssr,$arry_conn_mssr){
        //好友的朋友資訊

            $arry_good_friend_friend_info=array();
            $user_id=(int)$user_id;
            $arry_good_friend_info=$this->get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr);
            if(empty($arry_good_friend_info))return $arry_good_friend_friend_info;
            $good_friend_info=implode(',',$arry_good_friend_info);
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                    `mssr_forum`.`mssr_forum_friend`.`friend_id`
                FROM `mssr_forum`.`mssr_forum_friend`
                WHERE 1=1
                    AND (
                        `mssr_forum`.`mssr_forum_friend`.`user_id`   IN ({$good_friend_info})
                            OR
                        `mssr_forum`.`mssr_forum_friend`.`friend_id` IN ({$good_friend_info})
                    )
                    #AND `mssr_forum`.`mssr_forum_friend`.`user_id`   <> {$user_id}
                    #AND `mssr_forum`.`mssr_forum_friend`.`friend_id` <> {$user_id}
                    AND `mssr_forum`.`mssr_forum_friend`.`friend_state` IN (1)
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_user_id  =(int)$db_result['user_id'];
                    $rs_friend_id=(int)$db_result['friend_id'];
                    if($rs_user_id===$user_id)continue;
                    if($rs_friend_id===$user_id)continue;
                    if(in_array($rs_user_id,$arry_good_friend_info))$arry_good_friend_friend_info[]=$rs_friend_id;//if(!in_array($rs_friend_id,$arry_good_friend_friend_info))
                    if(in_array($rs_friend_id,$arry_good_friend_info))$arry_good_friend_friend_info[]=$rs_user_id;//if(!in_array($rs_user_id,$arry_good_friend_friend_info))
                }
            }
            return $arry_good_friend_friend_info;
        }
        Protected function get_arry_good_friend_info($user_id,$conn_mssr,$arry_conn_mssr){
        //好友資訊

            $arry_good_friend_info=array();
            $user_id=(int)$user_id;
            $sql="
                SELECT
                    `mssr_forum`.`mssr_forum_friend`.`user_id`,
                    `mssr_forum`.`mssr_forum_friend`.`friend_id`
                FROM `mssr_forum`.`mssr_forum_friend`
                WHERE 1=1
                    AND (
                        `mssr_forum`.`mssr_forum_friend`.`user_id`  ={$user_id}
                            OR
                        `mssr_forum`.`mssr_forum_friend`.`friend_id`={$user_id}
                    )
                    AND `mssr_forum`.`mssr_forum_friend`.`friend_state` IN (1)
            ";
            $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(),$arry_conn_mssr);
            if(!empty($db_results)){
                foreach($db_results as $db_result){
                    $rs_user_id  =(int)$db_result['user_id'];
                    $rs_friend_id=(int)$db_result['friend_id'];
                    if($rs_user_id===$user_id)if(!in_array($rs_friend_id,$arry_good_friend_info))$arry_good_friend_info[]=$rs_friend_id;
                    if($rs_friend_id===$user_id)if(!in_array($rs_user_id,$arry_good_friend_info))$arry_good_friend_info[]=$rs_user_id;
                }
            }
            return $arry_good_friend_info;
        }
        Protected function verify_parameter($user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr){
        //參數檢驗

            if(!isset($user_id)){
                $err_msg='NO USER_ID';
                $this->err_report($err_msg);
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    $err_msg='USER_ID IS INVALID';
                    $this->err_report($err_msg);
                }
            }
            if(!isset($conn_user)||!isset($conn_mssr)||!isset($arry_conn_user)||!isset($arry_conn_mssr)){
                $err_msg='NO DB INFO';
                $this->err_report($err_msg);
            }

        }
        Protected function err_report($err_msg=NULL){
            echo '<p>'.'INC RIGHT_SIDE：'.$err_msg.'</p>';
            die();
        }


        //建構子
        function __construct($user_id=NULL,$sess_school_code=NULL,$conn_user=NULL,$conn_mssr=NULL,$arry_conn_user=NULL,$arry_conn_mssr=NULL){
            $this->verify_parameter($user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
            //global $sess_school_code;
            //$this->sess_school_code=trim($sess_school_code);
        }

        //解構子
        function __destruct(){
        }

        //自訂
        Protected function get_blacklist_group_school($school_code,$group_id,$conn_mssr,$arry_conn_mssr){
        //提取學校小組黑名單資訊

            $school_code=addslashes($school_code);
            $group_id   =(int)($group_id);
            $db_results=array();
            $sql="
                SELECT `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`
                FROM  `mssr_forum`.`mssr_forum_blacklist_group_school`
                WHERE 1=1
                    AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`school_code`= '{$school_code  }'
                    AND `mssr_forum`.`mssr_forum_blacklist_group_school`.`group_id`   =  {$group_id     }
                LIMIT 1
            ";
            $result=$conn_mssr->query($sql) or die('DB_RESULT:QUERY FAIL');
            if(($result->rowCount())!==0){
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $db_results[]=$arry_row;
                }
            }
            return $db_results;
        }
        Protected function db_result($conn_type='pdo',$conn='',$sql,$arry_limit=array(),$arry_conn){
        //---------------------------------------------------
        //取得資料筆數
        //---------------------------------------------------
        //$conn_type    資料庫連結類型      mysql | pdo     預設 mysql
        //$conn         資料庫連結物件
        //$sql          SQL查詢字串
        //$arry_limit   資料筆數限制陣列(等同LIMIT inx,size)
        //$arry_conn    資料庫資訊陣列
        //---------------------------------------------------

            //檢核參數
            if(!in_array(trim($conn_type),array('','mysql','pdo'))){
                $err='DB_RESULT:CONN_TYPE INVALID';
                die($err);
            }else{
                if($conn_type===''){
                    $conn_type='mysql';
                }
            }
            if(!$sql){
                $err='DB_RESULT:NO SQL';
                die($err);
            }
            if((!$arry_conn)||(empty($arry_conn))){
                $err='DB_RESULT:NO ARRY_CONN';
                die($err);
            }

            //資料庫資訊
            $db_host  =$arry_conn['db_host'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_name  =$arry_conn['db_name'];
            $db_encode=$arry_conn['db_encode'];

            switch($conn_type){
            //資料庫連結類型

                case 'pdo':
                //連結類型為pdo

                    //連結物件判斷
                    $has_conn=false;

                    if(!$conn){
                        $has_conn=true;

                        $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                        $options = array(
                            PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                            PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                        );

                        try{
                            $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                        }catch(PDOException $e){
                            $err ='DB_RESULT:CONNECT FAIL';
                            die($err);
                        }
                    }else{
                        $has_conn=false;
                    }

                    //SQL敘述
                    if(!empty($arry_limit)){
                       $a=$arry_limit[0];
                       $b=$arry_limit[1];
                       $sql.=" LIMIT {$a},{$b}";
                    }
                    //echo $sql;

                    //資料庫
                    $err='DB_RESULT:QUERY FAIL';
                    $result=$conn->query($sql) or
                    die($err);

                    //建立資料集陣列
                    $arry_result=array();

                    if(($result->rowCount())!==0){
                        while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                            $arry_result[]=$arry_row;
                        }
                    }

                    //傳回資料集陣列
                    return $arry_result;

                    //釋放資源
                    //mysql_free_result($result);
                    if($has_conn==true){
                        $conn=NULL;
                    }

                break;

                default:
                //例外處理

                    $err='DB_RESULT:CONN_TYPE INVALID';
                    die($err);

                break;
            }
        }
        Protected function get_group_info($conn='',$group_id,$array_filter=array(),$arry_conn){
        //---------------------------------------------------
        //函式: get_group_info()
        //用途: 提取小組資訊
        //---------------------------------------------------
        //$conn             資料庫連結物件
        //$group_id         小組主索引
        //$array_filter     欄位條件,   預設空陣列 => 全部撈取
        //$arry_conn        資料庫資訊陣列
        //---------------------------------------------------

            //檢核參數
            if(!isset($group_id)||(trim($group_id)==='')){
                $err='GET_GROUP_INFO:NO GROUP_ID';
                die($err);
            }else{
                $group_id=(int)$group_id;
                if($group_id===0){
                    $err='GET_GROUP_INFO:GROUP_ID IS INVAILD';
                    die($err);
                }
            }

            if(!is_array($array_filter)){
                $err='GET_GROUP_INFO:ARRAY_FILTER IS INVAILD';
                die($err);
            }else{
                $array_filter=array_map("trim",$array_filter);
                if(empty($array_filter)){
                    $array_filter=array();
                }else{
                    $array_filter=$array_filter;
                }
            }

            if((!$arry_conn)||(empty($arry_conn))){
                $err='GET_GROUP_INFO:NO ARRY_CONN';
                die($err);
            }

            //資料庫資訊
            $db_host  =$arry_conn['db_host'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_name  =$arry_conn['db_name'];
            $db_encode=$arry_conn['db_encode'];


            //連結物件判斷
            $has_conn=false;

            if(!$conn){
                $has_conn=true;

                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err ='GET_GROUP_INFO:CONNECT FAIL';
                    die($err);
                }
            }else{
                $has_conn=false;
            }

            //SQL敘述
            switch(empty($array_filter)){
                case true:
                    $sql="
                        SELECT
                            *
                        FROM `mssr_forum`.`mssr_forum_group`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$group_id}
                        LIMIT 1;
                    ";
                break;

                default:
                    $array_filter="`".implode("`,`",$array_filter)."`";
                    $sql="
                        SELECT
                            {$array_filter}
                        FROM `mssr_forum`.`mssr_forum_group`
                        WHERE 1=1
                            AND `mssr_forum`.`mssr_forum_group`.`group_id`={$group_id}
                        LIMIT 1;
                    ";
                break;
            }

            //資料庫
            $err='GET_GROUP_INFO:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }
            }

            //傳回資料集陣列
            return $arrys_result;

            if($has_conn==true){
                $conn=NULL;
            }
        }
        Protected function get_user_info($conn='',$user_id,$array_filter=array(),$arry_conn){
        //---------------------------------------------------
        //函式: get_user_info()
        //用途: 提取使用者資訊
        //---------------------------------------------------
        //$conn             資料庫連結物件
        //$user_id          使用者主索引
        //$array_filter     欄位條件,   預設空陣列 => 全部撈取
        //$arry_conn        資料庫資訊陣列
        //---------------------------------------------------

            //檢核參數
            if(!isset($user_id)||(trim($user_id)==='')){
                $err='GET_USER_INFO:NO USER_ID';
                die($err);
            }else{
                $user_id=(int)$user_id;
                if($user_id===0){
                    $err='GET_USER_INFO:USER_ID IS INVAILD';
                    die($err);
                }
            }

            if(!is_array($array_filter)){
                $err='GET_USER_INFO:ARRAY_FILTER IS INVAILD';
                die($err);
            }else{
                $array_filter=array_map("trim",$array_filter);
                if(empty($array_filter)){
                    $array_filter=array();
                }else{
                    $array_filter=$array_filter;
                }
            }

            if((!$arry_conn)||(empty($arry_conn))){
                $err='GET_USER_INFO:NO ARRY_CONN';
                die($err);
            }

            //資料庫資訊
            $db_host  =$arry_conn['db_host'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_name  =$arry_conn['db_name'];
            $db_encode=$arry_conn['db_encode'];


            //連結物件判斷
            $has_conn=false;

            if(!$conn){
                $has_conn=true;

                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err ='GET_USER_INFO:CONNECT FAIL';
                    die($err);
                }
            }else{
                $has_conn=false;
            }

            //SQL敘述
            switch(empty($array_filter)){
                case true:
                    $sql="
                        SELECT
                            *
                        FROM `member`
                        WHERE 1=1
                            AND `uid`={$user_id}
                        LIMIT 1;
                    ";
                break;

                default:
                    $array_filter="`".implode("`,`",$array_filter)."`";
                    $sql="
                        SELECT
                            {$array_filter}
                        FROM `member`
                        WHERE 1=1
                            AND `uid`={$user_id}
                        LIMIT 1;
                    ";
                break;
            }

            //資料庫
            $err='GET_USER_INFO:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }
            }

            //傳回資料集陣列
            return $arrys_result;

            if($has_conn==true){
                $conn=NULL;
            }
        }
        Protected function get_book_info($conn='',$book_sid,$array_filter=array(),$arry_conn){
        //---------------------------------------------------
        //函式: get_book_info()
        //用途: 提取書本資訊
        //---------------------------------------------------
        //$conn             資料庫連結物件
        //$book_sid         書籍識別碼
        //$array_filter     欄位條件,   預設空陣列 => 全部撈取
        //$arry_conn        資料庫資訊陣列
        //---------------------------------------------------

            //檢核參數
            if(!isset($book_sid)||(trim($book_sid)==='')){
                $err='GET_BOOK_INFO:NO BOOK_SID';
                die($err);
            }else{
                $book_sid=trim($book_sid);
                if(!preg_match("/^mbc|^mbl|^mbg|^mbu/i",$book_sid)){
                    $err='GET_BOOK_INFO:BOOK_SID IS INVAILD';
                    die($err);
                }else{
                    $book_sid=addslashes($book_sid);
                }
            }

            if(!is_array($array_filter)){
                $err='GET_BOOK_INFO:ARRAY_FILTER IS INVAILD';
                die($err);
            }else{
                $array_filter=array_map("trim",$array_filter);
                if(empty($array_filter)){
                    $array_filter=array();
                }else{
                    $array_filter=$array_filter;
                }
            }

            if((!$arry_conn)||(empty($arry_conn))){
                $err='GET_BOOK_INFO:NO ARRY_CONN';
                die($err);
            }

            //資料庫資訊
            $db_host  =$arry_conn['db_host'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_name  =$arry_conn['db_name'];
            $db_encode=$arry_conn['db_encode'];


            //連結物件判斷
            $has_conn=false;

            if(!$conn){
                $has_conn=true;

                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err ='GET_BOOK_INFO:CONNECT FAIL';
                    die($err);
                }
            }else{
                $has_conn=false;
            }


            //SQL敘述
            if(preg_match("/^mbc/i",$book_sid)){
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *,
                                '' AS `book_library_code`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter},
                                '' AS `book_library_code`
                            FROM `mssr_book_class`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;
                }
            }elseif(preg_match("/^mbl/i",$book_sid)){
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *
                            FROM `mssr_book_library`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter},
                                `book_library_code`
                            FROM `mssr_book_library`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;
                }
            }elseif(preg_match("/^mbg/i",$book_sid)){
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *,
                                '' AS `book_library_code`
                            FROM `mssr_book_global`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter},
                                '' AS `book_library_code`
                            FROM `mssr_book_global`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;
                }
            }elseif(preg_match("/^mbu/i",$book_sid)){
                switch(empty($array_filter)){
                    case true:
                        $sql="
                            SELECT
                                *,
                                '' AS `book_library_code`
                            FROM `mssr_book_unverified`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;

                    default:
                        $array_filter="`".implode("`,`",$array_filter)."`";
                        $sql="
                            SELECT
                                {$array_filter},
                                '' AS `book_library_code`
                            FROM `mssr_book_unverified`
                            WHERE 1=1
                                AND `book_sid`='{$book_sid}'
                            LIMIT 1;
                        ";
                    break;
                }
            }else{
                $err='GET_BOOK_INFO:BOOK_SID IS INVAILD';
                die($err);
            }

            //資料庫
            $err='GET_BOOK_INFO:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }
            }

            //傳回資料集陣列
            return $arrys_result;

            if($has_conn==true){
                $conn=NULL;
            }
        }
    }
?>