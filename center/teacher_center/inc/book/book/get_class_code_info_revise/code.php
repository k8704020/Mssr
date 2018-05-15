<?php
//-------------------------------------------------------
//函式: get_class_code_info_revise()
//用途: 提取班級資訊(修正)
//日期: 2013年10月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function get_class_code_info_revise($conn='',$school_code='',$grade=0,$classroom=0,$compile_flag=false,$arry_conn){
    //---------------------------------------------------
    //函式: get_class_code_info_revise()
    //用途: 提取班級資訊(修正)
    //---------------------------------------------------
    //$conn             資料庫連結物件
    //$school_code      學校代號            預設空字串 => 不分學校
    //$grade            年級主索引          預設0      => 不分年級
    //$classroom        班級主索引          預設0      => 不分班級
    //$compile_flag     是否轉換班級名稱    預設       => 不轉換
    //$arry_conn        資料庫資訊陣列
    //---------------------------------------------------

        //-----------------------------------------------
        //檢核參數
        //-----------------------------------------------

            if(!isset($school_code)||(trim($school_code)==='')){
                $school_code='';
            }else{
                $school_code=trim($school_code);
            }

            if(!isset($grade)||((int)$grade===0)){
                $grade=0;
            }else{
                $grade=(int)$grade;
            }

            if(!isset($classroom)||((int)$classroom===0)){
                $classroom=0;
            }else{
                $classroom=(int)$classroom;
            }

            if(!isset($compile_flag)){
                $compile_flag=false;
            }else{
                $compile_flag=$compile_flag;
            }

            if((!$arry_conn)||(empty($arry_conn))){
                $err='GET_CLASS_CODE_INFO_REVISE:NO ARRY_CONN';
                die($err);
            }

            //目前日期
            $date=date("Y-m-d");

        //-----------------------------------------------
        //資料建立
        //-----------------------------------------------

            //資料庫資訊
            $db_host  =$arry_conn['db_host'];
            $db_user  =$arry_conn['db_user'];
            $db_pass  =$arry_conn['db_pass'];
            $db_name  =$arry_conn['db_name'];
            $db_encode=$arry_conn['db_encode'];


            //連結物件判斷
            $has_conn=false;

            if(!$conn){
                $has_conn=true;

                $conn_info='mysql'.":host={$db_host}".";dbname={$db_name}";
                $options = array(
                    PDO::ATTR_ERRMODE,           PDO::ERRMODE_SILENT,           //設置錯誤提示，只獲取代碼
                    PDO::ATTR_CASE,              PDO::CASE_NATURAL,             //列名按照原始的方式
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$db_encode}"    //設置語系
                );

                try{
                    $conn=@new PDO($conn_info, $db_user, $db_pass,$options);
                }catch(PDOException $e){
                    $err ='GET_CLASS_CODE_INFO_REVISE:CONNECT FAIL';
                    die($err);
                }
            }else{
                $has_conn=false;
            }


            //SQL敘述
            $sql ="";
            $sql.="
                SELECT
                    `semester`.`semester_code`,
                    `semester`.`school_code`,

                    `school`.`school_name`,

                    `class`.`class_code`,
                    `class`.`class_category`,
                    `class`.`grade`,
                    `class`.`classroom`
                FROM `semester`
                    INNER JOIN `school` ON
                    `semester`.`school_code`=`school`.`school_code`

                    INNER JOIN `class` ON
                    `semester`.`semester_code`=`class`.`semester_code`
                WHERE 1=1
                    #AND DATE(`semester`.`start`) < '{$date}'
                    #AND DATE(`semester`.`end`)   > '{$date}'
                    AND `class`.`class_category` <>0
                    AND `class`.`grade` <>0
                    AND `class`.`classroom` <>0
            ";
            if($school_code!==''){
                $sql.="
                    AND `semester`.`school_code` = '{$school_code}'
                ";
            }
            if($grade!==0){
                $sql.="
                    AND `class`.`grade` = {$grade}
                ";
            }
            if($classroom!==0){
                $sql.="
                    AND `class`.`classroom` = {$classroom}
                ";
            }
            $sql.="
                ORDER BY `class`.`grade`,`class`.`classroom` ASC
            ";

            //資料庫
            $err='GET_CLASS_CODE_INFO_REVISE:QUERY FAIL';
            $result=$conn->prepare($sql);
            $result->execute() or
            die($err);

            //建立資料集陣列
            $arrys_result=array();

            if(($result->rowCount())!==0){
            //有資料存在
                while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                    $arrys_result[]=$arry_row;
                }
            }

        //-----------------------------------------------
        //查找, 班級對應所屬名稱
        //-----------------------------------------------

            if($compile_flag){

                $sql="
                    SELECT *
                    FROM `class_name`
                    WHERE 1=1
                ";

                //資料庫
                $err='GET_CLASS_CODE_INFO_REVISE:QUERY FAIL';
                $result=$conn->prepare($sql);
                $result->execute() or
                die($err);

                //建立資料集陣列
                $tmp_arrys_result=array();

                //初始化, 班級對應所屬名稱
                $arrys_class_code_rev=array();

                //建立新資料集陣列
                $new_arrys_result=array();

                if(($result->rowCount())!==0){
                //有資料存在
                    while($arry_row=$result->fetch(PDO::FETCH_ASSOC)){
                        $tmp_arrys_result[]=$arry_row;
                    }
                }


                //資料整理
                foreach($tmp_arrys_result as $inx=>$tmp_arry_result){
                    $rs_class_category=(int)$tmp_arry_result['class_category'];
                    $rs_classroom     =(int)$tmp_arry_result['classroom'];
                    $rs_class_name    =trim($tmp_arry_result['class_name']);

                    //匯入, 班級對應所屬名稱
                    $arrys_class_code_rev[$rs_class_category][$rs_classroom]=$rs_class_name;
                }


                //資料轉換
                foreach($arrys_result as $inx=>$arry_result){
                    $rs_semester_code   =trim($arry_result[trim('semester_code  ')]);
                    $rs_school_code     =trim($arry_result[trim('school_code    ')]);
                    $rs_school_name     =trim($arry_result[trim('school_name    ')]);
                    $rs_class_code      =trim($arry_result[trim('class_code     ')]);
                    $rs_class_category  =(int)$arry_result[trim('class_category ')];
                    $rs_grade           =(int)$arry_result[trim('grade          ')];
                    $rs_classroom       =(int)$arry_result[trim('classroom      ')];

                    //轉換班級名稱
                    if(isset($arrys_class_code_rev[$rs_class_category][$rs_classroom])){
                        $new_classroom  =$arrys_class_code_rev[$rs_class_category][$rs_classroom];
                    }else{
                        $new_classroom  =$rs_classroom;
                    }

                    //回填
                    $new_arrys_result[$inx]['semester_code']    =$rs_semester_code;
                    $new_arrys_result[$inx]['school_code']      =$rs_school_code;
                    $new_arrys_result[$inx]['school_name']      =$rs_school_name;
                    $new_arrys_result[$inx]['class_code']       =$rs_class_code;
                    $new_arrys_result[$inx]['class_category']   =$rs_class_category;
                    $new_arrys_result[$inx]['grade']            =$rs_grade;
                    $new_arrys_result[$inx]['classroom']        =$new_classroom;
                }

                //傳回新資料集陣列
                return $new_arrys_result;
            }else{

                //傳回資料集陣列
                return $arrys_result;
            }

        //-----------------------------------------------
        //關閉連線
        //-----------------------------------------------

            if($has_conn==true){
                $conn=NULL;
            }
    }
?>