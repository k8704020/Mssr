<?php
//-------------------------------------------------------
//函式: dir_image_convert()
//用途: 目錄縮圖程式
//日期: 2011年12月5日
//作者: jeff@max-life
//-------------------------------------------------------

    function dir_image_convert($sdir,$tdir='',$fso_enc='BIG5',$min_width,$min_height,$rename=false){
    //---------------------------------------------------
    //目錄縮圖程式
    //---------------------------------------------------
    //$sdir         來源資料夾
    //$tdir         目的資料夾,預設'',表示輸出至同目錄下
    //$fso_enc      檔案系統語系編碼
    //$min_width    最小寬度
    //$min_height   最小高度
    //$rename       是否要更名,預設false,如更名,則會以流水號
    //
    //本函式會轉換來源資料夾下所有圖檔(包不含子資料夾),到
    //指定的目的資料夾下.
    //
    //成功時,錯誤訊息陣列 $arry_err為空陣列
    //失敗時,錯誤訊息陣列 $arry_err為非空陣列
    //或傳回false
    //
    //註:只要圖片長寬有超出'最小寬度'或'最小高度'時,便會
    //執行縮圖動作.
    //---------------------------------------------------

        //參數檢驗
        if(!isset($sdir)||(trim($sdir)=='')){
            return false;
        }
        if(!isset($fso_enc)||(trim($fso_enc)=='')){
            $fso_enc='BIG5';
        }
        if(!isset($min_width)||(trim($min_width)=='')||(!is_numeric($min_width))){
            return false;
        }
        if(!isset($min_height)||(trim($min_height)=='')||(!is_numeric($min_height))){
            return false;
        }
        if(!isset($rename)||(trim($rename)=='')){
            $rename=false;
        }

        //外掛函式處理
        if(false===(@include_once('gd/imagecreatefrombmp.php'))){
            //echo '外掛轉bmp函式失敗'.'<br/>';
            return false;
        }
        if(false===(@include_once('phpthumb/thumb_imagebysize.php'))){
            //echo '外掛轉其他類型圖檔函式失敗'.'<br/>';
            return false;
        }

        //轉碼處理
        $arry_enc=array('UTF-8','BIG5','GB2312');
        $page_enc=mb_internal_encoding();
        $sdir_enc=mb_convert_encoding($sdir,$fso_enc,$page_enc);
        $tdir_enc='';

        if(isset($tdir)&&(trim($tdir)!='')){
            $tdir_enc=mb_convert_encoding($tdir,$fso_enc,$page_enc);

            if(false===@file_exists($tdir_enc)){
                //echo "輸出目錄不存在"."<br/>";
                return false;
            }
        }else{
            $tdir_enc='';
        }

        //取回符合條件的檔案
        if(false===($arry_file=@scandir($sdir_enc))){
            //echo "來源目錄不存在"."<br/>";
            return false;
        }else{
            //echo $sdir_enc.'<br/>';
            //echo "<pre>";
            //print_r($arry_file);
            //echo "</pre>";

            $arry_imgfile0=array();             //FOR BMP
            $arry_imgfile1=array();             //FOR OTHER
            $exp="(gif|png|jpg|jpeg|bmp){1}$";  //允許圖片類型

            foreach($arry_file as $inx=>$file){
                $file_full="{$sdir_enc}/{$file}";

                if(is_file($file_full)&&(mb_eregi($exp,$file_full))){
                    if(false!==($info=@getimagesize($file_full))){
                    //---------------------------------------
                    //getimagesize(),非圖片回傳false
                    //---------------------------------------
                    //[0]     => 800                        => 寬
                    //[1]     => 600                        => 高
                    //[2]     => 6                          => 圖片類型常數
                    //[3]     => width="800" height="600"   => 寬高字串
                    //[bits]  => 24                         => 位元深度
                    //[mime]  => image/bmp                  => mime_type
                        $img_width      =$info[0];
                        $img_height     =$info[1];
                        $img_const      =$info[2];
                        $img_bits       =$info['bits'];
                        $img_mime_type  =$info['mime'];
                        //echo "{$file_full}-w:{$img_width},h:{$img_height}"."<br/>";

                        if(($img_width>$min_width)||($img_height>$min_height)){

                            if($img_mime_type=='image/bmp'){
                                //FOR BMP
                                $arry_imgfile0[]=$file_full;
                            }else{
                                //FOR OTHER
                                $arry_imgfile1[]=$file_full;
                            }
                        }
                    }
                }
            }

            //-------------------------------------------
            //轉檔處理
            //-------------------------------------------
                //echo "<pre>";
                //print_r($arry_imgfile0);
                //print_r($arry_imgfile1);
                //echo "</pre>";
                $arry_err=array();

                //圖片總數,前面補1個0
                //$padlen=strlen((string)$total_no)+1;

                $total_no   =count($arry_imgfile0)+count($arry_imgfile1);
                $pos        =1;
                $padlen     =5;
                $padstr     ='0';
                $bImg_prefix='';
                $sImg_prefix='s_';

                //---------------------------------------
                //FOR BMP
                //---------------------------------------
                //0.刪除既有輸出檔
                //1.來源bmp檔轉成jpg
                //2.複製轉出的jpg檔,到來源目錄
                //3.將轉出的jpg檔,建立縮圖
                //4.刪除原本的bmp檔

                foreach($arry_imgfile0 as $inx=>$bmp){
                    $info=pathinfo($bmp);
                    $name=$info['filename'];
                    $ext =$info['extension'];

                    if($tdir_enc!==''){
                    //-----------------------------------
                    //輸出目錄不為空
                    //-----------------------------------

                        //輸出檔名
                        $img_out="{$tdir_enc}/{$name}.jpg";

                        //0.刪除既有輸出檔
                        if((file_exists($img_out)===true)&&($tdir_enc!=$sdir_enc)){
                            if(false===@unlink($img_out)){
                                $err='轉BMP,刪除既有輸出檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }

                        //1.來源bmp檔轉成jpg
                        $img=imagecreatefrombmp($bmp);
                        if($img){
                            if(false===@imagejpeg($img,$img_out)){
                                $err='轉BMP,轉檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }

                        //2.複製轉出的jpg檔,到來源目錄
                        if($tdir_enc!=$sdir_enc){
                        //-------------------------------
                        //目的與來源目錄不同
                        //-------------------------------

                            //複製至來源目錄
                            if(false===@copy($img_out,"{$sdir_enc}/{$name}.jpg")){
                                $err='轉BMP,複製至來源目錄失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //3.將轉出的jpg檔,建立縮圖
                            if(false===@thumb_imagebysize($img_out,"{$tdir_enc}/{$sImg_prefix}{$name}.jpg",$min_width,$min_height,$type='same')){
                                $err='轉BMP,建立縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //4.刪除原本的bmp檔,跟輸出在目的資料夾的原圖轉檔
                            if(false===@unlink($bmp)){
                                $err='轉BMP,刪除既有來源檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                            if(false===@unlink($img_out)){
                                $err='轉BMP,刪除原圖轉檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //是否重新命名
                            if($rename===true){
                                $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                                $sImg=$tdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                                //echo "bImg={$bImg}<br/>";
                                //echo "sImg={$sImg}<br/>";
                                //echo "$img_out,pos={$pos}<p>";

                                if(false===@rename("{$sdir_enc}/{$name}.jpg",mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                    $err='轉BMP,重新命名大圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$sdir_enc}/{$name}.jpg",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                                if(false===@rename("{$tdir_enc}/{$sImg_prefix}{$name}.jpg",mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                    $err='轉BMP,重新命名縮圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$tdir_enc}/{$sImg_prefix}{$name}.jpg",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                            }else{
                                //echo "$img_out,pos={$pos}<p>";
                            }
                            $pos++;
                        }else{
                        //-------------------------------
                        //目的與來源目錄一樣
                        //-------------------------------

                            //1.來源bmp檔轉成jpg
                            $img=imagecreatefrombmp($bmp);
                            if($img){
                                if(false===@imagejpeg($img,$img_out)){
                                    $err='轉BMP,轉檔失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                            }

                            //3.將轉出的jpg檔,建立縮圖
                            if(false===@thumb_imagebysize($img_out,"{$sdir_enc}/{$sImg_prefix}{$name}.jpg",$min_width,$min_height,$type='same')){
                                $err='轉BMP,建立縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //4.刪除原本的bmp檔
                            if(false===@unlink($bmp)){
                                $err='轉BMP,刪除原圖轉檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //是否重新命名
                            if($rename===true){
                                $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                                $sImg=$sdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                                //echo "bImg={$bImg}<br/>";
                                //echo "sImg={$sImg}<br/>";
                                //echo "$img_out,pos={$pos}<br/>";

                                if(false===@rename("{$sdir_enc}/{$name}.jpg",mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                    $err='轉BMP,重新命名大圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$sdir_enc}/{$name}.jpg",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                                if(false===@rename("{$sdir_enc}/{$sImg_prefix}{$name}.jpg",mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                    $err='轉BMP,重新命名縮圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$sdir_enc}/{$sImg_prefix}{$name}.jpg",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                            }else{
                                //echo "$img_out,pos={$pos}<br/>";
                            }
                            $pos++;
                        }
                    }else{
                    //-----------------------------------
                    //輸出目錄為空
                    //-----------------------------------

                        //輸出檔名
                        $img_out="{$sdir_enc}/{$name}.jpg";

                        //1.來源bmp檔轉成jpg
                        $img=imagecreatefrombmp($bmp);
                        if($img){
                            if(false===@imagejpeg($img,$img_out)){
                                $err='轉BMP,轉檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }

                        //3.將轉出的jpg檔,建立縮圖
                        if(false===@thumb_imagebysize($img_out,"{$sdir_enc}/{$sImg_prefix}{$name}.jpg",$min_width,$min_height,$type='same')){
                            $err='轉BMP,建立縮圖失敗';
                            $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                            continue;
                        }

                        //4.刪除原本的bmp檔
                        if(false===@unlink($bmp)){
                            $err='轉BMP,刪除既有來源檔失敗';
                            $arry_err[]=array('img'=>mb_convert_encoding($bmp,$page_enc,$fso_enc),'err'=>$err);
                            continue;
                        }
                        //是否重新命名
                        if($rename===true){
                            $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                            $sImg=$sdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).'.jpg';
                            //echo "bImg={$bImg}<br/>";
                            //echo "sImg={$sImg}<br/>";
                            //echo "$img_out,pos={$pos}<br/>";

                            if(false===@rename($img_out,mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                $err='轉BMP,重新命名大圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                            if(false===@rename("{$sdir_enc}/{$sImg_prefix}{$name}.jpg",mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                $err='轉BMP,重新命名縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding("{$sdir_enc}/{$sImg_prefix}{$name}.jpg",$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }else{
                            //echo "$img_out,pos={$pos}<br/>";
                        }
                        $pos++;
                    }
                }

                //---------------------------------------
                //FOR OTHER
                //---------------------------------------
                //0.刪除既有輸出檔
                //1.建立縮圖

                foreach($arry_imgfile1 as $inx=>$img){
                    $info=pathinfo($img);
                    $name=$info['filename'];
                    $ext =$info['extension'];
                    $type='jpg';

                    if($tdir_enc!==''){
                    //-----------------------------------
                    //輸出目錄不為空
                    //-----------------------------------

                        $img_out="{$tdir_enc}/{$sImg_prefix}{$name}.{$ext}";

                        //0.刪除既有輸出檔
                        if((file_exists($img_out)===true)&&($tdir_enc!=$sdir_enc)){
                            if(false===@unlink($img_out)){
                                $err='轉 OTHER,刪除既有輸出檔失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }

                        //1.建立縮圖
                        if($tdir_enc!=$sdir_enc){
                        //-------------------------------
                        //目的與來源目錄不同
                        //-------------------------------

                            if(false===@thumb_imagebysize($img,"{$tdir_enc}/{$sImg_prefix}{$name}.{$ext}",$min_width,$min_height,$type='same')){
                                $err='轉 OTHER,建立縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //是否重新命名
                            if($rename===true){
                                $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                                $sImg=$tdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                                //echo "bImg={$bImg}<br/>";
                                //echo "sImg={$sImg}<br/>";
                                //echo "$img_out,pos={$pos}<br/>";

                                if(false===@rename($img,mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                    $err='轉 OTHER,重新命名大圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                                if(false===@rename("{$tdir_enc}/{$sImg_prefix}{$name}.{$ext}",mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                    $err='轉 OTHER,重新命名縮圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$tdir_enc}/{$sImg_prefix}{$name}.{$ext}",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                            }else{
                                //echo "$img_out,pos={$pos}<br/>";
                            }
                            $pos++;
                        }else{
                        //-------------------------------
                        //目的與來源目錄一樣
                        //-------------------------------

                            if(false===@thumb_imagebysize($img,"{$sdir_enc}/{$sImg_prefix}{$name}.{$ext}",$min_width,$min_height,$type='same')){
                                $err='轉 OTHER,建立縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }

                            //是否重新命名
                            if($rename===true){
                                $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                                $sImg=$tdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                                //echo "bImg={$bImg}<br/>";
                                //echo "sImg={$sImg}<br/>";
                                //echo "$img_out,pos={$pos}<br/>";

                                if(false===@rename($img,mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                    $err='轉 OTHER,重新命名大圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                                if(false===@rename("{$sdir_enc}/{$sImg_prefix}{$name}.{$ext}",mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                    $err='轉 OTHER,重新命名縮圖失敗';
                                    $arry_err[]=array('img'=>mb_convert_encoding("{$sdir_enc}/{$sImg_prefix}{$name}.{$ext}",$page_enc,$fso_enc),'err'=>$err);
                                    continue;
                                }
                            }else{
                                //echo "$img_out,pos={$pos}<br/>";
                            }
                            $pos++;
                        }
                    }else{
                    //-----------------------------------
                    //輸出目錄為空
                    //-----------------------------------

                        $img_out="{$sdir_enc}/{$sImg_prefix}{$name}.{$ext}";

                        //1.建立縮圖
                        if(false===@thumb_imagebysize($img,$img_out,$min_width,$min_height,$type='same')){
                            $err='轉 OTHER,建立縮圖失敗';
                            $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                            continue;
                        }
                        //是否重新命名
                        if($rename===true){
                            $bImg=$sdir.'/'.$bImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                            $sImg=$sdir.'/'.$sImg_prefix.str_pad($pos,$padlen,'0',STR_PAD_LEFT).".{$ext}";
                            //echo "bImg={$bImg}<br/>";
                            //echo "sImg={$sImg}<br/>";
                            //echo "$img_out,pos={$pos}<br/>";

                            if(false===@rename($img,mb_convert_encoding($bImg,$fso_enc,$page_enc))){
                                $err='轉 OTHER,重新命名大圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                            if(false===@rename($img_out,mb_convert_encoding($sImg,$fso_enc,$page_enc))){
                                $err='轉 OTHER,重新命名縮圖失敗';
                                $arry_err[]=array('img'=>mb_convert_encoding($img_out,$page_enc,$fso_enc),'err'=>$err);
                                continue;
                            }
                        }else{
                            //echo "$img_out,pos={$pos}<br/>";
                        }
                        $pos++;
                    }
                }
        }

        //回傳資訊
        return $arry_err;
    }
?>