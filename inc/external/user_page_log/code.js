//-------------------------------------------------------
//函式: user_page_log()
//用途: 使用者頁面紀錄
//日期: 2014年10月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function user_page_log(rd){
    //---------------------------------------------------
    //函式: user_page_log()
    //用途: 使用者頁面紀錄
    //---------------------------------------------------
    //rd    層級
    //---------------------------------------------------

        //參數檢驗
        var rd  =rd || 0;

        //層級處理
        var _rd="";
        for(var i=0;i<rd;i++){
            _rd="../"+_rd;
        }

        //設定
        window.jQuery||document.write("<script src="+_rd+"'lib/jquery/basic/code.js'>\x3C/script>");
        window.document.write("<script src='/ac/js/user_log.js'></script>");
    }