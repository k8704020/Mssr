<?php
//-------------------------------------------------------
//函式: book_name_comparison()
//用途: 書名比對
//-------------------------------------------------------

    ////-------------------------------------------------
    ////測試資料
    ////-------------------------------------------------
    //
    //    //設定頁面語系
    //    header("Content-Type: text/html; charset=UTF-8");
    //
    //    //設定文字內部編碼
    //    mb_internal_encoding("UTF-8");
    //
    //    //設定台灣時區
    //    date_default_timezone_set('Asia/Taipei');
    //
    //    $name_1='小王子 中.英.法對照 精裝珍藏版(附情境配樂 中‧英‧法朗讀MP3 + 紀念書籤)';
    //    $name_2='小王子【70周年精裝紀念版】';
    //    $name_3='小王子';
    //    $name_4='北小王子中';
    //    $name_5='小王子The Little Prince';
    //    $name_6='Le Petit Prince小王子行星漫遊著色本(中英文版)';
    //
    //    $book_name_1=$name_5;
    //    $book_name_2=$name_6;
    //
    //    echo "<Pre>";
    //    print_r(book_name_comparison($book_name_1,$book_name_2));
    //    echo "</Pre>";
    //    die();

    function book_name_comparison($book_name_1='',$book_name_2=''){
    //---------------------------------------------------
    //函式: book_name_comparison()
    //用途: 書名比對
    //---------------------------------------------------
    //$book_name_1      書名1
    //$book_name_2      書名2
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(trim($book_name_1==='')||!is_string($book_name_1) || !isset($book_name_1)){
                return false;
            }else{
                $book_name_1=trim($book_name_1);
            }

            if(trim($book_name_2==='')||!is_string($book_name_2) || !isset($book_name_2)){
                return false;
            }else{
                $book_name_2=trim($book_name_2);
            }

        //-----------------------------------------------
        //參數分析
        //-----------------------------------------------

            //去標點符號
            $book_name_1=str_replace(
            array('!', '"', '#', '$', '%', '&', '\'', '(', ')', '*',
                '+', ', ', '-', '.', '/', ':', ';', '<', '=', '>',
                '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|',
                '}', '~', '；', '﹔', '︰', '﹕', '：', '，', '﹐', '、',
                '．', '﹒', '˙', '·', '。', '？', '！', '～', '‥', '‧',
                '′', '〃', '〝', '〞', '‵', '‘', '’', '『', '』', '「',
                '」', '“', '”', '…', '❞', '❝', '﹁', '﹂', '﹃', '﹄', '【', '】', '《', '》'),
                '',
            $book_name_1);

            $book_name_2=str_replace(
            array('!', '"', '#', '$', '%', '&', '\'', '(', ')', '*',
                '+', ', ', '-', '.', '/', ':', ';', '<', '=', '>',
                '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|',
                '}', '~', '；', '﹔', '︰', '﹕', '：', '，', '﹐', '、',
                '．', '﹒', '˙', '·', '。', '？', '！', '～', '‥', '‧',
                '′', '〃', '〝', '〞', '‵', '‘', '’', '『', '』', '「',
                '」', '“', '”', '…', '❞', '❝', '﹁', '﹂', '﹃', '﹄', '【', '】', '《', '》'),
                '',
            $book_name_2);

            $is_en_str_name_1=false;
            $is_en_str_name_2=false;
            $comparison      =false;

            $arry_book_name_1=array();
            $arry_book_name_2=array();

            if((int)preg_match("/[a-zA-Z]/i", $book_name_1)===1)$is_en_str_name_1=true;
            if((int)preg_match("/[a-zA-Z]/i", $book_name_2)===1)$is_en_str_name_2=true;

//echo "<Pre>";
//print_r($book_name_1);
//echo "</Pre>";
//echo "<Pre>";
//print_r($book_name_2);
//echo "</Pre>";
//echo "<Pre>";
//print_r($is_en_str_name_1);
//echo "</Pre>";

            if(!$is_en_str_name_1){
                for($i=1;$i<=mb_strlen($book_name_1);$i++){
                    $rs_book_name=mb_substr($book_name_1,$i-1,1);
                    $arry_book_name_1[]=trim($rs_book_name);
                }
            }else{
                $tmp_arry_book_name_1=explode(" ",$book_name_1);
                foreach($tmp_arry_book_name_1 as $rs_book_name){
                    $rs_book_name=strtolower(trim($rs_book_name));
                    if((int)preg_match("/[^a-zA-Z]/i", $rs_book_name)===1){
                        $rs_book_name=preg_replace("/[a-zA-Z]/i","",$rs_book_name);
                    }
                    $arry_book_name_1[]=$rs_book_name;
                }
            }

            if(!$is_en_str_name_1){
                for($i=1;$i<=mb_strlen($book_name_2);$i++){
                    $rs_book_name=mb_substr($book_name_2,$i-1,1);
                    $arry_book_name_2[]=trim($rs_book_name);
                }
            }else{
                $tmp_arry_book_name_2=explode(" ",$book_name_2);
                foreach($tmp_arry_book_name_2 as $rs_book_name){
                    $rs_book_name=strtolower(trim($rs_book_name));
                    if((int)preg_match("/[^a-zA-Z]/i", $rs_book_name)===1){
                        $rs_book_name=preg_replace("/[a-zA-Z]/i","",$rs_book_name);
                    }
                    $arry_book_name_2[]=$rs_book_name;
                }
            }

            //去空
            $arry_book_name_1=array_diff($arry_book_name_1, array(null,'null','',' '));
            $arry_book_name_2=array_diff($arry_book_name_2, array(null,'null','',' '));

//echo "<Pre>";
//print_r($arry_book_name_1);
//echo "</Pre>";
//echo "<Pre>";
//print_r($arry_book_name_2);
//echo "</Pre>";

        //-----------------------------------------------
        //參數比對
        //-----------------------------------------------

            $arry_intersect_book_name=array_intersect($arry_book_name_1,$arry_book_name_2);

//echo "<Pre>";
//print_r($arry_intersect_book_name);
//echo "</Pre>";
//echo "<Pre>";
//print_r(count($arry_intersect_book_name));
//echo "</Pre>";
//echo "<Pre>";
//print_r(($book_name_1)." ".count($arry_book_name_1));
//echo "</Pre>";
//echo "<Pre>";
//print_r(($book_name_2)." ".count($arry_book_name_2));
//echo "</Pre>";

            if(count($arry_book_name_1)<=3 && count($arry_book_name_2)<=3){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*1))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*1))$comparison=true;

            }elseif(count($arry_book_name_1)<=3 && count($arry_book_name_2)===4){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*1))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)<=3 && count($arry_book_name_2)===5){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*1))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)<=3 && count($arry_book_name_2)===6){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*1))$comparison=true;
                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)<=3 && count($arry_book_name_2)>=7){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*1))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.5))$comparison=true;

            }elseif(count($arry_book_name_1)==4 && count($arry_book_name_2)<=3){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*1))$comparison=true;

            }elseif(count($arry_book_name_1)==4 && count($arry_book_name_2)===4){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==4 && count($arry_book_name_2)===5){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==4 && count($arry_book_name_2)===6){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==4 && count($arry_book_name_2)>=7){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.5))$comparison=true;

            }elseif(count($arry_book_name_1)==5 && count($arry_book_name_2)<=3){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*1))$comparison=true;

            }elseif(count($arry_book_name_1)==5 && count($arry_book_name_2)===4){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==5 && count($arry_book_name_2)===5){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==5 && count($arry_book_name_2)===6){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==5 && count($arry_book_name_2)>=7){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.5))$comparison=true;

            }elseif(count($arry_book_name_1)==6 && count($arry_book_name_2)<=3){

                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*1))$comparison=true;

            }elseif(count($arry_book_name_1)==6 && count($arry_book_name_2)===4){

                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==6 && count($arry_book_name_2)===5){

                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==6 && count($arry_book_name_2)===6){

                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)==6 && count($arry_book_name_2)>=7){

                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_1)*0.75))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.5))$comparison=true;

            }elseif(count($arry_book_name_1)>=7 && count($arry_book_name_2)<=3){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.5))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*1))$comparison=true;

            }elseif(count($arry_book_name_1)>=7 && count($arry_book_name_2)===4){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.5))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)>=7 && count($arry_book_name_2)===5){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.5))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)>=7 && count($arry_book_name_2)===6){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.5))$comparison=true;
                if(count($arry_intersect_book_name)>=floor(count($arry_book_name_2)*0.75))$comparison=true;

            }elseif(count($arry_book_name_1)>=7 && count($arry_book_name_2)>=7){

                if(count($arry_intersect_book_name)>=round(count($arry_book_name_1)*0.5))$comparison=true;
                if(count($arry_intersect_book_name)>=round(count($arry_book_name_2)*0.5))$comparison=true;

            }else{}

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------
//echo "<Pre>";
//print_r($comparison);
//echo "</Pre>";
            return $comparison;
    }
?>