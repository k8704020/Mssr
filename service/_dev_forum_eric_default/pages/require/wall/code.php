<?php
//-------------------------------------------------------
//函式: wall()
//用途: 動態牆資訊
//-------------------------------------------------------

    function wall(){
    //---------------------------------------------------
    //函式: wall()
    //用途: 動態牆資訊
    //---------------------------------------------------
    //xxx
    //---------------------------------------------------

        //-----------------------------------------------
        //引用
        //-----------------------------------------------

            global $is_forum_friend;

            global $rs_href_1;
            global $rs_href_2;

            global $rs_content_1;
            global $rs_content_2;
            global $rs_content_3;

            global $rs_img_1;
            global $rs_user_img;

            global $rs_user_name;
            global $rs_article_content;
            global $rs_keyin_cdate;

            global $get_from;
            global $rs_article_id;
            global $rs_group_id;
            global $rs_user_id;

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            $rs_href_1          =trim($rs_href_1         );
            $rs_href_2          =trim($rs_href_2         );
            $rs_content_1       =trim($rs_content_1      );
            $rs_content_2       =trim($rs_content_2      );
            $rs_content_3       =trim($rs_content_3      );
            $rs_img_1           =trim($rs_img_1          );
            $rs_user_img        =trim($rs_user_img       );
            $rs_user_name       =trim($rs_user_name      );
            $rs_article_content =trim($rs_article_content);
            $rs_keyin_cdate     =trim($rs_keyin_cdate    );

            $get_from           =(int)$get_from;
            $rs_article_id      =(int)$rs_article_id;
            $rs_group_id        =(int)$rs_group_id;
            $rs_user_id         =(int)$rs_user_id;

            $html               ="";

        //-----------------------------------------------
        //html  內容
        //-----------------------------------------------

            if(!$is_forum_friend){
                $html.="
                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                        <div class='media' style='margin:10px 0;'>
                            <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                <img class='media-object hidden-xs' src='{$rs_user_img}' width='108' height='108' alt='Media'>
                                <img class='media-object visible-xs' src='{$rs_user_img}' width='72' height='72' alt='Media'>
                            </a>
                            <div class='media-body'>
                                <h4 class='media-heading hidden-xs' style='position:relative;left:0px;'>
                                    <span style='color:#324fe1;'><b>
                                        【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                        在 {$rs_content_1}
                                    </b></span>
                                    <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                </h4>
                                <h5 class='media-heading visible-xs' style='position:relative;left:0px;'>
                                    <span style='color:#324fe1;'><b>
                                        【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                        在 {$rs_content_1}
                                    </b></span>
                                    <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                </h5>
                                <p style='position:relative;top:5px;'>
                                    <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}' style='color:#5f5f5f;'>
                                        <b>{$rs_article_content}</b>
                                    </a>
                                    <div class='hidden-xs' style='border-bottom:1px dashed #e1e1e1;margin-top:20px;margin-bottom:20px;'></div>
                                    <a class='hidden-xs' href='{$rs_href_1}' >
                                        <div style='background-color:#e1e1e1;'>
                                            <img class='media-object' src='{$rs_img_1}' width='60' height='60' alt='Media' style='float:left;'>
                                            <div style='font-size:10pt;position:relative;margin-top:10px;left:5px;min-height:60px;color:#5f5f5f;'>
                                                {$rs_content_2}<br>
                                                {$rs_content_3}
                                            </div>
                                        </div>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                ";
            }else{
                $html.="
                    <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                        <div class='media' style='margin:10px 0;'>
                            <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                <img class='media-object hidden-xs' src='{$rs_user_img}' width='108' height='108' alt='Media'>
                                <img class='media-object visible-xs' src='{$rs_user_img}' width='72' height='72' alt='Media'>
                            </a>
                            <div class='media-body'>
                                <h4 class='media-heading hidden-xs' style='position:relative;left:0px;'>
                                    <span style='color:#324fe1;'><b>
                                        你的朋友【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                        在 {$rs_content_1}
                                    </b></span>
                                    <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                </h4>
                                <h5 class='media-heading visible-xs' style='position:relative;left:0px;'>
                                    <span style='color:#324fe1;'><b>
                                        【<a href='user.php?user_id={$rs_user_id}&tab=1' style='color:#324fe1;'>{$rs_user_name}</a>】
                                        在 {$rs_content_1}
                                    </b></span>
                                    <p style='font-size:10pt;' class='pull-right hidden-xs'>{$rs_keyin_cdate}</p>
                                </h5>
                                <p style='position:relative;top:5px;'>
                                    <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}' style='color:#5f5f5f;'>
                                        <b>{$rs_article_content}</b>
                                    </a>
                                    <div class='hidden-xs' style='border-bottom:1px dashed #e1e1e1;margin-top:20px;margin-bottom:20px;'></div>
                                    <a class='hidden-xs' href='{$rs_href_1}' >
                                        <div style='background-color:#e1e1e1;'>
                                            <img class='media-object' src='{$rs_img_1}' width='60' height='60' alt='Media' style='float:left;'>
                                            <div style='font-size:10pt;position:relative;margin-top:10px;left:5px;min-height:60px;color:#5f5f5f;'>
                                                {$rs_content_2}<br>
                                                {$rs_content_3}
                                            </div>
                                        </div>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                ";
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $html;
    }
?>
<?php
//if(!$is_forum_friend){
//    $html.="
//        <!-- 解析度 xs -->
//        <div class='col-xs-12 visible-xs'>
//            <div class='media' style='margin:10px 0;display:none;'>
//                <a class='pull-left' href='{$rs_href_1}'>
//                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                </a>
//                <div class='media-body'>
//                    <h4 class='media-heading' style='position:relative;left:0px'>
//                        『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
//                        <br>在 {$rs_content_1}
//                    </h4>
//                    <p style='position:relative;top:5px;'>
//                        <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
//                            {$rs_article_content}
//                        </a>
//                    </p>
//                </div>
//                <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
//            </div>
//        </div>
//
//        <!-- 解析度 其他 -->
//        <div class='media-col-other visible-md visible-lg'>
//            <div class='media media-other' style='margin:10px 0;display:none;'>
//                <a class='pull-left' href='{$rs_href_1}'>
//                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                </a>
//                <div class='media-body'>
//                    <h4 class='media-heading' style='position:relative;left:0px'>
//                        『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
//                        <br>在 {$rs_content_1}
//                    </h4>
//                    <p style='position:relative;top:5px;'>
//                        <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
//                            {$rs_article_content}
//                        </a>
//                    </p>
//                </div>
//                <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
//            </div>
//        </div>
//    ";
//}else{
//    //解析度 xs
//    $html.="
//        <div class='col-xs-12 visible-xs'>
//            <div class='media' style='margin:10px 0;display:none;'>
//    ";
//                if($rs_group_id===0):
//    $html.="
//                    <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
//                        <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
//                    </a>
//    ";
//                else:
//    $html.="
//                    <a class='pull-left' href='{$rs_href_2}'>
//                        <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                    </a>
//    ";
//                endif;
//    $html.="
//                <div class='media-body'>
//                    <h4 class='media-heading' style='position:relative;left:0px'>
//                        你的朋友『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
//                        <br>在 {$rs_content_1}
//                    </h4>
//                    <p style='position:relative;top:5px;'>
//                        <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
//                            {$rs_article_content}
//                        </a>
//                    </p>
//                        <div class='submedia'>
//    ";
//                            if($rs_group_id===0):
//    $html.="
//                                <a class='pull-left' href='{$rs_href_2}'>
//                                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                                </a>
//                                <div class='media-body'>
//                                    <h4 class='media-heading submedia-heading'>{$rs_content_2}</h4>
//                                    <p class='submedia-heading'>{$rs_content_3}</p>
//                                </div>
//    ";
//                            else:
//    $html.="
//                                <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
//                                    <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
//                                </a>
//                                <div class='media-body'>
//                                    <h4 class='media-heading submedia-heading'>
//                                        <a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}
//                                    </h4>
//                                    <p class='submedia-heading'></p>
//                                </div>
//    ";
//                            endif;
//    $html.="
//                        </div>
//                </div>
//                <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
//            </div>
//        </div>
//    ";
//
//    //解析度 其他
//    $html.="
//        <div class='media-col-other visible-md visible-lg'>
//            <div class='media media-other' style='margin:10px 0;display:none;'>
//    ";
//                if($rs_group_id===0):
//    $html.="
//                    <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
//                        <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
//                    </a>
//    ";
//                else:
//    $html.="
//                    <a class='pull-left' href='{$rs_href_2}'>
//                        <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                    </a>
//    ";
//                endif;
//    $html.="
//                <div class='media-body'>
//                    <h4 class='media-heading' style='position:relative;left:0px'>
//                        你的朋友『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
//                        <br>在 {$rs_content_1}
//                    </h4>
//                    <p style='position:relative;top:5px;'>
//                        <a target='_blank' href='reply.php?get_from={$get_from}&article_id={$rs_article_id}'>
//                            {$rs_article_content}
//                        </a>
//                    </p>
//                        <div class='submedia'>
//    ";
//                            if($rs_group_id===0):
//    $html.="
//                                <a class='pull-left' href='{$rs_href_2}'>
//                                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
//                                </a>
//                                <div class='media-body'>
//                                    <h4 class='media-heading submedia-heading'>{$rs_content_2}</h4>
//                                    <p class='submedia-heading'>{$rs_content_3}</p>
//                                </div>
//    ";
//                            else:
//    $html.="
//                                <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
//                                    <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
//                                </a>
//                                <div class='media-body'>
//                                    <h4 class='media-heading submedia-heading'>
//                                        <a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}
//                                    </h4>
//                                    <p class='submedia-heading'></p>
//                                </div>
//    ";
//                            endif;
//    $html.="
//                        </div>
//                </div>
//                <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
//            </div>
//        </div>
//    ";
//}
?>