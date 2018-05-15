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
                    <!-- 解析度 xs -->
                    <div class='col-xs-12 visible-xs'>
                        <div class='media' style='margin:10px 0;display:none;'>
                            <a class='pull-left' href='{$rs_href_1}'>
                                <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                            </a>
                            <div class='media-body'>
                                <h4 class='media-heading' style='position:relative;left:0px'>
                                    『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
                                    <br>在 {$rs_content_1}
                                </h4>
                                <p style='position:relative;top:5px;'>
                                    {$rs_article_content}
                                </p>
                            </div>
                            <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
                        </div>
                    </div>

                    <!-- 解析度 其他 -->
                    <div class='media-col-other visible-md visible-lg'>
                        <div class='media media-other' style='margin:10px 0;display:none;'>
                            <a class='pull-left' href='{$rs_href_1}'>
                                <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                            </a>
                            <div class='media-body'>
                                <h4 class='media-heading' style='position:relative;left:0px'>
                                    『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
                                    <br>在 {$rs_content_1}
                                </h4>
                                <p style='position:relative;top:5px;'>
                                    {$rs_article_content}
                                </p>
                            </div>
                            <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
                        </div>
                    </div>
                ";
            }else{
                //解析度 xs
                $html.="
                    <div class='col-xs-12 visible-xs'>
                        <div class='media' style='margin:10px 0;display:none;'>
                ";
                            if($rs_group_id===0):
                $html.="
                                <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                    <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
                                </a>
                ";
                            else:
                $html.="
                                <a class='pull-left' href='{$rs_href_2}'>
                                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                                </a>
                ";
                            endif;
                $html.="
                            <div class='media-body'>
                                <h4 class='media-heading' style='position:relative;left:0px'>
                                    你的朋友『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
                                    <br>在 {$rs_content_1}
                                </h4>
                                <p style='position:relative;top:5px;'>
                                    {$rs_article_content}
                                </p>
                                    <div class='submedia'>
                ";
                                        if($rs_group_id===0):
                $html.="
                                            <a class='pull-left' href='{$rs_href_2}'>
                                                <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                                            </a>
                                            <div class='media-body'>
                                                <h4 class='media-heading submedia-heading'>{$rs_content_2}</h4>
                                                <p class='submedia-heading'>{$rs_content_3}</p>
                                            </div>
                ";
                                        else:
                $html.="
                                            <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                                <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
                                            </a>
                                            <div class='media-body'>
                                                <h4 class='media-heading submedia-heading'>
                                                    <a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}
                                                </h4>
                                                <p class='submedia-heading'></p>
                                            </div>
                ";
                                        endif;
                $html.="
                                    </div>
                            </div>
                            <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
                        </div>
                    </div>
                ";

                //解析度 其他
                $html.="
                    <div class='media-col-other visible-md visible-lg'>
                        <div class='media media-other' style='margin:10px 0;display:none;'>
                ";
                            if($rs_group_id===0):
                $html.="
                                <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                    <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
                                </a>
                ";
                            else:
                $html.="
                                <a class='pull-left' href='{$rs_href_2}'>
                                    <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                                </a>
                ";
                            endif;
                $html.="
                            <div class='media-body'>
                                <h4 class='media-heading' style='position:relative;left:0px'>
                                    你的朋友『<a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}</a>』
                                    <br>在 {$rs_content_1}
                                </h4>
                                <p style='position:relative;top:5px;'>
                                    {$rs_article_content}
                                </p>
                                    <div class='submedia'>
                ";
                                        if($rs_group_id===0):
                $html.="
                                            <a class='pull-left' href='{$rs_href_2}'>
                                                <img class='media-object' src='{$rs_img_1}' width='64' height='64' alt='Media'>
                                            </a>
                                            <div class='media-body'>
                                                <h4 class='media-heading submedia-heading'>{$rs_content_2}</h4>
                                                <p class='submedia-heading'>{$rs_content_3}</p>
                                            </div>
                ";
                                        else:
                $html.="
                                            <a class='pull-left' href='user.php?user_id={$rs_user_id}&tab=1'>
                                                <img class='media-object' src='{$rs_user_img}' width='64' height='64' alt='Media'>
                                            </a>
                                            <div class='media-body'>
                                                <h4 class='media-heading submedia-heading'>
                                                    <a href='user.php?user_id={$rs_user_id}&tab=1'>{$rs_user_name}
                                                </h4>
                                                <p class='submedia-heading'></p>
                                            </div>
                ";
                                        endif;
                $html.="
                                    </div>
                            </div>
                            <p style='position:relative;top:5px;' class='pull-right'>{$rs_keyin_cdate}</p>
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

<?php //if(!$is_forum_friend):?>
    <!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="media" style="margin:10px 0;display:none;">
            <a class="pull-left" href="<?php echo $rs_href_1;?>">
                <img class="media-object" src="<?php echo htmlspecialchars($rs_img_1);?>" width="64" height="64" alt="Media">
            </a>
            <div class="media-body">
                <h4 class="media-heading" style="position:relative;left:0px">
                    『<a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?></a>』
                    <br>在 <?php echo ($rs_content_1);?>
                </h4>
                <p style="position:relative;top:5px;">
                    <a target="_blank" href="reply.php?get_from=<?php echo (int)$get_from;?>&article_id=<?php echo (int)$rs_article_id;?>">
                        <?php echo htmlspecialchars($rs_article_content);?>
                    </a>
                </p>
            </div>
            <p style="position:relative;top:5px;" class='pull-right'><?php echo htmlspecialchars($rs_keyin_cdate);?></p>
        </div>
    </div> -->
<?php //else:?>
    <!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="media" style="margin:10px 0;display:none;">
            <?php if($rs_group_id===0):?>
                <a class="pull-left" href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                    <img class="media-object" src="<?php echo $rs_user_img;?>" width="64" height="64" alt="Media">
                </a>
            <?php else:?>
                <a class="pull-left" href="<?php echo $rs_href_2;?>">
                    <img class="media-object" src="<?php echo htmlspecialchars($rs_img_1);?>" width="64" height="64" alt="Media">
                </a>
            <?php endif;?>
            <div class="media-body">
                <h4 class="media-heading" style="position:relative;left:0px">
                    你的朋友『<a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?></a>』
                    <br>在 <?php echo ($rs_content_1);?>
                </h4>
                <p style="position:relative;top:5px;">
                    <a target="_blank" href="reply.php?get_from=<?php echo (int)$get_from;?>&article_id=<?php echo (int)$rs_article_id;?>">
                        <?php echo htmlspecialchars($rs_article_content);?>
                    </a>
                </p>
                    <div class="submedia">
                        <?php if($rs_group_id===0):?>
                            <a class="pull-left" href="<?php echo $rs_href_2;?>">
                                <img class="media-object" src="<?php echo htmlspecialchars($rs_img_1);?>" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading submedia-heading"><?php echo $rs_content_2;?></h4>
                                <p class="submedia-heading"><?php echo $rs_content_3;?></p>
                            </div>
                        <?php else:?>
                            <a class="pull-left" href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1">
                                <img class="media-object" src="<?php echo $rs_user_img;?>" width="64" height="64" alt="Media">
                            </a>
                            <div class="media-body">
                                <h4 class="media-heading submedia-heading">
                                    <a href="user.php?user_id=<?php echo $rs_user_id;?>&tab=1"><?php echo htmlspecialchars($rs_user_name);?>
                                </h4>
                                <p class="submedia-heading"></p>
                            </div>
                        <?php endif;?>
                    </div>
            </div>
            <p style="position:relative;top:5px;" class='pull-right'><?php echo htmlspecialchars($rs_keyin_cdate);?></p>
        </div>
    </div> -->
<?php //endif;?>