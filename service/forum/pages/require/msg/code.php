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
        require_once(str_repeat("../",5).'config/config.php');

        //外掛頁面檔
        require_once(str_repeat("../",3).'pages/code.php');

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

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //連線物件
        //-----------------------------------------------

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        //-----------------------------------------------
        //邀請 SQL
        //-----------------------------------------------

            $request_results=array();
            $html='';
            if(isset($_SESSION['uid'])&&isset($_SESSION['mssr_forum'][0])&&!empty($_SESSION['mssr_forum'][0])){
                $request_results=get_request_info($sess_user_id,'','',$arry_conn_user,$arry_conn_mssr);
            }

        //-----------------------------------------------
        //資料內容
        //-----------------------------------------------

            if(!empty($request_results)){
                $html.="
                    <li role='presentation' style='width:100%;'>
                        <a role='menuitem' tabindex='-1' href='forum.php?method=add_group' style='color:#4e4e4e;'>

                        </a>
                    </li>
                ";
                foreach($request_results as $time=>$request_result):
                    foreach($request_result as $request_type=>$arry_request):
                        extract($arry_request, EXTR_PREFIX_ALL, "rs");
                        if(!in_array(trim($request_type),array('request_friend','article_get_like','article_get_reply','request_friend_success'))){
                            $rs_request_from_sex =(int)$rs_request_from_sex;
                            $rs_request_to_sex   =(int)$rs_request_to_sex;
                            $rs_request_from_name=trim($rs_request_from_name);
                            $rs_request_to_name  =trim($rs_request_to_name);
                            $rs_request_from     =(int)$rs_request_from;
                            $rs_request_to       =(int)$rs_request_to;
                            $rs_request_id       =(int)$rs_request_id;
                            $rs_request_state    =(int)$rs_request_state;
                            $rs_request_read     =(int)$rs_request_read;
                            $rs_keyin_cdate      =trim($rs_keyin_cdate);
                            $rs_rev_id           =(int)$rs_rev_id;

                            $rs_request_from_img ='../img/default/user_boy.png';
                            $rs_request_to_img   ='../img/default/user_boy.png';

                            if($rs_request_from!==$sess_user_id){
		                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg")){
		                            $rs_request_from_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg";
		                        }
		                    }
		                    if($rs_request_to!==$sess_user_id){
		                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg")){
		                            $rs_request_to_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg";
		                        }
		                    }

                            if($rs_request_from_sex===2)$rs_request_from_img ='../img/default/user_girl.png';
                            if($rs_request_to_sex===2)$rs_request_to_img ='../img/default/user_girl.png';
                        }

                if(trim($request_type)==='article_get_reply'):
                    $rs_request_from_name=trim($rs_request_from_name);
                    $rs_request_to_name  =trim($rs_request_to_name);
                    $rs_request_from     =(int)$rs_request_from;
                    $rs_request_to       =(int)$rs_request_to;
                    $rs_article_id       =(int)$rs_article_id;
                    $rs_article_title    =trim($rs_article_title);
                    $rs_request_from_img ='../img/default/user_boy.png';
                    $rs_request_to_img   ='../img/default/user_boy.png';

                    if((int)$rs_request_from_sex===2)$rs_request_from_img ='../img/default/user_girl.png';
                    if((int)$rs_request_to_sex===2)$rs_request_to_img ='../img/default/user_girl.png';

                    if($rs_request_from!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg")){
                            $rs_request_from_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg";
                        }
                    }
                    if($rs_request_to!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg")){
                            $rs_request_to_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg";
                        }
                    }

                    $rs_group_id=(int)$rs_group_id;

                    if($rs_group_id===0)$get_from=1;
                    if($rs_group_id!==0)$get_from=2;

                    $href_1="user.php?user_id={$rs_request_from}&tab=1";
                    $href_2="user.php?user_id={$rs_request_to}&tab=1";
                    $href_3="reply.php?get_from={$get_from}&article_id={$rs_article_id}";

                    $rs_content="
                        已回覆你的文章：
                        <a href='javascript:void(0);' onclick=\"navbar_href('{$href_3}');\">{$rs_article_title}</a>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='javascript:void(0);' onclick=\"navbar_href('{$href_1}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='article_get_like'):
                    $rs_request_from_name=trim($rs_request_from_name);
                    $rs_request_to_name  =trim($rs_request_to_name);
                    $rs_request_from     =(int)$rs_request_from;
                    $rs_request_to       =(int)$rs_request_to;
                    $rs_article_id       =(int)$rs_article_id;
                    $rs_article_title    =trim($rs_article_title);
                    $rs_request_from_img ='../img/default/user_boy.png';
                    $rs_request_to_img   ='../img/default/user_boy.png';

                    if((int)$rs_request_from_sex===2)$rs_request_from_img ='../img/default/user_girl.png';
                    if((int)$rs_request_to_sex===2)$rs_request_to_img ='../img/default/user_girl.png';

                    if($rs_request_from!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg")){
                            $rs_request_from_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_from}/forum/user_sticker/1.jpg";
                        }
                    }
                    if($rs_request_to!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg")){
                            $rs_request_to_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_request_to}/forum/user_sticker/1.jpg";
                        }
                    }

                    $rs_group_id=(int)$rs_group_id;

                    if($rs_group_id===0)$get_from=1;
                    if($rs_group_id!==0)$get_from=2;

                    $href_1="user.php?user_id={$rs_request_from}&tab=1";
                    $href_2="user.php?user_id={$rs_request_to}&tab=1";
                    $href_3="reply.php?get_from={$get_from}&article_id={$rs_article_id}";

                    $rs_content="
                        已對你的文章：
                        <a href='javascript:void(0);' onclick=\"navbar_href('{$href_3}');\">{$rs_article_title}</a>
                        按讚
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='javascript:void(0);' onclick=\"navbar_href('{$href_1}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='ok_request_rec_us_book_rev'):
                    $rs_book_sid=trim($arry_request['book_sid']);
                    if($rs_book_sid!==''){
                        $arry_book_infos=get_book_info('',$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                        if(empty($arry_book_infos))continue;
                        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                        if(mb_strlen($rs_book_name)>12){
                            $rs_book_name=mb_substr($rs_book_name,0,12)."..";
                        }
                    }else{continue;}
                    $href_1="user.php?user_id={$rs_request_from}&tab=1";
                    $href_2="user.php?user_id={$rs_request_to}&tab=1";
                    $rs_content      ="
                        已回應 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_from_name}</a> 的請求，
                        推薦一本書籍給 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_from_name}</a>。
                    ";
                    $rs_content.="
                        <h5 style='position:relative;top:-3px;'>
                            書名：<a href='article.php?get_from=1&book_sid={$rs_book_sid}'>【{$rs_book_name}】</a>
                        </h5>
                    ";
                    $rs_content.="
                        <h5>
                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-6px;left:55px;'
                            request_id='{$rs_request_id}' onclick='quick_edit_ok_request_rec_us_book(this);void(0);'>確定</button>
                    ";
                    $rs_content.="
                        </h5>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:90px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_request_to_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_rec_us_book_rev'):
                    $href_1="user.php?user_id={$rs_request_to}&tab=1";
                    $href_2="user.php?user_id={$rs_request_from}&tab=1";
                    $rs_content="
                        向 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 提出邀請，
                        希望 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 能推薦一本書籍給{$rs_request_from_name}
                    ";
                    $rs_content.="
                        <h5><button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                        onclick='location.href=
                    ";
                    $rs_content.='"user.php?user_id=';
                    $rs_content.="{$sess_user_id}";
                    $rs_content.='&tab=6"';
                    $rs_content.="
                        ;'
                        >前往回應</button></h5>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_article_rev'):
                    $rs_group_id     =(int)$rs_group_id;
                    $rs_article_id   =(int)$rs_article_id;
                    $rs_article_title=trim($rs_article_title);
                    $href_1="user.php?user_id={$rs_request_to}&tab=1";
                    $href_2="user.php?user_id={$rs_request_from}&tab=1";
                    $href_3="reply.php?get_from=1&article_id={$rs_article_id}";
                    $href_4="reply.php?get_from=2&article_id={$rs_article_id}";
                    $rs_content      ="
                        向 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 提出邀請，
                        希望 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 能一起參與討論文章：
                    ";
                    if($rs_group_id===0){
                        $rs_content.="
                        	<a target='_blank' href='#' onclick=\"navbar_href('{$href_3}');\">
                        		{$rs_article_title}
                        	</a>
	                        <h5>
	                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;' request_id='{$rs_request_id}' url='{$href_3}' onclick='quick_request_article_rev(this);void(0);'>
	                            	立即前往討論
	                            </button>
	                        </h5>
	                    ";
                    }
                    if($rs_group_id!==0){
                        $rs_content.="
                        	<a target='_blank' href='#' onclick=\"navbar_href('{$href_4}');\">
                        		{$rs_article_title}
                        	</a>
                        	<h5>
	                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;' request_id='{$rs_request_id}' url='{$href_4}' onclick='quick_request_article_rev(this);void(0);'>
	                            	立即前往討論
	                            </button>
	                        </h5>
                        ";
                    }
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_create_group_rev'):
                    $rs_group_id  =(int)$rs_group_id;
                    $rs_group_name=trim($rs_group_name);
                    $href_1="user.php?user_id={$rs_request_to}&tab=1";
                    $href_2="article.php?get_from=2&group_id={$rs_group_id}";
                    $href_3="user.php?user_id={$rs_request_from}&tab=1";
                    $rs_content ="
                        向 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 提出邀請，
                        希望 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 能一同聯署建立小組：
                        <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_group_name}</a>
                    ";
                    $rs_content.="
                        <h5><button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                        onclick='location.href=
                    ";
                    $rs_content.='"user.php?user_id=';
                    $rs_content.="{$sess_user_id}";
                    $rs_content.='&tab=6"';
                    $rs_content.="
                        ;'
                        >前往回應</button></h5>
                    ";
                    $rs_content.="
                        </h5>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_3}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_join_to_group_rev'):
                    $rs_group_id  =(int)$rs_group_id;
                    $rs_group_name=trim($rs_group_name);
                    $href_1="user.php?user_id={$rs_request_to}&tab=1";
                    $href_2="article.php?get_from=2&group_id={$rs_group_id}";
                    $href_3="user.php?user_id={$rs_request_from}&tab=1";
                    $rs_content ="
                        向 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 提出申請，
                        希望能加入你的小組：
                        <a target='_blank' href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_group_name}</a>
                    ";
                    $rs_content.="
                        <h5>
                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                            request_id='{$rs_request_id}' onclick='quick_request_join_to_group(this,1);void(0);'>允許
                            </button>
                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                            request_id='{$rs_request_id}' onclick='quick_request_join_to_group(this,2);void(0);'>拒絕
                            </button>
                    ";
                    $rs_content.="
                        </h5>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_3}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_join_us_group_rev'):
                    $rs_group_id  =(int)$rs_group_id;
                    $rs_group_name=trim($rs_group_name);
                    $href_1="user.php?user_id={$rs_request_to}&tab=1";
                    $href_2="article.php?get_from=2&group_id={$rs_group_id}";
                    $href_3="user.php?user_id={$rs_request_from}&tab=1";
                    $rs_content ="
                        向 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 提出邀請，
                        希望 <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_request_to_name}</a> 能加入他的小組：
                        <a target='_blank' href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_group_name}</a>
                    ";
                    $rs_content.="
                        <h5>
                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                            request_id='{$rs_request_id}' onclick='quick_request_join_us_group(this,1);void(0);'>接受
                            </button>
                            <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                            request_id='{$rs_request_id}' onclick='quick_request_join_us_group(this,2);void(0);'>拒絕
                            </button>
                    ";
                    $rs_content.="
                        </h5>
                    ";
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <img src='{$rs_request_from_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    <a href='#' onclick=\"navbar_href('{$href_3}');\">{$rs_request_from_name}</a>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_friend'):
                    $rs_user_name       =trim($rs_user_name);
                    $rs_friend_name     =trim($rs_friend_name);
                    $rs_create_by       =(int)$rs_create_by;
                    $rs_user_id         =(int)$rs_user_id;
                    $rs_friend_id       =(int)$rs_friend_id;
                    $rs_friend_content  =trim($rs_content);
                    $rs_friend_state    =(int)$rs_friend_state;
                    $rs_keyin_mdate     =trim($rs_keyin_mdate);

                    if($rs_friend_state===1){
                        $rs_friend_state_html='成功';
                    }elseif($rs_friend_state===2){
                        $rs_friend_state_html='失敗';
                    }

                    $rs_user_img    ='../img/default/user_boy.png';
                    $rs_friend_img  ='../img/default/user_boy.png';

                    if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';
                    if($rs_friend_sex===2)$rs_friend_img ='../img/default/user_girl.png';

                    if($rs_user_id!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                        }
                    }
                    if($rs_friend_id!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg")){
                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg";
                        }
                    }

                    $href_1="user.php?user_id={$rs_user_id}&tab=1";
                    $href_2="user.php?user_id={$rs_friend_id}&tab=1";

                    if($rs_friend_state===3){
                        $rs_content ="
                            <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_user_name}</a>
                            已經提出要與
                            <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_friend_name}</a>
                            成為書友，
                            請問你是否要跟他成為書友?
                        ";
                        // $rs_content.="
                        //     <h5>
                        //         <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                        //         create_by='{$rs_create_by}'
                        //         user_id='{$rs_user_id}'
                        //         friend_id='{$rs_friend_id}'
                        //         onclick='quick_request_friend(this,1);void(0);'>接受
                        //         </button>
                        //         <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                        //         create_by='{$rs_create_by}'
                        //         user_id='{$rs_user_id}'
                        //         friend_id='{$rs_friend_id}'
                        //         onclick='quick_request_friend(this,2);void(0);'>拒絕
                        //         </button>
                        // ";
                        // if(trim($rs_friend_content)!==''){
                        //     $rs_content.="
                        //         <button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
                        //         onclick='location.href=
                        //     ";
                        //     $rs_content.='"user.php?user_id=';
                        //     $rs_content.="{$sess_user_id}";
                        //     $rs_content.='&tab=6"';
                        //     $rs_content.="
                        //         ;'
                        //         >觀看留言</button>
                        //     ";
                        // }
                        // $rs_content.="
                        //     </h5>
                        // ";
                        $rs_content.="
	                        <h5><button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
	                        onclick='location.href=
	                    ";
	                    $rs_content.='"user.php?user_id=';
	                    $rs_content.="{$sess_user_id}";
	                    $rs_content.='&tab=6"';
	                    $rs_content.="
	                        ;'
	                        >前往回應</button></h5>
	                    ";
                    }else{
                        $rs_content ="
                            <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_user_name}</a>
                            提出與
                            <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_friend_name}</a>
                            的
                            交友申請結果為 : {$rs_friend_state_html}
                        ";
                    }
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <!-- <img src='{$rs_user_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/> -->
                                <img src='{$rs_friend_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

                if(trim($request_type)==='request_friend_success'):
                    $rs_user_name       =trim($rs_user_name);
                    $rs_friend_name     =trim($rs_friend_name);
                    $rs_create_by       =(int)$rs_create_by;
                    $rs_user_id         =(int)$rs_user_id;
                    $rs_friend_id       =(int)$rs_friend_id;
                    $rs_friend_content  =trim($rs_content);
                    $rs_friend_state    =(int)$rs_friend_state;
                    $rs_message_state   =(int)$rs_message_state;
                    $rs_keyin_mdate     =trim($rs_keyin_mdate);

                    $rs_user_img    ='../img/default/user_boy.png';
                    $rs_friend_img  ='../img/default/user_boy.png';

                    if($rs_user_sex===2)$rs_user_img ='../img/default/user_girl.png';
                    if($rs_friend_sex===2)$rs_friend_img ='../img/default/user_girl.png';

                    if($rs_user_id!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg")){
                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_user_id}/forum/user_sticker/1.jpg";
                        }
                    }
                    if($rs_friend_id!==$sess_user_id){
                        if(@getimagesize("http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg")){
                            $rs_friend_img="http://".$arry_ftp1_info['host']."/mssr/info/user/{$rs_friend_id}/forum/user_sticker/1.jpg";
                        }
                    }

                    $href_1="user.php?user_id={$rs_user_id}&tab=1";
                    $href_2="user.php?user_id={$rs_friend_id}&tab=1";

                    if ($rs_friend_state === 1 && $rs_message_state === 2) {
                        $rs_content ="
                            <a href='#' onclick=\"navbar_href('{$href_1}');\">{$rs_user_name}</a>
                            成功與
                            <a href='#' onclick=\"navbar_href('{$href_2}');\">{$rs_friend_name}</a>
                            成為書友了
                        ";
                        $rs_content.="
	                        <h5><button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;' 
	                        	create_by='{$rs_create_by}'
								user_id='{$rs_user_id}'
								friend_id='{$rs_friend_id}'
								onclick='quick_request_friend(this,3);void(0);'>
								確定
							</button></h5>
	                    ";
                    }
                    $html.="
                        <li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
                            <div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
                                <!-- <img src='{$rs_user_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/> -->
                                <img src='{$rs_friend_img}' width='55px' height='55px' border='0' alt='0'
                                style='float:left;position:relative;left:-3px;top:7px;'
                                onmouseover=''/>
                                <a role='menuitem' tabindex='-1' href='javascript:void(0);'
                                style='color:#4e4e4e;text-decoration:none;'
                                onmouseover=''>
                                    {$rs_content}
                                </a>
                            </div>
                        </li>
                    ";
                endif;

				if (trim($request_type) === 'report_message'):
					$rs_content = "
						你收到來自聊書系統的通知
					";
					$rs_content .= "
						<h5><button type='button' class='btn btn-default btn-xs' style='position:relative;top:-3px;'
						onclick='location.href=
					";
					$rs_content .= '"user.php?user_id=';
					$rs_content .= "{$sess_user_id}";
					$rs_content .= '&tab=6"';
					$rs_content .= "
						;'
						>前往查看</button></h5>
					";
					$html .= "
						<li role='presentation' style='width:100%;height:70px;background-color:#fdfdff;border-bottom:1px solid #ebebeb;'>
							<div style='width:100%;word-break:break-all;padding:0px 5px 0px 5px;'>
								<img src='../img/default/user_boy.png' width='55px' height='55px' border='0' alt='0'
								style='float:left;position:relative;left:-3px;top:7px;'
								onmouseover=''/>
								<div>
									{$rs_content}
								</div>
							</div>
						</li>
					";
				endif;

            endforeach;endforeach;}

            //echo "<Pre>";
            print_r($html);
            //echo "</Pre>";
            die();
?>