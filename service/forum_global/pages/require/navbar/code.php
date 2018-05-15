<?php
//-------------------------------------------------------
//函式: navbar()
//用途: 導覽列
//日期: 2015年4月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------

        //$navbar=navbar($rd=0);
        //echo '<pre>';
        //print_r($navbar);
        //echo '</pre>';

    function navbar($rd=0){
    //---------------------------------------------------
    //函式: navbar()
    //用途: 導覽列
    //---------------------------------------------------
    //$rd   層級指標,預設0,表示在目前目錄下
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

            global $_SESSION;

            if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
                $sess_user_id  =(int)$_SESSION['mssr_forum_global']['uid'];
                $sess_user_name=trim($_SESSION['mssr_forum_global'][0]['name']);
                $sess_user_sex =(int)$_SESSION['mssr_forum_global'][0]['sex'];
                $sess_user_lv  =0;
                if(isset($_SESSION['mssr_forum_global'][0]['user_lv']))$sess_user_lv=(int)$_SESSION['mssr_forum_global'][0]['user_lv'];

                $sess_user_img ='';
                if($sess_user_sex===1)$sess_user_img='../img/default/user_boy.png';
                if($sess_user_sex===2)$sess_user_img='../img/default/user_girl.png';
            }

            //-------------------------------------------
            //html  內容
            //-------------------------------------------

                $html ='';
                $html.="
                    <!-- 導覽列,容器,start -->
                    <div class='navbar navbar-default navbar-fixed-top mx-navbar'>

                        <div class='container'>

                            <!-- 導覽列,標題列 -->
                            <div class='navbar-header mx-navbar-header'>
                                <!-- 導覽列,LOGO -->
                                <span class='navbar-brand mx-navbar-brand'>
                                    <a href='{$rd}view/index.php' class='navbar-link'>
                                        <img class='mx-navbar-logo' src='{$rd}img/logo.png' alt='logo,圖示'>
                                    </a>
                                </span>
                ";
                if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
                    $html.="
                                    <!-- 導覽列,縮合觸發 -->
                                    <button class='btn btn-deafult navbar-toggle' data-toggle='collapse' data-target='#navbar-collapse-1'>
                                        <span class='icon-bar'></span>
                                        <span class='icon-bar'></span>
                                        <span class='icon-bar'></span>
                                    </button>
                    ";
                }
                $html.="
                            </div>
                ";
                if(isset($_SESSION['mssr_forum_global']['uid'])&&isset($_SESSION['mssr_forum_global']['country_code'])){
                    $html.="
                                <!-- 導覽列,縮合列 -->
                                <div class='collapse navbar-collapse' id='navbar-collapse-1'>

                                    <!-- 資料搜尋,start -->
                                        <!-- <ul class='nav navbar-nav navbar-left hidden-xs'>
                                            <form class='navbar-form navbar-left' onsubmit='return false;'>
                                                <input type='text' class='form-control search_value input-sm' value='' placeholder='請輸入搜尋條件'>
                                                <select class='form-control search_type input-sm'>
                                                    <option value='1' selected>查詢人員</option>
                                                    <option value='2'>查詢書籍</option>
                                                    <option value='3'>查詢小組</option>
                                                    <option value='4'>查詢文章編號</option>
                                                </select>
                                                <button type='button' class='btn btn-default' onclick='search();void(0);'>搜尋</button>
                                            </form>
                                        </ul> -->
                                    <!-- 資料搜尋,end -->

                                    <ul class='nav navbar-nav navbar-right mx-navbar-nav'>
                                        <li class=''>
                                            <a href='{$rd}view/user.php?user_id={$sess_user_id}&tab=1'>
                                                <img class='user_img' src='{$sess_user_img}' width='20' height='20' alt='Media' border='0'>
                                                {$sess_user_name}
                                            </a>
                                        </li>
                                        <li class='hidden-sm hidden-md hidden-lg'>
                                            <a href='{$rd}view/user.php?user_id={$sess_user_id}&tab=2'><em class='glyphicon glyphicon-book'></em> 我的書櫃</a>
                                        </li>
                                        <li class='hidden-sm hidden-md hidden-lg'>
                                            <a href='{$rd}view/user.php?user_id={$sess_user_id}&tab=3'><em class='glyphicon glyphicon-pencil'></em> 我的討論</a>
                                        </li>
                                        <li class='hidden-sm hidden-md hidden-lg'>
                                            <a href='{$rd}view/user.php?user_id={$sess_user_id}&tab=4'><em class='glyphicon glyphicon-star'></em> 我的小組</a>
                                        </li>
                                        <li class='dropdown hidden-xs' onclick='close_drop_msg();void(0);'>
                                            <a id='drop1' href='javascript:void(0);' class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                <em class='glyphicon glyphicon-list-alt'></em> 我的資訊
                                                <ul class='dropdown-menu' role='menu' aria-labelledby='drop1' style='width:200px;'>
                                                    <li role='presentation' style='width:100%;'>
                                                        <a role='menuitem' tabindex='-1' href='{$rd}view/user.php?user_id={$sess_user_id}&tab=2' style='color:#4e4e4e;'><em class='glyphicon glyphicon-book'></em> 我的書櫃</a>
                                                    </li>
                                                    <li role='presentation' style='width:100%;'>
                                                        <a role='menuitem' tabindex='-1' href='{$rd}view/user.php?user_id={$sess_user_id}&tab=3' style='color:#4e4e4e;'><em class='glyphicon glyphicon-pencil'></em> 我的討論</a>
                                                    </li>
                                                    <li role='presentation' style='width:100%;'>
                                                        <a role='menuitem' tabindex='-1' href='{$rd}view/user.php?user_id={$sess_user_id}&tab=4' style='color:#4e4e4e;'><em class='glyphicon glyphicon-star'></em> 我的小組</a>
                                                    </li>
                                                </ul>
                                            </a>
                                        </li>
                    ";
                    $html.="
                                        <li class='dropdown' onclick='close_drop_msg();void(0);'>
                                            <a id='drop1' href='javascript:void(0);' class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                <em class='glyphicon glyphicon-cog'></em>
                                                <ul class='dropdown-menu' role='menu' aria-labelledby='drop1' style='width:200px;'>
                    ";
                    if(in_array($sess_user_lv,array(1,2,3,99))){
                        $html.="
                                                    <li role='presentation' style='width:100%;'>
                                                        <a role='menuitem' tabindex='-1' href='forum.php?method=add_group_teacher' style='color:#4e4e4e;'>建立聊書小組</a>
                                                    </li>
                        ";
                    }
                    $html.="
                                                    <li role='presentation' style='width:100%;'>
                                                        <a role='menuitem' tabindex='-1' href='../controller/logout.php' style='color:#4e4e4e;'>登出</a>
                                                    </li>
                                                </ul>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                    ";
                }
                $html.="
                        </div>
                    </div>
                    <!-- 導覽列,容器,end -->
                ";

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