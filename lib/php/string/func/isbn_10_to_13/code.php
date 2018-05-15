<?php
//-------------------------------------------------------
//函式: isbn_10_to_13()
//用途: 國際標準書號轉碼(10碼轉13碼)
//日期: 2013年08月25日
//作者: tim@cl_ncu
//-------------------------------------------------------

    function isbn_10_to_13($isbn_code){
    //---------------------------------------------------
    //國際標準書號轉碼(10碼轉13碼)
    //---------------------------------------------------
    //$isbn_code    ISBN 10碼
    //---------------------------------------------------

        switch (strlen($isbn_code)){
            case 10:
            case 13:
                $isbn_code = substr($isbn_code, 0, strlen($isbn_code)-1);
                if(strlen($isbn_code)==12)break;
            case 9:
                $isbn_code = "978".$isbn_code;
                //case 12:break;
        }
        $codeArr=str_split($isbn_code);
        $code = "";

        $i=1;
        $c=0;     // c:checksum
        for(;$i<12;$i+=2)$c=$c+$codeArr[$i];
        $c*=3;
        for($i=0;$i<12;$i+=2)$c=$c+$codeArr[$i];
        $a = (220-$c)%10;
        return ($isbn_code.$a);
    }
?>