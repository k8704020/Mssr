<?php
//-------------------------------------------------------
//require       外掛頁面
//-------------------------------------------------------
//  require/carousel()      廣告牆
//  require/footbar()       註腳列
//  require/meta()          標籤
//  require/navbar()        導覽列
//  require/modal_dialog()  模態框
//  require/right_side()    側邊欄資訊
//  require/wall()          動態牆資訊
//
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','carousel      /code.php'));
        require_once(preg_replace('/\s+/','','footbar       /code.php'));
        require_once(preg_replace('/\s+/','','meta          /code.php'));
        require_once(preg_replace('/\s+/','','navbar        /code.php'));
        require_once(preg_replace('/\s+/','','modal_dialog  /code.php'));
        require_once(preg_replace('/\s+/','','right_side    /code.php'));
        require_once(preg_replace('/\s+/','','wall          /code.php'));
?>