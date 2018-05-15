<?php
//-------------------------------------------------------
//inc
//-------------------------------------------------------
//root          根單元
//auth          系統功能
//book          書籍
//bookstore     書店
//category      類別
//user          學生
//user_info     教師專用功能
//read          閱讀
//rec           推薦
//forum         討論
//game          遊戲
//assessment    評量
//
//-------------------------------------------------------
//root          根單元
//-------------------------------------------------------
//  root/get_login_info()                       提取登入資訊
//  root/is_admin()                             是否為管理者
//  root/choose_class_code()                    切換班級
//  root/choose_identity()                      切換身份
//  root/fast_area()                            快速切換
//
//-------------------------------------------------------
//auth          系統功能
//-------------------------------------------------------
//  auth/auth_sys_arry_report()                 系統權限陣列(報表專用)
//  auth/auth_sys_arry_config()                 系統權限陣列(功能專用)
//  auth/auth_sys_img_arry()                    系統權限圖片陣列
//  auth/auth_sys_name_arry()                   系統權限名稱陣列
//  auth/auth_sys_target_arry()                 系統權限連結框架陣列
//  auth/auth_sys_url_arry()                    系統權限連結陣列
//  auth/auth_sys_check()                       系統權限判斷
//
//-------------------------------------------------------
//forum         討論
//-------------------------------------------------------
//  forum/update_setting_class_user_upload()    更新班級條件(使用者上傳)
//
//-------------------------------------------------------
//book          書籍
//-------------------------------------------------------
//  book/arrys_book_class()                     班級書籍陣列
//  book/arrys_book_library()                   圖書館書籍陣列
//  book/arrys_users()                          班級的學生
//  book/book_class_sid()                       班級書籍.識別碼
//  book/book_global_sid()                      系統書籍.識別碼
//  book/book_library_sid()                     圖書館書籍.識別碼
//  book/book_unverified_sid()                  未檢核的書籍.識別碼
//  book/find_book_bkl()                        查找博客來書籍資訊
//  book/find_book_bkl_m()                      查找博客來書籍資訊(行動版)
//  book/find_book_fbk()                        查找Findbook書籍資訊
//  book/find_book_fbk_m()                      查找Findbook書籍資訊(行動版)
//  book/find_book_fbk_img()                    查找Findbook書籍的圖片資訊
//  book/find_book_kst()                        查找金石堂書籍資訊
//  book/find_book_kst_m()                      查找金石堂書籍資訊(行動版)
//  book/find_book_ntu()                        查找台灣大學圖書館書籍資訊
//  book/get_book_info()                        提取書本資訊
//  book//get_class_code_info_revise()          提取班級資訊(修正)
//
//-------------------------------------------------------
//bookstore     書店
//-------------------------------------------------------
//  bookstore/update_rec_en_input()             更新推薦英文輸入鎖定條件
//  bookstore/update_coin_open()                更新葵幣開放條件
//  bookstore/update_rec_draw_open()            更新推薦畫圖開放條件
//  bookstore/update_rec_en_input()             更新推薦英文輸入鎖定條件
//
//-------------------------------------------------------
//category      類別
//-------------------------------------------------------
//  category/cat_code()                         類別代號
//
//-------------------------------------------------------
//user          學生
//-------------------------------------------------------
//  user/numrow_book_rec()                      學生推薦數
//  user/get_rec_book_cno_info()                提取書本推薦內容總調查計數表資訊
//  user/get_rec_comment_log_info()             提取老師對推薦內容評論表資訊
//  user/group_sid()                            組別識別碼
//
//-------------------------------------------------------
//user_info     教師專用功能
//-------------------------------------------------------
//  user_info/update_open_publish()             更新上架條件
//  user_info/book_borrow_school_rev()          借閱書學校關聯
//  user_info/update_read_opinion_limit_day()   更新閱讀登記期限條件
//  user_info/update_registration_code_pwd()    更新閱讀登記條碼版專用密碼
//
//-------------------------------------------------------
//read          閱讀
//-------------------------------------------------------
//  read/get_book_borrow_tmp_info()             提取借還書資料表資訊
//  read/get_book_read_opinion_log_info()       提取閱讀調查log表資訊
//  read/numrow_book_read_group()               學生閱讀本數
//  read/numrow_book_read_frequency()           學生閱讀次數
//  read/get_class_code_info_single()           提取班級資訊(修正版)
//  read//get_class_code_info_easy()            提取班級資訊(簡易版)
//
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔

            //root          根單元
            require_once(preg_replace('/\s+/','','get_login_info    /code.php'));
            require_once(preg_replace('/\s+/','','is_admin          /code.php'));
            require_once(preg_replace('/\s+/','','choose_class_code /code.php'));
            require_once(preg_replace('/\s+/','','choose_identity   /code.php'));
            require_once(preg_replace('/\s+/','','fast_area         /code.php'));

            //auth          系統功能
            require_once(preg_replace('/\s+/','','auth              /code.php'));

            //forum         討論
            require_once(preg_replace('/\s+/','','forum             /code.php'));

            //book          書籍
            require_once(preg_replace('/\s+/','','book              /code.php'));

            //bookstore     書店
            require_once(preg_replace('/\s+/','','bookstore         /code.php'));

            //category      類別
            require_once(preg_replace('/\s+/','','category          /code.php'));

            //user          學生
            require_once(preg_replace('/\s+/','','user              /code.php'));

            //user_info     教師專用功能
            require_once(preg_replace('/\s+/','','user_info         /code.php'));

            //read          閱讀
            require_once(preg_replace('/\s+/','','read              /code.php'));
?>