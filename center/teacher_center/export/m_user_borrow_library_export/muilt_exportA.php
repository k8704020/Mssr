<?php
//-------------------------------------------------------
//教師中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/net/code',
                    APP_ROOT.'lib/php/fso/code',
                    APP_ROOT.'lib/php/array/code',
                    APP_ROOT.'lib/php/string/code',
                    APP_ROOT.'lib/php/plugin/code',
                    APP_ROOT.'lib/php/vaildate/code',
                    APP_ROOT.'lib/php/upload/file_upload_save/code',
                    APP_ROOT.'lib/php/plugin/func/php_excel/PHPExcel/IOFactory'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

        //調配
        set_time_limit(0);
        ini_set( 'memory_limit', '3072M' );

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

        if($config_arrys['is_offline']['center']['teacher_center']){
            $url=str_repeat("../",5).'index.php';
            header("Location: {$url}");
            die();
        }

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        $arrys_login_info=get_login_info($db_type='mysql',$arry_conn_user,$APP_ROOT);
        if(empty($arrys_login_info)){
            die();
        }

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

        if(in_array('read_the_registration_code',$config_arrys['user_area'])){
        //清空閱讀登記條碼版登入資訊

            $_SESSION['config']['user_tbl']=array();
            $_SESSION['config']['user_type']='';
            $_SESSION['config']['user_lv']=0;
            if(in_array('read_the_registration_code',$_SESSION['config']['user_area'])){
                foreach($_SESSION['config']['user_area'] as $inx=>$area){
                    if(trim($area)==='read_the_registration_code'){
                        unset($_SESSION['config']['user_area'][$inx]);
                    }
                }
            }
        }

    //---------------------------------------------------
    //SESSION
    //---------------------------------------------------

        $sess_login_info=(isset($_SESSION['tc']['t|dt']))?$_SESSION['tc']['t|dt']:array();

    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            if(!auth_check($db_type='mysql',$arry_conn_user,$sess_login_info['permission'],$auth_type='mssr_tc')){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }else{
            //權限指標
            $auth_flag=false;
            foreach($arrys_login_info as $inx=>$arry_login_info){
                if(auth_check($db_type='mysql',$arry_conn_user,$arry_login_info['permission'],$auth_type='mssr_tc'))$auth_flag=true;
            }
            if(!$auth_flag){
                $msg="您沒有權限進入，請洽詢明日星球團隊人員!";
                $jscript_back="
                    <script>
                        alert('{$msg}');
                        history.back(-1);
                    </script>
                ";
                die($jscript_back);
            }
        }

    //---------------------------------------------------
    //管理者判斷
    //---------------------------------------------------

        if(!empty($sess_login_info)){
            $is_admin=is_admin(trim($sess_login_info['permission']));
            if($is_admin){
                $sess_login_info['responsibilities']=99;
            }
        }

    //---------------------------------------------------
    //系統權限判斷
    //---------------------------------------------------
    //1     校長
    //3     主任
    //5     帶班老師
    //12    行政老師
    //14    主任帶一個班
    //16    主任帶多個班
    //22    老師帶多個班
    //99    管理者

        if(!empty($sess_login_info)){
            $auth_sys_check_lv=auth_sys_check($sess_login_info['responsibilities'],'m_user_borrow_library_export');
        }

    //---------------------------------------------------
    //接收參數
    //---------------------------------------------------

        //$post_chk=array(
        //    'class_code          '
        //);
        //$post_chk=array_map("trim",$post_chk);
        //foreach($post_chk as $post){
        //    if(!isset($_POST[$post])){
        //        $arrys_ouput['flag']='false';
        //        $arrys_ouput['msg'] ='代號,未輸入!';
        //        echo json_encode($arrys_ouput,true);
        //        die();
        //    }
        //}

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------

        //回傳訊息
        $arrys_ouput=array();
        $arrys_ouput['flag']='';
        $arrys_ouput['msg'] ='';

        //SESSION
        if(isset($sess_login_info['uid'])){$sess_user_id=(int)$sess_login_info['uid'];}
        if(isset($sess_login_info['permission'])){$sess_permission=trim($sess_login_info['permission']);}
        if(isset($sess_login_info['school_code'])){$sess_school_code=trim($sess_login_info['school_code']);}
        if(isset($sess_login_info['responsibilities'])){
            $sess_responsibilities=(int)$sess_login_info['responsibilities'];
            if(in_array($auth_sys_check_lv,array(5,14,16,22,99))){
                $sess_class_code=trim($sess_login_info['arrys_class_code'][0]['class_code']);
                $sess_grade=(int)$sess_login_info['arrys_class_code'][0]['grade'];
                $sess_classroom=(int)$sess_login_info['arrys_class_code'][0]['classroom'];
            }
        }

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------

        $arry_err=array();

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            $arrys_ouput['flag']='false';
            $arrys_ouput['msg'] ='班級代號,未輸入!';
            echo json_encode($arrys_ouput,true);
            die();
        }

    //---------------------------------------------------
    //檔案預處理
    //---------------------------------------------------

        //資料夾建立設置
        $root=str_repeat("../",4)."info/excel_file/mssr_user_borrow_library_export/{$sess_user_id}";
        $path="{$root}/file_download";
        $arrys_path=array(
            "{$root}"   =>mb_convert_encoding("{$root}",$fso_enc,$page_enc),
            "{$path}"   =>mb_convert_encoding("{$path}",$fso_enc,$page_enc)
        );
        foreach($arrys_path as $c_path=>$c_path_enc){
            if(!file_exists($c_path_enc)){
                mk_dir($c_path,$mode=0777,$recursive=true,$fso_enc);
            }
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            //-------------------------------------------
            //檢核, 學期時間
            //-------------------------------------------

                $sess_school_code=mysql_prep($sess_school_code);

                $query_sql="
                    SELECT
                        `semester`.`start`,
                        `semester`.`end`
                    FROM `school`
                        INNER JOIN `semester` ON
                        `school`.`school_code`=`semester`.`school_code`
                    WHERE 1=1
                        AND CURDATE() BETWEEN `user`.`semester`.`start` AND `user`.`semester`.`end`
                        AND `user`.`semester`.`school_code`='{$sess_school_code}'
                    LIMIT 1;
                ";
                $err='QUERY FAIL';
                $result=$conn_user->query($query_sql) or die($err);
                $rowcount=$result->rowCount();
                if($rowcount===0){
                    $arrys_ouput['flag']='false';
                    $arrys_ouput['msg'] ='班級代號錯誤!';
                    echo json_encode($arrys_ouput,true);
                    die();
                }else{
                    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                        $rs_semester_start=trim($arry_row['start']);
                        $rs_semester_end  =trim($arry_row['end']);
                    }
                }

    //---------------------------------------------------
    //SQL處理
    //---------------------------------------------------

        //-----------------------------------------------
        //查詢學校的學生
        //-----------------------------------------------

            $arrys_user_result=array();
            $arrys_book_result=array();
            $arrys_user       =array();
            $user_lists       ="";

            $query_sql="
                SELECT
                    `student`.`uid`
                FROM `student`
                    INNER JOIN `user`.`member_id_numbers` ON
                    `user`.`student`.`uid`=`user`.`member_id_numbers`.`uid`

                    INNER JOIN `user`.`class` ON
                    `user`.`student`.`class_code`=`user`.`class`.`class_code`

                    INNER JOIN `user`.`semester` ON
                    `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`
                WHERE 1=1
                    AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                    AND `user`.`semester`.`school_code`='{$sess_school_code}'
                GROUP BY `student`.`uid`
                ORDER BY `student`.`number` ASC
                ;
            ";
            $err='QUERY FAIL';
            $result=$conn_user->query($query_sql) or die($err);
            $rowcount=$result->rowCount();
            if($rowcount===0){
                $arrys_ouput['flag']='false';
                $arrys_ouput['msg'] ='沒有學生 或 沒有任何學生有『身分證字號』!';
                echo json_encode($arrys_ouput,true);
                die();
            }else{
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_user_result[]=$arry_row;
                }
                foreach($arrys_user_result as $inx=>$arry_user_result){
                    $user_id         =(int)$arry_user_result['uid'];
                    $arrys_user[$inx]=$user_id;
                }
                $user_lists="'";
                $user_lists.=implode("','",$arrys_user);
                $user_lists.="'";
            }

        //-----------------------------------------------
        //查詢借閱的書籍資料 (參考全怡表格樣式，V為必填)
        //-----------------------------------------------
        //V 身分證號
        //V 姓名
        //V 讀者類別(教師/學生/志工)
        //V 讀者年級(國小請填1-6;國中請填7-9)
        //V 讀者單位(請填數字,例如101,312)
        //V 讀者座號(請填數字,例如01,23)
        //V 讀者性別(請填男或女)
        //V 書籍登錄號
        //V 書籍名稱
        //  分類號
        //V 學年(請填數字,例如101)
        //V 學期(上學期請填1;下學期請填2)
        //V 書籍借出時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)
        //  書籍應還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)
        //  書籍歸還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)

            if(!empty($arrys_user_result)){
                $query_sql="
                    SELECT

                        `mssr`.`mssr_book_borrow_log`.`user_id`,
                        `mssr`.`mssr_book_borrow_log`.`borrow_sdate`,
                        `mssr`.`mssr_book_library`.`book_name`,
                        `mssr`.`mssr_book_library`.`book_library_code` ,

                        `user`.`member`.`name`,
                        `user`.`member`.`sex`,
                        `user`.`member_id_numbers`.`id_numbers`,

                        `user`.`semester`.`semester_year`,
                        `user`.`semester`.`semester_term`,

                        `user`.`class`.`grade`,
                        `user`.`class`.`classroom`,
                        `user`.`student`.`number`

                    FROM `mssr`.`mssr_book_borrow_log`

                        INNER JOIN `user`.`member` ON
                        `mssr`.`mssr_book_borrow_log`.`user_id`=`user`.`member`.`uid`

                        INNER JOIN `user`.`member_id_numbers` ON
                        `mssr`.`mssr_book_borrow_log`.`user_id`=`user`.`member_id_numbers`.`uid`

                        INNER JOIN `user`.`student` ON
                        `mssr`.`mssr_book_borrow_log`.`user_id`=`user`.`student`.`uid`

                        INNER JOIN `user`.`class` ON
                        `user`.`student`.`class_code`=`user`.`class`.`class_code`

                        INNER JOIN `user`.`semester` ON
                        `user`.`class`.`semester_code`=`user`.`semester`.`semester_code`

                        INNER JOIN `mssr`.`mssr_book_library` ON
                        `mssr`.`mssr_book_borrow_log`.`book_sid`=`mssr`.`mssr_book_library`.`book_sid`

                    WHERE 1=1
                        AND `mssr`.`mssr_book_borrow_log`.`user_id` IN ({$user_lists})
                        AND `mssr`.`mssr_book_borrow_log`.`borrow_sdate` BETWEEN '{$rs_semester_start} 00:00:00' AND '{$rs_semester_end} 00:00:00'
                        AND `user`.`member_id_numbers`.`id_numbers` <>''
                        AND CURDATE() BETWEEN `user`.`student`.`start` AND `user`.`student`.`end`
                ";
                //echo "<Pre>";
                //print_r($query_sql);
                //echo "</Pre>";
                //die();
                $err='QUERY FAIL';
                $result=$conn_mssr->query($query_sql) or die($err);
                $rowcount=$result->rowCount();
                if($rowcount===0){
                    $arrys_ouput['flag']='false';
                    $arrys_ouput['msg'] ='目前無任何『圖書館書籍』之借閱資料!';
                    echo json_encode($arrys_ouput,true);
                    die();
                }else{
                    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                        $arrys_book_result[]=$arry_row;
                    }
                    foreach($arrys_book_result as $inx=>$arry_book_result){

                        extract($arry_book_result, EXTR_PREFIX_ALL, "rs");

                        $tmp_arrys_info         =array();

                        $rs_user_id             =(int)$rs_user_id;
                        $rs_semester_year       =(int)$rs_semester_year-(int)1911;
                        $rs_semester_term       =(int)$rs_semester_term;
                        $rs_grade               =(int)$rs_grade;
                        $rs_classroom           =(int)$rs_classroom;

                        $rs_number              =(int)$rs_number;
                        if($rs_number<=9){
                            $rs_number="0{$rs_number}";
                        }

                        $rs_sex                 =(int)$rs_sex;
                        if($rs_sex===1){
                            $rs_sex_html        ="男";
                        }else{
                            $rs_sex_html        ="女";
                        }

                        $rs_borrow_sdate        =trim($rs_borrow_sdate);
                        $rs_borrow_sdate        =date("Y-m-d H:i",strtotime($rs_borrow_sdate));
                        $rs_borrow_sdate        =str_replace("-","",$rs_borrow_sdate);
                        $rs_borrow_sdate        =str_replace(":","",$rs_borrow_sdate);
                        $rs_borrow_sdate        =str_replace(" ","",$rs_borrow_sdate);

                        $rs_book_name           =trim($rs_book_name);
                        $rs_book_library_code   =trim($rs_book_library_code);
                        $rs_name                =trim($rs_name);
                        $rs_id_numbers          =trim($rs_id_numbers);

                        $rs_grade_classroom     ="";
                        if($rs_classroom<=9){
                            $rs_grade_classroom="{$rs_grade}0{$rs_classroom}";
                        }else{
                            $rs_grade_classroom=$rs_grade.$rs_classroom;
                        }

                        //暫存結果
                        $tmp_arrys_info[]=$rs_id_numbers;                           //V 身分證號
                        $tmp_arrys_info[]=htmlspecialchars($rs_name);               //V 姓名
                        $tmp_arrys_info[]='學生';                                   //V 讀者類別(教師/學生/志工)
                        $tmp_arrys_info[]=$rs_grade;                                //V 讀者年級(國小請填1-6;國中請填7-9)
                        $tmp_arrys_info[]=htmlspecialchars($rs_grade_classroom);    //V 讀者單位(請填數字,例如101,312)
                        $tmp_arrys_info[]=$rs_number;                               //V 讀者座號(請填數字,例如01,23)
                        $tmp_arrys_info[]=htmlspecialchars($rs_sex_html);           //V 讀者性別(請填男或女)
                        $tmp_arrys_info[]=htmlspecialchars($rs_book_library_code);  //V 書籍登錄號
                        $tmp_arrys_info[]=htmlspecialchars($rs_book_name);          //V 書籍名稱
                        $tmp_arrys_info[]='';                                       //  分類號
                        $tmp_arrys_info[]=$rs_semester_year;                        //V 學年(請填數字,例如101)
                        $tmp_arrys_info[]=$rs_semester_term;                        //V 學期(上學期請填1;下學期請填2)
                        $tmp_arrys_info[]=htmlspecialchars($rs_borrow_sdate);       //V 書籍借出時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)
                        $tmp_arrys_info[]='';                                       //  書籍應還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)
                        $tmp_arrys_info[]='';                                       //  書籍歸還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)

                        //成功資料
                        $arrys_success_info[$inx]=$tmp_arrys_info;
                    }
                }
            }

    //---------------------------------------------------
    //寫檔
    //---------------------------------------------------

        //Excel標題資訊
        $arry_excel_title_info=array(
            'A'=>'A1',
            'B'=>'B1',
            'C'=>'C1',
            'D'=>'D1',
            'E'=>'E1',
            'F'=>'F1',
            'G'=>'G1',
            'H'=>'H1',
            'I'=>'I1',
            'J'=>'J1',
            'K'=>'K1',
            'L'=>'L1',
            'M'=>'M1',
            'N'=>'N1',
            'O'=>'O1'
        );

        //-----------------------------------------------
        //成功檔案
        //-----------------------------------------------

            if(!empty($arrys_success_info)){

                //初始化, Excel物件
                $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
                $cacheSettings = array('memoryCacheSize'=>'8MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
                $obj_success_excel=new PHPExcel();
                $obj_success_excel->setActiveSheetIndex(0);

                //設定標題背景顏色單色
                $obj_success_excel->getActiveSheet(0)->getStyle('A1:O1')->applyFromArray(
                    array('fill'=>array(
                            'type' =>PHPExcel_Style_Fill::FILL_SOLID,
                            'color'=>array('rgb'=>'FFFFFF')
                        ),
                        'alignment' => array(
                            'wrap'      =>true,
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'  =>PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );

                //設置標題欄位資訊
                foreach($arry_excel_title_info as $key=>$val){
                    //欄位寬度
                    if($key==='A')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='B')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='C')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='D')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='E')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='F')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='G')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='H')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='I')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='J')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='K')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='L')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='M')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='N')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);
                    if($key==='O')$obj_success_excel->getActiveSheet(0)->getColumnDimension($key)->setWidth(30);

                    //欄位值
                    if($val==='A1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('身分證號                                                                   '));
                    if($val==='B1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('姓名                                                                       '));
                    if($val==='C1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('讀者類別(教師/學生/志工)                                                   '));
                    if($val==='D1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('讀者年級(國小請填1-6;國中請填7-9)                                          '));
                    if($val==='E1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('讀者單位(請填數字,例如101,312)                                             '));
                    if($val==='F1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('讀者座號(請填數字,例如01,23)                                               '));
                    if($val==='G1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('讀者性別(請填男或女)                                                       '));
                    if($val==='H1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('書籍登錄號                                                                 '));
                    if($val==='I1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('書籍名稱                                                                   '));
                    if($val==='J1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('分類號                                                                     '));
                    if($val==='K1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('學年(請填數字,例如101)                                                     '));
                    if($val==='L1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('學期(上學期請填1;下學期請填2)                                              '));
                    if($val==='M1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('書籍借出時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)'));
                    if($val==='N1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('書籍應還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)'));
                    if($val==='O1')$obj_success_excel->getActiveSheet(0)->setCellValue($val,trim('書籍歸還時間(請填西元格式,精準到分可為:201401011203 ;精準到日可為 20140101)'));
                }

                //設置內容資訊
                foreach($arrys_success_info as $rs_row=>$arry_success_info){
                    $rs_row=$rs_row+2;
                    foreach($arry_success_info as $rs_cell=>$val){
                        //欄位值, 上下左右置中
                        $obj_success_excel->getActiveSheet(0)->getCellByColumnAndRow($rs_cell,$rs_row)->setValueExplicit("{$arry_success_info[$rs_cell]}",PHPExcel_Cell_DataType::TYPE_STRING);
                        $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $obj_success_excel->getActiveSheet(0)->getStyleByColumnAndRow($rs_cell,$rs_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
            }

    //---------------------------------------------------
    //匯出
    //---------------------------------------------------
    //mssr_user_borrow_library_export_list

        //-----------------------------------------------
        //移除舊檔
        //-----------------------------------------------

            $arry_save_excel_info=array(
                trim('mssr_user_borrow_library_export_list')
            );
            foreach($arry_save_excel_info as $excel_neme){

                $excel_neme=trim($excel_neme);

                //執行
                if(@file_exists("{$root}/file_download/{$excel_neme}.xls")){
                    @unlink("{$root}/file_download/{$excel_neme}.xls");
                }
                if(@file_exists("{$root}/file_download/{$excel_neme}.xlsx")){
                    @unlink("{$root}/file_download/{$excel_neme}.xlsx");
                }
            }

        //-----------------------------------------------
        //儲存新檔
        //-----------------------------------------------

            $obj_success_writer=PHPExcel_IOFactory::createWriter($obj_success_excel,'Excel5');

            //執行
            if(!empty($arrys_success_info)){
                $obj_success_writer->setPreCalculateFormulas(false);
                $obj_success_writer->save("{$root}/file_download/mssr_user_borrow_library_export_list.xls");
            }

    //---------------------------------------------------
    //結果回傳
    //---------------------------------------------------

        $succes_cno                 =(int)count($arrys_success_info);
        $arrys_ouput['flag']        ='true';
        $arrys_ouput['succes_cno']  =$succes_cno;
        $arrys_ouput['msg']         ="借閱資料(共{$succes_cno}筆) 匯出成功!";
        echo json_encode($arrys_ouput,true);
        die();
?>