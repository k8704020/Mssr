<?php
//-------------------------------------------------------
//func list
//-------------------------------------------------------
//  external/get_mssr_grade_avg_info()                      取得全學年平均積分資訊
//  external/get_mssr_user_avg_info()                       取得學生平均積分資訊
//  external/get_class_code_comment_frequency_info()        取得班級平均指導次數
//  external/get_class_code_read_group_info()               取得班級平均閱讀本數
//  external/get_class_code_rec_group_info()                取得班級平均推薦本數
//  external/school_usage_info()                            取得學校使用率
//  external/get_class_user_read_frequency_detail_info()    取得班級人員閱讀次數
//  external/get_class_user_read_group_detail_info()        取得班級人員閱讀本數
//  external/get_class_user_rec_group_detail_info()         取得班級人員推薦本數
//  external/mobile_borrow()                                行動裝置閱讀登記
//  external/mssr_tool()                                    明日書店 外掛函式庫說明

      //---------------------------------------------------
      //設定與引用
      //---------------------------------------------------

        //外掛函式檔
        $_funcs=array(
            trim('get_class_code_comment_frequency_info     '),
            trim('get_class_code_read_group_info            '),
            trim('get_class_code_rec_group_info             '),
            trim('mssr_tool                                 '),
            trim('get_mssr_grade_avg_info                   '),
            trim('school_usage_info                         '),
            trim('get_class_user_read_frequency_detail_info '),
            trim('get_class_user_read_group_detail_info     '),
            trim('get_class_user_rec_group_detail_info      '),
            trim('get_mssr_user_avg_info                    '),
            trim('mobile_borrow                             ')
        );

        foreach($_funcs as $inx=>$_func){
            if(!function_exists("{$_func}")){
                require_once("{$_func}/code.php");
            }
        }
?>