<?php
//-------------------------------------------------------
//函式: get_imageinfo()
//用途: 取得圖片資訊
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function get_imageinfo($file){
    //---------------------------------------------------
    //取得圖片資訊
    //---------------------------------------------------
    //本函式會傳回下列的資訊(類型:陣列)
    //[width]     => 800        => 寬
    //[height]    => 600        => 高
    //[const]     => 6          => 圖片類型常數
    //[bits]      => 24         => 位元深度
    //[mime_type] => image/bmp  => mime_type
    //---------------------------------------------------

        if(!isset($file)||(trim($file)=='')){
            return false;
        }

        if(false===($info=@getimagesize($file))){
        //圖檔不存在,或非圖檔

            return false;
        }else{
        //getimagesize()會傳回下列的圖片資訊(類型:陣列)
        //[0]     => 800                        => 寬
        //[1]     => 600                        => 高
        //[2]     => 6                          => 圖片類型常數
        //[3]     => width="800" height="600"   => 寬高字串
        //[bits]  => 24                         => 位元深度
        //[mime]  => image/bmp                  => mime_type

            $arry=array(
                'width'     =>$info[0],
                'height'    =>$info[1],
                'const'     =>$info[2],
                'bits'      =>$info['bits'],
                'mime_type' =>$info['mime']
            );
            return $arry;
        }
    }
?>