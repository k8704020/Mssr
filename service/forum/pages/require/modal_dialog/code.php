<?php
//-------------------------------------------------------
//函式: modal_dialog()
//用途: 模態框
//日期: 2015年4月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function modal_dialog($rd=0,$type=1){
    //---------------------------------------------------
    //函式: modal_dialog()
    //用途: 模態框
    //---------------------------------------------------
    //$rd       層級指標,預設0,表示在目前目錄下
    //$type     模態框類型
    //
    //              modal_jumbotron_note        書本內容簡介
    //              modal_request_article       邀請書友一同討論文章
    //              modal_add_friend            申請成為書友
    //
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($rd)||(int)$rd===0){
                $rd='';
            }else{
                $rd=str_repeat('../',$rd);
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            //-------------------------------------------
            //html  內容
            //-------------------------------------------

                $html="";

                switch($type){

                    case 3:
                    //modal_add_friend          申請成為書友

                        global $_GET;
                        global $arrys_sess_login_info;
                        global $conn_mssr;
                        global $arry_conn_mssr;

                        $sess_user_id=(isset($arrys_sess_login_info[0]['uid']))?(int)$arrys_sess_login_info[0]['uid']:0;
                        if($sess_user_id===0)die('您沒有權限進入，請洽詢明日星球團隊人員!');

                        $html.="
                            <div class='modal fade' id='modal_dialog_add_friend' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header' style='background-color:#428bca;color:#ffffff;'>
                                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                            <h4 class='modal-title' id='myModalLabel'><span id='add_friend_title''>邀請當你的書友</span></h4>
                                        </div>
                                        <div class='modal-body'>
                                            <textarea class='form-control add_friend_content' id='add_friend_content' name='add_friend_content' rows='2' placeholder='跟書友說說話吧!'
                                            maxlength='100'
                                            style='margin-top:-5px;margin-bottom:-20px;'></textarea>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-default' data-dismiss='modal'>關閉</button>
                                            <button type='button' class='btn_add_friend btn btn-default'
                                            user_id={$sess_user_id}
                                            >加為書友</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ";

                    break;

                    case 1:
                    //modal_jumbotron_note      書本內容簡介

                        global $_GET;
                        global $arrys_sess_login_info;
                        global $conn_mssr;
                        global $arry_conn_mssr;

                        $get_book_sid   =(isset($_GET['book_sid']))?trim($_GET['book_sid']):'';
                        $get_article_id =(isset($_GET['article_id']))?(int)$_GET['article_id']:0;

                        if($get_book_sid!==''&&$get_article_id===0){
                            $get_book_sid=trim($get_book_sid);
                            $arry_book_infos=get_book_info($conn_mssr,$get_book_sid,$array_filter=array('book_note'),$arry_conn_mssr);
                            $book_note='暫無簡介';
                            if(trim($arry_book_infos[0]['book_note'])!=='')$book_note=trim($arry_book_infos[0]['book_note']);
                        }

                        if($get_article_id!==0&&$get_book_sid===''){
                            $sql="
                                SELECT
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`book_sid`
                                FROM `mssr_forum`.`mssr_forum_article_book_rev`
                                    INNER JOIN `mssr_forum`.`mssr_forum_article` ON
                                    `mssr_forum`.`mssr_forum_article_book_rev`.`article_id`=`mssr_forum`.`mssr_forum_article`.`article_id`
                                WHERE 1=1
                                    AND `mssr_forum`.`mssr_forum_article`.`article_id`   ={$get_article_id}
                            ";
                            $article_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                            if(empty($article_results)){die();
                            }else{
                                $get_book_sid=trim($article_results[0]['book_sid']);
                                $arry_book_infos=get_book_info($conn_mssr,$get_book_sid,$array_filter=array('book_note'),$arry_conn_mssr);
                                $book_note='暫無簡介';
                                if(trim($arry_book_infos[0]['book_note'])!=='')$book_note=trim($arry_book_infos[0]['book_note']);
                            }
                        }

                        $book_note.="......";

                        $html.="
                            <div class='bs-example-modal-sm modal fade' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>
                                <div class='modal-dialog modal-md'>
                                    <div class='modal_jumbotron_note modal-content'>
                                        <span>
                                            <span class='jumbotron_note_title'>內容簡介</span><hr></hr>

                                            {$book_note}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ";

                    break;

                    case 2:
                    //modal_request_article     邀請書友一同討論文章

                        global $_GET;
                        global $arrys_sess_login_info;
                        global $conn_mssr;
                        global $arry_conn_mssr;

                        //提取聊書書友資訊
                        $get_article_id =(isset($_GET['article_id']))?(int)$_GET['article_id']:0;
                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_article`.`group_id`
                            FROM `mssr_forum`.`mssr_forum_article`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_article`.`article_id`={$get_article_id}
                        ";
                        $group_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                        if(!empty($group_results)){
                            $get_group_id =(int)$group_results[0]['group_id'];
                        }else{die();}
                        if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                        $friend_borrow_cno =0;
                        $arry_forum_friend =array();
                        $arry_forum_friends=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
                        if(!empty($arry_forum_friends)){
                            foreach($arry_forum_friends as $arry_val){
                                if((int)$arry_val['friend_state']===1){
                                    if((int)$arry_val['user_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['user_id'];
                                    if((int)$arry_val['friend_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['friend_id'];
                                }
                            }
                        }
                        $send_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

                        $html.="
                            <div class='modal_request_article modal fade' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
                                <div class='modal-dialog modal-lg'>
                                    <div class='modal-content' style='position:relative;padding:15px;'>
                                        <form id='Form1' name='Form1' method='post' onsubmit='return false;'>
                                            <h4 style='color:#ec6c01;font-weight:bold;'>讓我們開始邀請書友來討論...</h4>
                                            <hr></hr>
                                            <div class='input-group request_article_group' style='position:relative;margin:25px 0;'>
                                                <input type='text' class='form-control request_article_friend_name' name='request_article_friend_name[]' placeholder='請選擇或輸入書友名稱來討論'>
                                                <div class='input-group-btn request_article_group_btn'>
                                                    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                                                        選擇書友 <span class='caret'></span>
                                                    </button>
                                                    <ul class='dropdown-menu' role='menu'>

                                                <!-- <div class='form-group'>
                                                    <input type='text' class='form-control request_article_friend_name' name='request_article_friend_name' placeholder='請輸入書友名稱'>
                                                </div>
                                                <div class='row' style='padding:0 15px 0 15px;'> -->
                        ";
                                                    if(!empty($arry_forum_friend)){
                                                        foreach($arry_forum_friend as $arry_val):
                                                            $rs_friend_id=(int)$arry_val;
                                                            $sql="
                                                                SELECT `name`
                                                                FROM `user`.`member`
                                                                WHERE 1=1
                                                                    AND `user`.`member`.`uid`={$rs_friend_id}
                                                            ";
                                                            $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                            if(!empty($db_results)){
                                                                $rs_user_name=htmlspecialchars(trim($db_results[0]['name']));
                                                            }else{continue;}
                                                        $html.="<li><a href='javascript:void(0);' onclick='auto(this,0);void(0);'>{$rs_user_name}</a></li>";
                                                    endforeach;}

                                                    //if(!empty($arry_forum_friend)){
                                                    //    foreach($arry_forum_friend as $arry_val):
                                                    //        $rs_friend_id=(int)$arry_val;
                                                    //        $sql="
                                                    //            SELECT `name`
                                                    //            FROM `user`.`member`
                                                    //            WHERE 1=1
                                                    //                AND `user`.`member`.`uid`={$rs_friend_id}
                                                    //        ";
                                                    //        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                                                    //        if(!empty($db_results)){
                                                    //            $rs_user_name=htmlspecialchars(trim($db_results[0]['name']));
                                                    //        }else{continue;}
                                                    //    $html.="
                                                    //        <div class='col-xs-3 col-sm-2 col-md-2 col-lg-2 form-group text-center div_request_article_{$rs_friend_id}'
                                                    //        style='border:1px solid #e1e1e1;font-size:16px;'>
                                                    //            <input type='checkbox' name='request_article_friend_id[]' value='{$rs_friend_id}'
                                                    //            class='chk_request_article_{$rs_friend_id} request_article_friend_id'
                                                    //            onclick='chk_request_article(this,{$rs_friend_id});void(0);'>
                                                    //            <span style='position:relative;top:-2px;'>{$rs_user_name}</span>
                                                    //        </div>
                                                    //    ";
                                                    //endforeach;}
                        $html.="
                                                <!-- </div> -->

                                                    </ul>
                                                </div>
                                            </div>
                                            <div style='position:relative;' class='text-center'>
                                                <button type='button' class='btn btn-default btn-xs' onclick='clone_request_article_tag();'>十</button>
                                                <button type='button' class='btn btn-default btn-xs' onclick='del_request_article_tag();'>一</button>
                                            </div>

                                            <hr></hr>
                                            <div class='form-group text-right' style='position:relative;margin:5px 0;'>
                                                <button id='Btn_add_request_article' type='button' class='btn btn-primary'>送出</button>
                                                <button type='button' class='btn btn-default' data-dismiss='modal'>取消</button>
                                            </div>

                                            <div class='form-group hidden'>
                                                <!-- <input type='hidden' class='form-control' name='request_article_friend_id' value='0' id='request_article_friend_id'> -->
                                                <input type='hidden' class='form-control' name='article_id' value='{$get_article_id}'>
                                                <input type='hidden' class='form-control' name='group_id' value='{$get_group_id}'>
                                                <input type='hidden' class='form-control' name='method' value='add_request_article'>
                                                <input type='hidden' class='form-control' name='send_url' value='{$send_url}'>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        ";

                    break;

                    case 3:
                        $html.="

                        ";
                    break;

					case 4:
						global $_GET;
						global $arrys_sess_login_info;
						global $conn_mssr;
						global $arry_conn_mssr;

						if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
						$arry_forum_friend =array();
						$arry_forum_friends=get_forum_friend($sess_user_id,$friend_id=0,$arry_conn_mssr);
						if(!empty($arry_forum_friends)){
							foreach($arry_forum_friends as $arry_val){
								if((int)$arry_val['friend_state']===1){
									if((int)$arry_val['user_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['user_id'];
									if((int)$arry_val['friend_id']!==$sess_user_id)$arry_forum_friend[]=$arry_val['friend_id'];
								}
							}
						}

					    $send_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

					    $html.="
							<div class='modal_request_book modal fade' id='request_book' tabindex='-1' role='dialog' aria-labelledby='request_book_label' aria-hidden='true'>
							    <div class='modal-dialog'>
							        <div class='modal-content'>
								        <form id='Form2' name='Form2' method='post' onsubmit='return false;'>
								            <div class='modal-header'>
								                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
								                <h4 class='modal-title' id='request_book_label'>請求書友推薦一本書籍</h4>
								            </div>
								            <div class='modal-body'>
									            <div class='input-group request_book_group' style='position:relative;margin:25px 0;'>
									                <input type='text' class='form-control request_book_friend_name' name='request_book_friend_name[]' placeholder='請選擇或輸入一位書友來請求推薦一本書籍給你'>
									                <div class='input-group-btn'>
									                    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
									                        選擇書友 <span class='caret'></span>
									                    </button>
									                 	<ul class='dropdown-menu dropdown-menu-right' role='menu'>";
									                 		if(!empty($arry_forum_friend)){
								                                foreach($arry_forum_friend as $arry_val):
								                                    $rs_friend_id=(int)$arry_val;
								                                    $sql="
								                                        SELECT `name`
								                                        FROM `user`.`member`
								                                        WHERE 1=1
								                                            AND `user`.`member`.`uid`={$rs_friend_id}
								                                    ";
								                                    $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
								                                    if(!empty($db_results)){
								                                        $rs_user_name=htmlspecialchars(trim($db_results[0]['name']));
								                                    }else{continue;}
								                                $html.="<li><a href='javascript:void(0);' onclick='auto_user(this,0);void(0);'>{$rs_user_name}</a></li>";
								                            endforeach;}
						$html.="
														</ul>
													</div>
												</div>
                                                <div style='position:relative;' class='text-center'>
                                                    <button type='button' class='btn btn-default btn-xs' onclick='clone_request_book_tag();'>十</button>
                                                    <button type='button' class='btn btn-default btn-xs' onclick='del_request_book_tag();'>一</button>
                                                </div>
											</div>
											<div style='position:relative; padding-right: 25px;' class='text-right'>
												<button type='button' class='btn btn-default' onclick='request_book_all_friends();'>一次邀請全部書友</button>
											</div>
											<div class='modal-footer'>
												<button type='button' class='btn btn-default' data-dismiss='modal'>取消</button>
												<button id='Btn_add_request_book' type='button' class='btn btn-primary'>請求</button>
											</div>
											<div class='form-group hidden'>
												<input type='hidden' class='form-control' name='method' value='add_request_rec_us_book'>
												<input type='hidden' class='form-control' name='send_url' value='{$send_url}'>
											</div>
										</form>
									</div>
								</div>
							</div>
                        ";
                    break;

                }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(1==2){ //除錯用
                echo '<pre>';
                print_r($html);
                echo '</pre>';
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $html;
    }
?>