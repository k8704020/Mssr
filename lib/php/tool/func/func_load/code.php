<?php
//-------------------------------------------------------
//函式: func_load()
//用途: 動態載入函式
//日期: 2011年10月29日
//作者: jeff@max-life
//-------------------------------------------------------

    function func_load($arr,$debug=false){
    //---------------------------------------------------
    //動態載入函式
    //---------------------------------------------------
    //$arr      函式名稱陣列(名稱不用指定.php)
    //$debug    啟用除錯模式(如啟用,發生錯誤時,會列出錯誤)
    //---------------------------------------------------

        $errs=array();

        if(isset($arr)&&!empty($arr)){

            foreach($arr as $name){
                $name=$name.'.php';

                if(file_exists($name)){
                    require_once("{$name}");
                }else{
                    $errs[]=$name.' '.'載入失敗,指定檔案不存在!';
                }
            }
        }

        //判斷成功與否
        if(!empty($errs)){

            //除錯模式
            if($debug===true){
                echo '<pre>';
                print_r($errs);
                echo "</pre>";

                die('FUNC_LOAD:FAIL');
            }else{
                die('FUNC_LOAD:FAIL');
            }
        }
    }
?>