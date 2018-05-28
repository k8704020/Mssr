<?php
//-------------------------------------------------------
//auth
//-------------------------------------------------------
//auth/auth_sys_arry_report()   系統權限陣列(報表專用)
//auth/auth_sys_arry_config()   系統權限陣列(功能專用)
//auth/auth_sys_img_arry()      系統權限圖片陣列
//auth/auth_sys_name_arry()     系統權限名稱陣列
//auth/auth_sys_target_arry()   系統權限連結框架陣列
//auth/auth_sys_url_arry()      系統權限連結陣列
//auth/auth_sys_check()         系統權限判斷
//auth/auth_sys_diff_lv()       系統難易層級

function auth_sys_arry_report($type=''){
//-------------------------------------------------------
//系統權限陣列(報表專用)
//-------------------------------------------------------
//參數
//-------------------------------------------------------
//$type 類型
//
//  read            閱讀
//  rec             書店
//  forum           討論
//  game            遊戲
//  assessment      評量
//  back_to_front   前往明日閱讀
//
//  預設 : $type='',表示全取
//-------------------------------------------------------
//read          閱讀
//      m_user_read                 學生登記資料
//      m_user_read_group           學生總閱讀數量
//      m_user_borrow               學生借閱資料
//      m_book_ranking              書籍借閱排行榜
//      m_class_read_info           班級閱讀統計
//
//rec           書店
//      m_user_rec                  學生推薦資料
//      m_user_rec_book_best_class  班級優秀推薦
//      m_class_rec_info            班級推薦統計
//
//forum         聊書
//      m_forum_user_info           學生相關資料
//      m_forum_book_info           書籍相關資料
//      m_forum_group_info          小組相關資料
//      m_forum_article_info        學生聊書資料
//      m_forum_info                學生聊書資訊
//      m_forum_hot_booklist        學生聊書票選
//      m_class_forum_info          班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                      畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//
//back_to_front 前往明日閱讀
//
//
//-------------------------------------------------------

    //容許類型
    $types=array(
        trim('read          ')=>trim('閱讀              '),
        trim('rec           ')=>trim('書店              '),
        trim('forum         ')=>trim('討論              '),
        trim('game          ')=>trim('遊戲              '),
        trim('assessment    ')=>trim('評量              '),
        trim('go_to_ctrl    ')=>trim('前往老師管理      '),
        trim('back_to_front ')=>trim('前往明日閱讀  ')
    );

    //檢核
    if(isset($type)&&trim($type)!=''){
        if(!in_array($type,array_keys($types))){
            return array();
        }
    }

    //定義
    $auth_sys_arry=array(
        //read          閱讀
        //      m_user_read             學生登記資料
        //      m_user_read_group       學生總閱讀數量
        //      m_user_borrow           學生借閱資料
        //      m_book_ranking          書籍借閱排行榜
        //      m_class_read_info       班級閱讀統計
        'read'=>array(
            'm_user_read'=>array(
                'access'=>1
            ),
            'm_user_read_group'=>array(
                'access'=>1
            ),
            'm_user_borrow'=>array(
                'access'=>1
            ),
            'm_book_ranking'=>array(
                'access'=>1
            ),
            'm_class_read_info'=>array(
                'access'=>1
            )
        ),
        //rec           書店
        //      m_user_rec                  學生推薦資料
        //      m_user_rec_book_best_class  班級優秀推薦
        //      m_class_rec_info            班級推薦統計
        'rec'=>array(
            'm_user_rec'=>array(
                'access'=>1
            ),
            'm_user_rec_book_best_class'=>array(
                'access'=>1
            ),
            'm_class_rec_info'=>array(
                'access'=>1
            )
        ),
        //forum         聊書
        //      m_forum_user_info       學生相關資料
        //      m_forum_book_info       書籍相關資料
        //      m_forum_group_info      小組相關資料
        //      m_forum_article_info    學生聊書資料
        //      m_forum_info            學生聊書資訊
        //      m_forum_hot_booklist    學生聊書票選
        //      m_class_forum_info      班級聊書統計
        'forum'=>array(
            //'m_forum_user_info'=>array(
            //    'access'=>1
            //),
            //'m_forum_book_info'=>array(
            //    'access'=>1
            //),
            //'m_forum_group_info'=>array(
            //    'access'=>1
            //),
            'm_forum_article_info'=>array(
                'access'=>1
            ),
            //'m_forum_info'=>array(
            //    'access'=>1
            //),
            'm_forum_hot_booklist'=>array(
                'access'=>1
            ),
            'm_class_forum_info'=>array(
                'access'=>1
            )
        ),
        //game          遊戲
        //'game'=>array(
        //),
        //assessment    評量
        //      m_pptv                  畢保德成績查詢
        //'assessment'=>array(
        //    'm_pptv'=>array(
        //        'access'=>1
        //    )
        //),
        //go_to_ctrl    前往老師管理
        'go_to_ctrl'=>array(
        ),
        //back_to_front 前往明日閱讀
        'back_to_front'=>array(
        )
    );

    //回傳
    if(isset($type)&&trim($type)!=''){
        if(in_array($type,array_keys($types))){
            return $auth_sys_arry[$type];
        }
    }

    return $auth_sys_arry;
}

function auth_sys_arry_config($type=''){
//-------------------------------------------------------
//系統權限陣列(功能專用)
//-------------------------------------------------------
//參數
//-------------------------------------------------------
//$type 類型
//
//  user            學生管理
//  book            書籍管理
//  forum           聊書管理
//  user_info       專用功能
//
//  預設 : $type='',表示全取
//-------------------------------------------------------
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//forum         聊書管理
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//
//-------------------------------------------------------

    //容許類型
    $types=array(
        trim('user          ')=>trim('學生管理   '),
        trim('category      ')=>trim('類別管理   '),
        trim('book          ')=>trim('書籍管理   '),
        trim('bookstore     ')=>trim('書店管理   '),
        trim('forum         ')=>trim('聊書管理  '),
        trim('user_info     ')=>trim('專用功能   '),
        trim('export        ')=>trim('資料匯出   ')
    );

    //檢核
    if(isset($type)&&trim($type)!=''){
        if(!in_array($type,array_keys($types))){
            return array();
        }
    }

    //定義
    $auth_sys_arry=array(
        //user          學生管理
        //      m_user_group                    學生分組設定
        //      m_rec_book_draw_upload_report   推薦畫圖檢舉
        //      m_set_talk_declaration          管理招呼語及星球宣言
        //      m_transaction                   學生物品及交易紀錄
        'user'=>array(
            'm_user_group'=>array(
                'access'=>1
            ),
            //'m_rec_book_draw_upload_report'=>array(
            //    'access'=>1
            //),
            'm_set_talk_declaration'=>array(
                'access'=>1
            ),
            'm_transaction'=>array(
                'access'=>1
            )
        ),
        //category      類別管理
        //      m_category                      類別設定
        'category'=>array(
            'm_category'=>array(
                'access'=>1
            )
        ),
        //book          書籍管理
        //      m_book                          書籍設定
        //      m_black_book                    書籍黑名單設定
        //      m_isbn_code_create              列印書籍條碼
        //      m_book_donor_detail             全校捐書者明細
        //      m_book_unverified_verified      學生自建書籍檢核
        //      m_book_borrow_search            書籍借閱人查詢
        //      m_book_unverified_indecent      不雅書籍檢核
        'book'=>array(
            'm_book'=>array(
                'access'=>1
            ),
            'm_black_book'=>array(
                'access'=>1
            ),
            'm_isbn_code_create'=>array(
                'access'=>1
            ),
            'm_book_donor_detail'=>array(
                'access'=>1
            ),
            // 'm_book_unverified_verified'=>array(
            //     'access'=>1
            // ),
            'm_book_borrow_search'=>array(
                'access'=>1
            )
            // 'm_book_unverified_indecent'=>array(
            //     'access'=>1
            // )
        ),
        //bookstore     書店管理
        //      m_open_publish                  書店上架相關條件
        //      m_read_opinion_limit_day        閱讀登記期限條件
        //      m_borrow_limit_cno              閱讀登記數量限制
        //      m_rec_en_input                  推薦英文輸入條件
        //      m_rec_draw_open                 推薦畫圖開關
        //      m_coin_open                     書店葵幣開關
        //      m_open_publish_cno              書店上架本數條件
        //      m_over_school_view              是否開放跨校瀏覽
        //      m_registration_code_pwd         閱讀登記條碼版專用密碼
        //      m_registration_code_opinion     閱讀登記條碼版登記功能
        'bookstore'=>array(
            'm_open_publish'=>array(
                'access'=>1
            ),
            'm_read_opinion_limit_day'=>array(
                'access'=>1
            ),
            'm_borrow_limit_cno'=>array(
                'access'=>1
            ),
            'm_rec_en_input'=>array(
                'access'=>1
            ),
            'm_rec_draw_open'=>array(
                'access'=>1
            ),
            'm_coin_open'=>array(
                'access'=>1
            ),
            //'m_open_publish_cno'=>array(
            //    'access'=>1
            //),
            'm_over_school_view'=>array(
                'access'=>1
            ),
            'm_registration_code_pwd'=>array(
                'access'=>1
            ),
            'm_registration_code_opinion'=>array(
                'access'=>1
            )
        ),
        //forum         聊書管理
        //      m_forum_setting_class           聊書設定大頭貼與背景
        //      m_forum_setting_point           聊書設定發文點數
        'forum'=>array(
            'm_forum_setting_class'=>array(
                'access'=>1
            ),
            'm_forum_setting_point'=>array(
                'access'=>1
            )
        ),
        //user_info     專用功能
        //      m_teacher_rec                   教師快速登記書籍
        //      m_dw_browser                    下載瀏覽器
        //      m_mail                          電子郵件
        'user_info'=>array(
            'm_teacher_rec'=>array(
                'access'=>1
            ),
            //'m_dw_browser'=>array(
            //    'access'=>1
            //),
            //'m_mail'=>array(
            //    'access'=>1
            //)
        ),
        //export        資料匯出
        //      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
        //      m_user_borrow_export            學生學期借閱資料匯出
        'export'=>array(
            'm_user_borrow_library_export'=>array(
                'access'=>1
            ),
            'm_user_borrow_export'=>array(
                'access'=>1
            )
        )
    );

    //回傳
    if(isset($type)&&trim($type)!=''){
        if(in_array($type,array_keys($types))){
            return $auth_sys_arry[$type];
        }
    }

    return $auth_sys_arry;
}

function auth_sys_img_arry(){
//-------------------------------------------------------
//系統權限圖片陣列
//-------------------------------------------------------
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//

    $auth_sys_img_arry=array(
        //read          閱讀
        //      m_user_read                     學生登記資料
        //      m_user_read_group               學生總閱讀數量
        //      m_user_borrow                   學生借閱資料
        //      m_book_ranking                  書籍借閱排行榜
        //      m_class_read_info               班級閱讀統計
        trim('read                      ')=>trim('inc/auth/img/read/read                            ').".jpg",
        trim('m_user_read               ')=>trim('inc/auth/img/read/m_user_read                     ').".jpg",
        trim('m_user_read_group         ')=>trim('inc/auth/img/read/m_user_read_group               ').".jpg",
        trim('m_user_borrow             ')=>trim('inc/auth/img/read/m_user_borrow                   ').".jpg",
        trim('m_book_ranking            ')=>trim('inc/auth/img/read/m_book_ranking                  ').".jpg",
        trim('m_class_read_info         ')=>trim('inc/auth/img/read/m_class_read_info               ').".jpg",

        //rec           書店
        //      m_user_rec                      學生推薦資料
        //      m_user_rec_book_best_class      班級優秀推薦
        //      m_class_rec_info                班級推薦統計
        trim('rec                       ')=>trim('inc/auth/img/rec/rec                              ').".jpg",
        trim('m_user_rec                ')=>trim('inc/auth/img/rec/m_user_rec                       ').".jpg",
        trim('m_user_rec_book_best_class')=>trim('inc/auth/img/rec/m_user_rec_book_best_class       ').".jpg",
        trim('m_class_rec_info          ')=>trim('inc/auth/img/rec/m_class_rec_info                 ').".jpg",

        //forum         聊書
        //      m_forum_user_info               學生相關資料
        //      m_forum_book_info               書籍相關資料
        //      m_forum_group_info              小組相關資料
        //      m_forum_article_info            學生聊書資料
        //      m_forum_info                    學生聊書資訊
        //      m_forum_hot_booklist            學生聊書票選
        //      m_forum_setting_class           聊書設定大頭貼與背景
        //      m_forum_setting_point           聊書設定發文點數
        //      m_class_forum_info              班級聊書統計
        trim('forum                     ')=>trim('inc/auth/img/forum/forum                          ').".jpg",
        trim('m_forum_user_info         ')=>trim('inc/auth/img/forum/m_forum_user_info              ').".jpg",
        trim('m_forum_book_info         ')=>trim('inc/auth/img/forum/m_forum_book_info              ').".jpg",
        trim('m_forum_group_info        ')=>trim('inc/auth/img/forum/m_forum_group_info             ').".jpg",
        trim('m_forum_article_info      ')=>trim('inc/auth/img/forum/m_forum_article_info           ').".jpg",
        trim('m_forum_info              ')=>trim('inc/auth/img/forum/m_forum_info                   ').".jpg",
        trim('m_forum_hot_booklist      ')=>trim('inc/auth/img/forum/m_forum_hot_booklist           ').".jpg",
        trim('m_forum_setting_class     ')=>trim('inc/auth/img/forum/m_forum_setting_class          ').".jpg",
        trim('m_forum_setting_point     ')=>trim('inc/auth/img/forum/m_forum_setting_point          ').".jpg",
        trim('m_class_forum_info        ')=>trim('inc/auth/img/forum/m_class_forum_info             ').".jpg",

        //game          遊戲
        trim('game                      ')=>trim('inc/auth/img/game/game                            ').".jpg",

        //assessment    評量
        //      m_pptv                          畢保德成績查詢
        trim('assessment                ')=>trim('inc/auth/img/assessment/assessment                ').".jpg",
        trim('m_pptv                    ')=>trim('inc/auth/img/assessment/m_pptv                    ').".jpg",

        //go_to_ctrl    前往老師管理
        trim('go_to_ctrl                ')=>trim('inc/auth/img/go_to_ctrl/go_to_ctrl                ').".jpg",

        //back_to_front 前往明日閱讀
        trim('back_to_front             ')=>trim('inc/auth/img/back_to_front/back_to_front          ').".jpg",

        //user          學生管理
        //      m_user_group                    學生分組設定
        //      m_rec_book_draw_upload_report   推薦畫圖檢舉
        //      m_set_talk_declaration          管理招呼語及星球宣言
        //      m_transaction                   學生物品及交易紀錄
        trim('user                      ')=>trim('inc/auth/img/user/user                            ').".jpg",
        trim('m_user_group              ')=>trim('inc/auth/img/user/m_user_group                    ').".jpg",
        trim('m_rec_book_draw_upload_report')=>trim('inc/auth/img/user/m_rec_book_draw_upload_report').".jpg",
        trim('m_set_talk_declaration    ')=>trim('inc/auth/img/user/m_set_talk_declaration          ').".jpg",
        trim('m_transaction             ')=>trim('inc/auth/img/user/m_transaction                   ').".jpg",

        //category      類別管理
        //      m_category                      類別設定
        trim('category                  ')=>trim('inc/auth/img/category/category                    ').".jpg",
        trim('m_category                ')=>trim('inc/auth/img/category/m_category                  ').".jpg",

        //book          書籍管理
        //      m_book                          書籍設定
        //      m_black_book                    書籍黑名單設定
        //      m_isbn_code_create              列印書籍條碼
        //      m_book_donor_detail             全校捐書者明細
        //      m_book_unverified_verified      學生自建書籍檢核
        //      m_book_borrow_search            書籍借閱人查詢
        //      m_book_unverified_indecent      不雅書籍檢核
        trim('book                              ')=>trim('inc/auth/img/book/book                            ').".jpg",
        trim('m_book                            ')=>trim('inc/auth/img/book/m_book                          ').".jpg",
        trim('m_black_book                      ')=>trim('inc/auth/img/book/m_black_book                    ').".jpg",
        trim('m_isbn_code_create                ')=>trim('inc/auth/img/book/m_isbn_code_create              ').".jpg",
        trim('m_book_donor_detail               ')=>trim('inc/auth/img/book/m_book_donor_detail             ').".jpg",
        // trim('m_book_unverified_verified        ')=>trim('inc/auth/img/book/m_book_unverified_verified      ').".jpg",
        trim('m_book_borrow_search              ')=>trim('inc/auth/img/book/m_book_borrow_search            ').".jpg",
        // trim('m_book_unverified_indecent        ')=>trim('inc/auth/img/book/m_book_unverified_indecent      ').".jpg",

        //bookstore     書店管理
        //      m_open_publish                  書店上架相關條件
        //      m_read_opinion_limit_day        閱讀登記期限條件
        //      m_borrow_limit_cno              閱讀登記數量限制
        //      m_rec_en_input                  推薦英文輸入條件
        //      m_rec_draw_open                 推薦畫圖開關
        //      m_coin_open                     書店葵幣開關
        //      m_open_publish_cno              書店上架本數條件
        //      m_over_school_view              是否開放跨校瀏覽
        //      m_registration_code_pwd         閱讀登記條碼版專用密碼
        //      m_registration_code_opinion     閱讀登記條碼版登記功能
        trim('bookstore                 ')=>trim('inc/auth/img/bookstore/bookstore                  ').".jpg",
        trim('m_open_publish            ')=>trim('inc/auth/img/bookstore/m_open_publish             ').".jpg",
        trim('m_read_opinion_limit_day  ')=>trim('inc/auth/img/bookstore/m_read_opinion_limit_day   ').".jpg",
        trim('m_borrow_limit_cno        ')=>trim('inc/auth/img/bookstore/m_borrow_limit_cno         ').".jpg",
        trim('m_rec_en_input            ')=>trim('inc/auth/img/bookstore/m_rec_en_input             ').".jpg",
        trim('m_rec_draw_open           ')=>trim('inc/auth/img/bookstore/m_rec_draw_open            ').".jpg",
        trim('m_coin_open               ')=>trim('inc/auth/img/bookstore/m_coin_open                ').".jpg",
        trim('m_open_publish_cno        ')=>trim('inc/auth/img/bookstore/m_open_publish_cno         ').".jpg",
        trim('m_over_school_view        ')=>trim('inc/auth/img/bookstore/m_over_school_view         ').".jpg",
        trim('m_registration_code_pwd   ')=>trim('inc/auth/img/user_info/m_registration_code_pwd    ').".jpg",
        trim('m_registration_code_opinion')=>trim('inc/auth/img/user_info/m_registration_code_opinion').".jpg",

        //user_info     專用功能
        //      m_teacher_rec                   教師快速登記書籍
        //      m_dw_browser                    下載瀏覽器
        //      m_mail                          電子郵件
        trim('user_info                 ')=>trim('inc/auth/img/user_info/user_info                  ').".jpg",
        trim('m_teacher_rec             ')=>trim('inc/auth/img/user_info/m_teacher_rec              ').".jpg",
        trim('m_dw_browser              ')=>trim('inc/auth/img/user_info/m_dw_browser               ').".jpg",
        trim('m_mail                    ')=>trim('inc/auth/img/user_info/m_mail                     ').".jpg",

        //export        資料匯出
        //      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
        //      m_user_borrow_export            學生學期借閱資料匯出
        trim('export                            ')=>trim('inc/auth/img/export/export                        ').".jpg",
        trim('m_user_borrow_library_export      ')=>trim('inc/auth/img/export/m_user_borrow_library_export  ').".jpg",
        trim('m_user_borrow_export              ')=>trim('inc/auth/img/export/m_user_borrow_export          ').".jpg"
    );

    return $auth_sys_img_arry;
}

function auth_sys_name_arry(){
//-------------------------------------------------------
//系統權限名稱陣列
//-------------------------------------------------------
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//

    $auth_sys_name_arry=array(
        //read          閱讀
        //      m_user_read                     學生登記資料
        //      m_user_read_group               學生總閱讀數量
        //      m_user_borrow                   學生借閱資料
        //      m_book_ranking                  書籍借閱排行榜
        //      m_class_read_info               班級閱讀統計
            trim('read                      ')=>trim('閱讀                  '),
            trim('m_user_read               ')=>trim('學生登記資料          '),
            trim('m_user_read_group         ')=>trim('學生總閱讀數量        '),
            trim('m_user_borrow             ')=>trim('學生借閱資料          '),
            trim('m_book_ranking            ')=>trim('書籍借閱排行榜        '),
            trim('m_class_read_info         ')=>trim('班級閱讀統計          '),

        //rec           書店
        //      m_user_rec                      學生推薦資料
        //      m_user_rec_book_best_class      班級優秀推薦
        //      m_class_rec_info                班級推薦統計
            trim('rec                       ')=>trim('書店                  '),
            trim('m_user_rec                ')=>trim('學生推薦資料          '),
            trim('m_user_rec_book_best_class')=>trim('班級優秀推薦          '),
            trim('m_class_rec_info          ')=>trim('班級推薦統計          '),

        //forum         聊書
        //      m_forum_user_info               學生相關資料
        //      m_forum_book_info               書籍相關資料
        //      m_forum_group_info              小組相關資料
        //      m_forum_article_info            學生聊書資料
        //      m_forum_info                    學生聊書資訊
        //      m_forum_hot_booklist            學生聊書票選
        //      m_forum_setting_class           聊書設定大頭貼與背景
        //      m_forum_setting_point           聊書設定發文點數
        //      m_class_forum_info              班級聊書統計
            trim('forum                     ')=>trim('聊書                  '),
            trim('m_forum_user_info         ')=>trim('學生相關資料          '),
            trim('m_forum_book_info         ')=>trim('書籍相關資料          '),
            trim('m_forum_group_info        ')=>trim('小組相關資料          '),
            trim('m_forum_article_info      ')=>trim('學生聊書資料          '),
            trim('m_forum_info              ')=>trim('學生聊書資訊          '),
            trim('m_forum_hot_booklist      ')=>trim('學生聊書票選          '),
            trim('m_forum_setting_class     ')=>trim('聊書設定大頭貼與背景   '),
            trim('m_forum_setting_point     ')=>trim('聊書設定發文點數       '),
            trim('m_class_forum_info        ')=>trim('班級聊書統計          '),

        //game          遊戲
            trim('game                      ')=>trim('遊戲                  '),

        //assessment    評量
        //      m_pptv                          畢保德成績查詢
            trim('assessment                ')=>trim('評量                  '),
            trim('m_pptv                    ')=>trim('畢保德成績查詢        '),

        //go_to_ctrl    前往老師管理
            trim('go_to_ctrl                ')=>trim('前往老師管理          '),

        //back_to_front 前往明日閱讀
            trim('back_to_front             ')=>trim('前往明日閱讀          '),

        //user          學生管理
        //      m_user_group                    學生分組設定
        //      m_rec_book_draw_upload_report   推薦畫圖檢舉
        //      m_set_talk_declaration          管理招呼語及星球宣言
        //      m_transaction                   學生物品及交易紀錄
            trim('user                      ')=>trim('學生管理              '),
            trim('m_user_group              ')=>trim('學生分組設定          '),
            trim('m_rec_book_draw_upload_report')=>trim('推薦畫圖檢舉       '),
            trim('m_set_talk_declaration    ')=>trim('管理招呼語及星球宣言  '),
            trim('m_transaction             ')=>trim('學生物品及交易紀錄    '),

        //category      類別管理
        //      m_category                      類別設定
            trim('category                  ')=>trim('類別管理              '),
            trim('m_category                ')=>trim('類別設定              '),

        //book          書籍管理
        //      m_book                          書籍設定
        //      m_black_book                    書籍黑名單設定
        //      m_isbn_code_create              列印書籍條碼
        //      m_book_donor_detail             全校捐書者明細
        //      m_book_unverified_verified      學生自建書籍檢核
        //      m_book_borrow_search            書籍借閱人查詢
        //      m_book_unverified_indecent      不雅書籍檢核
            trim('book                              ')=>trim('書籍管理                  '),
            trim('m_book                            ')=>trim('書籍設定                  '),
            trim('m_black_book                      ')=>trim('書籍黑名單設定            '),
            trim('m_isbn_code_create                ')=>trim('列印書籍條碼              '),
            trim('m_book_donor_detail               ')=>trim('全校捐書者明細            '),
            // trim('m_book_unverified_verified        ')=>trim('學生自建書籍檢核          '),
            trim('m_book_borrow_search              ')=>trim('書籍借閱人查詢            '),
            // trim('m_book_unverified_indecent        ')=>trim('不雅書籍檢核              '),

        //bookstore     書店管理
        //      m_open_publish                  書店上架相關條件
        //      m_read_opinion_limit_day        閱讀登記期限條件
        //      m_borrow_limit_cno              閱讀登記數量限制
        //      m_rec_en_input                  推薦英文輸入條件
        //      m_rec_draw_open                 推薦畫圖開關
        //      m_coin_open                     書店葵幣開關
        //      m_open_publish_cno              書店上架本數條件
        //      m_over_school_view              是否開放跨校瀏覽
        //      m_registration_code_pwd         閱讀登記條碼版專用密碼
        //      m_registration_code_opinion     閱讀登記條碼版登記功能
            trim('bookstore                 ')=>trim('書店管理              '),
            trim('m_open_publish            ')=>trim('書店上架相關條件          '),
            trim('m_read_opinion_limit_day  ')=>trim('閱讀登記期限條件      '),
            trim('m_borrow_limit_cno        ')=>trim('閱讀登記數量限制      '),
            trim('m_rec_en_input            ')=>trim('推薦英文輸入條件      '),
            trim('m_rec_draw_open           ')=>trim('推薦畫圖開關          '),
            trim('m_coin_open               ')=>trim('書店葵幣開關          '),
            trim('m_open_publish_cno        ')=>trim('書店上架本數條件      '),
            trim('m_over_school_view        ')=>trim('是否開放跨校瀏覽      '),
            trim('m_registration_code_pwd   ')=>trim('閱讀登記條碼版專用密碼'),
            trim('m_registration_code_opinion')=>trim('閱讀登記條碼版登記功能'),

        //user_info     專用功能
        //      m_teacher_rec                   教師快速登記書籍
        //      m_dw_browser                    下載瀏覽器
        //      m_mail                          電子郵件
            trim('user_info                 ')=>trim('專用功能              '),
            trim('m_teacher_rec             ')=>trim('教師快速登記書籍      '),
            trim('m_dw_browser              ')=>trim('下載瀏覽器            '),
            trim('m_mail                    ')=>trim('電子郵件              '),

        //export        資料匯出
        //      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
        //      m_user_borrow_export
            trim('export                        ')=>trim('資料匯出                      '),
            trim('m_user_borrow_library_export  ')=>trim('教育部全國閱讀推動與圖書管理系統資料上傳    '),
            trim('m_user_borrow_export          ')=>trim('學生學期借閱資料匯出            '),

        //-----------------------------------------------
        //項目
        //-----------------------------------------------
            trim('access                    ')  =>trim('存取                ')
    );

    return $auth_sys_name_arry;
}

function auth_sys_target_arry(){
//-------------------------------------------------------
//系統權限連結框架陣列
//-------------------------------------------------------
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//

    $auth_sys_target_arry=array(
        //read          閱讀
        //      m_user_read                     學生登記資料
        //      m_user_read_group               學生總閱讀數量
        //      m_user_borrow                   學生借閱資料
        //      m_book_ranking                  書籍借閱排行榜
        //      m_class_read_info               班級閱讀統計
            trim('read                      ')=>trim('      '),
            trim('m_user_read               ')=>trim('_self '),
            trim('m_user_read_group         ')=>trim('_self '),
            trim('m_user_borrow             ')=>trim('_self '),
            trim('m_book_ranking            ')=>trim('_self '),
            trim('m_class_read_info         ')=>trim('_self '),

        //rec           書店
        //      m_user_rec                      學生推薦資料
        //      m_user_rec_book_best_class      班級優秀推薦
        //      m_class_rec_info                班級推薦統計
            trim('rec                       ')=>trim('      '),
            trim('m_user_rec                ')=>trim('_self '),
            trim('m_user_rec_book_best_class')=>trim('_self '),
            trim('m_class_rec_info          ')=>trim('_self '),

        //forum         聊書
        //      m_forum_user_info               學生相關資料
        //      m_forum_book_info               書籍相關資料
        //      m_forum_group_info              小組相關資料
        //      m_forum_article_info            學生聊書資料
        //      m_forum_info                    學生聊書資訊
        //      m_forum_hot_booklist            學生聊書票選
        //      m_forum_setting_class           聊書設定大頭貼與背景
        //      m_forum_setting_point           聊書設定發文點數
        //      m_class_forum_info              班級聊書統計
            trim('forum                     ')=>trim('      '),
            trim('m_forum_user_info         ')=>trim('_self '),
            trim('m_forum_book_info         ')=>trim('_self '),
            trim('m_forum_group_info        ')=>trim('_self '),
            trim('m_forum_article_info      ')=>trim('_self '),
            trim('m_forum_info              ')=>trim('_self '),
            trim('m_forum_hot_booklist      ')=>trim('_self '),
            trim('m_forum_setting_class     ')=>trim('_self '),
            trim('m_forum_setting_point     ')=>trim('_self '),
            trim('m_class_forum_info        ')=>trim('_self '),

        //game          遊戲
            trim('game                      ')=>trim('      '),

        //assessment    評量
        //      m_pptv                          畢保德成績查詢
            trim('assessment                ')=>trim('      '),
            trim('m_pptv                    ')=>trim('_self '),

        //go_to_ctrl    前往老師管理
            trim('go_to_ctrl                ')=>trim('_blank'),

        //back_to_front 前往明日閱讀
            trim('back_to_front             ')=>trim('_blank'),

        //user          學生管理
        //      m_user_group                    學生分組設定
        //      m_rec_book_draw_upload_report   推薦畫圖檢舉
        //      m_set_talk_declaration          管理招呼語及星球宣言
        //      m_transaction                   學生物品及交易紀錄
            trim('user                      ')=>trim('      '),
            trim('m_user_group              ')=>trim('_self '),
            trim('m_rec_book_draw_upload_report')=>trim('_self'),
            trim('m_set_talk_declaration    ')=>trim('_self '),
            trim('m_transaction             ')=>trim('_self '),

        //category      類別管理
        //      m_category                      類別設定
            trim('category                  ')=>trim('      '),
            trim('m_category                ')=>trim('_self '),

        //book          書籍管理
        //      m_book                          書籍設定
        //      m_black_book                    書籍黑名單設定
        //      m_isbn_code_create              列印書籍條碼
        //      m_book_donor_detail             全校捐書者明細
        //      m_book_unverified_verified      學生自建書籍檢核
        //      m_book_borrow_search            書籍借閱人查詢
        //      m_book_unverified_indecent      不雅書籍檢核
            trim('book                          ')=>trim('      '),
            trim('m_book                        ')=>trim('_self '),
            trim('m_black_book                  ')=>trim('_self '),
            trim('m_isbn_code_create            ')=>trim('_self '),
            trim('m_book_donor_detail           ')=>trim('_self '),
            // trim('m_book_unverified_verified    ')=>trim('_self '),
            trim('m_book_borrow_search          ')=>trim('_self '),
            // trim('m_book_unverified_indecent    ')=>trim('_self '),

        //bookstore     書店管理
        //      m_open_publish                  書店上架相關條件
        //      m_read_opinion_limit_day        閱讀登記期限條件
        //      m_borrow_limit_cno              閱讀登記數量限制
        //      m_rec_en_input                  推薦英文輸入條件
        //      m_rec_draw_open                 推薦畫圖開關
        //      m_coin_open                     書店葵幣開關
        //      m_open_publish_cno              書店上架本數條件
        //      m_over_school_view              是否開放跨校瀏覽
        //      m_registration_code_pwd         閱讀登記條碼版專用密碼
        //      m_registration_code_opinion     閱讀登記條碼版登記功能
            trim('bookstore                 ')=>trim('      '),
            trim('m_open_publish            ')=>trim('_self '),
            trim('m_read_opinion_limit_day  ')=>trim('_self '),
            trim('m_borrow_limit_cno        ')=>trim('_self '),
            trim('m_rec_en_input            ')=>trim('_self '),
            trim('m_rec_draw_open           ')=>trim('_self '),
            trim('m_coin_open               ')=>trim('_self '),
            trim('m_open_publish_cno        ')=>trim('_self '),
            trim('m_over_school_view        ')=>trim('_self '),
            trim('m_registration_code_pwd   ')=>trim('_self '),
            trim('m_registration_code_opinion')=>trim('_self'),

        //user_info     專用功能
        //      m_teacher_rec                   教師快速登記書籍
        //      m_dw_browser                    下載瀏覽器
        //      m_mail                          電子郵件
            trim('user_info                 ')=>trim('      '),
            trim('m_teacher_rec             ')=>trim('_self '),
            trim('m_dw_browser              ')=>trim('_self '),
            trim('m_mail                    ')=>trim('_self '),

        //export        資料匯出
        //      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
        //      m_user_borrow_export            學生學期借閱資料匯出
            trim('export                        ')=>trim('_self '),
            trim('m_user_borrow_library_export  ')=>trim('_self '),
            trim('m_user_borrow_export          ')=>trim('_self ')
    );

    return $auth_sys_target_arry;
}

function auth_sys_url_arry(){
//-------------------------------------------------------
//系統權限連結陣列
//-------------------------------------------------------
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//

    $auth_sys_url_arry=array(

        //read          閱讀
        //      m_user_read                     學生登記資料
        //      m_user_read_group               學生總閱讀數量
        //      m_user_borrow                   學生借閱資料
        //      m_book_ranking                  書籍借閱排行榜
        //      m_class_read_info               班級閱讀統計
            trim('read                      ')=>trim('javascript:void(0);               '),
            trim('m_user_read               ')=>trim('../../read/m_user_read            ').'/index.php',
            trim('m_user_read_group         ')=>trim('../../read/m_user_read_group      ').'/index.php',
            trim('m_user_borrow             ')=>trim('../../read/m_user_borrow          ').'/index.php',
            trim('m_book_ranking            ')=>trim('../../read/m_book_ranking         ').'/index.php',
            trim('m_class_read_info         ')=>trim('../../read/m_class_read_info      ').'/index.php',

        //rec           書店
        //      m_user_rec                      學生推薦資料
        //      m_user_rec_book_best_class      班級優秀推薦
        //      m_class_rec_info                班級推薦統計
            trim('rec                       ')=>trim('javascript:void(0);               '),
            trim('m_user_rec                ')=>trim('../../rec/m_user_rec              ').'/index.php',
            trim('m_user_rec_book_best_class')=>trim('../../rec/m_user_rec_book_best_class').'/index.php',
            trim('m_class_rec_info          ')=>trim('../../rec/m_class_rec_info        ').'/index.php',

        //forum         聊書
        //      m_forum_user_info               學生相關資料
        //      m_forum_book_info               書籍相關資料
        //      m_forum_group_info              小組相關資料
        //      m_forum_article_info            學生聊書資料
        //      m_forum_info                    學生聊書資訊
        //      m_forum_hot_booklist            學生聊書票選
        //      m_forum_setting_class           聊書設定大頭貼與背景
        //      m_forum_setting_point           聊書設定發文點數
        //      m_class_forum_info              班級聊書統計
            trim('forum                     ')=>trim('javascript:void(0);               '),
            trim('m_forum_user_info         ')=>trim('../../forum/m_forum_user_info     ').'/index.php',
            trim('m_forum_book_info         ')=>trim('../../forum/m_forum_book_info     ').'/index.php',
            trim('m_forum_group_info        ')=>trim('../../forum/m_forum_group_info    ').'/index.php',
            trim('m_forum_article_info      ')=>trim('../../forum/m_forum_article_info  ').'/index.php',
            trim('m_forum_info              ')=>trim('../../forum/m_forum_info          ').'/index.php',
            trim('m_forum_hot_booklist      ')=>trim('../../forum/m_forum_hot_booklist  ').'/index.php',
            trim('m_forum_setting_class     ')=>trim('forum/m_forum_setting_class       ').'/index.php',
            trim('m_forum_setting_point     ')=>trim('forum/m_forum_setting_point       ').'/index.php',
            trim('m_class_forum_info        ')=>trim('../../forum/m_class_forum_info    ').'/index.php',

        //game          遊戲
            trim('game                      ')=>trim('javascript:void(0);               '),

        //assessment    評量
        //      m_pptv                          畢保德成績查詢
            trim('assessment                ')=>trim('javascript:void(0);               '),
            trim('m_pptv                    ')=>trim('../../assessment/m_pptv           ').'/index.php',

        //go_to_ctrl    前往老師管理
        //
            trim('go_to_ctrl                ')=>trim('/ta/teacher_manage.php            '),

        //back_to_front 前往明日閱讀
        //
            trim('back_to_front             ')=>trim('/mssr/service/mssr_menu.php       '),

        //user          學生管理
        //      m_user_group                    學生分組設定
        //      m_rec_book_draw_upload_report   推薦畫圖檢舉
        //      m_set_talk_declaration          管理招呼語及星球宣言
        //      m_transaction                   學生物品及交易紀錄
            trim('user                      ')=>trim('javascript:void(0);               '),
            trim('m_user_group              ')=>trim('user/m_user_group                 ').'/index.php',
            trim('m_rec_book_draw_upload_report')=>trim('user/m_rec_book_draw_upload_report').'/index.php',
            trim('m_set_talk_declaration    ')=>trim('user/m_set_talk_declaration       ').'/index.php',
            trim('m_transaction             ')=>trim('user/m_transaction                ').'/index.php',

        //category      類別管理
        //      m_category                      類別設定
            trim('category                  ')=>trim('javascript:void(0);               '),
            trim('m_category                ')=>trim('category/m_category               ').'/index.php',

        //book          書籍管理
        //      m_book                          書籍設定
        //      m_black_book                    書籍黑名單設定
        //      m_isbn_code_create              列印書籍條碼
        //      m_book_donor_detail             全校捐書者明細
        //      m_book_unverified_verified      學生自建書籍檢核
        //      m_book_borrow_search            書籍借閱人查詢
        //      m_book_unverified_indecent      不雅書籍檢核
            trim('book                              ')=>trim('javascript:void(0);               '),
            trim('m_book                            ')=>trim('book/m_book                       ').'/index.php',
            trim('m_black_book                      ')=>trim('book/m_black_book                 ').'/index.php',
            trim('m_isbn_code_create                ')=>trim('book/m_isbn_code_create           ').'/index.php',
            trim('m_book_donor_detail               ')=>trim('book/m_book_donor_detail          ').'/index.php',
            // trim('m_book_unverified_verified        ')=>trim('book/m_book_unverified_verified   ').'/index.php',
            trim('m_book_borrow_search              ')=>trim('book/m_book_borrow_search         ').'/index.php',
            // trim('m_book_unverified_indecent        ')=>trim('book/m_book_unverified_indecent   ').'/index.php',

        //bookstore     書店管理
        //      m_open_publish                  書店上架相關條件
        //      m_read_opinion_limit_day        閱讀登記期限條件
        //      m_borrow_limit_cno              閱讀登記數量限制
        //      m_rec_en_input                  推薦英文輸入條件
        //      m_rec_draw_open                 推薦畫圖開關
        //      m_coin_open                     書店葵幣開關
        //      m_open_publish_cno              書店上架本數條件
        //      m_over_school_view              是否開放跨校瀏覽
        //      m_registration_code_pwd         閱讀登記條碼版專用密碼
        //      m_registration_code_opinion     閱讀登記條碼版登記功能
            trim('bookstore                 ')=>trim('javascript:void(0);               '),
            trim('m_open_publish            ')=>trim('bookstore/m_open_publish          ').'/index.php',
            trim('m_read_opinion_limit_day  ')=>trim('bookstore/m_read_opinion_limit_day').'/index.php',
            trim('m_borrow_limit_cno        ')=>trim('bookstore/m_borrow_limit_cno      ').'/index.php',
            trim('m_rec_en_input            ')=>trim('bookstore/m_rec_en_input          ').'/index.php',
            trim('m_rec_draw_open           ')=>trim('bookstore/m_rec_draw_open         ').'/index.php',
            trim('m_coin_open               ')=>trim('bookstore/m_coin_open             ').'/index.php',
            trim('m_open_publish_cno        ')=>trim('bookstore/m_open_publish_cno      ').'/index.php',
            trim('m_over_school_view        ')=>trim('bookstore/m_over_school_view      ').'/index.php',
            trim('m_registration_code_pwd   ')=>trim('bookstore/m_registration_code_pwd ').'/index.php',
            trim('m_registration_code_opinion')=>trim('bookstore/m_registration_code_opinion').'/index.php',

        //user_info     專用功能
        //      m_teacher_rec                   教師快速登記書籍
        //      m_dw_browser                    下載瀏覽器
        //      m_mail                          電子郵件
            trim('user_info                 ')=>trim('javascript:void(0);               '),
            trim('m_teacher_rec             ')=>trim('user_info/m_teacher_rec           ').'/index.php',
            trim('m_dw_browser              ')=>trim('user_info/m_dw_browser            ').'/index.php',
            trim('m_mail                    ')=>trim('user_info/m_mail                  ').'/index.php',

        //export        資料匯出
        //      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
        //      m_user_borrow_export            學生學期借閱資料匯出
            trim('export                        ')=>trim('javascript:void(0);                   '),
            trim('m_user_borrow_library_export  ')=>trim('export/m_user_borrow_library_export   ').'/index.php',
            trim('m_user_borrow_export          ')=>trim('export/m_user_borrow_export           ').'/index.php'

    );

    return $auth_sys_url_arry;
}

function auth_sys_check($responsibilities,$mod_type){
//-------------------------------------------------------
//系統權限判斷
//-------------------------------------------------------
//參數
//-------------------------------------------------------
//$responsibilities 身分類型
//
//      1                               校長
//      2                               主任
//      3                               老師
//
//$mod_type         模組類型
//
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//
//root          教師端根目錄
//      index                           教師端首頁
//

    //檢核
    global $_SESSION;
    if((isset($_SESSION['tc']['t|dt']['arrys_class_code']))&&(!empty($_SESSION['tc']['t|dt']['arrys_class_code']))){
        $arrys_class_code=$_SESSION['tc']['t|dt']['arrys_class_code'];
    }else{
        $arrys_class_code=array();
    }

    if((!isset($responsibilities))||((int)($responsibilities)===0)){
        $err='AUTH_SYS_CHECK:RESPONSIBILITIES IS INVAILD';
        die($err);
    }else{
        $responsibilities=(int)$responsibilities;
    }


    //模組參數容許類型
    $mod_types=array(
        trim('m_user_read                   '),
        trim('m_user_read_group             '),
        trim('m_user_rec                    '),
        trim('m_user_rec_book_best_class    '),
        trim('m_class_rec_info              '),
        trim('m_forum_user_info             '),
        trim('m_forum_book_info             '),
        trim('m_forum_group_info            '),
        trim('m_forum_article_info          '),
        trim('m_forum_info                  '),
        trim('m_forum_hot_booklist          '),
        trim('m_class_forum_info            '),
        trim('m_forum_setting_class         '),
        trim('m_forum_setting_point         '),
        trim('m_user_borrow                 '),
        trim('m_pptv                        '),
        trim('m_user_group                  '),
        trim('m_rec_book_draw_upload_report '),
        trim('m_set_talk_declaration        '),
        trim('m_transaction                 '),
        trim('m_book_ranking                '),
        trim('m_class_read_info             '),
        trim('m_category                    '),
        trim('m_book                        '),
        trim('m_book_borrow_search          '),
        trim('m_book_unverified_indecent    '),
        trim('m_black_book                  '),
        trim('m_open_publish                '),
        trim('m_read_opinion_limit_day      '),
        trim('m_borrow_limit_cno            '),
        trim('m_rec_en_input                '),
        trim('m_rec_draw_open               '),
        trim('m_coin_open                   '),
        trim('m_open_publish_cno            '),
        trim('m_over_school_view            '),
        trim('m_teacher_rec                 '),
        trim('m_registration_code_pwd       '),
        trim('m_registration_code_opinion   '),
        trim('m_isbn_code_create            '),
        trim('m_book_donor_detail           '),
        trim('m_user_borrow_library_export  '),
        trim('m_book_unverified_verified    '),
        trim('m_dw_browser                  '),
        trim('m_mail                        '),
        trim('m_user_borrow_export          '),
        trim('index                         ')
    );
    if(isset($mod_type)&&trim($mod_type)!=''){
        if(!in_array($mod_type,$mod_types)){
            $err='AUTH_SYS_CHECK:MOD_TYPE IS INVAILD';
            die($err);
        }
    }else{
        $err='AUTH_SYS_CHECK:NO MOD_TYPE';
        die($err);
    }


    //設置身分等級判斷
    $lv=0;
    switch($responsibilities){
        case 1:
        //1 校長
            $lv=$lv+1;
            if((isset($_SESSION['tc'][$responsibilities]['arrys_class_code']))&&(!empty($_SESSION['tc'][$responsibilities]['arrys_class_code']))){
            //有帶班級
                if(count($_SESSION['tc'][$responsibilities]['arrys_class_code'])===1){
                //帶1個班級
                    $lv=$lv+19;
                }else{
                //帶多個班級
                    if(count($_SESSION['tc'][$responsibilities]['arrys_class_code'])>1){
                        $lv=$lv+23;
                    }
                }
            }
        break;

        case 2:
        //2 主任
            $lv=$lv+3;
            if((isset($_SESSION['tc'][$responsibilities]['arrys_class_code']))&&(!empty($_SESSION['tc'][$responsibilities]['arrys_class_code']))){
            //有帶班級
                if(count($_SESSION['tc'][$responsibilities]['arrys_class_code'])===1){
                //帶1個班級
                    $lv=$lv+11;
                }else{
                //帶多個班級
                    if(count($_SESSION['tc'][$responsibilities]['arrys_class_code'])>1){
                        $lv=$lv+13;
                    }
                }
            }
        break;

        case 3:
        //3 老師
            $lv=$lv+5;
            if(empty($arrys_class_code)){
            //沒有班級
                $lv=$lv+7;
            }else{
            //帶多個班級
                if(count($_SESSION['tc'][$responsibilities]['arrys_class_code'])>1){
                    $lv=$lv+17;
                }
            }
        break;

        case 99:
        //99 管理者
            $lv=$lv+99;
        break;

        default:
            $err='AUTH_SYS_CHECK:RESPONSIBILITIES IS INVAILD';
            die($err);
        break;
    }


    //依等級檢核許可陣列
    switch($lv){
        case 1:
        //校長
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                // trim('m_book_unverified_verified    '),
                // trim('m_book_unverified_indecent    '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('index                         '),
                trim('m_user_borrow_export          '),
                trim('m_transaction                 ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 20:
        //校長帶一個班
            $auth_sys_check=array(
                trim('m_user_group                  '),
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_borrow_limit_cno            '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_forum_hot_booklist          '),
                // trim('m_book_unverified_verified    '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_registration_code_opinion   '),
                trim('index                         '),
                trim('m_user_borrow_export          '),
                trim('m_transaction                 ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 24:
        //校長帶多個班
            $auth_sys_check=array(
                trim('m_user_group                  '),
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                // trim('m_book_unverified_verified    '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                trim('m_borrow_limit_cno            '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_book_borrow_search          '),
                trim('m_class_forum_info            '),
                trim('m_book                        '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_registration_code_opinion   '),
                trim('index                         '),
                trim('m_user_borrow_export          '),
                trim('m_transaction                 ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 3:
        //主任
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                // trim('m_book_unverified_verified    '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_book                        '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('index                         '),
                trim('m_book_borrow_search          '),
                trim('m_user_borrow_export          '),
                trim('m_transaction                 ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 5:
        //帶班老師
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                // trim('m_book_unverified_verified    '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_user_group                  '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_set_talk_declaration        '),
                trim('m_transaction                 '),
                trim('m_book_ranking                '),
                trim('m_borrow_limit_cno            '),
                trim('m_category                    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_open_publish                '),
                trim('m_read_opinion_limit_day      '),
                trim('m_rec_en_input                '),
                trim('m_rec_draw_open               '),
                trim('m_coin_open                   '),
                trim('m_open_publish_cno            '),
                trim('m_over_school_view            '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_registration_code_opinion   '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('index                         '),
                trim('m_user_borrow_export          ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 12:
        //行政老師
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                // trim('m_book_unverified_verified    '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('index                         ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 14:
        //主任帶一個班
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                // trim('m_book_unverified_verified    '),
                trim('m_borrow_limit_cno            '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_user_group                  '),
                trim('m_set_talk_declaration        '),
                trim('m_transaction                 '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_category                    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_open_publish                '),
                trim('m_read_opinion_limit_day      '),
                trim('m_rec_en_input                '),
                trim('m_rec_draw_open               '),
                trim('m_coin_open                   '),
                trim('m_open_publish_cno            '),
                trim('m_over_school_view            '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_registration_code_opinion   '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('index                         '),
                trim('m_user_borrow_export          ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 16:
        //主任帶多個班
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                // trim('m_book_unverified_verified    '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_borrow_limit_cno            '),
                trim('m_pptv                        '),
                trim('m_user_group                  '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_set_talk_declaration        '),
                trim('m_transaction                 '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_category                    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_open_publish                '),
                trim('m_read_opinion_limit_day      '),
                trim('m_rec_en_input                '),
                trim('m_rec_draw_open               '),
                trim('m_coin_open                   '),
                trim('m_open_publish_cno            '),
                trim('m_over_school_view            '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_registration_code_opinion   '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('index                         '),
                trim('m_user_borrow_export          ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 22:
        //老師帶多個班
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_pptv                        '),
                trim('m_rec_book_draw_upload_report '),
                // trim('m_book_unverified_verified    '),
                trim('m_user_group                  '),
                trim('m_set_talk_declaration        '),
                trim('m_transaction                 '),
                trim('m_book_ranking                '),
                trim('m_category                    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_borrow_limit_cno            '),
                trim('m_user_borrow_library_export  '),
                trim('m_open_publish                '),
                trim('m_read_opinion_limit_day      '),
                trim('m_rec_en_input                '),
                trim('m_rec_draw_open               '),
                trim('m_coin_open                   '),
                trim('m_open_publish_cno            '),
                trim('m_over_school_view            '),
                trim('m_registration_code_opinion   '),
                trim('m_teacher_rec                 '),
                trim('m_registration_code_pwd       '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('index                         '),
                trim('m_user_borrow_export          ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        case 99:
        //99 管理者
            $auth_sys_check=array(
                trim('m_user_read                   '),
                trim('m_user_read_group             '),
                trim('m_user_rec                    '),
                trim('m_user_rec_book_best_class    '),
                trim('m_forum_user_info             '),
                trim('m_forum_book_info             '),
                trim('m_forum_group_info            '),
                trim('m_forum_article_info          '),
                trim('m_forum_info                  '),
                trim('m_forum_hot_booklist          '),
                trim('m_user_borrow                 '),
                trim('m_forum_setting_class         '),
                trim('m_forum_setting_point         '),
                trim('m_pptv                        '),
                trim('m_rec_book_draw_upload_report '),
                trim('m_user_group                  '),
                trim('m_registration_code_opinion   '),
                trim('m_set_talk_declaration        '),
                trim('m_transaction                 '),
                trim('m_book_ranking                '),
                trim('m_class_read_info             '),
                trim('m_class_rec_info              '),
                trim('m_class_forum_info            '),
                trim('m_category                    '),
                trim('m_book                        '),
                trim('m_book_borrow_search          '),
                trim('m_black_book                  '),
                trim('m_book_donor_detail           '),
                trim('m_user_borrow_library_export  '),
                trim('m_borrow_limit_cno            '),
                trim('m_book_unverified_verified    '),
                trim('m_book_unverified_indecent    '),
                trim('m_isbn_code_create            '),
                trim('m_dw_browser                  '),
                trim('m_mail                        '),
                trim('index                         '),
                trim('m_user_borrow_export          ')
            );
            if(in_array($mod_type,$auth_sys_check)){
                return $lv;
            }else{
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
                return false;
            }
        break;

        default:
            $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    history.back(-1);
                </script>
            ";
            die($jscript_back);
            return false;
        break;
    }
}

function auth_sys_diff_lv($mod_type){
//-------------------------------------------------------
//系統難易層級
//-------------------------------------------------------
//參數
//-------------------------------------------------------
//$mod_type         模組類型
//
//read          閱讀
//      m_user_read                     學生登記資料
//      m_user_read_group               學生總閱讀數量
//      m_user_borrow                   學生借閱資料
//      m_book_ranking                  書籍借閱排行榜
//      m_class_read_info               班級閱讀統計
//
//rec           書店
//      m_user_rec                      學生推薦資料
//      m_user_rec_book_best_class      班級優秀推薦
//      m_class_rec_info                班級推薦統計
//
//forum         聊書
//      m_forum_user_info               學生相關資料
//      m_forum_book_info               書籍相關資料
//      m_forum_group_info              小組相關資料
//      m_forum_article_info            學生聊書資料
//      m_forum_info                    學生聊書資訊
//      m_forum_hot_booklist            學生聊書票選
//      m_forum_setting_class           聊書設定大頭貼與背景
//      m_forum_setting_point           聊書設定發文點數
//      m_class_forum_info              班級聊書統計
//
//game          遊戲
//
//assessment    評量
//      m_pptv                          畢保德成績查詢
//
//go_to_ctrl    前往老師管理
//
//back_to_front 前往明日閱讀
//
//user          學生管理
//      m_user_group                    學生分組設定
//      m_rec_book_draw_upload_report   推薦畫圖檢舉
//      m_set_talk_declaration          管理招呼語及星球宣言
//      m_transaction                   學生物品及交易紀錄
//
//category      類別管理
//      m_category                      類別設定
//
//book          書籍管理
//      m_book                          書籍設定
//      m_black_book                    書籍黑名單設定
//      m_isbn_code_create              列印書籍條碼
//      m_book_donor_detail             全校捐書者明細
//      m_book_unverified_verified      學生自建書籍檢核
//      m_book_borrow_search            書籍借閱人查詢
//      m_book_unverified_indecent      不雅書籍檢核
//
//bookstore     書店管理
//      m_open_publish                  書店上架相關條件
//      m_read_opinion_limit_day        閱讀登記期限條件
//      m_borrow_limit_cno              閱讀登記數量限制
//      m_rec_en_input                  推薦英文輸入條件
//      m_rec_draw_open                 推薦畫圖開關
//      m_coin_open                     書店葵幣開關
//      m_open_publish_cno              書店上架本數條件
//      m_over_school_view              是否開放跨校瀏覽
//      m_registration_code_pwd         閱讀登記條碼版專用密碼
//      m_registration_code_opinion     閱讀登記條碼版登記功能
//
//user_info     專用功能
//      m_teacher_rec                   教師快速登記書籍
//      m_dw_browser                    下載瀏覽器
//      m_mail                          電子郵件
//
//export        資料匯出
//      m_user_borrow_library_export    教育部全國閱讀推動與圖書管理系統資料上傳
//      m_user_borrow_export            學生學期借閱資料匯出
//
//root          教師端根目錄
//      index                           教師端首頁
//

    //模組參數容許類型
    $mod_types=array(
        trim('m_user_read                   '),
        trim('m_user_read_group             '),
        trim('m_rec_book_draw_upload_report '),
        trim('m_user_rec                    '),
        trim('m_user_rec_book_best_class    '),
        trim('m_forum_user_info             '),
        trim('m_forum_book_info             '),
        trim('m_forum_group_info            '),
        trim('m_forum_setting_class         '),
        trim('m_forum_setting_point         '),
        trim('m_forum_article_info          '),
        trim('m_forum_info                  '),
        trim('m_forum_hot_booklist          '),
        trim('m_class_forum_info            '),
        trim('m_user_borrow                 '),
        trim('m_pptv                        '),
        trim('m_user_group                  '),
        trim('m_set_talk_declaration        '),
        trim('m_transaction                 '),
        trim('m_book_ranking                '),
        trim('m_class_read_info             '),
        trim('m_class_rec_info              '),
        trim('m_category                    '),
        trim('m_book                        '),
        trim('m_book_borrow_search          '),
        trim('m_black_book                  '),
        trim('m_open_publish                '),
        trim('m_read_opinion_limit_day      '),
        trim('m_borrow_limit_cno            '),
        trim('m_rec_en_input                '),
        trim('m_rec_draw_open               '),
        trim('m_coin_open                   '),
        trim('m_open_publish_cno            '),
        trim('m_over_school_view            '),
        trim('m_teacher_rec                 '),
        trim('m_registration_code_pwd       '),
        trim('m_registration_code_opinion   '),
        trim('m_isbn_code_create            '),
        trim('m_book_donor_detail           '),
        trim('m_user_borrow_library_export  '),
        trim('m_book_unverified_verified    '),
        trim('m_book_unverified_indecent    '),
        trim('m_dw_browser                  '),
        trim('m_mail                        '),
        trim('m_user_borrow_export          '),
        trim('index                         ')
    );
    if(isset($mod_type)&&trim($mod_type)!=''){
        if(!in_array($mod_type,$mod_types)){
            $err='AUTH_SYS_DIFF_LV:MOD_TYPE IS INVAILD';
            die($err);
        }
    }else{
        $err='AUTH_SYS_DIFF_LV:NO MOD_TYPE';
        die($err);
    }

    //系統難易層級分類
    $arry_sys_diff_lv=array();
    $arry_sys_diff_lv[trim('m_user_read                     ')]=1;
    $arry_sys_diff_lv[trim('m_user_read_group               ')]=1;
    $arry_sys_diff_lv[trim('m_user_rec                      ')]=1;
    $arry_sys_diff_lv[trim('m_user_rec_book_best_class      ')]=1;
    $arry_sys_diff_lv[trim('m_forum_user_info               ')]=1;
    $arry_sys_diff_lv[trim('m_forum_book_info               ')]=1;
    $arry_sys_diff_lv[trim('m_forum_group_info              ')]=1;
    $arry_sys_diff_lv[trim('m_forum_article_info            ')]=1;
    $arry_sys_diff_lv[trim('m_forum_info                    ')]=1;
    $arry_sys_diff_lv[trim('m_forum_hot_booklist            ')]=1;
    $arry_sys_diff_lv[trim('m_user_borrow                   ')]=1;
    $arry_sys_diff_lv[trim('m_pptv                          ')]=1;
    $arry_sys_diff_lv[trim('m_forum_setting_class           ')]=1;
    $arry_sys_diff_lv[trim('m_forum_setting_point           ')]=1;
    $arry_sys_diff_lv[trim('m_book_ranking                  ')]=1;
    $arry_sys_diff_lv[trim('m_class_read_info               ')]=1;
    $arry_sys_diff_lv[trim('m_class_forum_info              ')]=1;
    $arry_sys_diff_lv[trim('m_class_rec_info                ')]=1;
    $arry_sys_diff_lv[trim('m_user_group                    ')]=1;
    $arry_sys_diff_lv[trim('m_book                          ')]=1;
    $arry_sys_diff_lv[trim('m_black_book                    ')]=1;
    $arry_sys_diff_lv[trim('m_open_publish                  ')]=1;
    $arry_sys_diff_lv[trim('m_read_opinion_limit_day        ')]=1;
    $arry_sys_diff_lv[trim('m_rec_en_input                  ')]=1;
    $arry_sys_diff_lv[trim('m_open_publish_cno              ')]=1;
    $arry_sys_diff_lv[trim('m_teacher_rec                   ')]=1;
    $arry_sys_diff_lv[trim('m_isbn_code_create              ')]=1;
    $arry_sys_diff_lv[trim('m_book_unverified_indecent      ')]=1;
    $arry_sys_diff_lv[trim('m_user_borrow_library_export    ')]=1;
    $arry_sys_diff_lv[trim('index                           ')]=1;

    $arry_sys_diff_lv[trim('m_book_borrow_search            ')]=2;
    $arry_sys_diff_lv[trim('m_rec_book_draw_upload_report   ')]=2;
    $arry_sys_diff_lv[trim('m_borrow_limit_cno              ')]=2;
    $arry_sys_diff_lv[trim('m_over_school_view              ')]=2;
    $arry_sys_diff_lv[trim('m_registration_code_pwd         ')]=2;
    $arry_sys_diff_lv[trim('m_registration_code_opinion     ')]=2;
    $arry_sys_diff_lv[trim('m_set_talk_declaration          ')]=2;
    $arry_sys_diff_lv[trim('m_transaction                   ')]=2;
    $arry_sys_diff_lv[trim('m_category                      ')]=2;
    $arry_sys_diff_lv[trim('m_rec_draw_open                 ')]=2;
    $arry_sys_diff_lv[trim('m_coin_open                     ')]=2;
    $arry_sys_diff_lv[trim('m_book_unverified_verified      ')]=2;
    $arry_sys_diff_lv[trim('m_dw_browser                    ')]=2;
    $arry_sys_diff_lv[trim('m_mail                          ')]=2;
    $arry_sys_diff_lv[trim('m_user_borrow_export            ')]=2;
    $arry_sys_diff_lv[trim('m_book_donor_detail             ')]=2;

    //回傳
    return $arry_sys_diff_lv[$mod_type];
}