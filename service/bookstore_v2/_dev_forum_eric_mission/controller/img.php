<?php
//-------------------------------------------------------
//明日聊書
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3).'config/config.php');

        //外掛函式檔
        $funcs=array(
            APP_ROOT.'inc/code',
            APP_ROOT.'service/_dev_forum_eric_mission/inc/code',

            APP_ROOT.'lib/php/fso/code',
            APP_ROOT.'lib/php/upload/file_upload_save/code',
            APP_ROOT.'lib/php/image/phpthumb/thumb_imagebysize',
            APP_ROOT.'lib/php/db/code',
            APP_ROOT.'lib/php/net/code',
            APP_ROOT.'lib/php/array/code'
        );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_sess_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------
    //method    函式名稱
    //send_url  返回地址

        $method='';
        if(isset($_POST['method'])&&trim($_POST['method'])!=='')$method=trim($_POST['method']);
        if(isset($_GET['method'])&&trim($_GET['method'])!=='')$method=trim($_GET['method']);

        $send_url='';
        if(isset($_POST['send_url'])&&trim($_POST['send_url'])!=='')$send_url=trim($_POST['send_url']);
        if(isset($_GET['send_url'])&&trim($_GET['send_url'])!=='')$send_url=trim($_GET['send_url']);

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //method    函式名稱
    //send_url  返回地址

        if($method==='' || !function_exists($method) || $send_url===''){
            $msg="發生嚴重錯誤";
            $jscript_back="
                <script>
                    alert('{$msg}');
                    location.href='{$send_url}';
                </script>
            ";
            die($jscript_back);
        }

    //---------------------------------------------------
    //呼叫函式
    //---------------------------------------------------

        call_user_func($method,$send_url,$arrys_sess_login_info);

    //---------------------------------------------------
    //函式列表
    //---------------------------------------------------

        //-----------------------------------------------
        //函式: add_background_group_img()
        //用途: 新增上傳小組背景圖片
        //-----------------------------------------------

            function add_background_group_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_background_group_img()
            //用途: 新增上傳小組背景圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id=(int)$sess_user_id;
                        $group_id    =(int)$group_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //上傳處理
                //---------------------------------------

                    $allow_exts ="";  //類型清單陣列
                    $allow_mimes="";  //mime清單陣列
                    $allow_size ="";  //檔案容量上限

                    $allow_exts=array(
                        trim("jpeg"),
                        trim("jpg ")
                    );
                    $allow_mimes=array(
                        trim("image/jpeg "),
                        trim("image/jpg  "),
                        trim("image/pjpeg")
                    );
                    $allow_size=array('kb'=>100);

                    //判斷有沒有上傳檔案
                    if(isset($_FILES["background_group_file"])&&!empty($_FILES["background_group_file"])&&$_FILES["background_group_file"]['error']===0){

                        //變數設定
                        $File=$_FILES["background_group_file"];
                        $root=str_repeat("../",3)."info/forum/group/{$group_id}";
                        $path="{$root}/background_group";

                        //資料夾
                        $arrys_path=array(
                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                        );
                        foreach($arrys_path as $path=>$path_enc){
                            if(!file_exists($path_enc)){
                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                            }
                        }

                        //溢位判斷
                        if(!fso_isunder($root,$path,$fso_enc)){
                            $err_msg="上傳失敗,溢位.請重新上傳!";
                            die($err_msg);
                        }

                        //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
                        $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

                        if($upload_file!==false){
                        //上傳處理,成功

                            //刪除既有檔案
                            if(fso_isunder($path,"{$path}/front_cover_group_1.jpg",$fso_enc)){
                                @unlink("{$path}/front_cover_group_1.jpg");
                            }

                            //更改原圖檔名
                            rename($upload_file,"{$path}/front_cover_group_1.jpg");

                            if(isset($file_server_enable)&&($file_server_enable)){
                            //---------------------------
                            //FTP DATASERVER 上傳處理
                            //---------------------------

                                //ftp路徑
                                $ftp_root="public_html/mssr/info/forum";
                                $ftp_path="{$ftp_root}/group/{$group_id}/background_group";

                                //檢核資料夾
                                $arrys_ftp_path=array(
                                    "{$ftp_root}"                                 =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group"                           =>mb_convert_encoding("{$ftp_root}/group",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}"               =>mb_convert_encoding("{$ftp_root}/group/{$group_id}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}/background_group" =>mb_convert_encoding("{$ftp_root}/group/{$group_id}/background_group",$fso_enc,$page_enc)
                                );
                                foreach($arrys_ftp_path as $_path=>$_path_enc){
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                    }
                                    //關閉連線
                                    ftp_close($ftp_conn);
                                }

                                //圖片上傳
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                    //設定被動模式
                                    ftp_pasv($ftp_conn,TRUE);

                                    //設置ftp路徑
                                    ftp_chdir($ftp_conn,"{$ftp_path}");

                                    //ftp上傳
                                    ftp_put($ftp_conn,"front_cover_group_1.jpg","{$path}/front_cover_group_1.jpg",FTP_BINARY);

                                    //關閉連線
                                    ftp_close($ftp_conn);

                                //移除本機圖片
                                @unlink("{$path}/front_cover_group_1.jpg");
                            }
                        }else{
                        //上傳處理,失敗

                            $err_msg=array(
                                "上傳失敗,可能原因如下,請重新上傳!",
                                "",
                                "1.檔案類型不符合",
                                "2.檔案大小超出限制(100KB)"
                            );
                            $err_msg=implode('~',$err_msg);
                            die($err_msg);
                        }
                    }else{
                    //沒有上傳檔案
                        die("沒有上傳檔案");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="上傳成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_group_sticker_img()
        //用途: 裁切小組大頭貼圖片
        //-----------------------------------------------

            function edit_group_sticker_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_group_sticker_img()
            //用途: 裁切小組大頭貼圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //group_id
                //group_sticker_x1
                //group_sticker_y1
                //group_sticker_x2
                //group_sticker_y2
                //group_sticker_w
                //group_sticker_h

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id        ',
                            'group_sticker_x1',
                            'group_sticker_y1',
                            'group_sticker_x2',
                            'group_sticker_y2',
                            'group_sticker_w ',
                            'group_sticker_h '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id        =trim($_POST[trim('group_id        ')]);
                    $group_sticker_x1=trim($_POST[trim('group_sticker_x1')]);
                    $group_sticker_y1=trim($_POST[trim('group_sticker_y1')]);
                    $group_sticker_x2=trim($_POST[trim('group_sticker_x2')]);
                    $group_sticker_y2=trim($_POST[trim('group_sticker_y2')]);
                    $group_sticker_w =trim($_POST[trim('group_sticker_w ')]);
                    $group_sticker_h =trim($_POST[trim('group_sticker_h ')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id    =(int)$sess_user_id;
                        $group_id        =(int)$group_id;
                        $group_sticker_x1=(int)$group_sticker_x1;
                        $group_sticker_y1=(int)$group_sticker_y1;
                        $group_sticker_x2=(int)$group_sticker_x2;
                        $group_sticker_y2=(int)$group_sticker_y2;
                        $group_sticker_w =(int)$group_sticker_w;
                        $group_sticker_h =(int)$group_sticker_h;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id    =(int)$sess_user_id;
                    $group_id        =(int)$group_id;
                    $group_sticker_x1=(int)$group_sticker_x1;
                    $group_sticker_y1=(int)$group_sticker_y1;
                    $group_sticker_x2=(int)$group_sticker_x2;
                    $group_sticker_y2=(int)$group_sticker_y2;
                    $group_sticker_w =(int)$group_sticker_w;
                    $group_sticker_h =(int)$group_sticker_h;

                //---------------------------------------
                //裁切處理
                //---------------------------------------

                    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
                        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
                        $imageType = image_type_to_mime_type($imageType);

                        $newImageWidth = ceil($width * $scale);
                        $newImageHeight = ceil($height * $scale);
                        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
                        switch($imageType) {
                            case "image/gif":
                                $source=imagecreatefromgif($image);
                                break;
                            case "image/pjpeg":
                            case "image/jpeg":
                            case "image/jpg":
                                $source=imagecreatefromjpeg($image);
                                break;
                            case "image/png":
                            case "image/x-png":
                                $source=imagecreatefrompng($image);
                                break;
                        }
                        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
                        switch($imageType) {
                            case "image/gif":
                                imagegif($newImage,$thumb_image_name);
                                break;
                            case "image/pjpeg":
                            case "image/jpeg":
                            case "image/jpg":
                                imagejpeg($newImage,$thumb_image_name,90);
                                break;
                            case "image/png":
                            case "image/x-png":
                                imagepng($newImage,$thumb_image_name);
                                break;
                        }
                        @chmod($thumb_image_name, 0777);
                        return $thumb_image_name;
                    }

                //---------------------------------------
                //FTP DATASERVER 下載處理
                //---------------------------------------

                    $root   =str_repeat("../",3)."info/forum/group/{$group_id}";
                    $path   ="{$root}/group_sticker";

                    //資料夾
                    $arrys_path=array(
                        "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                        "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                    );
                    foreach($arrys_path as $path=>$path_enc){
                        if(!file_exists($path_enc)){
                            mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                        }
                    }

                    if(isset($file_server_enable)&&($file_server_enable)){
                        //ftp路徑
                        $ftp_root="public_html/mssr/info/forum";
                        $ftp_path="{$ftp_root}/group/{$group_id}/group_sticker";

                        //重新連接 | 重新登入 FTP
                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                        //設定被動模式
                        ftp_pasv($ftp_conn,TRUE);

                        //取回檔案
                        ftp_get($ftp_conn,"{$path}/1.jpg","{$ftp_path}/1.jpg",FTP_BINARY);
                    }

                    $file   ="{$path}/1.jpg";
                    $scale  =160/$group_sticker_w;

                    $cropped=resizeThumbnailImage($file,$file,$group_sticker_w,$group_sticker_h,$group_sticker_x1,$group_sticker_y1,$scale);

                //---------------------------------------
                //FTP DATASERVER 上傳處理
                //---------------------------------------

                    if(isset($file_server_enable)&&($file_server_enable)){
                        //ftp路徑
                        $ftp_root="public_html/mssr/info/forum";
                        $ftp_path="{$ftp_root}/group/{$group_id}/group_sticker";

                        //檢核資料夾
                        $arrys_ftp_path=array(
                            "{$ftp_root}"                                 =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                            "{$ftp_root}/group"                           =>mb_convert_encoding("{$ftp_root}/group",$fso_enc,$page_enc),
                            "{$ftp_root}/group/{$group_id}"               =>mb_convert_encoding("{$ftp_root}/group/{$group_id}",$fso_enc,$page_enc),
                            "{$ftp_root}/group/{$group_id}/group_sticker" =>mb_convert_encoding("{$ftp_root}/group/{$group_id}/group_sticker",$fso_enc,$page_enc)
                        );
                        foreach($arrys_ftp_path as $_path=>$_path_enc){
                            //重新連接 | 重新登入 FTP
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                            }
                            //關閉連線
                            ftp_close($ftp_conn);
                        }

                        //圖片上傳
                            //重新連接 | 重新登入 FTP
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                            //設定被動模式
                            ftp_pasv($ftp_conn,TRUE);

                            //設置ftp路徑
                            ftp_chdir($ftp_conn,"{$ftp_path}");

                            //ftp上傳
                            ftp_put($ftp_conn,"1.jpg","{$path}/1.jpg",FTP_BINARY);

                            //關閉連線
                            ftp_close($ftp_conn);

                        //移除本機圖片
                        @unlink("{$path}/1.jpg");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="裁切成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/article.php?get_from=2&group_id={$group_id}';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_group_sticker_img()
        //用途: 新增小組大頭貼圖片
        //-----------------------------------------------

            function add_group_sticker_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_group_sticker_img()
            //用途: 新增小組大頭貼圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id=(int)$sess_user_id;
                        $group_id    =(int)$group_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id=(int)$sess_user_id;
                    $group_id    =(int)$group_id;

                //---------------------------------------
                //上傳處理
                //---------------------------------------

                    $allow_exts ="";  //類型清單陣列
                    $allow_mimes="";  //mime清單陣列
                    $allow_size ="";  //檔案容量上限

                    $allow_exts=array(
                        trim("jpeg"),
                        trim("jpg ")
                    );
                    $allow_mimes=array(
                        trim("image/jpeg "),
                        trim("image/jpg  "),
                        trim("image/pjpeg")
                    );
                    $allow_size=array('kb'=>100);

                    //判斷有沒有上傳檔案
                    if(isset($_FILES["group_sticker_file"])&&!empty($_FILES["group_sticker_file"])&&$_FILES["group_sticker_file"]['error']===0){

                        //變數設定
                        $File=$_FILES["group_sticker_file"];
                        $root=str_repeat("../",3)."info/forum/group/{$group_id}";
                        $path="{$root}/group_sticker";

                        //資料夾
                        $arrys_path=array(
                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                        );
                        foreach($arrys_path as $path=>$path_enc){
                            if(!file_exists($path_enc)){
                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                            }
                        }

                        //溢位判斷
                        if(!fso_isunder($root,$path,$fso_enc)){
                            $err_msg="上傳失敗,溢位.請重新上傳!";
                            die($err_msg);
                        }

                        //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
                        $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

                        if($upload_file!==false){
                        //上傳處理,成功

                            //刪除既有檔案
                            if(fso_isunder($path,"{$path}/1.jpg",$fso_enc)){
                                @unlink("{$path}/1.jpg");
                            }

                            //更改原圖檔名
                            rename($upload_file,"{$path}/1.jpg");

                            //縮圖
                            $thumb_width =320;
                            $thumb_height=320;
                            thumb_imagebysize("{$path}/1.jpg","{$path}/1.jpg",$thumb_width,$thumb_height,$type='same');

                            if(isset($file_server_enable)&&($file_server_enable)){
                            //---------------------------
                            //FTP DATASERVER 上傳處理
                            //---------------------------

                                //ftp路徑
                                $ftp_root="public_html/mssr/info/forum";
                                $ftp_path="{$ftp_root}/group/{$group_id}/group_sticker";

                                //檢核資料夾
                                $arrys_ftp_path=array(
                                    "{$ftp_root}"                                 =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group"                           =>mb_convert_encoding("{$ftp_root}/group",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}"               =>mb_convert_encoding("{$ftp_root}/group/{$group_id}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}/group_sticker" =>mb_convert_encoding("{$ftp_root}/group/{$group_id}/group_sticker",$fso_enc,$page_enc)
                                );
                                foreach($arrys_ftp_path as $_path=>$_path_enc){
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                    }
                                    //關閉連線
                                    ftp_close($ftp_conn);
                                }

                                //圖片上傳
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                    //設定被動模式
                                    ftp_pasv($ftp_conn,TRUE);

                                    //設置ftp路徑
                                    ftp_chdir($ftp_conn,"{$ftp_path}");

                                    //ftp上傳
                                    ftp_put($ftp_conn,"1.jpg","{$path}/1.jpg",FTP_BINARY);

                                    //關閉連線
                                    ftp_close($ftp_conn);

                                //移除本機圖片
                                @unlink("{$path}/1.jpg");
                            }
                        }else{
                        //上傳處理,失敗

                            $err_msg=array(
                                "上傳失敗,可能原因如下,請重新上傳!",
                                "",
                                "1.檔案類型不符合",
                                "2.檔案大小超出限制(100KB)"
                            );
                            $err_msg=implode('~',$err_msg);
                            die($err_msg);
                        }
                    }else{
                    //沒有上傳檔案
                        die("沒有上傳檔案");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="上傳成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_style_group_img()
        //用途: 新增小組頁面圖片
        //-----------------------------------------------

            function add_style_group_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_style_group_img()
            //用途: 新增小組頁面圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------

                    if(!empty($_POST)){
                        $post_chk=array(
                            'group_id'
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $group_id=trim($_POST[trim('group_id')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id=(int)$sess_user_id;
                        $group_id    =(int)$group_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_group_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_group_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                     $sess_user_id=(int)$sess_user_id;
                     $group_id    =(int)$group_id;

                //---------------------------------------
                //上傳處理
                //---------------------------------------

                    $allow_exts ="";  //類型清單陣列
                    $allow_mimes="";  //mime清單陣列
                    $allow_size ="";  //檔案容量上限

                    $allow_exts=array(
                        trim("jpeg"),
                        trim("jpg ")
                    );
                    $allow_mimes=array(
                        trim("image/jpeg "),
                        trim("image/jpg  "),
                        trim("image/pjpeg")
                    );
                    $allow_size=array('kb'=>100);

                    //判斷有沒有上傳檔案
                    if(isset($_FILES["style_group_file"])&&!empty($_FILES["style_group_file"])&&$_FILES["style_group_file"]['error']===0){

                        //變數設定
                        $File=$_FILES["style_group_file"];
                        $root=str_repeat("../",3)."info/forum/group/{$group_id}";
                        $path="{$root}/style_group";

                        //資料夾
                        $arrys_path=array(
                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                        );
                        foreach($arrys_path as $path=>$path_enc){
                            if(!file_exists($path_enc)){
                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                            }
                        }

                        //溢位判斷
                        if(!fso_isunder($root,$path,$fso_enc)){
                            $err_msg="上傳失敗,溢位.請重新上傳!";
                            die($err_msg);
                        }

                        //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
                        $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

                        if($upload_file!==false){
                        //上傳處理,成功

                            //處理
                            switch(count($db_results)){

                                case 0:
                                    $sql="
                                        # for mssr_forum_style_group_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_style_group_rev` SET
                                            `create_by`     ={$sess_user_id },
                                            `group_id`      ={$group_id     },
                                            `style_id`      =1               ,
                                            `style_from`    =2               ,
                                            `keyin_mdate`   =NULL            ;
                                    ";
                                break;

                                default:
                                    $sql="
                                        # for mssr_forum_style_group_rev
                                        UPDATE `mssr_forum`.`mssr_forum_style_group_rev` SET
                                            `style_id`  =1,
                                            `style_from`=2
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_style_group_rev`.`group_id`={$group_id}
                                        LIMIT 1;
                                    ";
                                break;

                            }
                            //送出
                            $err ='DB QUERY FAIL';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            //刪除既有檔案
                            if(fso_isunder($path,"{$path}/bg_1.jpg",$fso_enc)){
                                @unlink("{$path}/bg_1.jpg");
                            }

                            if(isset($file_server_enable)&&($file_server_enable)){
                            //---------------------------
                            //FTP DATASERVER 上傳處理
                            //---------------------------

                                //ftp路徑
                                $ftp_root="public_html/mssr/info/forum";
                                $ftp_path="{$ftp_root}/group/{$group_id}/style_group";

                                //檢核資料夾
                                $arrys_ftp_path=array(
                                    "{$ftp_root}"                               =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group"                         =>mb_convert_encoding("{$ftp_root}/group",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}"             =>mb_convert_encoding("{$ftp_root}/group/{$group_id}",$fso_enc,$page_enc),
                                    "{$ftp_root}/group/{$group_id}/style_group" =>mb_convert_encoding("{$ftp_root}/group/{$group_id}/style_group",$fso_enc,$page_enc)
                                );
                                foreach($arrys_ftp_path as $_path=>$_path_enc){
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                    }
                                    //關閉連線
                                    ftp_close($ftp_conn);
                                }

                                //圖片上傳
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                    //設定被動模式
                                    ftp_pasv($ftp_conn,TRUE);

                                    //設置ftp路徑
                                    ftp_chdir($ftp_conn,"{$ftp_path}");

                                    //ftp上傳
                                    ftp_put($ftp_conn,"bg_1.jpg",$upload_file,FTP_BINARY);

                                    //關閉連線
                                    ftp_close($ftp_conn);

                                //更改原圖檔名
                                rename($upload_file,"{$path}/bg_1.jpg");

                                //移除本機圖片
                                @unlink("{$path}/bg_1.jpg");
                            }else{
                                //更改原圖檔名
                                rename($upload_file,"{$path}/bg_1.jpg");
                            }
                        }else{
                        //上傳處理,失敗

                            $err_msg=array(
                                "上傳失敗,可能原因如下,請重新上傳!",
                                "",
                                "1.檔案類型不符合",
                                "2.檔案大小超出限制(100KB)"
                            );
                            $err_msg=implode('~',$err_msg);
                            die($err_msg);
                        }
                    }else{
                    //沒有上傳檔案
                        die("沒有上傳檔案");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="上傳成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: edit_user_sticker_img()
        //用途: 裁切個人大頭貼圖片
        //-----------------------------------------------

            function edit_user_sticker_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: edit_user_sticker_img()
            //用途: 裁切個人大頭貼圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------
                //user_sticker_x1
                //user_sticker_y1
                //user_sticker_x2
                //user_sticker_y2
                //user_sticker_w
                //user_sticker_h

                    if(!empty($_POST)){
                        $post_chk=array(
                            'user_sticker_x1',
                            'user_sticker_y1',
                            'user_sticker_x2',
                            'user_sticker_y2',
                            'user_sticker_w ',
                            'user_sticker_h '
                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST
                    $user_sticker_x1=trim($_POST[trim('user_sticker_x1')]);
                    $user_sticker_y1=trim($_POST[trim('user_sticker_y1')]);
                    $user_sticker_x2=trim($_POST[trim('user_sticker_x2')]);
                    $user_sticker_y2=trim($_POST[trim('user_sticker_y2')]);
                    $user_sticker_w =trim($_POST[trim('user_sticker_w ')]);
                    $user_sticker_h =trim($_POST[trim('user_sticker_h ')]);

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id   =(int)$sess_user_id;
                        $user_sticker_x1=(int)$user_sticker_x1;
                        $user_sticker_y1=(int)$user_sticker_y1;
                        $user_sticker_x2=(int)$user_sticker_x2;
                        $user_sticker_y2=(int)$user_sticker_y2;
                        $user_sticker_w =(int)$user_sticker_w;
                        $user_sticker_h =(int)$user_sticker_h;

                //---------------------------------------
                //預設值
                //---------------------------------------

                    $sess_user_id   =(int)$sess_user_id;
                    $user_sticker_x1=(int)$user_sticker_x1;
                    $user_sticker_y1=(int)$user_sticker_y1;
                    $user_sticker_x2=(int)$user_sticker_x2;
                    $user_sticker_y2=(int)$user_sticker_y2;
                    $user_sticker_w =(int)$user_sticker_w;
                    $user_sticker_h =(int)$user_sticker_h;

                //---------------------------------------
                //裁切處理
                //---------------------------------------

                    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
                        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
                        $imageType = image_type_to_mime_type($imageType);

                        $newImageWidth = ceil($width * $scale);
                        $newImageHeight = ceil($height * $scale);
                        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
                        switch($imageType) {
                            case "image/gif":
                                $source=imagecreatefromgif($image);
                                break;
                            case "image/pjpeg":
                            case "image/jpeg":
                            case "image/jpg":
                                $source=imagecreatefromjpeg($image);
                                break;
                            case "image/png":
                            case "image/x-png":
                                $source=imagecreatefrompng($image);
                                break;
                        }
                        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
                        switch($imageType) {
                            case "image/gif":
                                imagegif($newImage,$thumb_image_name);
                                break;
                            case "image/pjpeg":
                            case "image/jpeg":
                            case "image/jpg":
                                imagejpeg($newImage,$thumb_image_name,90);
                                break;
                            case "image/png":
                            case "image/x-png":
                                imagepng($newImage,$thumb_image_name);
                                break;
                        }
                        @chmod($thumb_image_name, 0777);
                        return $thumb_image_name;
                    }

                //---------------------------------------
                //FTP DATASERVER 下載處理
                //---------------------------------------

                    $root   =str_repeat("../",3)."info/user/{$sess_user_id}/forum";
                    $path   ="{$root}/user_sticker";

                    //資料夾
                    $arrys_path=array(
                        "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                        "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                    );
                    foreach($arrys_path as $path=>$path_enc){
                        if(!file_exists($path_enc)){
                            mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                        }
                    }

                    if(isset($file_server_enable)&&($file_server_enable)){
                        //ftp路徑
                        $ftp_root="public_html/mssr/info/user";
                        $ftp_path="{$ftp_root}/{$sess_user_id}/forum/user_sticker";

                        //重新連接 | 重新登入 FTP
                        $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                        $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                        //設定被動模式
                        ftp_pasv($ftp_conn,TRUE);

                        //取回檔案
                        ftp_get($ftp_conn,"{$path}/1.jpg","{$ftp_path}/1.jpg",FTP_BINARY);
                    }

                    $file   ="{$path}/1.jpg";
                    $scale  =160/$user_sticker_w;

                    $cropped=resizeThumbnailImage($file,$file,$user_sticker_w,$user_sticker_h,$user_sticker_x1,$user_sticker_y1,$scale);

                //---------------------------------------
                //FTP DATASERVER 上傳處理
                //---------------------------------------

                    if(isset($file_server_enable)&&($file_server_enable)){
                        //ftp路徑
                        $ftp_root="public_html/mssr/info/user";
                        $ftp_path="{$ftp_root}/{$sess_user_id}/forum/user_sticker";

                        //檢核資料夾
                        $arrys_ftp_path=array(
                            "{$ftp_root}"                                   =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                            "{$ftp_root}/{$sess_user_id}"                   =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}",$fso_enc,$page_enc),
                            "{$ftp_root}/{$sess_user_id}/forum"             =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum",$fso_enc,$page_enc),
                            "{$ftp_root}/{$sess_user_id}/forum/user_sticker"=>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum/user_sticker",$fso_enc,$page_enc)
                        );
                        foreach($arrys_ftp_path as $_path=>$_path_enc){
                            //重新連接 | 重新登入 FTP
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                            if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                            }
                            //關閉連線
                            ftp_close($ftp_conn);
                        }

                        //圖片上傳
                            //重新連接 | 重新登入 FTP
                            $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                            $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                            //設定被動模式
                            ftp_pasv($ftp_conn,TRUE);

                            //設置ftp路徑
                            ftp_chdir($ftp_conn,"{$ftp_path}");

                            //ftp上傳
                            ftp_put($ftp_conn,"1.jpg","{$path}/1.jpg",FTP_BINARY);

                            //關閉連線
                            ftp_close($ftp_conn);

                        //移除本機圖片
                        @unlink("{$path}/1.jpg");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="裁切成功";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            location.href='../view/user.php?user_id={$sess_user_id}&tab=8';
                        </script>
                    ";
                    die($jscript_back);
            }

        //-----------------------------------------------
        //函式: add_user_sticker_img()
        //用途: 新增個人大頭貼圖片
        //-----------------------------------------------

            function add_user_sticker_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_user_sticker_img()
            //用途: 新增個人大頭貼圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------

                    if(!empty($_POST)){
                        $post_chk=array(

                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id=(int)$sess_user_id;

                //---------------------------------------
                //預設值
                //---------------------------------------

                     $sess_user_id=(int)$sess_user_id;

                //---------------------------------------
                //上傳處理
                //---------------------------------------

                    $allow_exts ="";  //類型清單陣列
                    $allow_mimes="";  //mime清單陣列
                    $allow_size ="";  //檔案容量上限

                    $allow_exts=array(
                        trim("jpeg"),
                        trim("jpg ")
                    );
                    $allow_mimes=array(
                        trim("image/jpeg "),
                        trim("image/jpg  "),
                        trim("image/pjpeg")
                    );
                    $allow_size=array('kb'=>100);

                    //判斷有沒有上傳檔案
                    if(isset($_FILES["user_sticker_file"])&&!empty($_FILES["user_sticker_file"])&&$_FILES["user_sticker_file"]['error']===0){

                        //變數設定
                        $File=$_FILES["user_sticker_file"];
                        $root=str_repeat("../",3)."info/user/{$sess_user_id}/forum";
                        $path="{$root}/user_sticker";

                        //資料夾
                        $arrys_path=array(
                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                        );
                        foreach($arrys_path as $path=>$path_enc){
                            if(!file_exists($path_enc)){
                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                            }
                        }

                        //溢位判斷
                        if(!fso_isunder($root,$path,$fso_enc)){
                            $err_msg="上傳失敗,溢位.請重新上傳!";
                            die($err_msg);
                        }

                        //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
                        $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

                        if($upload_file!==false){
                        //上傳處理,成功

                            //刪除既有檔案
                            if(fso_isunder($path,"{$path}/1.jpg",$fso_enc)){
                                @unlink("{$path}/1.jpg");
                            }

                            //更改原圖檔名
                            rename($upload_file,"{$path}/1.jpg");

                            //縮圖
                            $thumb_width =320;
                            $thumb_height=320;
                            thumb_imagebysize("{$path}/1.jpg","{$path}/1.jpg",$thumb_width,$thumb_height,$type='same');

                            if(isset($file_server_enable)&&($file_server_enable)){
                            //---------------------------
                            //FTP DATASERVER 上傳處理
                            //---------------------------

                                //ftp路徑
                                $ftp_root="public_html/mssr/info/user";
                                $ftp_path="{$ftp_root}/{$sess_user_id}/forum/user_sticker";

                                //檢核資料夾
                                $arrys_ftp_path=array(
                                    "{$ftp_root}"                                   =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}"                   =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}/forum"             =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}/forum/user_sticker"=>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum/user_sticker",$fso_enc,$page_enc)
                                );
                                foreach($arrys_ftp_path as $_path=>$_path_enc){
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                    }
                                    //關閉連線
                                    ftp_close($ftp_conn);
                                }

                                //圖片上傳
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                    //設定被動模式
                                    ftp_pasv($ftp_conn,TRUE);

                                    //設置ftp路徑
                                    ftp_chdir($ftp_conn,"{$ftp_path}");

                                    //ftp上傳
                                    ftp_put($ftp_conn,"1.jpg","{$path}/1.jpg",FTP_BINARY);

                                    //關閉連線
                                    ftp_close($ftp_conn);

                                //移除本機圖片
                                @unlink("{$path}/1.jpg");
                            }
                        }else{
                        //上傳處理,失敗

                            $err_msg=array(
                                "上傳失敗,可能原因如下,請重新上傳!",
                                "",
                                "1.檔案類型不符合",
                                "2.檔案大小超出限制(100KB)"
                            );
                            $err_msg=implode('~',$err_msg);
                            die($err_msg);
                        }
                    }else{
                    //沒有上傳檔案
                        die("沒有上傳檔案");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="上傳成功";
                    die($msg);
            }

        //-----------------------------------------------
        //函式: add_style_user_img()
        //用途: 新增個人頁面圖片
        //-----------------------------------------------

            function add_style_user_img($send_url,$arrys_sess_login_info){
            //-------------------------------------------
            //函式: add_style_user_img()
            //用途: 新增個人頁面圖片
            //-------------------------------------------

                //---------------------------------------
                //外部參數
                //---------------------------------------

                    global $_POST;
                    global $_GET;
                    global $_FILES;
                    global $page_enc;
                    global $fso_enc;
                    global $arry_conn_mssr;
                    global $file_server_enable;
                    global $arry_ftp1_info;

                //---------------------------------------
                //錯誤提示
                //---------------------------------------

                    $msg="發生嚴重錯誤";
                    $jscript_back="
                        <script>
                            alert('{$msg}');
                            history.back(-1);
                        </script>
                    ";

                //---------------------------------------
                //接收參數
                //---------------------------------------

                    if(!empty($_POST)){
                        $post_chk=array(

                        );
                        $post_chk=array_map("trim",$post_chk);
                        foreach($post_chk as $post){
                            if(!isset($_POST[$post]))die($msg);
                        }
                    }else{die($msg);}

                //---------------------------------------
                //設定參數
                //---------------------------------------

                    //POST

                    //SESSION
                    if(isset($arrys_sess_login_info[0]['uid']))$sess_user_id=(int)$arrys_sess_login_info[0]['uid'];
                    if(isset($arrys_sess_login_info[0]['user_lv']))$sess_user_lv=(int)$arrys_sess_login_info[0]['user_lv'];
                    if(isset($arrys_sess_login_info[0]['name']))$sess_name=trim($arrys_sess_login_info[0]['name']);
                    if(isset($arrys_sess_login_info[0]['account']))$sess_account=trim($arrys_sess_login_info[0]['account']);
                    if(isset($arrys_sess_login_info[0]['permission']))$sess_permission=trim($arrys_sess_login_info[0]['permission']);
                    if(isset($arrys_sess_login_info[0]['school_code']))$sess_school_code=trim($arrys_sess_login_info[0]['school_code']);
                    if(isset($arrys_sess_login_info[0]['responsibilities']))$sess_responsibilities=(int)$arrys_sess_login_info[0]['responsibilities'];
                    if(isset($arrys_sess_login_info[0]['school_name']))$sess_school_name=trim($arrys_sess_login_info[0]['school_name']);
                    if(isset($arrys_sess_login_info[0]['country_code']))$sess_country_code=trim($arrys_sess_login_info[0]['country_code']);
                    if(isset($arrys_sess_login_info[0]['arry_class_info']))$sess_arry_class_info=$arrys_sess_login_info[0]['arry_class_info'];
                    if(isset($arrys_sess_login_info[0]['arrys_class_info'])){
                        $sess_arrys_class_info=$arrys_sess_login_info[0]['arrys_class_info'];
                        foreach($sess_arrys_class_info as $inx=>$sess_arry_class_info){
                            $sess_arrys_class_info[$inx]=array_map("trim",$sess_arry_class_info);
                        }
                    }

                //---------------------------------------
                //檢驗參數
                //---------------------------------------

                    $arry_err=array();

                    if(count($arry_err)!==0){
                        if(1==2){//除錯用
                            echo "<pre>";
                            print_r($arry_err);
                            echo "</pre>";
                        }
                        die($msg);
                    }

                //---------------------------------------
                //資料庫
                //---------------------------------------

                    //-----------------------------------
                    //通用
                    //-----------------------------------

                        //建立連線 mssr
                        $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

                    //-----------------------------------
                    //預處理
                    //-----------------------------------

                        $sess_user_id=(int)$sess_user_id;

                    //-----------------------------------
                    //檢核樣式
                    //-----------------------------------

                        $sql="
                            SELECT
                                `mssr_forum`.`mssr_forum_style_user_rev`.`style_id`
                            FROM `mssr_forum`.`mssr_forum_style_user_rev`
                            WHERE 1=1
                                AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                        ";
                        $db_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);

                //---------------------------------------
                //預設值
                //---------------------------------------

                     $sess_user_id=(int)$sess_user_id;

                //---------------------------------------
                //上傳處理
                //---------------------------------------

                    $allow_exts ="";  //類型清單陣列
                    $allow_mimes="";  //mime清單陣列
                    $allow_size ="";  //檔案容量上限

                    $allow_exts=array(
                        trim("jpeg"),
                        trim("jpg ")
                    );
                    $allow_mimes=array(
                        trim("image/jpeg "),
                        trim("image/jpg  "),
                        trim("image/pjpeg")
                    );
                    $allow_size=array('kb'=>100);

                    //判斷有沒有上傳檔案
                    if(isset($_FILES["style_user_file"])&&!empty($_FILES["style_user_file"])&&$_FILES["style_user_file"]['error']===0){

                        //變數設定
                        $File=$_FILES["style_user_file"];
                        $root=str_repeat("../",3)."info/user/{$sess_user_id}/forum";
                        $path="{$root}/style_user";

                        //資料夾
                        $arrys_path=array(
                            "{$root}"=>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
                            "{$path}"=>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
                        );
                        foreach($arrys_path as $path=>$path_enc){
                            if(!file_exists($path_enc)){
                                mk_dir($path,$mode=0777,$recursive=true,$fso_enc);
                            }
                        }

                        //溢位判斷
                        if(!fso_isunder($root,$path,$fso_enc)){
                            $err_msg="上傳失敗,溢位.請重新上傳!";
                            die($err_msg);
                        }

                        //上傳處理,成功:儲存的路徑(檔案系統編碼),失敗:false
                        $upload_file=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);

                        if($upload_file!==false){
                        //上傳處理,成功

                            //處理
                            switch(count($db_results)){

                                case 0:
                                    $sql="
                                        # for mssr_forum_style_user_rev
                                        INSERT INTO `mssr_forum`.`mssr_forum_style_user_rev` SET
                                            `user_id`       ={$sess_user_id },
                                            `style_id`      =1               ,
                                            `style_from`    =2               ,
                                            `keyin_mdate`   =NULL            ;
                                    ";
                                break;

                                default:
                                    $sql="
                                        # for mssr_forum_style_user_rev
                                        UPDATE `mssr_forum`.`mssr_forum_style_user_rev` SET
                                            `style_id`  =1,
                                            `style_from`=2
                                        WHERE 1=1
                                            AND `mssr_forum`.`mssr_forum_style_user_rev`.`user_id`={$sess_user_id}
                                        LIMIT 1;
                                    ";
                                break;

                            }
                            //送出
                            $err ='DB QUERY FAIL';
                            $sth=$conn_mssr->prepare($sql);
                            $sth->execute()or die($err);

                            //刪除既有檔案
                            if(fso_isunder($path,"{$path}/bg_1.jpg",$fso_enc)){
                                @unlink("{$path}/bg_1.jpg");
                            }

                            if(isset($file_server_enable)&&($file_server_enable)){
                            //---------------------------
                            //FTP DATASERVER 上傳處理
                            //---------------------------

                                //ftp路徑
                                $ftp_root="public_html/mssr/info/user";
                                $ftp_path="{$ftp_root}/{$sess_user_id}/forum/style_user";

                                //檢核資料夾
                                $arrys_ftp_path=array(
                                    "{$ftp_root}"                                   =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}"                   =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}/forum"             =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum",$fso_enc,$page_enc),
                                    "{$ftp_root}/{$sess_user_id}/forum/style_user"  =>mb_convert_encoding("{$ftp_root}/{$sess_user_id}/forum/style_user",$fso_enc,$page_enc)
                                );
                                foreach($arrys_ftp_path as $_path=>$_path_enc){
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                                    if(false===@ftp_chdir($ftp_conn,$_path_enc)){
                                        mk_dir_ftp($ftp_conn,$_path,$mode=0777,$fso_enc);
                                    }
                                    //關閉連線
                                    ftp_close($ftp_conn);
                                }

                                //圖片上傳
                                    //重新連接 | 重新登入 FTP
                                    $ftp_conn =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                                    $ftp_login=ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);

                                    //設定被動模式
                                    ftp_pasv($ftp_conn,TRUE);

                                    //設置ftp路徑
                                    ftp_chdir($ftp_conn,"{$ftp_path}");

                                    //ftp上傳
                                    ftp_put($ftp_conn,"bg_1.jpg",$upload_file,FTP_BINARY);

                                    //關閉連線
                                    ftp_close($ftp_conn);

                                //更改原圖檔名
                                rename($upload_file,"{$path}/bg_1.jpg");

                                //移除本機圖片
                                @unlink("{$path}/bg_1.jpg");
                            }else{
                                //更改原圖檔名
                                rename($upload_file,"{$path}/bg_1.jpg");
                            }
                        }else{
                        //上傳處理,失敗

                            $err_msg=array(
                                "上傳失敗,可能原因如下,請重新上傳!",
                                "",
                                "1.檔案類型不符合",
                                "2.檔案大小超出限制(100KB)"
                            );
                            $err_msg=implode('~',$err_msg);
                            die($err_msg);
                        }
                    }else{
                    //沒有上傳檔案
                        die("沒有上傳檔案");
                    }

                //---------------------------------------
                //關閉連線
                //---------------------------------------

                    $conn_mssr=NULL;
                    $conn_user=NULL;

                //---------------------------------------
                //重導頁面
                //---------------------------------------

                    $msg="上傳成功";
                    die($msg);
            }
?>

