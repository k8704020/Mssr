<?php
//-------------------------------------------------------
//函式: isbn_13_to_10()
//用途: 國際標準書號轉碼(13碼轉10碼)
//日期: 2013年08月25日
//作者: tim@cl_ncu
//-------------------------------------------------------

    function isbn_13_to_10($isbn_code){
    //---------------------------------------------------
    //國際標準書號轉碼(13碼轉10碼)
    //---------------------------------------------------
    //$isbn_code    ISBN 13碼
    //---------------------------------------------------

        switch (strlen($isbn_code)){
            case 13:
            case 12:
                $isbn_code = substr($isbn_code, 3, strlen($isbn_code));
                if(strlen($isbn_code)==9)break;
            case 10:
                $isbn_code = substr($isbn_code, 0, strlen($isbn_code)-1);
                //case 9:	break;
        }
        $codeArr=str_split($isbn_code);
        $code = "";
        $i=0;
        $c=0;     // c:checksum
        for(;$i<9;)$c=$c+($codeArr[$i++]*$i);
        $c%=11;
        if($c==10)$c='X';
        return ($isbn_code.$c);
    }
?>