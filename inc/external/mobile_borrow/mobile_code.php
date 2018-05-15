<?php
//-------------------------------------------------------
//函式: mobile_borrow()
//用途: 行動裝置閱讀登記
//日期: 2015年09月05日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //設置測試資料
    //---------------------------------------------------

        ////設定頁面語系
        //header("Content-Type: text/html; charset=UTF-8");
        //
        ////設定文字內部編碼
        //mb_internal_encoding("UTF-8");
        //
        ////設定台灣時區
        //date_default_timezone_set('Asia/Taipei');
        //
        ////實體化
        //    $omobile_borrow =new mobile_borrow();
        //
        ////檢核書籍資訊
        //    $uid            =(int)5030;
        //    $school_code    =trim('gcp');
        //    $book_code      =trim('9783110336191');
        //    $arrys_book_info=$omobile_borrow->get_books_info($uid,$book_code,$school_code);
        //    //echo "<Pre>";
        //    //print_r($arrys_book_info);
        //    //echo "</Pre>";

        ////登記書籍
        //    $school_code    =trim('gcp');
        //    $book_sid       =trim('mbl1201310091653558761791');
        //    $borrow_book    =$omobile_borrow->borrow_book($uid,$school_code,$book_sid);

        ////新增並登記書籍
        //    $book_name      =addslashes(strip_tags(trim('自建書籍')));
        //    $book_author    =addslashes(strip_tags(trim('自建書籍')));
        //    $book_publisher =addslashes(strip_tags(trim('自建書籍')));
        //    $add_borrow_book=$omobile_borrow->add_borrow_book($uid,$school_code,$book_code,$book_name,$book_author,$book_publisher);

        ////取得所有或指定之可推薦的書籍(包含推薦過)
        //    $arrys_rec_book_info=$omobile_borrow->get_rec_books_info($uid,array('mbl1201310091653558761791'));
        //    echo "<Pre>";
        //    print_r($arrys_rec_book_info);
        //    echo "</Pre>";

        ////新增推薦
        //    $add_rec_book_star  =$omobile_borrow->add_rec_book_star($uid,$book_sid='mbl1201310091653558761791',$rec_rank=5,$arry_rec_reason=array(1,2));
        //    $add_rec_book_text  =$omobile_borrow->add_rec_book_text($uid,$book_sid='mbl1201310091653558761791',$arry_rec_content=array('哈哈12','嘿嘿12','ㄚㄚ12'));
        //    $add_rec_book_draw  =$omobile_borrow->add_rec_book_draw($uid,$book_sid='mbl1201310091653558761791');
        //    $add_rec_book_record=$omobile_borrow->add_rec_book_record($uid,$book_sid='mbl1201310091653558761791',$rec_time=50);

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        class mobile_borrow{

            private $is_online_version=false;
            private $document_root;

            public function add_rec_book_record($uid,$book_sid,$rec_time){
            //新增推薦record

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/config/config.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/lib/php/fso/code.php");
                require_once($this->document_root."/mssr/lib/php/upload/file_upload_save/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_star_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_text_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_draw_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_record_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid=(int)$uid;
                $book_sid=addslashes(strip_tags($book_sid));
                $rec_sid=trim(mssr_rec_book_record_sid($uid,mb_internal_encoding()));
                $rec_time=(int)$rec_time;
                $keyin_ip=get_ip();

                //判斷有沒有上傳檔案
                if(!isset($_FILES["file"])||empty($_FILES["file"])||$_FILES["file"]['error']!==0){
                    //回傳
                    return false;
                }else{
                    $allow_exts  ="";   //類型清單陣列
                    $allow_mimes ="";   //mime清單陣列
                    $allow_size  ="";   //檔案容量上限

                    $allow_exts=array(
                        "mp3"
                    );
                    $allow_mimes=array(
                        "audio/mpeg"
                    );
                    $allow_size=array('kb'=>10000);

                    //變數設定
                    $File        =$_FILES["file"];
                    $extension   =trim(pathinfo($_FILES['file']['name'])['extension']);
                    $root        ="{$uid}/{$book_sid}";
                    $path        ="{$root}";
                    $path_enc    =mb_convert_encoding($path,$fso_enc,$page_enc);

                    //開目錄
                    if(!is_dir("{$path}")){
                        mk_dir("{$path}",$mode=0777,$recursive=true,$fso_enc);
                    }

                    //上傳處理
                    $upload=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);
                    if(!$upload){
                        rm_dir("{$uid}");
                        //回傳
                        return false;
                    }else{
                        //更改檔名
                        rename($upload,"{$path}/1.{$extension}");

                        //FTP上傳
                        if(isset($file_server_enable)&&($file_server_enable)){
                            //ftp路徑
                            $ftp_root="public_html/mssr/info/user";
                            $ftp_path="{$ftp_root}/{$uid}/book/{$book_sid}/record";

                           //檢核資料夾
                            $arrys_ftp_path=array(
                                "{$ftp_root}"                                   =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}"                            =>mb_convert_encoding("{$ftp_root}/{$uid}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book"                       =>mb_convert_encoding("{$ftp_root}/{$uid}/book",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}"           =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}/record"    =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}/record",$fso_enc,$page_enc)
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
                                ftp_put($ftp_conn,"1.{$extension}","{$path}/1.{$extension}",FTP_BINARY);

                                //關閉連線
                                ftp_close($ftp_conn);

                            //移除本機圖片
                            @unlink("{$path}/1.{$extension}");
                            rm_dir("{$uid}");
                        }else{
                            rm_dir("{$uid}");
                            //回傳
                            return false;
                        }
                    }
                }

                $sql="
                    # for mssr_rec_book_record
                    INSERT INTO `mssr_rec_book_record` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_time`          = {$rec_time            } ,
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `rec_filename`      ='1.{$extension}'         ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;

                    # for mssr_rec_book_record_log
                    INSERT INTO `mssr_rec_book_record_log` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `log_id`            =NULL                     ,
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_time`          = {$rec_time            } ,
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `rec_filename`      ='1.{$extension}'         ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;
                ";

                //執行 mssr_rec_book_cno 統計
                $this->add_mssr_rec_book_cno($conn_mssr,$rec_type='record',$uid,$book_sid,$keyin_ip);

                //送出
                $err_msg ='ADD_REC_BOOK_RECORD() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }

            public function add_rec_book_draw($uid,$book_sid){
            //新增推薦draw

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/config/config.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/lib/php/fso/code.php");
                require_once($this->document_root."/mssr/lib/php/upload/file_upload_save/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_star_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_text_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_draw_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_record_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid=(int)$uid;
                $book_sid=addslashes(strip_tags($book_sid));
                $rec_sid=trim(mssr_rec_book_draw_sid($uid,mb_internal_encoding()));
                $keyin_ip=get_ip();

                //判斷有沒有上傳檔案
                if(!isset($_FILES["file"])||empty($_FILES["file"])||$_FILES["file"]['error']!==0){
                    //回傳
                    return false;
                }else{
                    $allow_exts  ="";   //類型清單陣列
                    $allow_mimes ="";   //mime清單陣列
                    $allow_size  ="";   //檔案容量上限

                    $allow_exts=array(
                        "jpg",
                        "jpeg"
                    );
                    $allow_mimes=array(
                        "image/jpeg",
                        "image/jpg",
                        "image/pjpeg"
                    );
                    $allow_size=array('kb'=>100);

                    //變數設定
                    $File        =$_FILES["file"];
                    $root        ="{$uid}/{$book_sid}";
                    $path        ="{$root}";
                    $path_enc    =mb_convert_encoding($path,$fso_enc,$page_enc);

                    //開目錄
                    if(!is_dir("{$path}")){
                        mk_dir("{$path}",$mode=0777,$recursive=true,$fso_enc);
                    }

                    //上傳處理
                    $upload=file_upload_save($File,$path,$fso_enc,$allow_exts,$allow_mimes,$allow_size,true);
                    if(!$upload){
                        rm_dir("{$uid}");
                        //回傳
                        return false;
                    }else{
                        //更改檔名
                        rename($upload,"{$path}/upload_1.jpg");

                        //FTP上傳
                        if(isset($file_server_enable)&&($file_server_enable)){
                            //ftp路徑
                            $ftp_root="public_html/mssr/info/user";
                            $ftp_path="{$ftp_root}/{$uid}/book/{$book_sid}/draw/bimg";

                           //檢核資料夾
                            $arrys_ftp_path=array(
                                "{$ftp_root}"                                        =>mb_convert_encoding("{$ftp_root}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}"                                 =>mb_convert_encoding("{$ftp_root}/{$uid}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book"                            =>mb_convert_encoding("{$ftp_root}/{$uid}/book",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}"                =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}/draw"           =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}/draw",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}/draw/bimg"      =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}/draw/bimg",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}/draw/simg"      =>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}/draw/simg",$fso_enc,$page_enc),
                                "{$ftp_root}/{$uid}/book/{$book_sid}/draw/base64_img"=>mb_convert_encoding("{$ftp_root}/{$uid}/book/{$book_sid}/draw/base64_img",$fso_enc,$page_enc)
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
                                ftp_put($ftp_conn,"upload_1.jpg","{$path}/upload_1.jpg",FTP_BINARY);

                                //關閉連線
                                ftp_close($ftp_conn);

                            //移除本機圖片
                            @unlink("{$path}/upload_1.jpg");
                            rm_dir("{$uid}");
                        }else{
                            rm_dir("{$uid}");
                            //回傳
                            return false;
                        }
                    }
                }

                $sql="
                    # for mssr_rec_book_draw
                    INSERT INTO `mssr_rec_book_draw` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;

                    # for mssr_rec_book_draw_log
                    INSERT INTO `mssr_rec_book_draw_log` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `log_id`            =NULL                     ,
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;
                ";

                //執行 mssr_rec_book_cno 統計
                $this->add_mssr_rec_book_cno($conn_mssr,$rec_type='draw',$uid,$book_sid,$keyin_ip);

                //送出
                $err_msg ='ADD_REC_BOOK_DRAW() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }

            public function add_rec_book_text($uid,$book_sid,$arry_rec_content){
            //新增推薦text

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/config/config.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_star_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_text_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_draw_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_record_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid=(int)$uid;
                $book_sid=addslashes(strip_tags($book_sid));
                $rec_sid=trim(mssr_rec_book_text_sid($uid,mb_internal_encoding()));
                $keyin_ip=get_ip();
                $arry_rec_content=array_map("trim",$arry_rec_content);
                $arry_rec_content=array_map("strip_tags",$arry_rec_content);
                $arry_rec_content=array_map("addslashes",$arry_rec_content);
                $arry_rec_content=array_map("gzcompress",$arry_rec_content);
                $arry_rec_content=array_map("base64_encode",$arry_rec_content);
                $arry_rec_content=serialize($arry_rec_content);

                $sql="
                    # for mssr_rec_book_text
                    INSERT INTO `mssr_rec_book_text` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_content`       ='{$arry_rec_content    }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;

                    # for mssr_rec_book_text_log
                    INSERT INTO `mssr_rec_book_text_log` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `log_id`            =NULL                     ,
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_content`       ='{$arry_rec_content    }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='無'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;
                ";

                //執行 mssr_rec_book_cno 統計
                $this->add_mssr_rec_book_cno($conn_mssr,$rec_type='text',$uid,$book_sid,$keyin_ip);

                //送出
                $err_msg ='ADD_REC_BOOK_TEXT() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }

            public function add_rec_book_star($uid,$book_sid,$rec_rank,$arry_rec_reason){
            //新增推薦star

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/config/config.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_star_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_text_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_draw_sid/code.php");
                require_once($this->document_root."/mssr/service/bookstore_v2/inc/mssr_rec_book_record_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid=(int)$uid;
                $book_sid=addslashes(strip_tags($book_sid));

                $rec_rank=(int)$rec_rank;
                if($rec_rank===0)$rec_rank=1;

                $arry_rec_reason=$arry_rec_reason;
                $tmp_arry_rec_reason=array('x','x','x','x','x','x','x');
                foreach($arry_rec_reason as $rec_reason){
                    $rec_reason=(int)$rec_reason;
                    if(isset($tmp_arry_rec_reason[$rec_reason-1]))$tmp_arry_rec_reason[$rec_reason-1]='o';
                }

                $arry_rec_reason=implode("",$tmp_arry_rec_reason);

                $rec_sid=trim(mssr_rec_book_star_sid($uid,mb_internal_encoding()));
                $keyin_ip=get_ip();

                $sql="
                    # for mssr_rec_book_star
                    INSERT INTO `mssr_rec_book_star` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_rank`          = {$rec_rank            } ,
                        `rec_reason`        ='{$arry_rec_reason     }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='有'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;

                    # for mssr_rec_book_star_log
                    INSERT INTO `mssr_rec_book_star_log` SET
                        `user_id`           = {$uid                 } ,
                        `book_sid`          ='{$book_sid            }',
                        `log_id`            =NULL                     ,
                        `rec_sid`           ='{$rec_sid             }',
                        `rec_rank`          = {$rec_rank            } ,
                        `rec_reason`        ='{$arry_rec_reason     }',
                        `rec_operate_time`  =0                        ,
                        `rec_reward`        ='有'                     ,
                        `rec_state`         ='顯示'                   ,
                        `keyin_cdate`       =NOW()                    ,
                        `keyin_ip`          ='{$keyin_ip}'            ;
                ";

                //執行 mssr_rec_book_cno 統計
                $this->add_mssr_rec_book_cno($conn_mssr,$rec_type='star',$uid,$book_sid,$keyin_ip);

                //送出
                $err_msg ='ADD_REC_BOOK_STAR() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }

            private function add_mssr_rec_book_cno($conn_mssr,$rec_type,$uid,$book_sid,$keyin_ip){
            //執行 mssr_rec_book_cno 統計

                //參數設置
                $uid     =(int)$uid;
                $book_sid=addslashes(strip_tags(trim($book_sid)));
                $keyin_ip=trim($keyin_ip);
                $rec_type=trim($rec_type);
                $sql     ="";

                if($rec_type==='star'){
                    $sql="
                        # for mssr_rec_book_cno
                        UPDATE `mssr_rec_book_cno` SET
                            `rec_stat_cno`          =`rec_stat_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_one_week
                        UPDATE `mssr_rec_book_cno_one_week` SET
                            `rec_stat_cno`          =`rec_stat_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_semester
                        UPDATE `mssr_rec_book_cno_semester` SET
                            `rec_stat_cno`          =`rec_stat_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno
                        INSERT IGNORE INTO `mssr_rec_book_cno` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =1                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_one_week
                        INSERT IGNORE INTO `mssr_rec_book_cno_one_week` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =1                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_semester
                        INSERT IGNORE INTO `mssr_rec_book_cno_semester` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =1                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;
                    ";
                }else if($rec_type==='text'){
                    $sql="
                        # for mssr_rec_book_cno
                        UPDATE `mssr_rec_book_cno` SET
                            `rec_text_cno`          =`rec_text_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_one_week
                        UPDATE `mssr_rec_book_cno_one_week` SET
                            `rec_text_cno`          =`rec_text_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_semester
                        UPDATE `mssr_rec_book_cno_semester` SET
                            `rec_text_cno`          =`rec_text_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno
                        INSERT IGNORE INTO `mssr_rec_book_cno` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =1                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_one_week
                        INSERT IGNORE INTO `mssr_rec_book_cno_one_week` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =1                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_semester
                        INSERT IGNORE INTO `mssr_rec_book_cno_semester` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =1                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;
                    ";
                }else if($rec_type==='draw'){
                    $sql="
                        # for mssr_rec_book_cno
                        UPDATE `mssr_rec_book_cno` SET
                            `rec_draw_cno`          =`rec_draw_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_one_week
                        UPDATE `mssr_rec_book_cno_one_week` SET
                            `rec_draw_cno`          =`rec_draw_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_semester
                        UPDATE `mssr_rec_book_cno_semester` SET
                            `rec_draw_cno`          =`rec_draw_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno
                        INSERT IGNORE INTO `mssr_rec_book_cno` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =1                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_one_week
                        INSERT IGNORE INTO `mssr_rec_book_cno_one_week` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =1                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_semester
                        INSERT IGNORE INTO `mssr_rec_book_cno_semester` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =1                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =0                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;
                    ";
                }else if($rec_type==='record'){
                    $sql="
                        # for mssr_rec_book_cno
                        UPDATE `mssr_rec_book_cno` SET
                            `rec_record_cno`          =`rec_record_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_one_week
                        UPDATE `mssr_rec_book_cno_one_week` SET
                            `rec_record_cno`          =`rec_record_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno_semester
                        UPDATE `mssr_rec_book_cno_semester` SET
                            `rec_record_cno`          =`rec_record_cno`+1,
                            `keyin_mdate`           =NOW()
                        WHERE 1=1
                            AND `user_id`           = {$uid         }
                            AND `book_sid`          ='{$book_sid    }'
                        LIMIT 1;

                        # for mssr_rec_book_cno
                        INSERT IGNORE INTO `mssr_rec_book_cno` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =1                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_one_week
                        INSERT IGNORE INTO `mssr_rec_book_cno_one_week` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =1                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;

                        # for mssr_rec_book_cno_semester
                        INSERT IGNORE INTO `mssr_rec_book_cno_semester` SET
                            `edit_by`               = {$uid             } ,
                            `user_id`               = {$uid             } ,
                            `book_sid`              ='{$book_sid        }',
                            `rec_stat_cno`          =0                    ,
                            `rec_draw_cno`          =0                    ,
                            `rec_text_cno`          =0                    ,
                            `rec_record_cno`        =1                    ,
                            `has_publish`           ='否'                 ,
                            `book_on_shelf_state`   ='下架'               ,
                            `rec_state`             =1                    ,
                            `keyin_cdate`           =NOW()                ,
                            `keyin_mdate`           =NOW()                ,
                            `keyin_ip`              ='{$keyin_ip}'        ;
                    ";
                }else{
                    //回傳
                    return false;
                }

                if($sql!==''){
                    //送出
                    $conn_mssr->exec($sql);

                    //回傳
                    return true;
                }else{
                    //回傳
                    return false;
                }
            }

            public function get_rec_books_info($uid,$arry_book=array()){
            //取得所有或指定之可推薦的書籍(包含推薦過)

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/config/config.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $ftp_root   ="public_html/mssr/info/user/".(int)$uid."/book";
                $http_path  ="http://".$arry_ftp1_info['host']."/mssr/info/user/".(int)$uid."/book/";
                $ftp_conn   =ftp_connect($arry_ftp1_info['host'],$arry_ftp1_info['port']);
                $ftp_login  =ftp_login($ftp_conn,$arry_ftp1_info['account'],$arry_ftp1_info['password']);
                ftp_pasv($ftp_conn,TRUE);

                $uid                =(int)$uid;
                $arrys_rec_book_info=[];
                $tmp_arry_book      =[];
                $list_book          ='';
                if(isset($arry_book)&&!empty($arry_book))$list_book=trim(implode("','",$arry_book));

                //SQL設置
                $sql="
                    SELECT `user_id`, `book_sid`
                    FROM `mssr_book_read_opinion_cno`
                    WHERE 1=1
                        AND `user_id`={$uid}
                ";
                if(trim($list_book)!==''){
                    $sql.="
                        AND `book_sid` IN ('{$list_book}')
                    ";
                }
                $sql.="
                    GROUP BY `user_id`, `book_sid`
                    ORDER BY `keyin_mdate` DESC
                ";
                $arrys_book_read_opinion=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(empty($arrys_book_read_opinion)){
                    //回傳
                    return $arrys_book_read_opinion;
                }

                foreach($arrys_book_read_opinion as $inx=>$arry_book_read_opinion){
                    $rs_book_sid=trim($arry_book_read_opinion['book_sid']);
                    $arrys_rec_book_info[$inx]['uid']=$uid;
                    $arrys_rec_book_info[$inx]['book_sid']=$rs_book_sid;
                }

                foreach($arrys_rec_book_info as $arry_rec_book_info){
                    $rs_uid=(int)($arry_rec_book_info['uid']);
                    $rs_book_sid=trim($arry_rec_book_info['book_sid']);
                    $tmp_arry_book[]=$rs_book_sid;
                }
                $tmp_list_book=implode("','",$tmp_arry_book);

                //評星
                $sql="
                    SELECT `book_sid`,`rec_sid`,`rec_rank`,`rec_reason`,`rec_reward`,`rec_state`,`keyin_cdate`
                    FROM `mssr_rec_book_star_log`
                    WHERE 1=1
                        AND `user_id` = {$uid}
                        AND `book_sid` IN ('{$tmp_list_book}')
                    ORDER BY `keyin_cdate` DESC
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1));
                if(!empty($db_results)){
                    $cno=0;
                    foreach($db_results as $db_result){
                        $rs_book_sid     =trim($db_result['book_sid']);
                        $rs_rec_sid      =trim($db_result['rec_sid']);
                        $rs_rec_star_rank=str_repeat('★',(int)$db_result['rec_rank']);
                        $rs_rec_reward   =trim($db_result['rec_reward']);
                        $rs_rec_state    =trim($db_result['rec_state']);
                        $rs_keyin_cdate  =trim($db_result['keyin_cdate']);

                        $arrys_rs_rec_star_reason=array();
                        $rs_rec_star_reason=trim($db_result['rec_reason']);
                        if($rs_rec_star_reason!==''){
                            foreach($config_arrys['service']['bookstore']['rec_reason'] as $inx1=>$val1){
                                if($rs_rec_star_reason[$inx1]==='o'){
                                    array_push($arrys_rs_rec_star_reason,$val1);
                                }
                            }
                            $arrys_rs_rec_star_reason=implode("、",$arrys_rs_rec_star_reason);
                        }
                        foreach($arrys_rec_book_info as $inx=>$arry_rec_book_info){
                            if($rs_book_sid===$arry_rec_book_info['book_sid']){
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_sid']   =$rs_rec_sid;
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_rank']  =$rs_rec_star_rank;
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_reason']=$arrys_rs_rec_star_reason;
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_reward']=$rs_rec_reward;
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_state'] =$rs_rec_state;
                                $arrys_rec_book_info[$inx]['arry_rec_star']['rec_cdate'] =$rs_keyin_cdate;
                                $cno++;
                            }
                        }
                    }
                }

                //文字
                $sql="
                    SELECT `book_sid`,`rec_sid`,`rec_content`,`rec_reward`,`rec_state`,`keyin_cdate`
                    FROM `mssr_rec_book_text_log`
                    WHERE 1=1
                        AND `user_id` = {$uid}
                        AND `book_sid` IN ('{$tmp_list_book}')
                    ORDER BY `keyin_cdate` DESC
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1));
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_book_sid     =trim($db_result['book_sid']);
                        $rs_rec_sid      =trim($db_result['rec_sid']);
                        $rs_rec_content  =trim($db_result['rec_content']);
                        $rs_rec_reward   =trim($db_result['rec_reward']);
                        $rs_rec_state    =trim($db_result['rec_state']);
                        $rs_keyin_cdate  =trim($db_result['keyin_cdate']);

                        if(@unserialize($rs_rec_content)){
                            $rs_rec_content=@unserialize($rs_rec_content);
                        }else{continue;}
                        $rs_rec_content=array_map("base64_decode",$rs_rec_content);
                        $rs_rec_content=array_map("gzuncompress",$rs_rec_content);
                        $rs_rec_content=array_map("htmlspecialchars",$rs_rec_content);

                        foreach($arrys_rec_book_info as $inx=>$arry_rec_book_info){
                            if($rs_book_sid===$arry_rec_book_info['book_sid']){
                                $arrys_rec_book_info[$inx]['arry_rec_text']['rec_sid']    =$rs_rec_sid;
                                $arrys_rec_book_info[$inx]['arry_rec_text']['arry_rec_content']=$rs_rec_content;
                                $arrys_rec_book_info[$inx]['arry_rec_text']['rec_reward'] =$rs_rec_reward;
                                $arrys_rec_book_info[$inx]['arry_rec_text']['rec_state']  =$rs_rec_state;
                                $arrys_rec_book_info[$inx]['arry_rec_text']['rec_cdate']  =$rs_keyin_cdate;
                                $cno++;
                            }
                        }
                    }
                }

                //畫圖
                $sql="
                    SELECT `book_sid`,`rec_sid`,`rec_reward`,`rec_state`,`keyin_cdate`
                    FROM `mssr_rec_book_draw_log`
                    WHERE 1=1
                        AND `user_id` = {$uid}
                        AND `book_sid` IN ('{$tmp_list_book}')
                    ORDER BY `keyin_cdate` DESC
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1));
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_book_sid     =trim($db_result['book_sid']);
                        $rs_rec_sid      =trim($db_result['rec_sid']);
                        $rs_rec_reward   =trim($db_result['rec_reward']);
                        $rs_rec_state    =trim($db_result['rec_state']);
                        $rs_keyin_cdate  =trim($db_result['keyin_cdate']);

                        //手繪
                        $draw_path="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/1.jpg";
                        $arry_ftp_file_draw_path=ftp_nlist($ftp_conn,$draw_path);

                        //上傳
                        $up_load_draw_path_1="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_1.jpg";
                        $up_load_draw_path_2="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_2.jpg";
                        $up_load_draw_path_3="{$ftp_root}/".trim($rs_book_sid)."/draw/bimg/upload_3.jpg";
                        $arry_ftp_file_up_load_draw_path_1=ftp_nlist($ftp_conn,$up_load_draw_path_1);
                        $arry_ftp_file_up_load_draw_path_2=ftp_nlist($ftp_conn,$up_load_draw_path_2);
                        $arry_ftp_file_up_load_draw_path_3=ftp_nlist($ftp_conn,$up_load_draw_path_3);

                        if(empty($arry_ftp_file_draw_path)&&empty($arry_ftp_file_up_load_draw_path_1)&&empty($arry_ftp_file_up_load_draw_path_2)&&empty($arry_ftp_file_up_load_draw_path_3)){
                            continue;
                        }

                        foreach($arrys_rec_book_info as $inx=>$arry_rec_book_info){
                            if($rs_book_sid===$arry_rec_book_info['book_sid']){
                                $arrys_rec_book_info[$inx]['arry_rec_draw']['rec_sid']    =$rs_rec_sid;
                                $arrys_rec_book_info[$inx]['arry_rec_draw']['rec_reward'] =$rs_rec_reward;
                                $arrys_rec_book_info[$inx]['arry_rec_draw']['rec_state']  =$rs_rec_state;
                                $arrys_rec_book_info[$inx]['arry_rec_draw']['rec_cdate']  =$rs_keyin_cdate;
                                if(!empty($arry_ftp_file_draw_path))$arrys_rec_book_info[$inx]['arry_rec_draw']['file_path_1']=$http_path.trim($rs_book_sid)."/draw/bimg/1.jpg";
                                if(!empty($arry_ftp_file_up_load_draw_path_1))$arrys_rec_book_info[$inx]['arry_rec_draw']['file_path_2']=$http_path.trim($rs_book_sid)."/draw/bimg/upload_1.jpg";
                                if(!empty($arry_ftp_file_up_load_draw_path_2))$arrys_rec_book_info[$inx]['arry_rec_draw']['file_path_3']=$http_path.trim($rs_book_sid)."/draw/bimg/upload_2.jpg";
                                if(!empty($arry_ftp_file_up_load_draw_path_3))$arrys_rec_book_info[$inx]['arry_rec_draw']['file_path_4']=$http_path.trim($rs_book_sid)."/draw/bimg/upload_3.jpg";
                                $cno++;
                            }
                        }
                    }
                }

                //錄音
                $sql="
                    SELECT `book_sid`,`rec_sid`,`rec_reward`,`rec_state`,`keyin_cdate`
                    FROM `mssr_rec_book_record_log`
                    WHERE 1=1
                        AND `user_id` = {$uid}
                        AND `book_sid` IN ('{$tmp_list_book}')
                    ORDER BY `keyin_cdate` DESC
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array(0,1));
                if(!empty($db_results)){
                    foreach($db_results as $db_result){
                        $rs_book_sid     =trim($db_result['book_sid']);
                        $rs_rec_sid      =trim($db_result['rec_sid']);
                        $rs_rec_reward   =trim($db_result['rec_reward']);
                        $rs_rec_state    =trim($db_result['rec_state']);
                        $rs_keyin_cdate  =trim($db_result['keyin_cdate']);

                        $record_path_mp3="{$ftp_root}/".trim($rs_book_sid)."/record/1.mp3";
                        $arry_ftp_file_record_path_mp3=ftp_nlist($ftp_conn,$record_path_mp3);
                        $record_path_wav="{$ftp_root}/".trim($rs_book_sid)."/record/1.wav";
                        $arry_ftp_file_record_path_wav=ftp_nlist($ftp_conn,$record_path_wav);

                        if((empty($arry_ftp_file_record_path_mp3))&&(empty($arry_ftp_file_record_path_wav))){
                            continue;
                        }

                        foreach($arrys_rec_book_info as $inx=>$arry_rec_book_info){
                            if($rs_book_sid===$arry_rec_book_info['book_sid']){
                                $arrys_rec_book_info[$inx]['arry_rec_record']['rec_sid']    =$rs_rec_sid;
                                $arrys_rec_book_info[$inx]['arry_rec_record']['rec_reward'] =$rs_rec_reward;
                                $arrys_rec_book_info[$inx]['arry_rec_record']['rec_state']  =$rs_rec_state;
                                $arrys_rec_book_info[$inx]['arry_rec_record']['rec_cdate']  =$rs_keyin_cdate;
                                if(!empty($arry_ftp_file_record_path_mp3))$arrys_rec_book_info[$inx]['arry_rec_record']['file_path_1']=$http_path.trim($rs_book_sid)."/record/1.mp3";
                                if(!empty($arry_ftp_file_record_path_wav))$arrys_rec_book_info[$inx]['arry_rec_record']['file_path_2']=$http_path.trim($rs_book_sid)."/record/1.wav";
                                $cno++;
                            }
                        }
                    }
                }

                //回傳
                return $arrys_rec_book_info;
            }

            public function add_borrow_book($uid,$school_code,$book_code,$book_name,$book_author,$book_publisher){
            //新增並登記書籍

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/inc/search_book_info_online/code.php");
                require_once($this->document_root."/mssr/center/teacher_center/inc/book/book/book_unverified_sid/code.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/inc/book_borrow_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $create_by  =(int)$uid;
                $edit_by    =(int)$uid;
                $book_id    ="NULL";
                $book_sid_unverified=book_unverified_sid($create_by,mb_internal_encoding());

                $ch_isbn_10=ch_isbn_10($book_code, $convert=false);
                $ch_isbn_13=ch_isbn_13($book_code, $convert=false);

                $_lv=0; //錯誤指標
                if(isset($ch_isbn_10['error'])){
                    $_lv=$_lv+1;
                }
                if(isset($ch_isbn_13['error'])){
                    $_lv=$_lv+3;
                }

                switch($_lv){
                    case 1:
                    //10碼錯誤，利用13碼轉換更新
                        $book_isbn_10=isbn_13_to_10($book_code);
                        $book_isbn_13=$book_code;
                    break;

                    case 3:
                    //13碼錯誤，利用10碼轉換更新
                        $book_isbn_10=$book_code;
                        $book_isbn_13=isbn_10_to_13($book_code);
                    break;

                    case 4:
                        $err_msg='ADD_BORROW_BOOK() IS INVALID';
                        $this->err_report($err_msg);
                    break;
                }

                $book_name      =addslashes(strip_tags(trim($book_name)));
                $book_author    =addslashes(strip_tags(trim($book_author)));
                $book_publisher =addslashes(strip_tags(trim($book_publisher)));
                $book_page_count=0;
                $book_word      =0;
                $book_note      ='';
                $book_phonetic  ='無';
                $keyin_cdate    ="NOW()";
                $keyin_mdate    ="NULL";
                $keyin_ip       =get_ip();

                $opinion_answer ='a:5:{i:0;a:2:{s:8:"topic_id";i:1;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:1;a:2:{s:8:"topic_id";i:2;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:2;a:2:{s:8:"topic_id";i:3;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:3;a:2:{s:8:"topic_id";i:4;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:4;a:2:{s:8:"topic_id";i:5;s:14:"opinion_answer";a:1:{i:0;s:1:"6";}}}';

                $sql="
                    # for mssr_book_unverified
                    INSERT INTO `mssr_book_unverified` SET
                        `create_by`         =  {$create_by          } ,
                        `edit_by`           =  {$edit_by            } ,
                        `book_id`           =  {$book_id            } ,
                        `book_sid`          = '{$book_sid_unverified}',
                        `book_isbn_10`      = '{$book_isbn_10       }',
                        `book_isbn_13`      = '{$book_isbn_13       }',
                        `book_name`         = '{$book_name          }',
                        `book_author`       = '{$book_author        }',
                        `book_publisher`    = '{$book_publisher     }',
                        `book_page_count`   =  {$book_page_count    } ,
                        `book_word`         =  {$book_word          } ,
                        `book_from`         =1                        ,
                        `book_note`         = '{$book_note          }',
                        `book_phonetic`     = '{$book_phonetic      }',
                        `book_verified`     =3                        ,
                        `keyin_cdate`       =  {$keyin_cdate        } ,
                        `keyin_mdate`       =  {$keyin_mdate        } ,
                        `keyin_ip`          = '{$keyin_ip           }';
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //參數設置
                $uid            =(int)$uid;
                $book_sid       =addslashes(strip_tags($book_sid_unverified));
                $school_code    =addslashes(strip_tags($school_code));
                $class_category =(int)0;
                $grade          =(int)0;
                $classroom      =(int)0;
                $user_id        =(int)$uid;
                $borrow_sid     =book_borrow_sid($user_id,mb_internal_encoding());
                $keyin_cdate    ='NOW()';
                $log_id         ='NULL';
                $borrow_sdate   ='NOW()';
                $borrow_edate   ='0000-00-00 00:00:00';
                $keyin_ip       =get_ip();

                $sql="
                    # for mssr_book_borrow_log
                    INSERT INTO `mssr_book_borrow_log` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `log_id`                = {$log_id                  } ,
                        `borrow_sid`            ='{$borrow_sid              }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                $new_borrow_sid=(int)$conn_mssr->lastInsertId();

                $sql="
                    # for mssr_book_borrow
                    INSERT INTO `mssr_book_borrow` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_semester
                    INSERT INTO `mssr_book_borrow_semester` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion
                    INSERT INTO `mssr_book_read_opinion` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$keyin_cdate             } ,
                        `opinion_answer`        ='{$opinion_answer          }',
                        `keyin_cdate`           = {$keyin_cdate             } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion_log
                    INSERT INTO `mssr_book_read_opinion_log` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$keyin_cdate             } ,
                        `log_id`                = {$log_id                  } ,
                        `opinion_answer`        ='{$opinion_answer          }',
                        `keyin_cdate`           = {$keyin_cdate             } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion_cno
                    UPDATE `mssr_book_read_opinion_cno` SET
                        `opinion_cno`           =`opinion_cno`+1
                    WHERE 1=1
                        AND `user_id`           = {$user_id                 }
                        AND `book_sid`          ='{$book_sid                }'
                    LIMIT 1;

                    # for mssr_book_read_opinion_cno
                    INSERT IGNORE INTO `mssr_book_read_opinion_cno` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `opinion_cno`           = 1,
                        `keyin_mdate`           = {$log_id                  };

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`            ='{$new_borrow_sid          }'
                    WHERE 1=1
                        AND `log_id`            ='{$new_borrow_sid          }'
                    LIMIT 1;
                ";
                //送出
                $err_msg ='ADD_BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }
            public function borrow_book($uid,$school_code,$book_sid){
            //登記書籍

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/inc/search_book_info_online/code.php");
                require_once($this->document_root."/mssr/center/teacher_center/inc/book/book/book_global_sid/code.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");
                require_once($this->document_root."/mssr/inc/book_borrow_sid/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid            =(int)$uid;
                $book_sid       =addslashes(strip_tags($book_sid));
                $school_code    =addslashes(strip_tags($school_code));
                $class_category =(int)0;
                $grade          =(int)0;
                $classroom      =(int)0;
                $user_id        =(int)$uid;
                $borrow_sid     =book_borrow_sid($user_id,mb_internal_encoding());
                $keyin_cdate    ='NOW()';
                $keyin_mdate    ="NOW()";
                $log_id         ='NULL';
                $borrow_sdate   ='NOW()';
                $borrow_edate   ='0000-00-00 00:00:00';
                $keyin_ip       =get_ip();

                $opinion_answer ='a:5:{i:0;a:2:{s:8:"topic_id";i:1;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:1;a:2:{s:8:"topic_id";i:2;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:2;a:2:{s:8:"topic_id";i:3;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:3;a:2:{s:8:"topic_id";i:4;s:14:"opinion_answer";a:1:{i:0;s:1:"3";}}i:4;a:2:{s:8:"topic_id";i:5;s:14:"opinion_answer";a:1:{i:0;s:1:"6";}}}';

                $sql="
                    # for mssr_book_borrow_log
                    INSERT INTO `mssr_book_borrow_log` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `log_id`                = {$log_id                  } ,
                        `borrow_sid`            ='{$borrow_sid              }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';
                ";
                //送出
                $err_msg ='BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                $new_borrow_sid=(int)$conn_mssr->lastInsertId();

                $sql="
                    # for mssr_book_borrow
                    INSERT INTO `mssr_book_borrow` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_borrow_semester
                    INSERT INTO `mssr_book_borrow_semester` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `school_code`           ='{$school_code             }',
                        `school_category`       = {$class_category          } ,
                        `grade_id`              = {$grade                   } ,
                        `classroom_id`          = {$classroom               } ,
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$borrow_sdate            } ,
                        `borrow_edate`          ='{$borrow_edate            }',
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion
                    INSERT INTO `mssr_book_read_opinion` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$keyin_cdate             } ,
                        `opinion_answer`        ='{$opinion_answer          }',
                        `keyin_cdate`           = {$keyin_cdate             } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion_log
                    INSERT INTO `mssr_book_read_opinion_log` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `borrow_sid`            ='{$new_borrow_sid          }',
                        `borrow_sdate`          = {$keyin_cdate             } ,
                        `log_id`                = {$log_id                  } ,
                        `opinion_answer`        ='{$opinion_answer          }',
                        `keyin_cdate`           = {$keyin_cdate             } ,
                        `keyin_ip`              ='{$keyin_ip                }';

                    # for mssr_book_read_opinion_cno
                    UPDATE `mssr_book_read_opinion_cno` SET
                        `opinion_cno`           =`opinion_cno`+1
                    WHERE 1=1
                        AND `user_id`           = {$user_id                 }
                        AND `book_sid`          ='{$book_sid                }'
                    LIMIT 1;

                    # for mssr_book_read_opinion_cno
                    INSERT IGNORE INTO `mssr_book_read_opinion_cno` SET
                        `user_id`               = {$user_id                 } ,
                        `book_sid`              ='{$book_sid                }',
                        `opinion_cno`           = 1,
                        `keyin_mdate`           = {$log_id                  };

                    # for mssr_book_borrow_log
                    UPDATE `mssr_book_borrow_log` SET
                        `borrow_sid`            ='{$new_borrow_sid          }'
                    WHERE 1=1
                        AND `log_id`            ='{$new_borrow_sid          }'
                    LIMIT 1;
                ";
                //送出
                $err_msg ='BORROW_BOOK() IS DB QUERY FAIL';
                $conn_mssr->exec($sql)
                or $this->err_report($err_msg);

                //回傳
                return true;
            }
            public function get_books_info($uid,$book_code,$school_code){
            //檢核書籍資訊

                //參數檢驗
                $this->verify_parameter($uid);

                //引用函式檔
                require_once($this->document_root."/mssr/inc/search_book_info_online/code.php");
                require_once($this->document_root."/mssr/center/teacher_center/inc/book/book/book_global_sid/code.php");
                require_once($this->document_root."/mssr/lib/php/string/code.php");
                require_once($this->document_root."/mssr/lib/php/vaildate/code.php");
                require_once($this->document_root."/mssr/lib/php/net/code.php");

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //參數設置
                $uid            =(int)$uid;
                $book_code      =addslashes(trim($book_code));
                $arrys_book_info=[];

                //SQL設置
                $sql="
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_class`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                            AND
                            `school_code` = '{$school_code}'
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_library`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                            AND
                            `school_code` = '{$school_code}'
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_global`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                    UNION
                        SELECT
                            `book_sid`,
                            `book_name`,
                            `book_author`,
                            `book_publisher`
                        FROM `mssr_book_unverified`
                        WHERE 1=1
                            AND (
                                `book_isbn_10`='{$book_code}'
                                    OR
                                `book_isbn_13`='{$book_code}'
                            )
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(empty($db_results)){
                //線上搜尋
                    $arry_book_info_online=search_book_info_online($book_code);

                    if(!empty($arry_book_info_online['book_name'][0])){ //$arry_book_info_online['book_name'][0]
                        $create_by      =(int)$uid;
                        $edit_by        =(int)$uid;
                        $book_id        ="NULL";
                        $book_sid_global=book_global_sid($create_by,mb_internal_encoding());

                        $ch_isbn_10=ch_isbn_10($book_code, $convert=false);
                        $ch_isbn_13=ch_isbn_13($book_code, $convert=false);

                        $_lv=0; //錯誤指標
                        if(isset($ch_isbn_10['error'])){
                            $_lv=$_lv+1;
                        }
                        if(isset($ch_isbn_13['error'])){
                            $_lv=$_lv+3;
                        }

                        switch($_lv){
                            case 1:
                            //10碼錯誤，利用13碼轉換更新
                                $book_isbn_10=isbn_13_to_10($book_code);
                                $book_isbn_13=$book_code;
                            break;

                            case 3:
                            //13碼錯誤，利用10碼轉換更新
                                $book_isbn_10=$book_code;
                                $book_isbn_13=isbn_10_to_13($book_code);
                            break;

                            case 4:
                                $err_msg='GET_BOOKS_INFO() IS INVALID';
                                $this->err_report($err_msg);
                            break;
                        }

                        $book_name      =addslashes(strip_tags($arry_book_info_online['book_name'][0]));
                        $book_author    =addslashes(strip_tags($arry_book_info_online['book_author'][0]));
                        $book_publisher =addslashes(strip_tags($arry_book_info_online['book_publisher'][0]));
                        $book_page_count=0;
                        $book_word      =0;
                        $book_note      ='';
                        $book_phonetic  ='無';
                        $keyin_cdate    ="NOW()";
                        $keyin_mdate    ="NULL";
                        $keyin_ip       =get_ip();

                        $sql="
                            # for mssr_book_global
                            INSERT INTO `mssr_book_global` SET
                                `create_by`         =  {$create_by      } ,
                                `edit_by`           =  {$edit_by        } ,
                                `book_id`           =  {$book_id        } ,
                                `book_sid`          = '{$book_sid_global}',
                                `book_isbn_10`      = '{$book_isbn_10   }',
                                `book_isbn_13`      = '{$book_isbn_13   }',
                                `book_name`         = '{$book_name      }',
                                `book_author`       = '{$book_author    }',
                                `book_publisher`    = '{$book_publisher }',
                                `book_page_count`   =  {$book_page_count} ,
                                `book_word`         =  {$book_word      } ,
                                `book_note`         = '{$book_note      }',
                                `book_phonetic`     = '{$book_phonetic  }',
                                `keyin_cdate`       =  {$keyin_cdate    } ,
                                `keyin_mdate`       =  {$keyin_mdate    } ,
                                `keyin_ip`          = '{$keyin_ip       }';
                        ";
                        $conn_mssr->exec($sql);

                        $arrys_book_info[0]['book_sid']      =$book_sid_global;
                        $arrys_book_info[0]['book_name']     =$book_name;
                        $arrys_book_info[0]['book_author']   =$book_author;
                        $arrys_book_info[0]['book_publisher']=$book_publisher;
                    }else{
                        $arrys_book_info=[];
                    }
                }else{
                    $arrys_book_info=$db_results;
                }

                //回傳
                return $arrys_book_info;
            }

            Protected function verify_parameter($uid=0){
            //參數檢驗

                if(isset($uid)&&(int)$uid!==0){

                }else{
                    $err_msg='VERIFY_PARAMETER() IS INVALID';
                    $this->err_report($err_msg);
                }
            }
            Protected function err_report($err_msg=NULL){
            //錯誤回報

                echo '<p>'.'INC MOBILE_BORROW '.$err_msg.'</p>';
                die();
            }

            //建構子
            function __construct(){
                if(!$this->is_online_version){
                    $this->document_root=$_SERVER['DOCUMENT_ROOT']."/new_cl_ncu";
                }else{
                    $this->document_root=$_SERVER['DOCUMENT_ROOT']."";
                }
            }

            //解構子
            function __destruct(){
            }

            //自訂
            Protected function db_conn($db_name=''){
            //取得連線資訊

                if(!is_string($db_name)||trim($db_name)===''){
                    $err_msg='DB_CONN() IS INVALID';
                    $this->err_report($err_msg);
                }

                if(!$this->is_online_version){
                    switch(trim($db_name)){
                        case 'mssr':
                            $arry_conn=array(
                                'db_host'   =>'localhost',
                                'db_name'   =>'mssr',
                                'db_user'   =>'root',
                                'db_pass'   =>'12345',
                                'db_encode' =>'UTF8'
                            );
                            $db_host  =$arry_conn['db_host'];
                            $db_user  =$arry_conn['db_user'];
                            $db_pass  =$arry_conn['db_pass'];
                            $db_name  =$arry_conn['db_name'];
                            $db_encode=$arry_conn['db_encode'];
                        break;
                        case 'user':
                            $arry_conn=array(
                                'db_host'   =>'localhost',
                                'db_name'   =>'user',
                                'db_user'   =>'root',
                                'db_pass'   =>'12345',
                                'db_encode' =>'UTF8'
                            );
                            $db_host  =$arry_conn['db_host'];
                            $db_user  =$arry_conn['db_user'];
                            $db_pass  =$arry_conn['db_pass'];
                            $db_name  =$arry_conn['db_name'];
                            $db_encode=$arry_conn['db_encode'];
                        break;
                        default:
                            $err_msg='DB_CONN() IS INVALID';
                            $this->err_report($err_msg);
                        break;
                    }
                }else{
                    switch(trim($db_name)){
                        case 'mssr':
                            $arry_conn=array(
                                'db_host'   =>'140.115.16.104',
                                'db_name'   =>'mssr',
                                'db_user'   =>'mssr',
                                'db_pass'   =>'UeR1up0u',
                                'db_encode' =>'UTF8'
                            );
                            $db_host  =$arry_conn['db_host'];
                            $db_user  =$arry_conn['db_user'];
                            $db_pass  =$arry_conn['db_pass'];
                            $db_name  =$arry_conn['db_name'];
                            $db_encode=$arry_conn['db_encode'];
                        break;
                        case 'user':
                            $arry_conn=array(
                                'db_host'   =>'140.115.16.104',
                                'db_name'   =>'user',
                                'db_user'   =>'mssr',
                                'db_pass'   =>'UeR1up0u',
                                'db_encode' =>'UTF8'
                            );
                            $db_host  =$arry_conn['db_host'];
                            $db_user  =$arry_conn['db_user'];
                            $db_pass  =$arry_conn['db_pass'];
                            $db_name  =$arry_conn['db_name'];
                            $db_encode=$arry_conn['db_encode'];
                        break;
                        default:
                            $err_msg='DB_CONN() IS INVALID';
                            $this->err_report($err_msg);
                        break;
                    }
                }

                //連結物件判斷
                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                //執行連線
                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err_msg='DB_CONN() IS INVALID';
                    $this->err_report($err_msg);
                }

                //回傳
                return $conn;
            }
            Protected function db_result($conn_type='pdo',$conn='',$sql,$arry_limit=array()){
            //---------------------------------------------------
            //取得資料筆數
            //---------------------------------------------------
            //$conn_type    資料庫連結類型      mysql | pdo     預設 mysql
            //$conn         資料庫連結物件
            //$sql          SQL查詢字串
            //$arry_limit   資料筆數限制陣列(等同LIMIT inx,size)
            //---------------------------------------------------

                //檢核參數
                if(!in_array(trim($conn_type),array('','mysql','pdo'))){
                    $err='DB_RESULT:CONN_TYPE INVALID';
                    die($err);
                }else{
                    if($conn_type===''){
                        $conn_type='mysql';
                    }
                }
                if((!$conn)||(!is_object($conn))){
                    $err='DB_RESULT:NO CONN';
                    die($err);
                }
                if(!$sql){
                    $err='DB_RESULT:NO SQL';
                    die($err);
                }

                switch($conn_type){
                //資料庫連結類型

                    case 'pdo':
                    //連結類型為pdo

                        //連結物件判斷
                        $has_conn=false;

                        if(!$conn){
                            $err='DB_RESULT:NO CONN';
                            die($err);
                        }else{
                            $has_conn=false;
                        }

                        //SQL敘述
                        if(!empty($arry_limit)){
                           $a=$arry_limit[0];
                           $b=$arry_limit[1];
                           $sql.=" LIMIT {$a},{$b}";
                        }
                        //echo $sql;

                        //資料庫
                        $err='DB_RESULT:QUERY FAIL';
                        $result=$conn->query($sql) or
                        die($err);

                        //建立資料集陣列
                        $arry_result=array();

                        if(($result->rowCount())!==0){
                            while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                                $arry_result[]=$arry_row;
                            }
                        }

                        //傳回資料集陣列
                        return $arry_result;

                        //釋放資源
                        //mysql_free_result($result);
                        if($has_conn==true){
                            $conn=NULL;
                        }

                    break;

                    default:
                    //例外處理
                        $err='DB_RESULT:CONN_TYPE INVALID';
                        die($err);
                    break;
                }
            }
        }
?>