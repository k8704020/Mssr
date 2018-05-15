<?php
//-------------------------------------------------------
//函式: footbar()
//用途: 註腳列
//日期: 2015年4月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------

        //$footbar=footbar($rd=0);
        //echo '<pre>';
        //print_r($footbar);
        //echo '</pre>';

    function footbar($rd=0){
    //---------------------------------------------------
    //函式: footbar()
    //用途: 註腳列
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
                    <!-- 註腳列,容器,大解析度,start -->
                    <div class='container hidden-xs mx-footbar'>
                        <div class='mx-footbar-div'>
                            <!-- 註腳列,清單 -->
                            <div class='pull-right' style='margin-top:2px'>
                                <ul class='nav mx-footbar-ul'>
                                    <li class='mx-footbar-li'><a class='mx-link-gray' href='javascript:void(0);'>關於</a></li>
                                    <li class='mx-footbar-li'><a class='mx-link-gray' href='javascript:void(0);'>&copy; National Central University All Rights Reserved.</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- 註腳列,容器,大解析度,end -->

                    <!-- 註腳列,容器,小解析度,start -->
                    <div class='navbar navbar-default hidden-sm hidden-md hidden-lg mx-footbar'>
                        <div class='container'>
                            <!-- 註腳列,清單 -->
                            <div class='mx-footbar-div'>
                                <ul class='nav navbar-nav navbar-right mx-footbar-ul'>
                                    <li class='mx-footbar-li'><a class='mx-link-gray' href='javascript:void(0);'>關於</a></li>
                                    <li class='mx-footbar-li'><a class='mx-link-gray' href='javascript:void(0);'>&copy; National Central University All Rights Reserved.</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- 註腳列,容器,小解析度,end -->
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