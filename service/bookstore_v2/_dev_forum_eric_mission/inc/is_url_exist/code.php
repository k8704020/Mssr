<?php
//-------------------------------------------------------
//函式: is_url_exist()
//用途: 確認網址是否存在
//-------------------------------------------------------

    function is_url_exist($url){
    //---------------------------------------------------
    //函式: is_url_exist()
    //用途: 確認網址是否存在
    //---------------------------------------------------
    //$url  網址
    //---------------------------------------------------

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }

        curl_close($ch);
        return $status;
    }
?>