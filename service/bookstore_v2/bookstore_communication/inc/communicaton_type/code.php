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
                       	//trim('總得分')=>array("rec","all","total"),
                        //trim('上學期')=>array("rec","all","semester"),
						trim('上個月的排行')=>array("rec","all","month"),
						trim('上一周的排行')=>array("rec","all","week"),
						trim('這周的排行')=>array("rec","all","now_week")
                    ),
                    trim('自己學校所有人')=>array(
                        //trim('總得分')=>array("rec","school_all","total"),
                        //trim('上學期')=>array("rec","school_all","semester"),
						trim('上個月的排行')=>array("rec","school_all","month"),
						trim('上一周的排行')=>array("rec","school_all","week")
                    ),
					trim('自己學校同年級')=>array(
                        trim('總得分')=>array("rec","school_grade","total"),
						trim('上個月的排行')=>array("rec","school_grade","month"),
						trim('上一周的排行')=>array("rec","school_grade","week")
                    ),
					trim('自己學校同班級')=>array(
                        trim('總得分')=>array("rec","school_class","total"),
                        trim('上學期')=>array("rec","school_class","semester"),
						trim('上個月的排行')=>array("rec","school_class","month"),
						trim('上一周的排行')=>array("rec","school_class","week")
                    ),
                ),
                trim('熱門書籍')  =>array(
                    trim('所有學校同年級')=>array(
                        trim('全部時間的排行')=>array("books","all_school_grade","total"),
                       //ds trim('上學期')=>array("books","all_school_grade","semester"),
						trim('上個月的排行')=>array("books","all_school_grade","month"),
						trim('上一周的排行')=>array("books","all_school_grade","week")
                    ),
                    trim('自己學校同年級')=>array(
                        trim('全部時間的排行')=>array("books","school_grade","total"),
                        //ds trim('上學期')=>array("books","school_grade","semester"),
						trim('上個月的排行')=>array("books","school_grade","month"),
						trim('上一周的排行')=>array("books","school_grade","week")
                    ),
					trim('自己學校同班級')=>array(
                        trim('全部時間的排行')=>array("books","school_class","total"),
                        //ds trim('上學期')=>array("books","school_class","semester"),
						trim('上個的排行月')=>array("books","school_class","month"),
						trim('上一周的排行')=>array("books","school_class","week")
                    )
                ),trim('熱門佈置')  =>array(
                    trim('所有玩家')=>array(
                        trim('全部時間的排行')=>array("star","all","total"),
                       //ds trim('上學期')=>array("star","all","semester"),
						trim('上個月的排行')=>array("star","all","month"),
						trim('上一周的排行')=>array("star","all","week")
                    ),
                    trim('自己學校所有人')=>array(
                        trim('全部時間的排行')=>array("star","school_all","total"),
                        //ds trim('上學期')=>array("star","school_all","semester"),
						trim('上個月的排行')=>array("star","school_all","month"),
						trim('上一周的排行')=>array("star","school_all","week")
                    ),
					trim('自己學校同年級')=>array(
                        trim('全部時間的排行')=>array("star","school_grade","total"),
                       //ds trim('上學期')=>array("star","school_grade","semester"),
						trim('上個月的排行')=>array("star","school_grade","month"),
						trim('上一周的排行')=>array("star","school_grade","week")
                    ),
					trim('自己學校同班級')=>array(
                        trim('全部時間的排行')=>array("star","school_class","total"),
                       //ds trim('上學期')=>array("star","school_class","semester"),
						trim('上個月的排行')=>array("star","school_class","month"),
						trim('上一周的排行')=>array("star","school_class","week")
                    )
                )
            );

        //----------------------------------
        //回傳
        //-----------------------------------------------
		return $type_array;
    }
?>