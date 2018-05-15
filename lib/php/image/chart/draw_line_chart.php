<?php
//-------------------------------------------------------
//函式: draw_line_chart()
//用途: 曲線圖函式
//日期: 2012年2月11日
//作者: jeff@max-life
//-------------------------------------------------------

    function draw_line_chart($rd=0,$array=array()){
    //---------------------------------------------------
    //曲線圖函式
    //---------------------------------------------------
    //$rd  函式庫路徑階層,預設0
    //
    //array['data']         值陣列
    //array['tick']         尺規文字陣列
    //array['width']        圖片寬度
    //array['height']       圖片高度
    //array['color']        線段顏色
    //array['title']        主標題
    //array['subtitle']     子標題
    //array['footer']       註腳
    //array['legend']       標籤
    //array['x_title']      X軸標題
    //array['y_title']      Y軸標題
    //array['margin']       邊界陣列(左,右,上,下)
    //array['show_mark']    顯示標註點,預設 false
    //array['show_value']   顯示數值,預設 false
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
        if(false===(@include("{$path}/lib/image/jpgraph/jpgraph_line.php"))){
            $err=array();
            $err[]="外掛 jpgraph_line.php 函式檔失敗!";
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
            $req=array('data','tick','width','height');

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
            //array['data']         值陣列
            //array['tick']         尺規文字陣列
            //array['width']        圖片寬度
            //array['height']       圖片高度
            //array['color']        線段顏色
            //array['title']        主標題
            //array['subtitle']     子標題
            //array['footer']       註腳
            //array['legend']       標籤
            //array['x_title']      X軸標題
            //array['y_title']      Y軸標題
            //array['margin']       邊界陣列(左,右,上,下)
            //array['show_mark']    顯示標註點,預設 false
            //array['show_value']   顯示數值,預設 false

            $data       =$array['data'];
            $tick       =$array['tick'];
            $width      =$array['width'];
            $height     =$array['height'];

            $color      =(isset($array['color'])&&trim($array['color'])!='')?$array['color']:'';
            $title      =(isset($array['title'])&&trim($array['title'])!='')?$array['title']:'';
            $subtitle   =(isset($array['subtitle'])&&trim($array['subtitle'])!='')?$array['subtitle']:'';
            $footer     =(isset($array['footer'])&&trim($array['footer'])!='')?$array['footer']:'';
            $legend     =(isset($array['legend'])&&trim($array['legend'])!='')?$array['legend']:'';
            $x_title    =(isset($array['x_title'])&&trim($array['x_title'])!='')?$array['x_title']:'';
            $y_title    =(isset($array['y_title'])&&trim($array['y_title'])!='')?$array['y_title']:'';
            $margin     =(isset($array['margin'])&&!empty($array['margin']))?$array['margin']:'';
            $show_mark  =(isset($array['show_mark'])&&trim($array['show_mark'])!='')?$array['show_mark']:false;
            $show_value =(isset($array['show_value'])&&trim($array['show_value'])!='')?$array['show_value']:false;

            //-------------------------------------------
            //曲線圖
            //-------------------------------------------
            //array['data']         值陣列
            //array['tick']         尺規文字陣列
            //array['width']        圖片寬度
            //array['height']       圖片高度
            //array['color']        線段顏色
            //array['title']        主標題
            //array['subtitle']     子標題
            //array['footer']       註腳
            //array['legend']       標籤
            //array['x_title']      X軸標題
            //array['y_title']      Y軸標題
            //array['show_mark']    顯示標註點,預設 false
            //array['show_value']   顯示數值,預設 false

            //圖片畫布
            $graph =new Graph($width,$height);
            $graph->SetScale('textlin');
            $graph->ygrid->Show();
            $graph->xgrid->Show();
            $graph->xaxis->SetTickLabels($tick);
            $graph->xaxis->SetFont(FF_BIG5);
            $graph->xaxis->title->SetFont(FF_BIG5);
            $graph->yaxis->title->SetFont(FF_BIG5);
            $graph->legend->SetFont(FF_BIG5);

            //曲線物件
            $lineplot=new LinePlot($data);
            $graph->Add($lineplot);

            //線段顏色
            if($color!==''){
                $lineplot->SetColor($color);
            }

            //設定樣式,'solid', 'dotted', 'dashed'
            $lineplot->SetStyle('solid');

            //主標題,子標題,註腳
            if($title!==''){
                $graph->title->Set($title);
                $graph->title->SetFont(FF_BIG5);
            }
            if($title!==''){
                $graph->title->Set($title);
                $graph->title->SetFont(FF_BIG5);
            }
            if($subtitle!==''){
                $graph->subtitle->Set($subtitle);
                $graph->subtitle->SetFont(FF_BIG5);
            }
            if($footer!==''){
                $graph->footer->right->Set($footer);
                $graph->footer->right->SetFont(FF_BIG5);
            }

            //標籤
            if($legend!==''){
                $lineplot->SetLegend($legend);
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

            //顯示標註點,預設 false
            if($show_mark===true){
                $lineplot->mark->SetType(MARK_IMG_MBALL,$color,0.4);
            }

            //顯示數值,預設 false
            if($show_value===true){
                $lineplot->value->Show();
            }

            //顯示圖片
            $graph->Stroke();
        }
    }
?>