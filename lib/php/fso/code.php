<?php
//-------------------------------------------------------
//fso
//-------------------------------------------------------
//bytes_to()            bytes轉其他單位
//copy_folder()         複製目錄
//dir_eregfiles()       取得目錄下檔案陣列,採用正規式篩選
//dir_files()           取得目錄下檔案陣列
//fcsv_to_array()       載入csv檔,並傳回資料陣列
//file_fso_encode()     轉換路徑成檔案系統編碼
//file_putcontents()    寫檔
//fso_chmod()           檔案目錄變更權限
//fso_chmod_recursive() 檔案目錄變更權限,遞回變更
//fso_isunder()         是否在指定路徑下
//fso_rename()          檔案目錄更名,採數值序列方式命名
//fsolists()            檔案目錄清單
//fsolists_recursive()  檔案目錄清單,回遞所有結構
//getfile_content()     載入檔案
//load_filetemplate()   載入樣板
//mk_dir()              建立目錄
//mk_dir_ftp()          建立ftp目錄
//rm_dir()              刪除目錄
//size_to()             單位互轉
//to_bytes()            其他單位轉bytes
//to_fso_encode()       轉換為檔案系統語系編碼
//to_page_encode()      轉換為頁面語系編碼
//dir_size()            取得目錄容量,回遞所有結構

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //外掛函式檔
        require_once(preg_replace('/\s+/','','func/bytes_to           /code.php'));
        require_once(preg_replace('/\s+/','','func/copy_folder        /code.php'));
        require_once(preg_replace('/\s+/','','func/dir_eregfiles      /code.php'));
        require_once(preg_replace('/\s+/','','func/dir_files          /code.php'));
        require_once(preg_replace('/\s+/','','func/fcsv_to_array      /code.php'));
        require_once(preg_replace('/\s+/','','func/file_fso_encode    /code.php'));
        require_once(preg_replace('/\s+/','','func/file_putcontents   /code.php'));
        require_once(preg_replace('/\s+/','','func/fso_chmod          /code.php'));
        require_once(preg_replace('/\s+/','','func/fso_chmod_recursive/code.php'));
        require_once(preg_replace('/\s+/','','func/fso_isunder        /code.php'));
        require_once(preg_replace('/\s+/','','func/fso_rename         /code.php'));
        require_once(preg_replace('/\s+/','','func/fsolists           /code.php'));
        require_once(preg_replace('/\s+/','','func/fsolists_recursive /code.php'));
        require_once(preg_replace('/\s+/','','func/getfile_content    /code.php'));
        require_once(preg_replace('/\s+/','','func/load_filetemplate  /code.php'));
        require_once(preg_replace('/\s+/','','func/mk_dir             /code.php'));
        require_once(preg_replace('/\s+/','','func/mk_dir_ftp         /code.php'));
        require_once(preg_replace('/\s+/','','func/rm_dir             /code.php'));
        require_once(preg_replace('/\s+/','','func/size_to            /code.php'));
        require_once(preg_replace('/\s+/','','func/to_bytes           /code.php'));
        require_once(preg_replace('/\s+/','','func/to_fso_encode      /code.php'));
        require_once(preg_replace('/\s+/','','func/to_page_encode     /code.php'));
        require_once(preg_replace('/\s+/','','func/dir_size           /code.php'));
?>