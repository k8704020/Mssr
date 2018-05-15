<?php
//-------------------------------------------------------
//函式: draw_bars_chart()
//用途: 直方圖函式
//日期: 2012年2月11日
//作者: jeff@max-life
//-------------------------------------------------------

    function draw_bars_chart($rd=0,$arrays=array()){
    //---------------------------------------------------
    //直方圖函式
    //---------------------------------------------------
    //$rd  函式庫路徑階層,預設0
    //
    //$arrays=array(
    //  array('data'=>array(),'tick'=>array()), //第1組
    //  array('data'=>array(),'tick'=>array())  //第2組
    //);
    //
    //array['data']           值陣列
    //array['tick']           尺規文字陣列
    //array['width']          圖片寬度
    //array['height']         圖片高度
    //array['color']          邊框顏色
    //array['fillcolor']      背景顏色
    //array['title']          主標題
    //array['subtitle']       子標題
    //array['footer']         註腳
    //array['legend']         標籤
    //array['x_title']        X軸標題
    //array['y_title']        Y軸標題
    //array['margin']         邊界陣列(左,右,上,下)
    //array['show_shadow']    顯示陰影,預設 false
    //array['show_value']     顯示數值,預設 false
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
        if(false===(@include("{$path}/lib/image/jpgraph/jpgraph_bar.php"))){
            $err=array();
            $err[]="外掛 jpgraph_bar.php 函式檔失敗!";
            return $err;
        }

        //參數檢驗
        if(!isset($arrays)||!is_array($arrays)||empty($arrays)){
            $err=array();
            $err[]="參數陣列,未指定或非陣列型態,或為空陣列";
            return $err;
        }else{
            //錯誤資訊陣列
            $errs=array();

            //必須參數名稱陣列
            $req=array('data','tick','width','height');

            //檢驗參數
            foreach($arrays as $inx=>$array){
                $err=array();
                $cno=0;

                if(empty($array)){
                    $err[]="參數陣列,未指定或非陣列型態,或為空陣列";
                    $errs["直方{$inx}"]=$err;
                    continue;
                }

                foreach($req as $val){
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
                    $errs["直方{$inx}"]=$err;
                }
            }

            if(!empty($errs)){
                return $errs;
            }

            //-------------------------------------------
            //處理
            //-------------------------------------------
            //array['data']           值陣列
            //array['tick']           尺規文字陣列
            //array['width']          圖片寬度
            //array['height']         圖片高度
            //array['color']          邊框顏色
            //array['fillcolor']      背景顏色
            //array['title']          主標題
            //array['subtitle']       子標題
            //array['footer']         註腳
            //array['legend']         標籤
            //array['x_title']        X軸標題
            //array['y_title']        Y軸標題
            //array['margin']         邊界陣列(左,右,上,下)
            //array['show_shadow']    顯示陰影,預設 false
            //array['show_value']     顯示數值,預設 false

            //圖片畫布,以第一組為基準
            $tick  =$arrays[0]['tick'];
            $width =$arrays[0]['width'];
            $height=$arrays[0]['height'];

            $title   =(isset($arrays[0]['title'])&&trim($arrays[0]['title'])!='')?$arrays[0]['title']:'';
            $subtitle=(isset($arrays[0]['subtitle'])&&trim($arrays[0]['subtitle'])!='')?$arrays[0]['subtitle']:'';
            $footer  =(isset($arrays[0]['footer'])&&trim($arrays[0]['footer'])!='')?$arrays[0]['footer']:'';
            $x_title =(isset($arrays[0]['x_title'])&&trim($arrays[0]['x_title'])!='')?$arrays[0]['x_title']:'';
            $y_title =(isset($arrays[0]['y_title'])&&trim($arrays[0]['y_title'])!='')?$arrays[0]['y_title']:'';
            $margin  =(isset($arrays[0]['margin'])&&!empty($arrays[0]['margin']))?$arrays[0]['margin']:'';

            $graph =new Graph($width,$height);
            $graph->SetScale('textlin');
            $graph->ygrid->Show();
            $graph->xgrid->Show();
            $graph->xaxis->SetTickLabels($tick);
            $graph->xaxis->SetFont(FF_BIG5);
            $graph->xaxis->title->SetFont(FF_BIG5);
            $graph->yaxis->title->SetFont(FF_BIG5);
            $graph->title->SetFont(FF_BIG5);
            $graph->subtitle->SetFont(FF_BIG5);
            $graph->footer->right->SetFont(FF_BIG5);
            $graph->legend->SetFont(FF_BIG5);

            //主標題,子標題,註腳
            if($title!==''){
                $graph->title->Set($title);
            }
            if($title!==''){
                $graph->title->Set($title);
            }
            if($subtitle!==''){
                $graph->subtitle->Set($subtitle);
            }
            if($footer!==''){
                $graph->footer->right->Set($footer);
            }

            //X軸標題
            if($x_title!==''){
                $graph->xaxis->title->Set($x_title);
            }

            //Y軸標題
            if($y_title!==''){
                $graph->yaxis->title->Set($y_title);
            }

            //邊界陣列(左,右,上,下)
            if($margin!==''){
                $graph->img->SetMargin($margin[0],$margin[1],$margin[2],$margin[3]);
            }

            $arry_gbplot=array();

            //設定直方資料
            foreach($arrays as $inx=>$array){

                $data   =$array['data'];
                $barplot=new BarPlot($data);

                $arry_gbplot[]=$barplot;

            }

            $gbplot=new GroupBarPlot($arry_gbplot);
            $graph->Add($gbplot);

            //設定直方屬性
            foreach($arrays as $inx=>$array){

                $barplot=$arry_gbplot[$inx];

                $color      =(isset($array['color'])&&trim($array['color'])!='')?$array['color']:'';
                $fillcolor  =(isset($array['fillcolor'])&&trim($array['fillcolor'])!='')?$array['fillcolor']:'';
                $legend     =(isset($array['legend'])&&trim($array['legend'])!='')?$array['legend']:'';
                $show_shadow=(isset($array['show_shadow'])&&trim($array['show_shadow'])!='')?$array['show_shadow']:false;
                $show_value =(isset($array['show_value'])&&trim($array['show_value'])!='')?$array['show_value']:false;

                if($color!==''){
                    $barplot->SetColor($color);
                }
                if($fillcolor!==''){
                    $barplot->Setfillcolor($fillcolor);
                }
                if($legend!==''){
                    $barplot->SetLegend($legend);
                }
                if($show_shadow===true){
                    $barplot->SetShadow();
                }
                if($show_value===true){
                    $barplot->value->Show();
                }
            }

            //顯示圖片
            $graph->Stroke();
        }
    }
?>