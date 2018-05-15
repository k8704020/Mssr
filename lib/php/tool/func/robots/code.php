<?php
//-------------------------------------------------------
//函式: robots()
//用途: 防止網站登錄
//日期: 2011年10月29日
//作者: jeff@max-life
//-------------------------------------------------------

    function robots($allow=false){
    //---------------------------------------------------
    //防止網站登錄
    //---------------------------------------------------
    //$allow    true | false (預設)
    //---------------------------------------------------

        if(!isset($allow)||(trim($allow)==='')){
            $allow=false;
        }

        $nl ="\r\n";
        $tab="\t";
        if(!$allow){
            echo "<meta name='robots' content='noindex,nofollow,nosnippet,noarchive'>{$nl}";
        }else{
            echo "<meta name='robots' content='index,follow,snippet,archive'>{$nl}";
        }
    }
?>