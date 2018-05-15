<?php
//-------------------------------------------------------
//inc
//-------------------------------------------------------
//root  根單元
//
//-------------------------------------------------------
//root  根單元
//-------------------------------------------------------
//  root/auth_check()               權限檢核
//  root/bing_analysis()            bing分析設定
//  root/book_borrow_sid()          借閱識別碼
//  root/conn()                     資料庫連線設定
//  root/footer()                   註腳列
//  root/google_analysis()          google分析設定
//  root/login_check()              登入檢核
//  root/meta_description()         頁面關鍵字描述設定
//  root/meta_keywords()            頁面關鍵字設定
//	root/get_black_book_info()      提取黑名單書本資訊
//	root/get_book_info()            書號查詢書籍資訊
//  root/get_class_code_info()      提取班級資訊
//  root/get_rec_info()             提取推薦資訊
//  root/get_user_info()            提取使用者資訊
//  root/search_book_info_online()  查找線上書籍資訊
//  root/search_book_page_online()  查找線上書籍頁數資訊
//  root/tx_sid()                   交易識別碼
//  root/pptv_coda()                設置畢保德測驗次數額度
//
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔

            //root  根單元
            require_once(preg_replace('/\s+/','','auth_check                /code.php'));
            require_once(preg_replace('/\s+/','','bing_analysis             /code.php'));
            require_once(preg_replace('/\s+/','','book_borrow_sid           /code.php'));
            require_once(preg_replace('/\s+/','','conn                      /code.php'));
            require_once(preg_replace('/\s+/','','footer                    /code.php'));
            require_once(preg_replace('/\s+/','','google_analysis           /code.php'));
            require_once(preg_replace('/\s+/','','login_check               /code.php'));
            require_once(preg_replace('/\s+/','','meta_description          /code.php'));
            require_once(preg_replace('/\s+/','','meta_keywords             /code.php'));
			require_once(preg_replace('/\s+/','','get_black_book_info       /code.php'));
            require_once(preg_replace('/\s+/','','get_book_info             /code.php'));
            require_once(preg_replace('/\s+/','','get_class_code_info       /code.php'));
            require_once(preg_replace('/\s+/','','get_rec_info              /code.php'));
            require_once(preg_replace('/\s+/','','get_user_info             /code.php'));
            require_once(preg_replace('/\s+/','','search_book_info_online   /code.php'));
            require_once(preg_replace('/\s+/','','search_book_page_online   /code.php'));
            require_once(preg_replace('/\s+/','','tx_sid                    /code.php'));
            require_once(preg_replace('/\s+/','','pptv_coda                 /code.php'));
?>
