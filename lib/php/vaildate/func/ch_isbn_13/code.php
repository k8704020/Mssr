<?php
//-------------------------------------------------------
//函式: ch_isbn_13()
//用途: 國際標準書號檢核
//日期: 2013年08月20日
//作者: tim@max-life
//-------------------------------------------------------

    function ch_isbn_13($input, $convert=false){
    //---------------------------------------------------
    //國際標準書號檢核
    //---------------------------------------------------
    //$input        ISBN 13碼
    //$convert      是否轉碼
    //---------------------------------------------------

        $output = FALSE;
        if (strlen($input) < 12){
            $output = array('error'=>'ISBN too short.');
        }
        if (strlen($input) > 13){
            $output = array('error'=>'ISBN too long.');
        }
        if (!$output){
            $runningTotal = 0;
            $r = 1;
            $multiplier = 1;
            for ($i = 0; $i < 13 ; $i++){
                $nums[$r] = substr($input, $i, 1);
                $r++;
            }
            $inputChecksum = array_pop($nums);
            foreach($nums as $key => $value){
                $runningTotal += $value * $multiplier;
                $multiplier = $multiplier == 3 ? 1 : 3;
            }
            $div = $runningTotal / 10;
            $remainder = $runningTotal % 10;

            $checksum = $remainder == 0 ? 0 : 10 - substr($div, -1);

            $output = array('checksum'=>$checksum);
            $output['isbn13'] = substr($input, 0, 12) . $checksum;
            if ($convert){
                $output['isbn10'] = isbn13to10($output['isbn13']);
            }
            if (is_numeric($inputChecksum) && $inputChecksum != $checksum){
                $output['error'] = 'Input checksum digit incorrect: ISBN not valid';
                $output['input_checksum'] = $inputChecksum;
            }
        }

        //回傳
        return $output;
    }
?>