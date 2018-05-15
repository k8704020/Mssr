<!DOCTYPE html>
<?php session_start(); ?>
	<head>

		<script src="../../lib/jquery/basic/code.js"></script>

		<?php
		//---------------------------------------------------
		//設定與引用
		//---------------------------------------------------

			//SESSION
			@session_start();

			//啟用BUFFER
			@ob_start();

			//外掛設定檔
			require_once(str_repeat("../",2)."/config/config.php");

			 //外掛函式檔
			$funcs=array(
						APP_ROOT.'inc/code',
						APP_ROOT.'lib/php/db/code',
						APP_ROOT.'lib/php/array/code'
						);
			func_load($funcs,true);


			//清除並停用BUFFER
			@ob_end_clean();

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

		//---------------------------------------------------
		//END   設定與引用
		//---------------------------------------------------

        //---------------------------------------------------
        //撈取, 學校資訊
        //---------------------------------------------------

            $sess_uid        =(isset($_SESSION['uid']))?(int)$_SESSION['uid']:0;
            $sess_class_code =(isset($_SESSION['class'][0][1]))?trim($_SESSION['class'][0][1]):'';
            $sess_school_code=(isset($_SESSION['school_code']))?trim($_SESSION['school_code']):'';
            $sess_sem_year   =(isset($_SESSION['sem_year']))?(int)$_SESSION['sem_year']:0;
            $sess_sem_term   =(isset($_SESSION['sem_term']))?(int)$_SESSION['sem_term']:0;
            $sess_grade      =(isset($_SESSION['grade']))?(int)$_SESSION['grade']:0;

            if((trim($sess_school_code)==='')&&(trim($sess_class_code)!=='')){
                $sess_class_code=mysql_prep($sess_class_code);
                $sql="
                    SELECT
                        `class`.`grade`,
                        `semester`.`school_code`
                    FROM `class`
                        INNER JOIN `semester` ON
                        `class`.`semester_code`=`semester`.`semester_code`
                    WHERE 1=1
                        AND `class`.`class_code`='{$sess_class_code}'
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(0,1),$arry_conn_user);
                if(!empty($arrys_result)){
                    $sess_school_code=trim($arrys_result[0]['school_code']);
                    if($sess_grade===0){
                        $sess_grade=(int)$arrys_result[0]['grade'];
                    }
                }
            }

        //---------------------------------------------------
        //撈取分店類型相關
        //---------------------------------------------------

            $sess_school_code   =mysql_prep($sess_school_code);

            //初始化, 階層陣列
            $arrys_category_info=array();
            $json_category_info =json_encode($arrys_category_info,true);

            $sql="
                SELECT
                    *
                FROM `mssr_book_category`
                WHERE 1=1
                    AND `mssr_book_category`.`cat1_id`<>1
                    AND `mssr_book_category`.`school_code`='{$sess_school_code}'
                    AND `mssr_book_category`.`cat_state`  ='啟用'
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
            if(!empty($arrys_result)){
            //回填各階層相關陣列
                foreach($arrys_result as $inx=>$arry_result){
                    $cat1_id=(int)$arry_result['cat1_id'];
                    $cat2_id=(int)$arry_result['cat2_id'];
                    $cat3_id=(int)$arry_result['cat3_id'];

                    if(($cat2_id===1)&&($cat3_id===1)){
                        $lv_flag=1;
                        $arrys_category_info['lv1'][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id===1)){
                        $lv_flag=2;
                        $arrys_category_info['lv2'][$cat1_id][]=$arry_result;
                    }else if(($cat2_id!==1)&&($cat3_id!==1)){
                        $lv_flag=3;
                        $arrys_category_info['lv3'][$cat2_id][]=$arry_result;
                    }else{
                        $lv_flag=0;
                        die("發生嚴重錯誤，請洽詢明日星球團隊人員!");
                    }
                }
                $json_category_info =json_encode($arrys_category_info,true);
            }

		//---------------------------------------------------
		//CSS 設定
		//---------------------------------------------------
		?>
		<style>
            body
            {}
			/*灰階特效*/
			.grays {
				 filter: grayscale(100%);
				 -webkit-filter: grayscale(100%);
				 -moz-filter: grayscale(100%);
				 -ms-filter: grayscale(100%);
				 -o-filter: grayscale(100%);
				 transition: 0.5s;
			 }
			 .nograys {
				 filter: grayscale(0%);
				 -webkit-filter: grayscale(0%);
				 -moz-filter: grayscale(0%);
				 -ms-filter: grayscale(0%);
				 -o-filter: grayscale(0%);
				 transition: 0.5s;
			 }
			/*水平翻轉*/
			.trun90
			{-moz-transform:scaleX(-1);-webkit-transform:scaleX(-1);-o-transform:scaleX(-1);transform:scaleX(-1);filter:FlipH();}
            /*大寫標題*/
			.title_1
			{
				font-size:18px;
				text-align:left;
				font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
			}

			.ba_1
			{
				background: #5e2400; /* Old browsers */
				background: -moz-linear-gradient(left, #5e2400 0%, #ea9152 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, right top, color-stop(0%,#5e2400), color-stop(100%,#ea9152)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(left, #5e2400 0%,#ea9152 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(left, #5e2400 0%,#ea9152 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(left, #5e2400 0%,#ea9152 100%); /* IE10+ */
				background: linear-gradient(to right, #5e2400 0%,#ea9152 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#5e2400', endColorstr='#ea9152',GradientType=1 ); /* IE6-9 */

			}
            /*數字特效用*/
            .number_bar
            {
            text-shadow:2px 0px 0px rgba(128,23,15,1),
                        0px -2px 0px rgba(128,23,15,1),
                        -2px 0px 0px rgba(128,23,15,1),
                        0px 2px 0px rgba(128,23,15,1),
                        2px 2px 0px rgba(128,23,15,1),
                        2px -2px 0px rgba(128,23,15,1),
                        -2px 2px 0px rgba(128,23,15,1),
                        -2px -2px 0px rgba(128,23,15,1);
            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:20px;
            text-align:center;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }

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

            font-size:22px;
            text-align:center;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
			.world_bar2
            {
            text-shadow:2px 0px 1px rgba(128,23,15,1),
                        0px -2px 1px rgba(128,23,15,1),
                        -2px 0px 1px rgba(128,23,15,1),
                        0px 2px 1px rgba(128,23,15,1),
                        2px 2px 1px rgba(128,23,15,1),
                        2px -2px 1px rgba(128,23,15,1),
                        -2px 2px 1px rgba(128,23,15,1),
                        -2px -2px 1px rgba(128,23,15,1)
						,2px 0px 1px rgba(128,23,15,1),
                        0px -2px 1px rgba(128,23,15,1),
                        -2px 0px 1px rgba(128,23,15,1),
                        0px 2px 1px rgba(128,23,15,1),
                        2px 2px 1px rgba(128,23,15,1),
                        2px -2px 1px rgba(128,23,15,1),
                        -2px 2px 1px rgba(128,23,15,1),
                        -2px -2px 1px rgba(128,23,15,1);


            font-weight:bold;
            color:#FCFFFF;
            letter-spacing:0pt;

            font-size:22px;
            text-align:left;
            font-family:comic sans, comic sans ms, cursive, verdana, arial, sans-serif;
            }
        </style>
	</head>

<body>





        <?php
        //預設讀入的資料

		//GET
		$user_id = (isset($_GET[trim('user_id')]))?(int)$_GET[trim('user_id')]:$sess_uid;
		$branch_id = $_GET["branch_id"];

        //-----------------------------------------------
        //撈取是否為朋友
        //-----------------------------------------------

            $friend_ident=0;
            if($sess_uid!==$user_id){
                $sql="
                    SELECT
                        `track_id`
                    FROM `mssr_track_user`
                    WHERE 1=1
                        AND `track_from` IN ({$sess_uid},{$user_id})
                        AND `track_to` IN ({$sess_uid},{$user_id})
                ";
                $arrys_result=db_result($conn_type='pdo',$conn_mssr,$sql,array(0,1),$arry_conn_mssr);
                if(!empty($arrys_result)){
                    $friend_ident=1;
                }
            }

		?>
        <script type="text/javascript" src="../branch/inc/add_action_branch_log/code.js"></script>
        <script src="../../lib/jquery/plugin/code.js"></script>

		<!--========================================================
		//DEBUG文字列
		//========================================================-->
		<div style="left:0px; top:480px; position: absolute;" id="debug"></div>
		<script language="javascript">
        var json_category_info=<?php echo $json_category_info;?>;
		debug = true;//DEBUG開關   true / false
		//事件 :  遮罩開關
		function set_hide(open_on,text)
		{
			if(open_on==false)
			{
				//$.unblockUI();
			}else if(open_on==true)
			{
				//$.blockUI({ message: '<div style="z-index: 2000;">'+text+'</div>'});
			}
		}
		function add_debug(value)
		{
			if(0)
			{
				window.document.getElementById("debug").innerHTML = value+"<br>"+window.document.getElementById("debug").innerHTML;
			}
		}
		set_hide(1,"讀取中");

		//========================================================
		//建立
		//========================================================
		var user_id = <?php echo $user_id; ?>;
		var branch_id = <?php echo $branch_id;?>;
		var visit = <?php if($user_id != $_SESSION['uid']){echo 1;}else{echo 0;}?>;
		var branch_rank = -1;
		var branch_cs = -1;
		var branch_visit = -1;
		var branch_lv = -1;
		var branch_nickname = "";
		var coin = -1;//目前金錢
		var coin_show = -1;//顯示的金錢
		var coin_timer;//跳錢動畫
		var up_branch_cs = -1;//升級條件 : 滿意度
		var open_up_branch = 0;//升店開啟
		var up_branch_spent_coin = -1;//升級條件 : 需求金錢s
		var up_branch_ok = -1;//低於本店的 分店數量
		var up_branch_rec = -1; //此分店的推薦量 起始4/21
		var up_branch_read = -1; // 此分店的閱讀量 起始4/21
		var up_branch_need_read = -1; // 此分店的需求閱讀量
		var up_branch_need_rec = -1; // 此分店的需求推薦量
		var user_name;
		var user_sex;
		var mission_number = 2;
		var mission_sid = "";
		var mission_mode = 1;
		var branch_name = '';
		var open_report_on = 0;
		var show_coin_on=1; //跳錢動畫許可
		var mission_array; //任務列表
		var mission_lv = new Array();//任務難度
		mission_lv[0] = -1;
		mission_lv[1] = -1;
		mission_lv[2] = -1;
		mission_lv[3] = -1;
		var person_id_list  = new Array(); //紀錄在場上NPC的ID
		var person_id = "";//目前點擊的NPC ID
		//========================================================
		//圖片預載
		//========================================================

		//===========設定圖片預載陣列==============
		add_debug("圖片預載:開始");
		var images = new Array();

		//通用  common
		images.push("img/common/spc_1.png",
					"img/common/up_back.png",
					"img/common/back_box.png"	);

		//任務相關 MISSION
		images.push("img/mission/mission_back.png",
					"img/mission/mission_back2.png",
					"img/mission/number_bar_1.png",
					"img/mission/number_bar_2.png",
					"img/mission/number_bar_3.png",
					"img/mission/book_pik.png"
					);


		//迢迢相干 bar
		images.push("img/bar/bar_popularity.png",
					"img/bar/bar_rank_1.png",
					"img/bar/bar_rank_2.png",
					"img/bar/bar_rank_3.png",
					"img/bar/bar_read.png",
					"img/btn/btn_gift.png",
					"img/btn/btn_mission.png",
					"img/btn/btn_paper.png",
					"img/btn/btn_up.png",
					"img/btn/arrow.png",
					"img/btn/yes.png",
					"img/btn/no.png",
					"img/bar/ui_1.png",
					"img/bar/head_f.png",
					"img/bar/head_m.png"
					);

		//布景相關 Scene
		images.push("img/Scene/branch_2/LV_2_door.png",
					"img/Scene/branch_2/LV_3_door.png",
					"img/Scene/branch_3/LV_2_door.png",
					"img/Scene/branch_3/LV_3_door.png",
					"img/Scene/branch_4/LV_2_door.png",
					"img/Scene/branch_4/LV_3_door.png",
					"img/Scene/branch_5/LV_2_door.png",
					"img/Scene/branch_5/LV_3_door.png",
					"img/Scene/branch_6/LV_2_door.png",
					"img/Scene/branch_6/LV_3_door.png",
					"img/Scene/LV_1_back.png",
					"img/Scene/LV_1_book_1.png",
					"img/Scene/LV_1_book_2.png",
					"img/Scene/LV_2_3_back.png",
					"img/Scene/LV_2_3_layer1.png",
					"img/Scene/LV_2_3_layer2.png",
					"img/Scene/LV_2_3_layer3.png"
					);

		//人物相關 person
		images.push("img/person/npc_1.png",
					"img/person/npc_2.png",
					"img/person/npc_3.png",
					"img/person/saver_f.png",
					"img/person/saver_m.png");

		function preloader(images)
		{
			// counter
			var i = 0;
			// create object
			imageobj = new Image();
			// set image list

			for(i=0; i<= images.length; i++)
			{
				add_debug("圖片預載:載入中:"+i+"/"+images.length);
				imageobj.src=images[i];
			}
		}
		//========================================================
		//圖片預載結束
		//========================================================
		preloader(images);
		</script>
        <div id="scene" style="left:0px; top:0px; position: absolute;" >
            <!--物景-->
            <!--<img src= "img/Scene/LV_2_3_back.png" style="left:0px; top:0px; position: absolute;"><img src= "img/Scene/LV_2_3_layer1.png" style="left:142px; top:119px; position: absolute;">
            <div id="layer_person_3">
            <img src= "img/person/npc_1.png" style="left:494px; top:252px; position: absolute; width: 76px; height: 108px;">
            <img src= "img/person/npc_2.png" style="left:564px; top:202px; position: absolute; width: 86px; height: 150px;">
            <img src= "img/person/npc_3.png" style="left:571px; top:180px; position: absolute; width: 123px; height: 181px;">
            </div>
            <img src= "img/Scene/LV_2_3_layer2.png" style="left:386px; top:116px; position: absolute;">
            <div id="layer_person_2">
            <img src= "img/person/npc_1.png" style="left:568px; top:313px; position: absolute; width: 92px; height: 124px;">
            <img src= "img/person/npc_2.png" style="left:646px; top:205px; position: absolute; width: 120px; height: 228px;">
            <img src= "img/person/npc_3.png" style="left:493px; top:188px; position: absolute; width: 169px; height: 240px;">
            </div>
         	<img src= "img/person/saver_m.png" width="100px" style="left:257px; top:186px; position: absolute;"><img src= "img/Scene/LV_2_3_layer3.png" style="left:197px; top:300px; position: absolute;"><img src= "img/Scene/branch_2/LV_2_door.png" style="left:0px; top:0px; position: absolute;">
            <div id="layer_person_1">
            <img src= "img/person/npc_1.png" style="left:191px; top:338px; position: absolute;">
            <img src= "img/person/npc_2.png" style="left:246px; top:221px; position: absolute;">
            <img src= "img/person/npc_3.png" style="left:793px; top:193px; position: absolute;">
            </div>-->
        </div>




		<!--顯示BAR條-->
       	<div style="left:140px; top:0px; position: absolute;">
            <!--等級-->
            <img src= "img/bar/bar_rank_3.png" style="left:166px; top:33px; position: absolute;">

            <div  style="left:165px; top:34px; position: absolute; overflow:hidden; width:300px; height:30px">
                <img id="rank_bar" src= "img/bar/bar_rank_2.png" style="left:-230px; position: absolute;transition: 1.5s;">
            </div>

            <img src= "img/bar/bar_rank_1.png" style="left:96px; top:4px; position: absolute; ">

            <div id="rank_text" class="number_bar" style="left:130px; top:25px; position: absolute; width:300px"></div>

         	<div id="lv_text" class="number_bar" style="left:122px; top:24px; position: absolute;"></div>
            <!-- 頭像位置"img/btn/ui_1.png",
					"img/btn/head_f.png",
					"img/btn/head_m.png" -->
            <div>
           	  <div style="position:absolute; left:-78px; top:78px; width:280px; height:40px; background-color:#4B1E0A; opacity:0.7;" class="ba_1"></div>
            	<div id = "branch_name" align="left" style="left:1px; top:69px; width:280px; font:bold; font-size:36px; position: absolute; color:#FFF" class="world_bar2"></div>
            	<img src= "img/bar/head_1.png" id = "head_pic" style="left:-125px; top:31px; position: absolute;">
            	<img src= "img/bar/ui_1.png" style="left:-132px; top:28px; position: absolute;">

              <div id = "user_name" style="left:-1px; top:34px; width:100px; font:bold; position: absolute;"></div>
          </div>

            <!--人氣-->
            <img src= "img/bar/bar_popularity.png" style="left:423px; top:5px; position: absolute;">
       	  	<div id="popularity_text" class="number_bar" style="left:485px; top:25px; position: absolute;"></div>

            <!--閱讀量-->
            <img src= "img/bar/bar_read.png" style="left:-35px; top:704px; position: absolute;">
            <div id="read_text" class="number_bar" style="left:18px; top:713px; position: absolute;"></div>

            <!--金錢-->
            <img src= "img/bar/bar_coin.png" style="left:545px; top:-3px; position: absolute; transition: 0.8s;">
       	  <div id="coin_text" class="number_bar" style="left:609px; top:25px; position: absolute;"></div>

		</div>

        <div id="report">
        <!--報表-->
        </div>
        <!--升級  介面-->
        <div id="up_branch_page" style="left:200px; top:100px; position: absolute; display:none;">
       	  <div>
            <img src= "img/common/up_back.png" onClick="" style="left:2px; top:-15px; position: absolute;">
           </div>
          <div style="left:71px; top:114px; width:600px; position: absolute;">
            <table>
            	<tr>
                	<td width="52" style=" font-size:22px; background-color:#FCDA76; font-weight:bold">
                    	金錢
                    </td>
                    <td  align="right" width="62" style=" font-size:22px">
                    	現有
                    </td>
                    <td id= "up_branch_coin" align="left" width="82" style=" font-size:22px">

                  </td>
                    <td width="138" style=" font-size:22px">
                    	－ 升級所需
                    </td>
                    <td id= "up_branch_need_coin" width="80" style=" font-size:22px; color:#F00 ">

                    </td>
                    <td width="109" style=" font-size:22px">
                    	＝ 剩餘
                    </td>
                    <td id= "up_branch_over_coin" width="108" style=" font-size:22px">

                    </td>
                </tr>
            </table>
            </div>
            <div style="left:72px; top:163px; width:560px; position: absolute;">
            <table width="556">
            	<tr>
                	<td width="179" style=" font-size:22px; background-color:#FCDA76; font-weight:bold">閱讀與推薦本數</td>

                    <td  align="left" width="52" style=" font-size:22px">閱讀:</td>
                    <td  id= "up_branch_read" width="50" style=" font-size:22px">

                    </td>
                    <td width="10" style=" font-size:22px; ">/</td>
                    <td id= "up_branch_need_read" width="52"  style=" font-size:22px"></td>

                    <td  align="left" width="50" style=" font-size:22px">推薦:</td>
                    <td  id= "up_branch_rec" width="50" style=" font-size:22px">

                    </td>
                    <td width="10" style=" font-size:22px; ">/</td>
                    <td id= "up_branch_need_rec" width="50"  style=" font-size:22px"></td>
                </tr>
            </table>
            </div>
            <div style="left:170px; top:250px; width:385px; position: absolute;">
            <div align="center" id= "up_branch_help" style="left:-119px; top:-43px; width:606px; height:30px; position: absolute; font-size:22px; color:#FF0000; font-weight:bold;">
            </div>
            <table>
            	<tr>
                    <td  align="center" width="100" id="up_branch_click_ok" style=" background-color: #B6DF11; display:block; font-size:22px; display:none;cursor: pointer;" onClick="set_lv_up_branch_ok()" >確定</td>
                    <td  width="100"></td>
                    <td  align="center" width="100" style=" font-size:22px; background-color:#FCBA56;cursor: pointer;" onClick="open_lv_up_branch()">關閉</td>
              </tr>
            </table>
          </div>
        </div>

        <!--我是測試的按鈕喔喔喔
        <div onClick="add_cs(180)" style="position:absolute; width:30px; height:30px; top:706px; left:560px; background-color:#339966">
        </div>-->


		<!--顯示按鈕列表-->
       	<div style="left:0px; top:0px; position: absolute;">
            <!--報表-->
            <img src= "img/btn/btn_paper.png" onClick="open_report()" style="left:19px; top:160px; position: absolute; cursor: pointer;">
            <!--升級-->
            <img id="btn_up_branch" src= "img/btn/btn_up_branch.png" onClick="open_lv_up_branch()"  style="left:19px; top:256px; position: absolute; cursor: pointer;">
            <!--上架-->
            <img src= "img/btn/btn_up.png" onClick="go_up_page()"  style="left:27px; top:361px; position: absolute; cursor: pointer;">
        	<!--回到大地圖-->
            <img src= "img/btn/btn_back.png" onClick="go_branch_page()"  style="left:848px; top:6px; position: absolute; cursor: pointer;">
        </div>

		<div id="mission" style="left:0px; top:0px; display:none; position: absolute;" >
        	<!--任務-->
        	<div onClick="" style="left:-30px; top:-30px; position: absolute; width:1100px; height:1000px;"></div>
            <img src= "img/mission/mission_back.png" style="left:113px; top:86px; position: absolute;">
            <!--任務 BAR-->
       		<div style="left:523px; top:302px; position: absolute;">

            	<img src= "img/mission/number_bar_2.png" style="left:2px; top:2px; position: absolute;">
                <div id="mission_bar" style="left:4px; top:8px; position: absolute; width:1px; height:25px; background-color:#6F0;"></div>
                <img src= "img/mission/number_bar_1.png" style="left:0px; top:0px; position: absolute;">
                <div id="mission_bar_line"  style="left:4px; top:4px; position: absolute;">

                </div>
            </div>
            <!--任務 選擇按鈕-->
            <div style="left:500px; width:98px; top:352px; position: absolute;" class="title_1">
            	我想要達成
        	</div>
            <div style="left:751px; width:98px; top:353px; position: absolute;" class="title_1">
            	本書籍
        	</div>
            <img src= "img/btn/arrow.png" onClick="set_mission_number(1,1)" style="cursor: pointer;left:698px; top:342px; position: absolute; width: 50px;">
            <img src= "img/btn/arrow.png" onClick="set_mission_number(1,-1)" style="cursor: pointer;left:595px; top:341px; position: absolute; width: 50px;" class="trun90">
          	<div id=mission_number  style="left:645px; top:347px; position: absolute; width:40px; border-style:solid; border-color:#000000; border-width:4px;" class="number_bar">5</div>
            <img src= "img/person/saver_f.png" id="mission_saver" style="left:334px; top:180px; position: absolute;">
            <div align="center"  id = "mission_content_tittle"  style="left:119px; width:330px; top:168px; position: absolute; font-weight: bold; font-size:30px;" class="title_0">
            上架任務</div>
            <div id = "mission_content"  style="left:129px; width:330px; top:207px; position: absolute;" class="title_1">
            根據市場調查。<BR>最近客人比較喜歡買<div style="color:#FF0000;display:inline;font-size:22px;" id="mission_content_branch">生活</div>
            <BR>類的書籍。<BR>
			右邊為幾位客人的意見。<BR>
			<BR>
			店長您覺得哪位的意見<BR>比較好呢？
       	  </div>
            <!--任務 難度按鈕-->
            <div id = "mission_lv_1" onClick="set_mission_number(0,0)" style="left:460px; top:81px; position: absolute;cursor: pointer;">
            	<img src= "img/mission/mission_back2.png" style="left:0px; top:0px; position: absolute;">
                <div style="left:21px; top:26px; position: absolute; width:130px;" class="title_1">
                	先
           	  <div style=" color:#FF0000;display:inline;" id="mission_com_lv_1">7</div>試賣看看吧！</div>
            	<img src= "img/person/npc_1.png" style="left:58px; top:74px; position: absolute;">

            </div>
       	 	<div id = "mission_lv_2" onClick="set_mission_number(0,1)" style="left:620px; top:81px; position: absolute;cursor: pointer;">
           		<img src= "img/mission/mission_back2.png" style="left:0px; top:0px; position: absolute;">
              <div style="left:68px; top:24px; position: absolute; width:130px; height: 197px;" class="title_1">
              	<BR><BR>　　　<div style=" color:#FF0000;display:inline;" id="mission_com_lv_2">7</div><BR>
              	　好像比較<BR> 　剛好。<BR></div>
                <img src= "img/person/npc_2.png" style="left:-8px; top:-10px; position: absolute; width: 120px;">

         	</div>
            <div id = "mission_lv_3" onClick="set_mission_number(0,2)" style="left:780px; top:81px; position: absolute;cursor: pointer;">
           		<img src= "img/mission/mission_back2.png" style="left:0px; top:0px; position: absolute;">
                <div style="left:20px; top:23px; position: absolute; width:130px;" class="title_1">
                	我想多看<BR>一些書<BR><BR><BR>就<div style=" color:#FF0000;display:inline;" id="mission_com_lv_3">7</div>吧。
                   </div>
            	<img src= "img/person/npc_3.png" width="153" height="226" style="left:54px; top:-19px; position: absolute;">
            </div>
            <img src= "img/btn/yes.png" style="left:603px; top:409px; position: absolute; width: 60px;cursor: pointer;" onClick="submit_mission()">
            <img src= "img/btn/no.png" style="left:761px; top:411px; position: absolute; width: 60px;cursor: pointer;" onClick="close_mission()">
		</div>
		<!-- 任務詳細頁-->
        <div id="task_info_page" style="position:absolute; top:80px; left:100px;">

        </div>

		<div id="prompt" style=" position:absolute; top:0px; left:0px; display:none;">
            <!--貼金小題字-->
          	<div style=" position:absolute; top:0px; left:0px; background-color:rgba(0,0,0,0.7); width:1200px; height:600px; "></div>
            <img src="img/common/back_box.png"  style="position:absolute; top:116px; left:305px;">
            <div align="center" onClick="hide_prompt()" style="position:absolute; top:294px; left:462px; width:100px; height:30px; background-color: #9F0; font-size:24px; cursor: pointer">確認</div>
            <div id="prompt_text" style="position:absolute; top:156px; left:338px; width:335px; height:129px; font-size:24px;">d d dd d </div>
  			<div id="prompt_pic_mode" style="position:absolute; top:117px; left:165px; ">
            	<img src="img/mission/book_pik.png" width="160" height="298" style="position:absolute; top:-45px; left:-17px;">
                <img id="prompt_pic_1" src="img/mission/0.png" width="80" height="70"  style="position:absolute; top:1px; left:-4px; font-size:24px;">
                <img id="prompt_pic_2" src="img/mission/0.png" width="80" height="70"  style="position:absolute; top:68px; left:-4px; font-size:24px;">
                <img id="prompt_pic_3" src="img/mission/0.png" width="80" height="70"  style="position:absolute; top:135px; left:-4px; font-size:24px;">
		  	</div>
        </div>

        <!-- 上架頁 -->
        <div id="up_page" style="position:absolute; top:-10px; left:-10px;">

        </div>


<script>
		//建立  任務詳細介面
		function go_task_info(name,task_number,task_time,branch_id,user_id)
		{
			//window.alert(name+task_number+task_time+branch_id+user_id);
			window.document.getElementById("task_info_page").innerHTML = '<iframe frameborder="0" src="./report/task/task_info/task_info.php?name='+name+'&task_number='+task_number+'&task_time='+task_time+'&branch_id='+branch_id+'&user_id='+user_id+'" width="900" height="400" style="position:absolute;top:0px;left:0px;"></iframe>';
		}

		//建立  上架介面
		function open_push_up()
		{
			add_debug("open_push_up:開始");
			add_debug("open_push_up:結束");
		}
		//建立  報表介面
		function open_report()
		{
			add_debug("open_report:開始");
			if(open_report_on == 0)
			{
				open_report_on = 1;
				add_debug("open_report:產生報表");
				window.document.getElementById("report").innerHTML = '<div onClick="" style="left:-30px; top:-30px; position: absolute; width:1100px; height:1000px;"></div><iframe src="./report/index_1.php?branch_id=<?php echo (int)$branch_id;?>&user_id=<?php echo $user_id; ?>" frameborder="0" style="left:120px; top:80px; position: absolute; width:800px; height:380px; overflow:hidden; overflow-y:auto;"></iframe>';

                //action_log
                for(key1 in json_category_info['lv1']){

                    var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                    var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                    if(cat_name===data_array["branch_name"]){
                        //action_log
                        add_action_branch_log(
                            '../branch/inc/add_action_branch_log/code.php',
                            'rep01',
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            '',
                            0,
                            <?php echo $branch_id;?>,
                            '',
                            0,
                            0,
                            0,
                            0,
                            cat_id,
                            '',
                            ''
                        );
                    }
                }

                var lv2_in_flag=false;
                for(key2 in json_category_info['lv2']){
                    var key2=parseInt(key2);
                    for(key3 in json_category_info['lv2'][key2]){
                        var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                        var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                        if(cat_name===data_array["branch_name"]){
                            //action_log
                            add_action_branch_log(
                                '../branch/inc/add_action_branch_log/code.php',
                                'rep01',
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                '',
                                0,
                                <?php echo $branch_id;?>,
                                '',
                                0,
                                0,
                                0,
                                0,
                                key2,
                                '',
                                ''
                            );

                            if(!lv2_in_flag){
                                add_action_branch_log(
                                    '../branch/inc/add_action_branch_log/code.php',
                                    'rep01',
                                    <?php echo $sess_uid;?>,
                                    <?php echo $user_id;?>,
                                    '',
                                    0,
                                    <?php echo $branch_id;?>,
                                    '',
                                    0,
                                    0,
                                    0,
                                    0,
                                    cat_id,
                                    '',
                                    ''
                                );
                                lv2_in_flag=true;
                            }
                        }
                    }
                }
			}
			else if(open_report_on == 1)
			{
				open_report_on = 0;
				add_debug("open_report:關閉報表");
				window.document.getElementById("report").innerHTML = "";
			}
			add_debug("open_report:結束");
		}
		//建立  任務介面 (任務流水號)
		function open_mission(value,value_2)
		{

			add_debug("open_mission:開始:接取任務:"+value);
			window.document.getElementById("mission").style.transition="0.9s";
			window.document.getElementById("mission").style.display = "block";
			person_id = value_2;
			mission_lv[0] = mission_array[value]["task_sdl_initial_easy"];
			mission_lv[1] = mission_array[value]["task_sdl_initial_normal"];
			mission_lv[2] = mission_array[value]["task_sdl_initial_hard"];
			mission_lv[3] = mission_array[value]["task_sdl_max"];


			//367 設定文字說明
			window.document.getElementById("mission_content_tittle").innerHTML = mission_array[value]['task_name'] + "(閱讀+推薦)";
			window.document.getElementById("mission_content_branch").innerHTML = branch_name;
			for(i = 1 ; i <=3;i++)
			{
				window.document.getElementById("mission_com_lv_"+i).innerHTML = mission_lv[i-1]+"本";
			}
			window.document.getElementById("mission_bar_line").innerHTML = "";
			var count_leght = 367/mission_lv[3];
			for( i =1;i < mission_lv[3];i++)
			{
				window.document.getElementById("mission_bar_line").innerHTML = window.document.getElementById("mission_bar_line").innerHTML+'<div style="left:'+count_leght*i+'px; top:0px; position: absolute; width:3px; height:15px; background-color:#000"></div>';
			}

			mission_sid = mission_array[value]["task_sid"];

			add_debug("open_mission:設定接取等級:"+mission_array[value]["task_sdl_initial_easy"]+"-__-"+mission_array[value]["task_sdl_initial_normal"]+"-__-"+mission_array[value]["task_sdl_initial_hard"]+"-__-"+mission_array[value]["task_sdl_max"]);

			set_mission_number(0,1);
			add_debug("open_mission:結束");

            //action_log
            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===data_array["branch_name"]){
                    //action_log
                    add_action_branch_log(
                        '../branch/inc/add_action_branch_log/code.php',
                        'm02',
                        <?php echo $sess_uid;?>,
                        <?php echo $user_id;?>,
                        '',
                        0,
                        <?php echo $branch_id;?>,
                        '',
                        0,
                        0,
                        0,
                        0,
                        cat_id,
                        '',
                        ''
                    );
                }
            }

            var lv2_in_flag=false;
            for(key2 in json_category_info['lv2']){
                var key2=parseInt(key2);
                for(key3 in json_category_info['lv2'][key2]){
                    var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                    var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                    if(cat_name===data_array["branch_name"]){
                        //action_log
                        add_action_branch_log(
                            '../branch/inc/add_action_branch_log/code.php',
                            'm02',
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            '',
                            0,
                            <?php echo $branch_id;?>,
                            '',
                            0,
                            0,
                            0,
                            0,
                            key2,
                            '',
                            ''
                        );

                        if(!lv2_in_flag){
                            add_action_branch_log(
                                '../branch/inc/add_action_branch_log/code.php',
                                'm02',
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                '',
                                0,
                                <?php echo $branch_id;?>,
                                '',
                                0,
                                0,
                                0,
                                0,
                                cat_id,
                                '',
                                ''
                            );
                            lv2_in_flag=true;
                        }
                    }
                }
            }
		}
		//關閉  任務介面
		function close_mission()
		{
			add_debug("close_mission:開始");
			window.document.getElementById("mission").style.transition="0.9s";
			window.document.getElementById("mission").style.display = "none";
			add_debug("close_mission:結束");

		}
		//建立  場景介面
		function set_scene(lv,type)
		{
			add_debug("建立場景物件:開始:LV"+lv);
			var sex_ = 'f';
			if(user_sex == 1)
			{ sex_ = 'm'};
			if(lv == 1)
			{
				window.document.getElementById("scene").innerHTML =
				'<div id="layer_person_3"></div><div id="layer_person_2"></div><img src= "img/Scene/LV_1_back.png" style="left:0px; top:0px; position: absolute;"><img src= "img/person/saver_'+sex_+'.png" style="left:452px; top:161px; position: absolute;"><div id = "layer_person_1"></div>';
			}else if(lv == 2)
			{
				window.document.getElementById("scene").innerHTML =
           		'<img src= "img/Scene/LV_2_3_back.png" style="left:0px; top:0px; position: absolute;"><img src= "img/Scene/LV_2_3_layer1.png" style="left:142px; top:119px; position: absolute;"><div id="layer_person_3"></div><img src= "img/Scene/LV_2_3_layer2.png" style="left:386px; top:116px; position: absolute;"><div id="layer_person_2"></div><img src= "img/person/saver_'+sex_+'.png" width="100" ="100px" style="left:257px; top:186px; position: absolute;"><img src= "img/Scene/LV_2_3_layer3.png" style="left:197px; top:300px; position: absolute;"><img src= "img/Scene/branch_2/LV_2_door.png" style="left:0px; top:0px; position: absolute;"><div id="layer_person_1"></div>';

			}else if(lv == 3)
			{
				window.document.getElementById("scene").innerHTML =
				'<img src= "img/Scene/LV_2_3_back.png" style="left:0px; top:0px; position: absolute;"><img src= "img/Scene/LV_2_3_layer1.png" style="left:142px; top:119px; position: absolute;"><div id="layer_person_3"></div><img src= "img/Scene/LV_2_3_layer2.png" style="left:386px; top:116px; position: absolute;"><div id="layer_person_2"></div><img src= "img/person/saver_'+sex_+'.png" width="100" ="100px" style="left:257px; top:186px; position: absolute;"><img src= "img/Scene/LV_2_3_layer3.png" style="left:197px; top:300px; position: absolute;"><img src= "img/Scene/branch_2/LV_3_door.png" style="left:0px; top:0px; position: absolute;"><div id="layer_person_1"></div>';


			}
			window.document.getElementById("mission_saver").src = "img/person/saver_"+sex_+".png";
			add_debug("建立場景物件:結束");
			get_mission_info();
		}

		//獲取分店任務可接資訊
		function get_mission_info()
		{
			add_debug("get_mission_info:開始");
			var url = "./ajax/get_mission_info.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data)
				{
					add_debug("get_mission_info:post:get_data -> "+data);
					mission_array = JSON.parse(data);

				}).error(function(e){
					add_debug("get_mission_info:post:error -> "+e );
				}).complete(function(e){
					add_debug("get_mission_info:post:complete");
					set_person();
				});

		}
		//設定NPC
		function set_person()
		{
			add_debug("set_person:開始");
			window.document.getElementById("layer_person_1").innerHTML="";
			window.document.getElementById("layer_person_2").innerHTML="";
			window.document.getElementById("layer_person_3").innerHTML="";


			var person_list_max = 3;
			var layer_max = 1;
			var person_list = new Array();
			var tmp = 0;
			person_id_list  = new Array();

			{//初始化NPC 位置與大小陣列
				person_list[tmp] = new Array();
				person_list[tmp]["src"] = "img/person/npc_1.png";
				person_list[tmp]["left"] = new Array("191","568","494");
				person_list[tmp]["top"] = new Array("338","313","252");
				person_list[tmp]["width"] = new Array("105","92","76");
				person_list[tmp]["height"] = new Array("151","124","108");
				tmp++;
			}
			{
				person_list[tmp] = new Array();
				person_list[tmp]["src"] = "img/person/npc_2.png";
				person_list[tmp]["left"] = new Array("568","646","568");
				person_list[tmp]["top"] = new Array("221","205","202");
				person_list[tmp]["width"] = new Array("145","120","86");
				person_list[tmp]["height"] = new Array("270","228","150");
				tmp++;
			}
			{
				person_list[tmp] = new Array();
				person_list[tmp]["src"] = "img/person/npc_3.png";
				person_list[tmp]["left"] = new Array("793","493","571");
				person_list[tmp]["top"] = new Array("193","188","180");
				person_list[tmp]["width"] = new Array("198","169","123");
				person_list[tmp]["height"] = new Array("293","240","181");
				tmp++;
			}


			if(branch_rank == 1)
			{
				layer_max = 1;
			}
			else
			{
				layer_max = 1;
			}

			add_debug("set_person:???"+layer_max);
			var random_tmp = new Array();
			var list_tmp  = new Array();
			for(var i= 0;i < person_list.length;i++)
			{
				tmp = Math.floor(Math.random()*layer_max+1);
				random_tmp[i] = new Array();
				random_tmp[i]["value"] = '<div id="person_'+i+'" style="left:'+person_list[i]["left"][tmp-1]+'px; top:'+person_list[i]["top"][tmp-1]+'px; position: absolute; width: '+person_list[i]["width"]+'px; height: '+person_list[i]["height"][tmp-1]+'px;"><img src= "'+person_list[i]["src"]+'"></div>';
				random_tmp[i]["layer"] = tmp;
				random_tmp[i]["id"] = i;
				list_tmp[i] = i;
			}

			//選出抽中的NPC
			var lottery_number = 2 ;//抽獎數目
			for(i=1;i<=person_list.length*5;i++)
			{
			　　　　j=Math.floor(Math.random()*person_list.length);
			　　　　k=Math.floor(Math.random()*person_list.length);
				 var temp = list_tmp[j];
			　　　　list_tmp[j]=list_tmp[k];
			　　　　list_tmp[k]=temp;
			}

			//建立抽中的NPC
			for(var i = 0; lottery_number > i ; i++)
			{
				person_id_list[i] = random_tmp[list_tmp[i]]["id"];
				window.document.getElementById("layer_person_"+random_tmp[list_tmp[i]]["layer"]).innerHTML = window.document.getElementById("layer_person_"+random_tmp[list_tmp[i]]["layer"]).innerHTML + random_tmp[list_tmp[i]]["value"];
			}

            //前往設定任務
			if(!visit)set_mission();
		}
		//設定接取任務
		function set_mission()
		{
			add_debug("set_mission:開始");

			var list_tmp  = new Array();
			for(var i = 0 ; i < mission_array.length ; i++)
			{
				list_tmp[i] = i;
			}

			//選出抽中的任務
			var lottery_number = 2 ;//抽獎數目
			if(mission_array.length!=1){
				for(i=0 ; i<=mission_array.length*5 ; i++)
				{
				　　　　j=Math.floor(Math.random()*mission_array.length);
				　　　　k=Math.floor(Math.random()*mission_array.length);
					 var temp = list_tmp[j];
				　　　　list_tmp[j]=list_tmp[k];
				　　　　list_tmp[k]=temp;
				}
			}

			//建立抽中的任務
			for(var i = 0 ; i < lottery_number &&  i < mission_array.length; i ++)
			{
				add_debug("set_mission:選取的角色:"+person_id_list[i]+"--->接取任務:"+list_tmp[i]);
				window.document.getElementById("person_"+person_id_list[i]).innerHTML = window.document.getElementById("person_"+person_id_list[i]).innerHTML + '<img src= "img/common/spc_1.png" id="'+"person_"+person_id_list[i]+'_btn" style="right:30px; top:10px; position: absolute;">';
				window.document.getElementById("person_"+person_id_list[i]).name = list_tmp[i];
				window.document.getElementById("person_"+person_id_list[i]).onclick  = function(){ open_mission(this.name,this.id);}
			}
			add_debug("set_mission:結束");

			set_hide(0,"讀取中");
			add_debug("全數讀取完畢  開啟畫面");
		}


		//設定等級BAR條
		function set_rank()
		{
			add_debug("設定等級BAR條:開始");
			window.document.getElementById("lv_text").innerHTML = branch_rank;
			window.document.getElementById("rank_text").innerHTML = branch_cs+" / "+up_branch_cs;
			if(up_branch_cs==-1)window.document.getElementById("rank_text").innerHTML = branch_cs+" /　--　";
			pic_length = 230;
			add_debug("設定等級BAR條:長度設定:"+(up_branch_cs-branch_cs));
			if((up_branch_cs-branch_cs)<0)branch_cs = up_branch_cs;
			pa = branch_cs/up_branch_cs;
			add_debug("設定等級BAR條:長度設定:"+pa+'='+branch_cs+'/'+up_branch_cs);
			rank_bar_print = pic_length*pa - pic_length;
			window.document.getElementById("rank_bar").style.left = rank_bar_print+"px";
			add_debug("設定等級BAR條:結束");
		}

		function add_cs(value)
		{
				branch_cs = Math.floor(branch_cs) + Math.floor(value);
				set_rank();
		}


		//設定任務接取數直   (模式:0指定/1加減 ， 數值)
		function set_mission_number(mode,value)
		{
			add_debug("設定任務接取數:開始");
			var value_max = mission_lv[3];
			if(mode == 0)
			{
				if(value==0)
				{
					mission_number=mission_lv[value];
					mission_mode=1;
					window.document.getElementById("mission_lv_1").className = "nograys";
					window.document.getElementById("mission_lv_2").className = "grays";
					window.document.getElementById("mission_lv_3").className = "grays";
					window.document.getElementById("mission_bar").style.backgroundColor="#6F0";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
				if(value==1)
				{
					mission_number=mission_lv[value];
					mission_mode=2;
					window.document.getElementById("mission_lv_1").className = "grays";
					window.document.getElementById("mission_lv_2").className = "nograys";
					window.document.getElementById("mission_lv_3").className = "grays";
					window.document.getElementById("mission_bar").style.backgroundColor="#FC0";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
				if(value==2)
				{
					mission_number=mission_lv[value];
					mission_mode=3;
					window.document.getElementById("mission_lv_1").className = "grays";
					window.document.getElementById("mission_lv_2").className = "grays";
					window.document.getElementById("mission_lv_3").className = "nograys";
					window.document.getElementById("mission_bar").style.backgroundColor="#F00";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
			}
			if(mode == 1)
			{
				add_debug("設定任務接取數:模式:細微調整:value"+value);
				mission_number=mission_number+value;
				if(mission_number<mission_lv[0]) mission_number=mission_lv[0];
				if(mission_number>mission_lv[3]) mission_number=mission_lv[3];

				if(mission_number < mission_lv[1])
				{
					mission_mode=1;
					window.document.getElementById("mission_lv_1").className = "nograys";
					window.document.getElementById("mission_lv_2").className = "grays";
					window.document.getElementById("mission_lv_3").className = "grays";
					window.document.getElementById("mission_bar").style.backgroundColor="#6F0";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
				else if(mission_number < mission_lv[2])
				{
					mission_mode=2;
					window.document.getElementById("mission_lv_1").className = "grays";
					window.document.getElementById("mission_lv_2").className = "nograys";
					window.document.getElementById("mission_lv_3").className = "grays";
					window.document.getElementById("mission_bar").style.backgroundColor="#FC0";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
				else
				{
					mission_mode=3;
					window.document.getElementById("mission_lv_1").className = "grays";
					window.document.getElementById("mission_lv_2").className = "grays";
					window.document.getElementById("mission_lv_3").className = "nograys";
					window.document.getElementById("mission_bar").style.backgroundColor="#F00";
					window.document.getElementById("mission_bar").style.transition="0.5s";
				}
			}
			window.document.getElementById("mission_number").innerHTML=mission_number;
			var mission_bar_width = 367*mission_number/value_max;
			window.document.getElementById("mission_bar").style.width = mission_bar_width+"px";
			add_debug("設定任務接取數:結束");
		}

		//設定人氣指數  (人氣值)
		function set_popularity(value)
		{
			add_debug("設定人氣指數:開始");
			window.document.getElementById("popularity_text").innerHTML = value;
			add_debug("設定人氣指數:結束");
		}

		//設定閱讀指數   (閱讀數量)
		function set_read(value)
		{
			add_debug("設定閱讀指數:開始");
			window.document.getElementById("read_text").innerHTML = value;
			add_debug("設定閱讀指數:結束");
		}

		//追加金錢
		function add_coin(value)
		{
			add_debug("追加金錢:開始");
			coin = Math.floor(coin) + Math.floor(value);
			add_debug("追加金錢:coin=>"+coin);
			set_coin();
		}


		//設定金錢數   (奎壁)
		function set_coin()
		{
			add_debug("set_coin:設定金錢數:開始:");
			if(coin_show == -1)
			{
				coin_show = coin;
				window.document.getElementById("coin_text").innerHTML = coin_show;
			}else
			{
				if(show_coin_on)
				{
					window.document.getElementById("coin_text").style.fontSize = '35px';

					show_coin_on = 0;
					add_debug("show_coin:金錢跳動動畫:開始")
					coin_timer = window.setInterval(show_coin,20);
				}
			}
			add_debug("set_coin:設定金錢數:結束");
		}
		//金錢跳動動畫   (奎壁)
		function show_coin()
		{

			coin_show =parseInt(coin_show +1);
			if((coin-3) <= coin_show && coin_show <= (coin+3))
			{
				window.document.getElementById("coin_text").style.fontSize = '20px';
				coin_show = coin;
				window.document.getElementById("coin_text").innerHTML = coin_show;
				clearInterval(coin_timer);
				show_coin_on = 1;

			}
			window.document.getElementById("coin_text").innerHTML = coin_show;

		}

		//獲取分店基本資訊
		function get_branch_info()
		{
			if(visit)set_visit();//啟動拜訪者模式

			add_debug("get_branch_info:開始");
			var url = "./ajax/get_branch_info.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data)
				{
					add_debug("get_branch_info:post:get_data -> "+data);
					if(data == "" || data == null || data == "null")set_hide(1,"錯誤嘗試!");
					data_array = JSON.parse(data);
					branch_rank = data_array["branch_rank"];
					branch_cs = data_array["branch_cs"];
					branch_visit = data_array["branch_visit"];
					branch_nickname = data_array["branch_nickname"];
					branch_lv = data_array["branch_lv"];
					branch_name =  data_array["branch_name"];
					user_name =  data_array["name"];
					user_sex =  data_array["sex"];
					up_branch_ok = data_array["up_branch_ok"];
					up_branch_cs = -1;//data_array["up_branch_cs"];
					up_branch_need_rec  = data_array["up_rec_book"];
					up_branch_need_read  = data_array["up_read_book"];
					up_branch_rec = data_array["up_branch_rec"];
					up_branch_read = data_array["up_branch_read"];
					up_branch_spent_coin = data_array["up_spent_coin"];

					coin =  data_array["user_coin"];
				}).error(function(e){
					add_debug("get_branch_info:post:error -> "+e );
				}).complete(function(e){
					add_debug("get_branch_info:post:complete");
					set_rank();
					set_read(240);//------------------------------讀書資歷暫無
					set_lv_up_branch();
					set_coin();
					set_head_info();
					set_popularity(branch_visit);
					set_scene(branch_rank,0);

				});
		}



		//任務確認送出
		function submit_mission()
		{
            //action_log
            for(key1 in json_category_info['lv1']){

                var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                if(cat_name===data_array["branch_name"]){
                    //action_log
                    add_action_branch_log(
                        '../branch/inc/add_action_branch_log/code.php',
                        'm01',
                        <?php echo $sess_uid;?>,
                        <?php echo $user_id;?>,
                        '',
                        0,
                        <?php echo $branch_id;?>,
                        '',
                        0,
                        0,
                        0,
                        0,
                        cat_id,
                        '<?php echo date("Y-m-d H:i:s");?>',
                        ''
                    );
                }
            }

            var lv2_in_flag=false;
            for(key2 in json_category_info['lv2']){
                var key2=parseInt(key2);
                for(key3 in json_category_info['lv2'][key2]){
                    var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                    var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                    if(cat_name===data_array["branch_name"]){
                        //action_log
                        add_action_branch_log(
                            '../branch/inc/add_action_branch_log/code.php',
                            'm01',
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            '',
                            0,
                            <?php echo $branch_id;?>,
                            '',
                            0,
                            0,
                            0,
                            0,
                            key2,
                            '<?php echo date("Y-m-d H:i:s");?>',
                            ''
                        );

                        if(!lv2_in_flag){
                            add_action_branch_log(
                                '../branch/inc/add_action_branch_log/code.php',
                                'm01',
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                '',
                                0,
                                <?php echo $branch_id;?>,
                                '',
                                0,
                                0,
                                0,
                                0,
                                cat_id,
                                '<?php echo date("Y-m-d H:i:s");?>',
                                ''
                            );
                            lv2_in_flag=true;
                        }
                    }
                }
            }

			set_hide(1,"讀取中");
			var url = "./ajax/submit_mission.php";
			$.post(url, {
					mission_number:mission_number,
					mission_sid:mission_sid,
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data)
				{
					add_debug("submit_mission:post:get_data -> "+data);
					data_array = JSON.parse(data);
					add_debug("submit_mission:post:error -> "+person_id );
					window.document.getElementById(person_id).onclick  = function(){}//取消任務觸發
					window.document.getElementById(person_id+'_btn').style.display = "none";//關閉任務
					show_prompt("閱讀左圖相同的書才能完成任務喔!!",1);
				}).error(function(e){
					add_debug("submit_mission:post:error -> "+e );
				}).complete(function(e){
					add_debug("submit_mission:post:complete");
					window.document.getElementById("mission").style.display = "none";//關閉任務

					set_hide(0,"");
					reload();
				});
		}

		//開啟店面升級介面
		function open_lv_up_branch()
		{
			if(open_up_branch == 0)
			{
				open_up_branch = 1;
				window.document.getElementById("up_branch_page").style.display="block";
				add_debug("open_lv_up_branch:開啟升級介面");

                //action_log
                for(key1 in json_category_info['lv1']){

                    var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                    var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                    if(cat_name===data_array["branch_name"]){
                        //action_log
                        add_action_branch_log(
                            '../branch/inc/add_action_branch_log/code.php',
                            'u01',
                            <?php echo $sess_uid;?>,
                            <?php echo $user_id;?>,
                            '',
                            0,
                            <?php echo $branch_id;?>,
                            '',
                            0,
                            0,
                            0,
                            0,
                            cat_id,
                            '',
                            ''
                        );
                    }
                }

                var lv2_in_flag=false;
                for(key2 in json_category_info['lv2']){
                    var key2=parseInt(key2);
                    for(key3 in json_category_info['lv2'][key2]){
                        var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                        var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                        if(cat_name===data_array["branch_name"]){
                            //action_log
                            add_action_branch_log(
                                '../branch/inc/add_action_branch_log/code.php',
                                'u01',
                                <?php echo $sess_uid;?>,
                                <?php echo $user_id;?>,
                                '',
                                0,
                                <?php echo $branch_id;?>,
                                '',
                                0,
                                0,
                                0,
                                0,
                                key2,
                                '',
                                ''
                            );

                            if(!lv2_in_flag){
                                add_action_branch_log(
                                    '../branch/inc/add_action_branch_log/code.php',
                                    'u01',
                                    <?php echo $sess_uid;?>,
                                    <?php echo $user_id;?>,
                                    '',
                                    0,
                                    <?php echo $branch_id;?>,
                                    '',
                                    0,
                                    0,
                                    0,
                                    0,
                                    cat_id,
                                    '',
                                    ''
                                );
                                lv2_in_flag=true;
                            }
                        }
                    }
                }
			}else
			{
				window.document.getElementById("up_branch_page").style.display="none";
				open_up_branch = 0;
				add_debug("open_lv_up_branch:關閉升級介面");
			}
			//
		}

		//設定頭像等資訊
		function set_head_info()
		{
			window.document.getElementById("user_name").innerHTML =user_name;
			window.document.getElementById("branch_name").innerHTML =branch_name+"書店";
			window.document.getElementById("head_pic").src = "img/bar/head_"+user_sex+".png";

		}

		//設定
		function set_lv_up_branch()
		{
			add_debug("set_lv_up_branch:開始");
			window.document.getElementById("up_branch_help").innerHTML = "";
			window.document.getElementById("up_branch_coin").innerHTML = coin ;
			window.document.getElementById("up_branch_need_coin").innerHTML = up_branch_spent_coin ;
			window.document.getElementById("up_branch_over_coin").innerHTML = coin -  up_branch_spent_coin;

			//window.document.getElementById("up_branch_cs").innerHTML = branch_cs ;
			//window.document.getElementById("up_branch_need_cs").innerHTML = up_branch_cs ;//
			window.document.getElementById("up_branch_read").innerHTML = up_branch_read ;
			window.document.getElementById("up_branch_need_read").innerHTML = up_branch_need_read ;

			window.document.getElementById("up_branch_rec").innerHTML = up_branch_rec ;
			window.document.getElementById("up_branch_need_rec").innerHTML = up_branch_need_rec ;
			window.document.getElementById("up_branch_click_ok").style.display = "block";
			if((coin -  up_branch_spent_coin) < 0)
			{
				window.document.getElementById("up_branch_click_ok").style.display = "none";
				window.document.getElementById("up_branch_help").innerHTML= "升級金錢不足　　";
			}
			/*if((branch_cs -  up_branch_cs) < 0)
			{
				window.document.getElementById("up_branch_click_ok").style.display = "none";
				window.document.getElementById("up_branch_help").innerHTML= window.document.getElementById("up_branch_help").innerHTML+" 滿意度不足　　";

			}*/

			if((up_branch_rec -  up_branch_need_rec)< 0)
			{
				window.document.getElementById("up_branch_click_ok").style.display = "none";
				window.document.getElementById("up_branch_help").innerHTML= window.document.getElementById("up_branch_help").innerHTML+"推薦量不足　　";
			}
			if((up_branch_read -  up_branch_need_read) < 0)
			{
				window.document.getElementById("up_branch_click_ok").style.display = "none";
				window.document.getElementById("up_branch_help").innerHTML= window.document.getElementById("up_branch_help").innerHTML+"閱讀量不足　　";
			}


			if(up_branch_ok <= 0)
			{
				window.document.getElementById("up_branch_help").innerHTML= "其他分店等級不足  請統一提升到"+branch_rank+"級";
				window.document.getElementById("up_branch_click_ok").style.display = "none";
			}

			if(up_branch_spent_coin == -1 )
			{
				window.document.getElementById("up_branch_click_ok").style.display = "none";
				window.document.getElementById("up_branch_over_coin").innerHTML= "----";
				window.document.getElementById("up_branch_need_coin").innerHTML= "----";
				window.document.getElementById("up_branch_need_rec").innerHTML= "----";
				window.document.getElementById("up_branch_need_read").innerHTML = "----";
				window.document.getElementById("up_branch_help").innerHTML= "書店已升級最高階";
			}
			add_debug("set_lv_up_branch:結束");
		}

		function set_lv_up_branch_ok()
		{
			add_debug("set_lv_up_branch_ok:post:開始");
			set_hide(1,"讀取中");
			var url = "./ajax/set_up_branch.php";
			$.post(url, {
					user_id:user_id,
					branch_id:branch_id
				}).success(function (data)
				{
					add_debug("set_lv_up_branch_ok:post:get_data -> "+data);
					data_array = JSON.parse(data);
					if(data_array["state"] != 'ok')
					{
						window.alert("錯誤資訊:"+data_array["state"]+"請重新整理頁面");
						window.location.reload() ;
					}else{
                        //action_log
                        for(key1 in json_category_info['lv1']){

                            var cat_id  =parseInt(json_category_info['lv1'][key1]['cat_id']);
                            var cat_name=(json_category_info['lv1'][key1]['cat_name']);

                            if(cat_name===data_array["branch_name"]){
                                //action_log
                                add_action_branch_log(
                                    '../branch/inc/add_action_branch_log/code.php',
                                    'u02',
                                    <?php echo $sess_uid;?>,
                                    <?php echo $user_id;?>,
                                    '',
                                    0,
                                    <?php echo $branch_id;?>,
                                    '',
                                    data_array["branch_rank"],
                                    0,
                                    0,
                                    0,
                                    cat_id,
                                    '',
                                    ''
                                );
                            }
                        }

                        var lv2_in_flag=false;
                        for(key2 in json_category_info['lv2']){
                            var key2=parseInt(key2);
                            for(key3 in json_category_info['lv2'][key2]){
                                var cat_id  =parseInt(json_category_info['lv2'][key2][key3]['cat_id']);
                                var cat_name=(json_category_info['lv2'][key2][key3]['cat_name']);

                                if(cat_name===data_array["branch_name"]){
                                    //action_log
                                    add_action_branch_log(
                                        '../branch/inc/add_action_branch_log/code.php',
                                        'u02',
                                        <?php echo $sess_uid;?>,
                                        <?php echo $user_id;?>,
                                        '',
                                        0,
                                        <?php echo $branch_id;?>,
                                        '',
                                        data_array["branch_rank"],
                                        0,
                                        0,
                                        0,
                                        key2,
                                        '',
                                        ''
                                    );

                                    if(!lv2_in_flag){
                                        add_action_branch_log(
                                            '../branch/inc/add_action_branch_log/code.php',
                                            'u02',
                                            <?php echo $sess_uid;?>,
                                            <?php echo $user_id;?>,
                                            '',
                                            0,
                                            <?php echo $branch_id;?>,
                                            '',
                                            data_array["branch_rank"],
                                            0,
                                            0,
                                            0,
                                            cat_id,
                                            '',
                                            ''
                                        );
                                        lv2_in_flag=true;
                                    }
                                }
                            }
                        }
                    }
				}).error(function(e){
					add_debug("set_lv_up_branch_ok:post:error -> "+e );
				}).complete(function(e){
					add_debug("set_lv_up_branch_ok:post:complete");
					get_branch_info();
				});


		}
		function go_branch_page()
		{
			window.location="../branch/index_php.php?uid="+user_id+"&branch_id="+branch_id+"";
		}

		function hide_prompt()
		{
			window.document.getElementById('prompt').style.display = 'none';
		}

		function show_prompt(value,mode)
		{
			add_debug("show_prompt:value>"+value+":mode>"+mode);
			if(mode == 0)
			{//純訊息
				window.document.getElementById('prompt_pic_mode').style.display = 'none';
				window.document.getElementById('prompt_text').innerHTML = value;
			}
			else if(mode == 1)
			{//書本提示貼紙

				window.document.getElementById('prompt_text').innerHTML = value;
				window.document.getElementById('prompt_pic_mode').style.display = 'block';
				window.document.getElementById('prompt_pic_1').src = "img/mission/0.png";
				window.document.getElementById('prompt_pic_2').src = "img/mission/0.png";
				window.document.getElementById('prompt_pic_3').src = "img/mission/0.png";
				window.document.getElementById('prompt_pic_'+branch_rank).src = "img/mission/"+branch_id+".png";
			}
			window.document.getElementById('prompt').style.display = 'block';
		}
		function go_up_page()
		{
			window.document.getElementById("up_page").innerHTML='<iframe src="./branch_shelf/index.php?user_id='+user_id+'&branch_id='+branch_id+'" width="1050" height="520" style="position:absolute; top:0px; left:0px;"></iframe>';
		}
		function set_visit()
		{
			window.document.getElementById('btn_up_branch').style.display = "none";
		}
		get_branch_info();

        $(function(){
            <?php if($sess_uid!==$user_id):?>
                //action_log
                add_action_branch_log(
                    '../branch/inc/add_action_branch_log/code.php',
                    'la04',
                    <?php echo $sess_uid;?>,
                    <?php echo $user_id;?>,
                    '',
                    <?php echo $friend_ident;?>,
                    <?php echo $branch_id;?>,
                    '',
                    0,
                    0,
                    0,
                    0,
                    0,
                    '',
                    ''
                );
            <?php endif;?>
        });
		</script>


</body>