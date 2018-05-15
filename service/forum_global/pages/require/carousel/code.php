<?php
//-------------------------------------------------------
//函式: carousel()
//用途: 廣告牆
//日期: 2015年4月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------

        //$carousel=carousel($rd=0);
        //echo '<pre>';
        //print_r($carousel);
        //echo '</pre>';

    function carousel($rd=0){
    //---------------------------------------------------
    //函式: carousel()
    //用途: 廣告牆
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

            //-------------------------------------------
            //html  內容
            //-------------------------------------------

                $date=date('Y',time());
                $html="
                    <!-- carousel,容器,start -->
                    <div id='carousel-1' class='carousel slide' data-ride='carousel' data-interval='3000' data-pause='hover'>
                        <div class='carousel-inner'>
                            <div class='item active'>
                                <div class='carousel-caption'></div>
                                <a href='#' target='_self'>
                                    <img src='{$rd}img/carousel/carousel_1.png' border='0'>
                                </a>
                            </div>
                            <div class='item'>
                                <div class='carousel-caption'></div>
                                <a href='#' target='_self'>
                                    <img src='{$rd}img/carousel/carousel_2.png' border='0'>
                                </a>
                            </div>
                            <div class='item'>
                                <div class='carousel-caption'></div>
                                <a href='#' target='_self'>
                                    <img src='{$rd}img/carousel/carousel_3.png' border='0'>
                                </a>
                            </div>
                        </div>

                        <ol class='carousel-indicators hidden-xs hidden-sm hidden-md hidden-lg'>
                            <li data-target='#carousel-1' data-slide-to='0' class='active'>
                            <li data-target='#carousel-1' data-slide-to='1'></li>
                            <li data-target='#carousel-1' data-slide-to='2'></li>
                        </ol>

                        <a class='left carousel-control hidden-xs' href='#carousel-1' data-slide='prev'>
                            <span class='glyphicon glyphicon-chevron-left'></span>
                        </a>
                        <a class='right carousel-control hidden-xs' href='#carousel-1' data-slide='next'>
                            <span class='glyphicon glyphicon-chevron-right'></span>
                        </a>
                    </div>
                    <!-- carousel,容器,end -->
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