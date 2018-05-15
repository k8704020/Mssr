<?php
//-------------------------------------------------------
//函式: date_diff.class
//用途: 日期差異類別
//-------------------------------------------------------

    class date_diff
    {
    //---------------------------------------------------
    //函式: date_diff.class
    //用途: 日期差異類別
    //---------------------------------------------------
        public $dateFrom    ;
        public $dateTo      ;
        private $intDays    ;
        private $intHours   ;
        private $intMinutes ;
        private $sError     ;

        function __construct( $dateFrom , $dateTo ){
            $from = $this->isValidDate($dateFrom);
            $to   = $this->isValidDate($dateTo);

            if( $from && $to ){
                $this->setdate_diff( $from , $to );
            }
        }

        public function getError(){
            return "Invalid date format from dateFrom or dateTo.";
        }

        private function setdate_diff( $from = array() , $to = array() ){
            //$date1 = time();
            //$date2 = mktime(0,0,0,02,29,2008);
            //$dateDiff = $date1 - $date2;
            $dateDiff = mktime( $from['hour']  , $from['minutes'] , $from['seconds'] ,
                                $from['month'] , $from['day']     , $from['year'] ) -
                        mktime( $to['hour']    , $to['minutes']   , $to['seconds'] ,
                                $to['month']   , $to['day']       , $to['year'] );
            $this->intDays    = floor(  $dateDiff / (60*60*24) );
            $this->intHours   = floor( ($dateDiff - ($this->intDays*60*60*24) ) / (60*60) );
            $this->intMinutes = floor( ($dateDiff - ($this->intDays*60*60*24) - ($this->intHours*60*60) ) /60 );
        }

        private function isValidDate($sDate){
            $dateString = split( " "    , $sDate      );
            $dateParts  = split( "[/-]" , $dateString[0] );
            $dateParts2 = split( "[:]"  , $dateString[1] );

            if( !checkdate($dateParts[1], $dateParts[2], $dateParts[0]) ){
                return false;
            }else{
                return array(
                     'year'    => $dateParts[0] ,
                     'month'   => $dateParts[1] ,
                     'day'     => $dateParts[2] ,
                     'hour'    => $dateParts2[0] ,
                     'minutes' => $dateParts2[1] ,
                     'seconds' => $dateParts2[2]
                    );
            }
        }

        public function getDiffInDays(){ return $this->intDays;    }
        public function getDiffInHours(){ return $this->intHours;   }
        public function getDiffInMinutes(){ return $this->intMinutes; }

        function __destruct(){
            unset($this->dateFrom);
            unset($this->dateTo);
            unset($this->intDays);
            unset($this->intHours);
            unset($this->intMinutes);
        }
    }
?>
