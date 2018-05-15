<?php
//-------------------------------------------------------
//函式: get_imagetype()
//用途: 取得圖檔類型資訊
//日期: 2011年12月2日
//作者: jeff@max-life
//-------------------------------------------------------

    function get_imagetype($type_const){
    //---------------------------------------------------
    //取得圖檔類型資訊
    //---------------------------------------------------
    //$type_const   GD圖檔類型常數值
    //
    //本函式會依據你傳入的常數值,取回對應的圖檔類型資訊
    //取得資訊如下..
    //
    //array['mime_type']    image/gif
    //array['type']         gif
    //array['const']        1    
    //---------------------------------------------------
    //常數名稱            常數值  MIME TYPE
    //IMAGETYPE_GIF       1       image/gif
    //IMAGETYPE_JPEG      2       image/jpeg
    //IMAGETYPE_PNG       3       image/png
    //IMAGETYPE_SWF       4       application/x-shockwave-flash
    //IMAGETYPE_PSD       5       image/psd
    //IMAGETYPE_BMP       6       image/bmp
    //IMAGETYPE_TIFF_II   7       image/tiff
    //IMAGETYPE_TIFF_MM   8       image/tiff
    //IMAGETYPE_JPC       9       application/octet-stream
    //IMAGETYPE_JP2       10      image/jp2
    //IMAGETYPE_JPX       11      application/octet-stream
    //IMAGETYPE_JB2       12      application/octet-stream
    //IMAGETYPE_SWC       13      application/x-shockwave-flash
    //IMAGETYPE_IFF       14      image/iff
    //IMAGETYPE_WBMP      15      image/vnd.wap.wbmp
    //IMAGETYPE_XBM       16      image/xbm

        if(!isset($type_const)||trim($type_const)==''||!is_numeric($type_const)){
            return false;
        }

        $imagetype=array(
            IMAGETYPE_GIF    =>array('mime_type'=>trim('image/gif                    '),'type'=>trim('gif    '),'const'=>1 ),
            IMAGETYPE_JPEG   =>array('mime_type'=>trim('image/jpeg                   '),'type'=>trim('jpeg   '),'const'=>2 ),
            IMAGETYPE_PNG    =>array('mime_type'=>trim('image/png                    '),'type'=>trim('png    '),'const'=>3 ),
            IMAGETYPE_SWF    =>array('mime_type'=>trim('application/x-shockwave-flash'),'type'=>trim('swf    '),'const'=>4 ),
            IMAGETYPE_PSD    =>array('mime_type'=>trim('image/psd                    '),'type'=>trim('psd    '),'const'=>5 ),
            IMAGETYPE_BMP    =>array('mime_type'=>trim('image/bmp                    '),'type'=>trim('bmp    '),'const'=>6 ),
            IMAGETYPE_TIFF_II=>array('mime_type'=>trim('image/tiff                   '),'type'=>trim('tiff_ii'),'const'=>7 ),
            IMAGETYPE_TIFF_MM=>array('mime_type'=>trim('image/tiff                   '),'type'=>trim('tiff_mm'),'const'=>8 ),
            IMAGETYPE_JPC    =>array('mime_type'=>trim('application/octet-stream     '),'type'=>trim('jpc    '),'const'=>9 ),
            IMAGETYPE_JP2    =>array('mime_type'=>trim('image/jp2                    '),'type'=>trim('jp2    '),'const'=>10),
            IMAGETYPE_JPX    =>array('mime_type'=>trim('application/octet-stream     '),'type'=>trim('jpx    '),'const'=>11),
            IMAGETYPE_JB2    =>array('mime_type'=>trim('application/octet-stream     '),'type'=>trim('jb2    '),'const'=>12),
            IMAGETYPE_SWC    =>array('mime_type'=>trim('application/x-shockwave-flash'),'type'=>trim('swc    '),'const'=>13),
            IMAGETYPE_IFF    =>array('mime_type'=>trim('image/iff                    '),'type'=>trim('iff    '),'const'=>14),
            IMAGETYPE_WBMP   =>array('mime_type'=>trim('image/vnd.wap.wbmp           '),'type'=>trim('wbmp   '),'const'=>15),
            IMAGETYPE_XBM    =>array('mime_type'=>trim('image/xbm                    '),'type'=>trim('xbm    '),'const'=>16),
        );
        ksort($imagetype);
        //echo "<pre>";
        //print_r($imagetype);
        //echo "</pre>";

        foreach($imagetype as $key=>$val){
            if($key==$type_const){
                return $val;
                break;
            }
        }
        return false;
    }
?>