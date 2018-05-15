<?php
//-------------------------------------------------------
//函式: date_diff_by_unit.class
//用途: 日期差異類別
//-------------------------------------------------------

    class date_diff_by_unit{
    //---------------------------------------------------
    //函式: date_diff_by_unit.class
    //用途: 日期差異類別
    //---------------------------------------------------

        //to store the value of the first date passed
        var $firstDate;
        //to store the value of the second date passed
        var $secondDate;
        //to store the value of the format in which date is to be returned
        var $interval;
        //to store the difference between the two dates
        var $diff;
        //to store the value of the flag
        var $flag = 0;
        //to calculate the difference in months
        var $monthBegin;
        //to calculate the difference in months
        var $monthEnd;
        //to calculate the difference in months
        var $monthDiff;

        function date_diff_by_unit($firstDate,$secondDate,$interval){
            $this->firstDate = strtotime($firstDate);
            $this->secondDate = strtotime($secondDate);
            $this->interval = $interval;
            $this->checkValid();
        }

        /*****************************
        function name:checkValid()
        description:check validity of the dates passed
        purpose:to check validity of the dates passed
        arguments:nothing
        returns:nothing
        ******************************/
        function checkValid(){
            if($this->firstDate == -1){
                $this->flag = 1;
            }
            if($this->secondDate == -1){
                $this->flag = 2;
            }
            if($this->secondDate < $this->firstDate){
                $this->flag = 3;
            }
            if($this->flag == 1){
                echo "first date entered is invalid";
            }
            elseif($this->flag == 2){
                echo "second date entered is invalid";
            }
            elseif($this->flag == 3){
                echo "second date cannot be less than firstDate";
            }
        }

        /*****************************
        function name:calDiff
        description:calculate difference between the dates
        purpose:to calculate difference between the dates and return them in all formats
        arguments:nothing
        returns:returns the difference in all the formats
        ******************************/
        function calDiff(){
            $this->diff = $this->secondDate - $this->firstDate;
            switch($this->interval){
                //return the difference in seconds
                case "s":
                    return $this->diff;
                //return the difference in minutes
                case "m":
                    return (ceil($this->diff/60));
                //return the difference in hours
                case "h":
                    return (ceil($this->diff/3600));
                //return the difference in days
                case "d":
                    return (ceil($this->diff/86400));
                //return the difference in weeks
                case "ww":
                    return (ceil($this->diff/604800));
                //return the difference in years
                case "y":
                    return (date("Y",$this->secondDate)-date("Y",$this->firstDate));
                //return the difference in months
                case "mm":
                    $this->monthBegin = (date("Y",$this->firstDate)*12)+date("n",$this->firstDate);
                    $this->monthEnd = (date("Y",$this->secondDate)*12)+date("n",$this->secondDate);
                    $this->monthDiff = $this->monthEnd-$this->monthBegin;
                    return $this->monthDiff;
                //return the difference in days
                default:
                    return(ceil($this->dif/86400));
            }
        }
    }
?>
