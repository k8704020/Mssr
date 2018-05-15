<?php
//-------------------------------------------------------
//函式: pagination()
//用途: 分頁顯示
//-------------------------------------------------------

    function pagination($pinx,$psize,$pnos,$url){
    //---------------------------------------------------
    //函式: pagination()
    //用途: 分頁顯示
    //---------------------------------------------------
    //$pinx     目前分頁索引
    //$psize    單頁筆數
    //$pnos     分頁筆數
    //$url      目標URL
    //---------------------------------------------------

        //-----------------------------------------------
        //顯示
        //-----------------------------------------------

            $pagination_htm="<ul class='pagination pagination-sm'>";

            //第一頁顯示
            if($pinx>3):
                $pagination_htm.="
                    <li>
                        <a href='{$url}&pinx=1&psize={$psize}'>
                            1
                        </a>
                    </li>
                ";
            endif;

            //上一頁顯示
            if($pinx>1):$i=$pinx-1;
                $pagination_htm.="
                    <li><a href='{$url}&pinx={$i}&psize={$psize}'>上一頁</a></li>
                    <li><a href='javascript:void(0);'>......</a></li>
                ";
            endif;

            //前列頁導引顯示
            $cno=0;for($i=($pinx-2);$i<$pinx;$i++):if($i>0):
                $pagination_htm.="
                    <li>
                        <a href='{$url}&pinx={$i}&psize={$psize}'>
                            {$i}
                        </a>
                    </li>
                ";
            $cno++;endif;endfor;

            //當前頁顯示
            $pagination_htm.="
                <li class='active'>
                    <a href='{$url}&pinx={$pinx}&psize={$psize}'>
                        {$pinx}
                    </a>
                </li>
            ";

            //後列頁導引顯示
            $filter_i=5-$cno;for($i=($pinx+1);$i<($pinx+$filter_i);$i++):if($i<=$pnos):
                $pagination_htm.="
                    <li>
                        <a href='{$url}&pinx={$i}&psize={$psize}'>
                            {$i}
                        </a>
                    </li>
                ";
            endif;endfor;

            //下一頁顯示
            if(($pinx+1)<=$pnos):$i=$pinx+1;
                $pagination_htm.="
                    <li><a href='javascript:void(0);'>......</a></li>
                    <li><a href='{$url}&pinx={$i}&psize={$psize}'>下一頁</a></li>
                ";
            endif;

            //最末頁顯示
            if(($pinx+2)<$pnos):
                $pagination_htm.="
                    <li><a href='{$url}&pinx={$pnos}&psize={$psize}'>
                        {$pnos}
                    </a></li>
                ";
            endif;

            $pagination_htm.="</ul>";

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $pagination_htm;
    }
?>