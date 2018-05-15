<?php
//-------------------------------------------------------
//版本編號 2.0
//明日星球,書店 -> 朋友列表
//(內頁)  //主頁面 or 內頁
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",3)."/config/config.php");
		require_once(str_repeat("../",3)."/inc/get_book_info/code.php");
	
		 //外掛函式檔
		$funcs=array(
					APP_ROOT.'inc/code',
					APP_ROOT.'lib/php/db/code',
					);
		func_load($funcs,true);
		
	
		//清除並停用BUFFER
		@ob_end_clean();
		
		//建立連線 user
		$conn_mssr=conn($db_type='mysql',$arry_conn_mssr);
		$conn_user=conn($db_type='mysql',$arry_conn_user);

    //---------------------------------------------------
    //有無維護
    //---------------------------------------------------

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

    //---------------------------------------------------
    //重複登入
    //---------------------------------------------------

    ///---------------------------------------------------
    //SESSION
    //---------------------------------------------------
   
        
		$user_id       =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:$_SESSION['uid'];
		$permission     = (isset($_SESSION['permission']))?$_SESSION['permission']:'0';
    //---------------------------------------------------
    //權限,與判斷
    //---------------------------------------------------
		if($permission =='0' || $user_id=='0') die("喔喔 你非法進入喔 可能是沒有權限進入或是尚未登入");
    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
		$page = (int)$_GET["page"];
		$page_limit = ($page-1 )*10;
	//---------------------------------------------------
	//SQL
	//---------------------------------------------------
		$array = array();
		

		$sql = "SELECT track_to
				FROM `mssr_track_user`
				WHERE track_from = ".$user_id;
		$retrun = db_result($conn_type='pdo',$conn_mssr,$sql,$arry_limit=array($page_limit,10),$arry_conn_mssr);
		$array = array();
		foreach($retrun as $key1=>$val1)
		{
			$sql = "SELECT `name`,`sex`
					FROM  `member` 
					WHERE uid = '".$val1["track_to"]."'";
			$name = db_result($conn_type='pdo',$conn_user,$sql,$arry_limit=array(0,1),$arry_conn_user);
			if(sizeof($name)!=0)
			{
				$tmp['id']=$val1["track_to"];
				$tmp['name'] = $name[0]['name'];
				$tmp['sex'] = $name[0]['sex'];
				array_push($array,$tmp);
			}
		}
		

?>
<!DOCTYPE HTML>
<Html>
<Head>
    <script src="../js/select_thing.js" type="text/javascript"></script>
    <style>
		 /*中文特效用*/
            .world_bar
            {
            text-shadow:2px 0px 1px rgba(0,0,0,1),
                        0px -2px 1px rgba(0,0,0,1),
                        -2px 0px 1px rgba(0,0,0,1),
                        0px 2px 1px rgba(0,0,0,1),
                        2px 2px 1px rgba(0,0,0,1),
                        2px -2px 1px rgba(0,0,0,1),
                        -2px 2px 1px rgba(0,0,0,1),
                        -2px -2px 1px rgba(0,0,0,1)
						,2px 0px 1px rgba(0,0,0,1),
                        0px -2px 1px rgba(0,0,0,1),
                        -2px 0px 1px rgba(0,0,0,1),
                        0px 2px 1px rgba(0,0,0,1),
                        2px 2px 1px rgba(0,0,0,1),
                        2px -2px 1px rgba(0,0,0,1),
                        -2px 2px 1px rgba(0,0,0,1),
                        -2px -2px 1px rgba(0,0,0,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;
         

            font-size:16px;
            text-align:center;
			
            font-family:"微軟正黑體","sans-serif","黑體-繁","新細明體","Ariel";
            }
		.man1{
			position:absolute;
			width:62px;
			height:71px;
			background: url('img/man.png') 0px 0;
		}
		.man2 {
			position:absolute;
			width:62px;
			height:71px;
    		background: url('img/man.png') 0px -71px;
		}
		.man3{
			position:absolute;
			width:62px;
			height:71px;
			background: url('img/man.png') -62px 0;
		}
		.man4 {
			position:absolute;
			width:62px;
			height:71px;
    		background: url('img/man.png') -62px -71px;
		}
		/* .friendName{
			font-family: 微軟正黑體;
		} */
	</style>
</Head>
<body style="overflow:hidden;">

	<!--==================================================
    html內容
    ====================================================== -->
	<? for($i = 0 ; $i < sizeof($array);$i++){ ?>
	 <? if($array[$i]["sex"] ==1){ ?>
     <div onClick='btn_click(<? echo $array[$i]["id"]; ?>)' onMouseOver="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man2';" onMouseOut="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man1';" style='position: relative; top:22px; left:<? echo $i*94; ?>px; cursor:pointer;'>
    	<a id="man_<? echo $array[$i]["id"]; ?>" class='man1' onMouseOver="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man2';"></a>
        
        <div style='position: absolute; top:44px;left: -10px; width:82px;text-align:center;' class="world_bar friendName">
			<? echo $array[$i]["name"]; ?>
		</div>
    </div>
    <? }else{ ?>
    <div onClick='btn_click(<? echo $array[$i]["id"]; ?>)' onMouseOver="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man4';" onMouseOut="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man3';" style='position: relative; top:22px; left:<? echo $i*94; ?>px; cursor:pointer;'>
    	<a id="man_<? echo $array[$i]["id"]; ?>" class='man3' onMouseOver="window.document.getElementById('man_<? echo $array[$i]["id"]; ?>').className='man4';"></a>
        
        <div style='position: absolute; top:44px;left: -10px;  width:80px;text-align:center;' class="world_bar friendName">
			<? echo $array[$i]["name"]; ?>
		</div>
    </div>
    <? } ?>
    <? } ?>
	
    
    <script>

	//---------------------------------------------------
	//初始化
	//---------------------------------------------------
	
	//---------------------------------------------------
	//FUNCTION
	//---------------------------------------------------
	
	//點擊欄位事件ceffb7
	function btn_click(value)
	{
		window.parent.parent.set_action_bookstore_log(window.parent.parent.user_id,'e32',1);
		window.parent.parent.location.href="../bookstore_courtyard/index.php?uid="+value;
	}
	//cover
	function cover(text,type)
	{
		window.parent.cover(text,type);
	}
	//debug
	function echo(text)
	{
		window.parent.echo(text);
	}	
	//---------------------------------------------------
    //ONLOAD
    //---------------------------------------------------
	echo("追蹤內容開啟");
    </script>
</Html>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    