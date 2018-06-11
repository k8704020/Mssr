<?php
//-------------------------------------------------------
//函式: article_eagle()
//用途: 發文鷹架
//-------------------------------------------------------

    function communicaton_type(){
    //---------------------------------------------------
    //函式: article_eagle()
    //用途: 發文鷹架
    //---------------------------------------------------
    //$eagle_type   鷹架類型    內容=>1 | 代號=>2
    //---------------------------------------------------

        //-----------------------------------------------
        //鷹架類型 => 內容
        //-----------------------------------------------

            $type_array=array(
                trim('熱門推薦')  =>array(
                    trim('所有玩家')=>array(
                        trim('上學期的排行')=>array("rec","all","last_semester"),
                        trim('這學期的排行')=>array("rec","all","now_semester"),
                        trim('上個月的排行')=>array("rec","all","last_month"),
						trim('這個月的排行')=>array("rec","all","now_month"),
						trim('上一周的排行')=>array("rec","all","last_week"),
						trim('這周的排行')=>array("rec","all","now_week")
                    ),
                    trim('自己學校所有人')=>array(
                        trim('上學期的排行')=>array("rec","school_all","last_semester"),
                        trim('這學期的排行')=>array("rec","school_all","now_semester"),
                        trim('上個月的排行')=>array("rec","school_all","last_month"),
						trim('這個月的排行')=>array("rec","school_all","now_month"),
						trim('上一周的排行')=>array("rec","school_all","last_week"),
						trim('這周的排行')=>array("rec","school_all","now_week")
					),
					trim('自己學校同年級')=>array(
                        trim('這學期的排行')=>array("rec","school_grade","now_semester"),
                        trim('上個月的排行')=>array("rec","school_grade","last_month"),
						trim('這個月的排行')=>array("rec","school_grade","now_month"),
						trim('上一周的排行')=>array("rec","school_grade","last_week"),
						trim('這周的排行')=>array("rec","school_grade","now_week")
                    ),
					trim('自己學校同班級')=>array(
                        trim('這學期的排行')=>array("rec","school_class","now_semester"),
                        trim('上個月的排行')=>array("rec","school_class","last_month"),
						trim('這個月的排行')=>array("rec","school_class","now_month"),
						trim('上一周的排行')=>array("rec","school_class","last_week"),
						trim('這周的排行')=>array("rec","school_class","now_week")
                    ),
                ),
                trim('熱門書籍')  =>array(
                    trim('所有學校同年級')=>array(
                        trim('全部時間的排行')=>array("books","all_school_grade","total"),
                        trim('上學期')=>array("books","all_school_grade","semester"),
						trim('上個月的排行')=>array("books","all_school_grade","month"),
						trim('上一周的排行')=>array("books","all_school_grade","week"),
						trim('這周的排行')=>array("books","school_class","now_week")
                    ),
                    trim('自己學校同年級')=>array(
                        trim('全部時間的排行')=>array("books","school_grade","total"),
                        trim('上學期')=>array("books","school_grade","semester"),
						trim('上個月的排行')=>array("books","school_grade","month"),
						trim('上一周的排行')=>array("books","school_grade","week"),
						trim('這周的排行')=>array("books","school_class","now_week")
                    ),
					trim('自己學校同班級')=>array(
                        trim('全部時間的排行')=>array("books","school_class","total"),
                        trim('上學期')=>array("books","school_class","semester"),
						trim('上個的排行月')=>array("books","school_class","month"),
						trim('上一周的排行')=>array("books","school_class","week"),
						trim('這周的排行')=>array("books","school_class","now_week")
                    )
                ),trim('熱門佈置')  =>array(
                    trim('所有玩家')=>array(
                    	trim('上學期的排行')=>array("star","all","last_semester"),
                        trim('這學期的排行')=>array("star","all","now_semester"),
                        trim('上個月的排行')=>array("star","all","last_month"),
						trim('這個月的排行')=>array("star","all","now_month"),
						trim('上一周的排行')=>array("star","all","last_week"),
						trim('這周的排行')=>array("star","all","now_week")
                    ),
                    trim('自己學校所有人')=>array(
                    	trim('上學期的排行')=>array("star","school_all","last_semester"),
                        trim('這學期的排行')=>array("star","school_all","now_semester"),
                        trim('上個月的排行')=>array("star","school_all","last_month"),
						trim('這個月的排行')=>array("star","school_all","now_month"),
						trim('上一周的排行')=>array("star","school_all","last_week"),
						trim('這周的排行')=>array("star","school_all","now_week")
                    ),
					trim('自己學校同年級')=>array(
						trim('上學期的排行')=>array("star","school_grade","last_semester"),
                        trim('這學期的排行')=>array("star","school_grade","now_semester"),
                        trim('上個月的排行')=>array("star","school_grade","last_month"),
						trim('這個月的排行')=>array("star","school_grade","now_month"),
						trim('上一周的排行')=>array("star","school_grade","last_week"),
						trim('這周的排行')=>array("star","school_grade","now_week")
					),
					trim('自己學校同班級')=>array(
						trim('上學期的排行')=>array("star","school_class","last_semester"),
                        trim('這學期的排行')=>array("star","school_class","now_semester"),
                        trim('上個月的排行')=>array("star","school_class","last_month"),
						trim('這個月的排行')=>array("star","school_class","now_month"),
						trim('上一周的排行')=>array("star","school_class","last_week"),
						trim('這周的排行')=>array("star","school_class","now_week")
                    )
                )
            );

        //----------------------------------
        //回傳
        //-----------------------------------------------
		return $type_array;
    }
?>