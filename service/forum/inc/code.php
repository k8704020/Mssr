<?php
//-------------------------------------------------------
//inc
//-------------------------------------------------------
//root          根單元
//
//-------------------------------------------------------
//root          根單元
//-------------------------------------------------------
//  root/add_forum_article_view_log()       新增文章瀏覽次數log
//  root/article_eagle()                    發文鷹架
//  root/reply_eagle()                      回文鷹架
//  root/bad_content_filter()               不雅文字過濾器
//  root/book_name_comparison()             書名比對
//  root/cat_code()                         書籍類別代號
//  root/default_book_category()            初始化書籍類別
//  root/fb_api()                           fb_api外掛
//  root/get_blacklist_group_school()       提取學校小組黑名單資訊
//  root/get_forum_friend()                 提取聊書書友資訊
//  root/get_login_info()                   提取登入資訊
//  root/get_request_info()                 取得邀請資訊
//  root/pagination()                       分頁顯示
//  root/search_book_ch_no_online()         查找線上書籍資訊
//  root/search_book_note_online()          查找線上書籍簡介資訊
//  root/update_setting_class_user_upload() 更新班級條件(使用者上傳)
//  root/google_track()                     google analytics
//  root/is_url_exist()                     確認網址是否存在
//  root/get_blacklist_article_school()     提取學校文章黑名單資訊
//  root/get_blacklist_reply_school()       提取學校回覆黑名單資訊
//  root/get_rank_and_point()               取得使用者積分以及發文點數
//  root/point()                            處理使用者發文點數
//  root/rank()                             處理使用者積分
//  root/friend_intimate()                  處理使用者親密度
//  root/stranger_familiar()                處理使用者熟悉度
//  root/get_appellation()                  取得使用者頭銜
//  root/get_friend_intimate()              取得使用者與書友的親密度
//  set_forum_action_log()                  紀錄使用者行為
//
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔

            //root          根單元
            require_once(preg_replace('/\s+/','','add_forum_article_view_log        /code.php'));
            require_once(preg_replace('/\s+/','','article_eagle                     /code.php'));
            require_once(preg_replace('/\s+/','','reply_eagle                       /code.php'));
            require_once(preg_replace('/\s+/','','bad_content_filter                /code.php'));
            require_once(preg_replace('/\s+/','','book_name_comparison              /code.php'));
            require_once(preg_replace('/\s+/','','cat_code                          /code.php'));
            require_once(preg_replace('/\s+/','','default_book_category             /code.php'));
            require_once(preg_replace('/\s+/','','fb_api                            /code.php'));
            require_once(preg_replace('/\s+/','','get_blacklist_group_school        /code.php'));
            require_once(preg_replace('/\s+/','','get_forum_friend                  /code.php'));
            require_once(preg_replace('/\s+/','','get_login_info                    /code.php'));
            require_once(preg_replace('/\s+/','','get_request_info                  /code.php'));
            require_once(preg_replace('/\s+/','','pagination                        /code.php'));
            require_once(preg_replace('/\s+/','','search_book_ch_no_online          /code.php'));
            require_once(preg_replace('/\s+/','','search_book_note_online           /code.php'));
            require_once(preg_replace('/\s+/','','update_setting_class_user_upload  /code.php'));
            require_once(preg_replace('/\s+/','','google_track                      /code.php'));
            require_once(preg_replace('/\s+/','','is_url_exist                      /code.php'));
            require_once(preg_replace('/\s+/','','get_blacklist_article_school      /code.php'));
            require_once(preg_replace('/\s+/','','get_blacklist_reply_school        /code.php'));
            require_once(preg_replace('/\s+/','','get_rank_and_point                /code.php'));
            require_once(preg_replace('/\s+/','','point                             /code.php'));
            require_once(preg_replace('/\s+/','','rank                              /code.php'));
            require_once(preg_replace('/\s+/','','friend_intimate                   /code.php'));
            require_once(preg_replace('/\s+/','','stranger_familiar                 /code.php'));
            require_once(preg_replace('/\s+/','','get_appellation                   /code.php'));
            require_once(preg_replace('/\s+/','','get_friend_intimate               /code.php'));
            // require_once(preg_replace('/\s+/','','set_forum_action_log              /code.php'));
?>