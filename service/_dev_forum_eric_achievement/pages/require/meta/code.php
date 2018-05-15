<?php
//-------------------------------------------------------
//函式: meta()
//用途: 標籤
//日期: 2015年4月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //測試
    //---------------------------------------------------

        //$meta=meta($rd=0);
        //echo '<pre>';
        //print_r($meta);
        //echo '</pre>';

    function meta($rd=0,$keywords='',$description=''){
    //---------------------------------------------------
    //函式: meta()
    //用途: 標籤
    //---------------------------------------------------
    //$rd           層級指標,預設0,表示在目前目錄下
    //$keywords     增加keywords
    //$description  增加description
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

            $html="";

            //-------------------------------------------
            //html  內容
            //-------------------------------------------

                $html.="
                    <meta charset='utf-8'>
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;' />
                    <meta http-equiv='content-language' content='utf-8'>
                    <meta http-equiv='content-type' content='text/html;charset=utf-8'>

                    <meta name='keywords' content='明日星球, 明日閱讀, 明日書店, 閱讀登記, 悅讀登記, 明日聊書, 夫子學院, 明日夫子學院, 明日創作, 明日數學, 明日學堂, 夫子課堂, 夫子魔課, 教師百寶箱, 明日Cafe' />
                ";

                if($description!==''){
                    $html.="<meta name='description' content='{$description}' />";
                }else{
                    $html.="<meta name='description' content='明日聊書，擴展學生閱讀視野，結合社交因素，建立學生閱讀社群，線上聊書是明日書店的進階推薦方式，更適合國小中高年級以上的學生。' />";
                }

                $html.="
                    <meta name='author' content='國立中央大學 版權所有 National Central University' />
                    <meta name='copyright' content='National Central University © All Rights Reserved.' />

                    <!-- google plus 設定 -->
                    <meta itemprop='name' content='明日星球, 明日閱讀, 明日書店, 閱讀登記, 悅讀登記, 明日聊書, 夫子學院, 明日夫子學院, 明日創作, 明日數學, 明日學堂, 夫子課堂, 夫子魔課, 教師百寶箱, 明日Cafe'>
                    <meta itemprop='image' content='http://www.cot.org.tw/mssr/service/forum/img/logo.png'>
                    <meta itemprop='description' content='明日聊書，擴展學生閱讀視野，結合社交因素，建立學生閱讀社群，線上聊書是明日書店的進階推薦方式，更適合國小中高年級以上的學生。'>

                    <!-- Facebook 設定 -->
                    <meta property='og:locale' content='zh_TW' />
                    <meta property='og:title' content='明日聊書' />
                    <meta property='og:type' content='website' />
                    <meta property='og:site_name' content='明日聊書' />
                    <meta property='og:url' content='http://www.cot.org.tw/mssr/service/forum/view/'>
                    <meta property='og:image' content='http://www.cot.org.tw/mssr/service/forum/img/logo.png' />
                    <meta property='og:description' content='明日聊書，擴展學生閱讀視野，結合社交因素，建立學生閱讀社群，線上聊書是明日書店的進階推薦方式，更適合國小中高年級以上的學生。' />

                    <!-- icon 設定 -->
                    <link rel='shortcut icon' href='http://www.cot.org.tw/mssr/service/forum/img/favicon.ico'>

                    <!-- 連結元素指出偏好網址 設定 -->
                    <link rel='canonical' href='http://www.cot.org.tw/mssr/service/forum/view/' />

                    <!--Google網站管理中心之追蹤程式碼-->
                    <meta name='google-site-verification' content='' />

                    <!--Bing網站管理員之追蹤程式碼-->
                    <meta name='msvalidate.01' content='' />

                    <!-- robots 設定 -->
                    <meta name='robots' content='index,follow,snippet,archive'>

                    <!-- 指定其他語言版本的網頁 設定 -->
                    <link rel='alternate' hreflang='zh-Hant' href='http://www.cot.org.tw/mssr/service/forum/view/' />

                    <!--Google分析專用追蹤程式碼-->
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