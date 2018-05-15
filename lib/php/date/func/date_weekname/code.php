<?php
//-------------------------------------------------------
//函式: date_weekname()
//用途: 取回中文禮拜幾名稱
//日期: 2011年12月25日
//作者: jeff@max-life
//-------------------------------------------------------

    function date_weekname($key,$type='numeric'){
	//---------------------------------------------------
	//取回中文禮拜幾名稱
	//---------------------------------------------------
	//$key  鍵值
	//$type 鍵值型態    numeric/string,預設:numeric
	//
	//      $type='numeric'   0-6
	//        0 --> 禮拜日
	//        6 --> 禮拜六
    //
	//      $type='string'    sun,mon,tue,wed,thu,fri,sat
	//---------------------------------------------------

        //參數檢驗
        if(!isset($key)){
            return false;
        }

        $arry_type=array('numeric','string');
        if(!isset($type)||(trim($type)=='')){
            $type='numeric';
        }else{
            if(!in_array(strtolower($type),$arry_type)){
                $type='numeric';
            }
        }

        //取回
        $key =strtolower($key);
        $type=strtolower($type);

		if($type=="numeric"){
			$arry_weekname=array(
                0=>"禮拜日",
                1=>"禮拜一",
                2=>"禮拜二",
                3=>"禮拜三",
                4=>"禮拜四",
                5=>"禮拜五",
                6=>"禮拜六"
			);
			return $arry_weekname[$key];
		}else if($type=="string"){
			$arry_weekname=array(
                "sun"=>"禮拜日",
                "mon"=>"禮拜一",
                "tue"=>"禮拜二",
                "wed"=>"禮拜三",
                "thu"=>"禮拜四",
                "fri"=>"禮拜五",
                "sat"=>"禮拜六"
			);
			return $arry_weekname[$key];
		}
	}
?>