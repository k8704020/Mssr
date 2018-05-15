<?php
//-------------------------------------------------------
//函式: get_cht_chnnum()
//用途: 取得中文字筆畫
//日期: 2014年9月10日
//作者: peter@cl.ncu
//-------------------------------------------------------

    function get_cht_chnnum($string){
    //---------------------------------------------------
    //取得中文字筆畫(字串)
    //---------------------------------------------------
    //$string   字串變數
    //本函式將會回傳筆畫數
    //---------------------------------------------------

        //UTF-8的話先轉成BIG5
        $string = iconv('UTF-8','BIG5//IGNORE',addslashes($string));
        if(strlen($string) != 2)return false;
        if(ord($string[0]) <0xA1)return false;
        $code = hexdec(bin2hex($string));
        $StrokeArray=Array(
            Array(1 ,0xA440,0xA441),    ## 常用字區
            Array(2 ,0xA442,0xA453),
            Array(3 ,0xA454,0xA47E),
            Array(4 ,0xA4A1,0xA4FD),
            Array(5 ,0xA4FE,0xA5DF),
            Array(6 ,0xA5E0,0xA6E9),
            Array(7 ,0xA6EA,0xA8C2),
            Array(8 ,0xA8C3,0xAB44),
            Array(9 ,0xAB45,0xADBB),
            Array(10,0xADBC,0xB0AD),
            Array(11,0xB0AE,0xB3C2),
            Array(12,0xB3C3,0xB6C3),
            Array(13,0xB6C4,0xB9AB),
            Array(14,0xB9AC,0xBBF4),
            Array(15,0xBBF5,0xBEA6),
            Array(16,0xBEA7,0xC074),
            Array(17,0xC075,0xC24E),
            Array(18,0xC24F,0xC35E),
            Array(19,0xC35F,0xC454),
            Array(20,0xC455,0xC4D6),
            Array(21,0xC3D7,0xC56A),
            Array(22,0xC56B,0xC5C7),
            Array(23,0xC5C8,0xC5C7),
            Array(24,0xC5F1,0xC654),
            Array(25,0xC655,0xC664),
            Array(26,0xC665,0xC66B),
            Array(27,0xC66C,0xC675),
            Array(28,0xC676,0xC67A),
            Array(29,0xF9C7,0xF9CB),
            Array(2 ,0xC940,0xC944),    ## 次常用字區I
            Array(3 ,0xC945,0xC94C),
            Array(4 ,0xC94D,0xC95C),
            Array(5 ,0xC95D,0xC9AA),
            Array(6 ,0xC9AB,0xC959),
            Array(7 ,0xCA5A,0xCBB0),
            Array(8 ,0xCBB1,0xCDDC),
            Array(9 ,0xCDDD,0xD0C7),
            Array(10,0xD0C8,0xD44A),
            Array(11,0xD44B,0xD850),
            Array(12,0xD851,0xDCB0),
            Array(13,0xDCB1,0xE0EF),
            Array(14,0xE0F0,0xE4E5),
            Array(15,0xE4E6,0xE8F3),
            Array(16,0xE8F4,0xECB8),
            Array(17,0xECB9,0xEFB6),
            Array(18,0xEFB7,0xF1EA),
            Array(19,0xF1EB,0xF3FC),
            Array(20,0xF3FD,0xF5BF),
            Array(21,0xF5C0,0xF6D5),
            Array(22,0xF6D6,0xF7CF),
            Array(23,0xF6D6,0xF7CF),
            Array(24,0xF8A5,0xF8ED),
            Array(25,0xF8E9,0xF96A),
            Array(26,0xF96B,0xF9A1),
            Array(27,0xF9A2,0xF9B9),
            Array(28,0xF9BA,0xF9C5),
            Array(29,0xF9C6,0xF9DC),
            Array(9 ,0xF9DA,0xF9DA),    ## 次常用字區II
            Array(12,0xF9DB,0xF9DB),
            Array(13,0xF9D6,0xF9D8),
            Array(15,0xF9DC,0xF9DC),
            Array(16,0xF9D9,0xF9D9),
            Array(30,0xC67B,0xC67D),    ## 次常用字區III修正碼
            Array(30,0xF9CC,0xF9CF),
            Array(31,0xF9C6,0xF9C6),
            Array(31,0xF9D0,0xF9D0),
            Array(32,0xF9D1,0xF9D1),
            Array(33,0xC67E,0xC67E),
            Array(33,0xF9D2,0xF9D2),
            Array(34,0xF9D3,0xF9D3),
            Array(36,0xF9D4,0xF9D5)
        );

        for($i = 0; $i < count($StrokeArray); $i++){
            if($StrokeArray[$i][1] <= $code and $StrokeArray[$i][2] >= $code){
                return $StrokeArray[$i][0];
            }
        }
    }
?>

