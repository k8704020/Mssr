<?php
//-------------------------------------------------------
//函式: bad_content_filter()
//用途: 不雅文字過濾器
//-------------------------------------------------------

    function bad_content_filter($content){
    //---------------------------------------------------
    //函式: bad_content_filter()
    //用途: 不雅文字過濾器
    //---------------------------------------------------
    //$content  文字內容
    //---------------------------------------------------

        //-----------------------------------------------
        //過濾條件
        //-----------------------------------------------

            $arry_bad_content_filter=array(
                trim("去你媽    "),
                trim("去你      "),
                trim("你媽      "),
                trim("黑人      "),
                trim("榦        "),
                trim("屌        "),
                trim("fuck      "),
                trim("Fuck      "),
                trim("FUCK      "),
                trim("FXXK      "),
                trim("FUXK      "),
                trim("FXCK      "),
                trim("Fxxk      "),
                trim("Fuxk      "),
                trim("Fxck      "),
                trim("幹你娘    "),
                trim("幹        "),
                trim("白癡      "),
                trim("智障      "),
                trim("ㄍㄢˋ     "),
                trim("ㄍㄢ      "),
                trim("娘        "),
                trim("X         "),
                trim("靠北      "),
                trim("靠杯      "),
                trim("靠ㄅ      "),
                trim("ㄎㄅ      "),
                trim("機掰      "),
                trim("GY        "),
                trim("gy        "),
                trim("基掰      "),
                trim("雞掰      "),
                trim("雞ㄅ      "),
                trim("ㄐㄅ      "),
                trim("白癡      "),
                trim("白吃      "),
                trim("白痴      "),
                trim("FUCK      "),
                trim("賤        "),
                trim("爛        "),
                trim("屎        "),
                trim("笨        "),
                trim("FXUXK     ")
            );

        //-----------------------------------------------
        //過濾處理
        //-----------------------------------------------

            foreach($arry_bad_content_filter as $bad_content_filter){
                $content=trim(str_replace($bad_content_filter,"*",trim($content)));
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $content;
    }
?>