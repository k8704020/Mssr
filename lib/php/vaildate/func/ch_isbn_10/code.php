<?php
//-------------------------------------------------------
//函式: ch_isbn_10()
//用途: 國際標準書號檢核
//日期: 2013年08月20日
//作者: tim@max-life
//-------------------------------------------------------

    function ch_isbn_10($input, $convert=false){
    //---------------------------------------------------
    //國際標準書號檢核
    //---------------------------------------------------
    //$input        ISBN 10碼
    //$convert      是否轉碼
    //---------------------------------------------------

        $output = false;
        if (strlen($input) < 9){
            $output = array('error'=>'ISBN too short.');
        }
        if (strlen($input) > 10){
            $output = array('error'=>'ISBN too long.');
        }
        if (!$output){
            $runningTotal = 0;
            $r = 1;
            $multiplier = 10;
            for ($i = 0; $i < 10 ; $i++){
                $nums[$r] = substr($input, $i, 1);
                $r++;
            }
            $inputChecksum = array_pop($nums);
            foreach($nums as $key => $value){
                $runningTotal += $value * $multiplier;
                //echo $value . 'x' . $multiplier . ' + ';
                $multiplier --;
                if ($multiplier === 1){
                    break;
                }
            }
            //echo ' = ' . $runningTotal;
            $remainder = $runningTotal % 11;
            $checksum = $remainder == 1 ? 'X' : 11 - $remainder;
            $checksum = $checksum == 11 ? 0 : $checksum;
            $output = array('checksum'=>$checksum);
            $output['isbn10'] = substr($input, 0, 9) . $checksum;
            if ($convert){
                $output['isbn13'] = isbn10to13($output['isbn10']);
            }
            if ((is_numeric($inputChecksum) || $inputChecksum == 'X') && $inputChecksum != $checksum){
                $output['error'] = 'Input checksum digit incorrect: ISBN not valid';
                $output['input_checksum'] = $inputChecksum;
            }
        }

        //回傳
        return $output;
    }
?>