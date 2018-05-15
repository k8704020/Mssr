<?php
//-------------------------------------------------------
//函式: draw_pie_chart()
//用途: 圓餅圖函式
//日期: 2012年1月17日
//作者: jeff@max-life
//-------------------------------------------------------

    function draw_pie_chart($rd=0,$array=array()){
    //---------------------------------------------------
    //圓餅圖函式
    //---------------------------------------------------
    //$rd  函式庫路徑階層,預設0
    //
    //array['data']     值陣列
    //array['legend']   標籤
    //array['width']    圖片寬度
    //array['height']   圖片高度
    //array['title']    主標題
    //array['subtitle'] 子標題
    //array['footer']   註腳
    //array['show_3d']  顯示3d,預設 false
    //
    //本函式如果執行失敗,會傳回錯誤訊息陣列
    //---------------------------------------------------

        //外掛函式檔
        if(!isset($rd)||!is_numeric($rd)){
            $rd=0;
        }
        $path=str_repeat('../',$rd);

        if(false===(@include("{$path}/lib/image/jpgraph/jpgraph.php"))){
            $err=array();
            $err[]="外掛 jpgraph.php 函式檔失敗!";
            return $err;
        }
        if(false===(@include("{$path}/lib/image/jpgraph/jpgraph_pie.php"))){
            $err=array();
            $err[]="外掛 jpgraph_pie.php 函式檔失敗!";
            return $err;
        }
        if(false===(@include("{$path}/lib/image/jpgraph/jpgraph_pie3d.php"))){
            $err=array();
            $err[]="外掛 jpgraph_pie3d.php 函式檔失敗!";
            return $err;
        }

        //參數檢驗
        if(!isset($array)||!is_array($array)||empty($array)){
            $err=array();
            $err[]="參數陣列,未指定或非陣列型態,或為空陣列";
            return $err;
        }else{
            //錯誤資訊陣列
            $err=array();

            //必須參數名稱陣列
            $req=array('data','legend','width','height');

            //檢驗參數
            $cno=0;
            foreach($req as $inx=>$val){
                if(in_array($val,array_keys($array))){
                    if(!empty($array[$val])){
                        $cno++;
                    }else{
                       $err[]="成員[{$val}],值未指定";
                    }
                }else{
                   $err[]="成員[{$val}],不在參數陣列裡";
                }
            }
            if(count($req)!=$cno){
                return $err;
            }

            //-------------------------------------------
            //處理
            //-------------------------------------------
            //array['data']     值陣列
            //array['legend']   標籤
            //array['width']    圖片寬度
            //array['height']   圖片高度
            //array['title']    主標題
            //array['subtitle'] 子標題
            //array['footer']   註腳
            //array['show_3d']  顯示3d,預設 false

            $data    =$array['data'];
            $legend  =$array['legend'];
            $width   =$array['width'];
            $height  =$array['height'];

            $title   =(isset($array['title'])&&trim($array['title'])!='')?$array['title']:'';
            $subtitle=(isset($array['subtitle'])&&trim($array['subtitle'])!='')?$array['subtitle']:'';
            $footer  =(isset($array['footer'])&&trim($array['footer'])!='')?$array['footer']:'';
            $show_3d =(isset($array['show_3d'])&&trim($array['show_3d'])!='')?$array['show_3d']:false;

            //-------------------------------------------
            //圓餅圖
            //-------------------------------------------
            //array['data']     值陣列
            //array['legend']   標籤
            //array['width']    圖片寬度
            //array['height']   圖片高度
            //array['title']    主標題
            //array['subtitle'] 子標題
            //array['footer']   註腳

            //圖片畫布
            $graph =new PieGraph($width,$height);

            $graph->legend->SetFont(FF_BIG5);

            $graph->title->Set($title);
            $graph->title->SetFont(FF_BIG5);

            $graph->subtitle->Set($subtitle);
            $graph->subtitle->SetFont(FF_BIG5);

            $graph->footer->right->Set($footer);
            $graph->footer->right->SetFont(FF_BIG5);

            //圓餅圖物件
            if($show_3d===true){
                $p1=new PiePlot3D($data);
                $p1->SetAngle(30);
            }else{
                $p1=new PiePlot($data);
            }

            if($legend!==''){
                $p1->SetLegends($legend);
            }

            $graph->Add($p1);

            //顯示圖片
            $graph->Stroke();
        }
    }
?>