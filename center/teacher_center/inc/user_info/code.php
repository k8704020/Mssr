<?php
//-------------------------------------------------------
//book
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  user_info/open_publish/update_open_publish()                        更新上架條件
//  user_info/teacher_rec/book_borrow_school_rev()                      借閱書學校關聯
//  user_info/read_opinion_limit_day/update_read_opinion_limit_day()    更新閱讀登記期限條件
//  user_info/registration_code_pwd/update_registration_code_pwd()      更新閱讀登記條碼版專用密碼

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','open_publish              /code.php'));
        require_once(preg_replace('/\s+/','','teacher_rec               /code.php'));
        require_once(preg_replace('/\s+/','','read_opinion_limit_day    /code.php'));
        require_once(preg_replace('/\s+/','','registration_code_pwd     /code.php'));
?>