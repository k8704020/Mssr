<?php
//-------------------------------------------------------
//函式: school_usage_info()
//用途: 取得學校使用率
//日期: 2015年02月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    ////---------------------------------------------------
    ////設置測試資料
    ////---------------------------------------------------
    //
    //    $school_code    =trim('gcp');
    //    $time           =trim('2014-11');
    //
    //    $oschool_usage_info   =new school_usage_info();
    //    $arry_class_code_info =$oschool_usage_info->get_class_code_borrow_info($school_code,$time);
    //    $class_code_borrow_cno=$oschool_usage_info->get_class_code_borrow_cno;
    //
    //    echo "<Pre>";
    //    print_r($arry_class_code_info);
    //    echo "</Pre>";
    //
    //    echo "<Pre>";
    //    print_r($class_code_borrow_cno);
    //    echo "</Pre>";

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        class school_usage_info{

            //取得班級使用借閱的數量
            public $get_class_code_borrow_cno=0;

            public function get_class_code_borrow_info($school_code,$time){
            //取得班級使用借閱的名單

                //參數檢驗
                $this->verify_parameter($school_code,$time);

                //參數設置
                $school_code=addslashes(trim($school_code));
                $time       =addslashes(trim($time));
                $arry_class_code_borrow_info[$time]=array();

                //建立連線
                $conn_mssr=$this->db_conn('mssr');

                //sql敘述
                $time =trim($time);
                $year =date("Y",strtotime($time));
                $month=date("m",strtotime($time));
                $begin=mktime(0,0,0,$month,1,$year);
                $end  =mktime(0,0,0,$month+1,0,$year);
                $diff =$end-$begin;
                $days =($diff/86400)+1;

                $sql="
                    SELECT
                        `user`.`semester`.`start`
                    FROM `user`.`semester`
                    WHERE 1=1
                        AND `user`.`semester`.`school_code`='{$school_code}'
                        AND `user`.`semester`.`start`<='{$time}-1 00:00:00'
                        AND (
                            `user`.`semester`.`end`  >='{$time}-{$days} 00:00:00'
                            OR
                            `user`.`semester`.`end`  >='{$time}-1 00:00:00'
                        )
                    LIMIT 1
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    $semester_start=trim($db_results[0]['start']);
                }else{
                    //回傳
                    return $arry_class_code_borrow_info;
                }

                $sql="
                    SELECT
                        `user`.`semester`.`end`
                    FROM `user`.`semester`
                    WHERE 1=1
                        AND `user`.`semester`.`school_code`='{$school_code}'
                        AND `user`.`semester`.`start`<='{$time}-1 00:00:00'
                        AND (
                            `user`.`semester`.`end`  >='{$time}-{$days} 00:00:00'
                            OR
                            `user`.`semester`.`end`  >='{$time}-1 00:00:00'
                        )
                    LIMIT 1
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    $semester_end=trim($db_results[0]['end']);
                }else{
                    //回傳
                    return $arry_class_code_borrow_info;
                }

                $sql="
                    SELECT
                        `user`.`class`.`class_code`
                    FROM `user`.`semester`
                        INNER JOIN `user`.`class` ON
                        `user`.`semester`.`semester_code`=`user`.`class`.`semester_code`
                    WHERE 1=1
                        AND `user`.`semester`.`school_code`='{$school_code}'
                        AND `user`.`semester`.`start`<='{$time}-1 00:00:00'
                        AND `user`.`semester`.`end`  >='{$time}-{$days} 00:00:00'
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    $class_code_list="";
                    foreach($db_results as $inx=>$db_result){
                        $class_code_list.="'";
                        $class_code_list.=trim($db_result['class_code']);
                        $class_code_list.="'";
                        if($inx!==count($db_results)-1)$class_code_list.=",";
                    }
                }else{
                    //回傳
                    return $arry_class_code_borrow_info;
                }

                $sql="
                    SELECT
                        #`user`.`student`.`start`,
                        #`user`.`student`.`end`,
                        `user`.`student`.`uid`
                    FROM `user`.`student`
                    WHERE 1=1
                        AND `user`.`student`.`class_code` IN ({$class_code_list})
                        AND `user`.`student`.`start` >='{$semester_start}'
                        AND `user`.`student`.`start` <='{$semester_end}'
                        AND `user`.`student`.`end`   >='{$time}-{$days} 00:00:00'
                        AND `user`.`student`.`end`   <='{$semester_end}'
                    GROUP BY `user`.`student`.`uid`
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    $uid_list="";
                    foreach($db_results as $inx=>$db_result){
                        $uid_list.=(int)($db_result['uid']);
                        if($inx!==count($db_results)-1)$uid_list.=",";
                    }
                }else{
                    //回傳
                    return $arry_class_code_borrow_info;
                }

                $sql="
                    SELECT
                        `mssr`.`mssr_book_borrow_log`.`user_id`
                    FROM `mssr`.`mssr_book_borrow_log`
                    WHERE 1=1
                        AND (`mssr`.`mssr_book_borrow_log`.`borrow_sdate`) BETWEEN '{$time}-01 00:00:00' AND '{$time}-{$days} 23:59:59'
                        AND `mssr`.`mssr_book_borrow_log`.`user_id` IN ({$uid_list})
                    GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    $uid_list="";
                    foreach($db_results as $inx=>$db_result){
                        $uid_list.=(int)($db_result['user_id']);
                        if($inx!==count($db_results)-1)$uid_list.=",";
                    }
                }else{
                    //回傳
                    return $arry_class_code_borrow_info;
                }

                $sql="
                    SELECT
                        `user`.`student`.`class_code`
                    FROM `user`.`student`
                    WHERE 1=1
                        AND `user`.`student`.`uid` IN ({$uid_list})
                        AND `user`.`student`.`start` >='{$semester_start}'
                        AND `user`.`student`.`start` <='{$semester_end}'
                        AND `user`.`student`.`end`   >='{$time}-{$days} 00:00:00'
                        AND `user`.`student`.`end`   <='{$semester_end}'
                    GROUP BY `user`.`student`.`class_code`
                ";
                $db_results=$this->db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array());
                if(!empty($db_results)){
                    foreach($db_results as $inx=>$db_result){
                        $rs_class_code=trim($db_result['class_code']);
                        $arry_class_code_borrow_info[$time][]=$rs_class_code;
                    }
                }
                //回傳, 指定
                $this->get_class_code_borrow_cno=count($arry_class_code_borrow_info[$time]);
                return $arry_class_code_borrow_info;
            }

            Protected function verify_parameter($school_code='',$time=''){
            //參數檢驗

                if((is_string($school_code))&&(is_string($time))){
                    if(!isset($school_code)||(trim($school_code)==='')||(!is_string($school_code))){
                        $err_msg='SCHOOL_CODE IS INVALID';
                        $this->err_report($err_msg);
                    }
                    if(!isset($time)||(trim($time)==='')||(!is_string($time))){
                        $err_msg='TIME IS INVALID';
                        $this->err_report($err_msg);
                    }
                }else{
                    $err_msg='VERIFY_PARAMETER() IS INVALID';
                    $this->err_report($err_msg);
                }
            }
            Protected function err_report($err_msg=NULL){
            //錯誤回報

                echo '<p>'.'INC SCHOOL_USAGE_INFO '.$err_msg.'</p>';
                die();
            }

            //建構子
            function __construct(){
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