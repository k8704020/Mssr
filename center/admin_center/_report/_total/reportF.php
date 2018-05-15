<?php
//-------------------------------------------------------
//網管中心
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        @session_start();

        //啟用BUFFER
        @ob_start();

        //外掛設定檔
        require_once(str_repeat("../",4).'config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'inc/code',
                    APP_ROOT.'center/teacher_center/inc/code',

                    APP_ROOT.'lib/php/db/code',
                    APP_ROOT.'lib/php/array/code'
                    );
        func_load($funcs,true);

        //清除並停用BUFFER
        @ob_end_clean();

    //---------------------------------------------------
    //初始化
    //---------------------------------------------------

        //初始化, 班級對應所屬名稱
        $arrys_class_code_rev=array();

        //初始化, 所有班級
        $arrys_class_code=array();

        //初始化, 所有家長
        $arrys_kinship=array();

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

            //本學期開始時間
            $semester_start =trim('2015-01-23');
            //本學期結束時間
            $semester_end   =trim('2015-07-31');

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            //建立連線 user
            $conn_user=conn($db_type='mysql',$arry_conn_user);

            //建立連線 mssr
            $conn_mssr=conn($db_type='mysql',$arry_conn_mssr);

        //-----------------------------------------------
        //查找, 班級對應所屬名稱
        //-----------------------------------------------

            $sql="
                SELECT *
                FROM `class_name`
                WHERE 1=1
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);
            foreach($arrys_result as $inx=>$arry_result){
                $rs_class_category=(int)$arry_result['class_category'];
                $rs_classroom=(int)$arry_result['classroom'];
                $rs_class_name=trim($arry_result['class_name']);

                //匯入, 班級對應所屬名稱
                $arrys_class_code_rev[$rs_class_category][$rs_classroom]=$rs_class_name;
            }

        //-----------------------------------------------
        //查找, 所有班級
        //-----------------------------------------------

            $sql="
                SELECT
                    `semester`.`semester_code`,
                    `semester`.`school_code`,

                    `school`.`school_name`,

                    `class`.`class_code`,
                    `class`.`class_category`,
                    `class`.`grade`,
                    `class`.`classroom`,

                    `teacher`.`uid`,

                    `member`.`name`
                FROM `semester`
                    INNER JOIN `school` ON
                    `semester`.`school_code`=`school`.`school_code`

                    INNER JOIN `class` ON
                    `semester`.`semester_code`=`class`.`semester_code`

                    INNER JOIN `teacher` ON
                    `class`.`class_code`=`teacher`.`class_code`

                    INNER JOIN `member` ON
                    `teacher`.`uid`=`member`.`uid`
                WHERE 1=1
                    AND `semester`.`start` = '{$semester_start}'
                    AND `semester`.`end`   = '{$semester_end  }'

                    AND `teacher`.`responsibilities`=1
                    AND `teacher`.`start`  = '{$semester_start}'
                    AND `teacher`.`end`    = '{$semester_end  }'

                    #AND `semester`.`school_code`='gnd'
                ORDER BY `school`.`school_name`, `class`.`grade`,`class`.`classroom` ASC
            ";
            $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array(),$arry_conn_user);

    //---------------------------------------------------
    //分頁處理
    //---------------------------------------------------

        $numrow=0;
        $numrow=count($arrys_result);   //資料總筆數
        $psize =500;                    //單頁筆數,預設全部
        $pnos  =0;                      //分頁筆數
        $pinx  =1;                      //目前分頁索引,預設1
        $sinx  =0;                      //值域起始值
        $einx  =0;                      //值域終止值

        if(isset($_GET['psize'])){
            $psize=(int)$_GET['psize'];
            if($psize===0){
                $psize=10;
            }
        }
        if(isset($_GET['pinx'])){
            $pinx=(int)$_GET['pinx'];
            if($pinx===0){
                $pinx=1;
            }
        }

        $pnos  =ceil($numrow/$psize);
        $pinx  =($pinx>$pnos)?$pnos:$pinx;

        $sinx  =(($pinx-1)*$psize)+1;
        $einx  =(($pinx)*$psize);
        $einx  =($einx>$numrow)?$numrow:$einx;
        //echo $numrow."<br/>";

    //---------------------------------------------------
    //資料,設定
    //---------------------------------------------------

        $arrys_result=db_result($conn_type='pdo',$conn_user,$sql,array($sinx-1,$psize),$arry_conn_user);

        //匯入, 所有班級
        $arrys_class_code=$arrys_result;

        //網頁標題
        $title='明日書店, 相關報表';
?>
<!DOCTYPE HTML>
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo meta_keywords($key='mssr');?>
    <?php echo meta_description($key='mssr');?>
    <?php echo bing_analysis($allow=true);?>
    <?php echo robots($allow=true);?>

    <!-- 通用 -->
    <script type="text/javascript" src="../../../../inc/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/jquery/basic/code.js"></script>

    <!-- 專屬 -->
    <script type="text/javascript" src="../../../../lib/js/public/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/string/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/table/code.js"></script>
    <script type="text/javascript" src="../../../../lib/js/array/code.js"></script>
</Head>

<Body>
<!-- rules='rows' -->
<!-- 容器區塊 開始 -->
<div id="container">
    <table id="mod_data_tbl" cellpadding="0" cellspacing="0" border="1" width="100%" style="font-size:20px;" rules='rows'/>
        <tr>
            <td colspan="13">
            </td>
        </tr>
        <tr align="center">
            <td width="150px" height="40px">學校名稱                </td>
            <td width="150px" height="40px">年級                    </td>
            <td width="150px" height="40px">班級                    </td>
            <td width="150px" height="40px">導師名稱                </td>
            <td width="150px" height="40px">導師UID                 </td>
            <td width="150px" height="40px">學生人數                </td>
            <!-- <td width="150px" height="40px">老師閱讀本數            </td> -->
            <td width="150px" height="40px">老師推薦本數            </td>
            <td width="150px" height="40px">老師指導次數            </td>
            <td width="" height="40px">老師指導本數            </td>
        </tr>
        <?php foreach($arrys_class_code as $inx=>$arry_class_code):?>
        <?php
        //-----------------------------------------------
        //接收欄位
        //-----------------------------------------------

            extract($arry_class_code, EXTR_PREFIX_ALL, "rs");

        //-----------------------------------------------
        //處理欄位
        //-----------------------------------------------

            $rs_semester_code =trim($rs_semester_code);
            $rs_school_code   =trim($rs_school_code);
            $rs_school_name   =trim($rs_school_name);
            $rs_class_code    =trim($rs_class_code);

            $rs_class_category=(int)($rs_class_category);
            $rs_grade         =(int)($rs_grade);
            $rs_classroom     =(int)($rs_classroom);
            $rs_uid           =(int)($rs_uid);

            $rs_name          =trim($rs_name);

        //-----------------------------------------------
        //學生人數
        //-----------------------------------------------

            $users=arrys_users($conn_user,$rs_class_code,$date=$semester_start,$arry_conn_user);
            if(empty($users)){
                $arrys_users=array();
            }else{
                $arrys_users=explode("','",$users);
                $arrys_users_cno=count($arrys_users);
                foreach($arrys_users as $inx=>$val){
                    if(($inx===0)||($inx===$arrys_users_cno-1)){
                        $val=str_replace("'","", $val);
                        $arrys_users[$inx]=(int)$val;
                    }
                }
            }

            //學生人數
            $users_cno=count($arrys_users);
        ?>
        <tr align="center">
            <td><?php echo htmlspecialchars($rs_school_name);?> </td>
            <td><?php echo ($rs_grade);?>年                     </td>
            <td>
                <?php echo ($arrys_class_code_rev[$rs_class_category][$rs_classroom]);?>班
            </td>
            <td><?php echo htmlspecialchars($rs_name);?>        </td>
            <td><?php echo ($rs_uid);?>                         </td>
            <td><?php echo (count($arrys_users));?>             </td>
            <!-- <td>
                <span name="t_read_group_<?php echo $rs_class_code;?>">
                    <img name="img_read_group" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                </span>
            </td> -->
            <td>
                <span name="t_rec_group_<?php echo $rs_class_code;?>">
                    <img name="img_read_group" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                </span>
            </td>
            <td>
                <span name="t_comment_frequency_<?php echo $rs_class_code;?>">
                    <img name="img_read_group" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                </span>
            </td>
            <td>
                <span name="t_comment_group_<?php echo $rs_class_code;?>">
                    <img name="img_read_group" src="../../../../img/icon/loader.gif" width="20" height="20" border="0" alt="讀取中..."/>
                </span>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
    <table border="0" width="100%">
        <tr valign="middle">
            <td align="left">
                <!-- 分頁列 -->
                <span id="page" style="position:relative;margin-top:10px;"></span>
            </td>
        </tr>
    </table>
</div>
<?php
//echo "<Pre>";
//print_r($arrys_class_code);
//echo "</Pre>";
?>
<!-- 容器區塊 結束 -->
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    var psize=<?php echo $psize;?>;
    var pinx =<?php echo $pinx;?>;

    function ajax_set(arrys_class_code){
    //啟用ajax設置

        //參數
        var $url        ="data_detailed.php";
        var $type       ="GET";
        var $datatype   ="json";
        var arrys_class_code  =arrys_class_code;

        for(key in arrys_class_code){

            var class_code=arrys_class_code[key]['class_code'];

            ////老師閱讀本數
            //ajax($url,$type,$datatype,'t_read_group',class_code);

            //老師推薦本數
            ajax($url,$type,$datatype,'t_rec_group',class_code);

            //老師指導次數
            ajax($url,$type,$datatype,'t_comment_frequency',class_code);

            //老師指導本數
            ajax($url,$type,$datatype,'t_comment_group',class_code);
        }

        function ajax($url,$type,$datatype,$data_type,$class_code){
        //ajax設置
            $.ajax({
            //參數設置
                async      :true,
                cache      :false,
                global     :true,
                timeout    :5000000,
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",

                url        :$url,
                type       :$type,
                datatype   :$datatype,
                data       :{
                    data_type :encodeURI(trim($data_type)),
                    class_code:encodeURI(trim($class_code))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理

                    var respones=jQuery.parseJSON(respones);
                    var data_type=respones['data_type'];
                    var class_code=respones['class_code'];
                    var cno=respones['cno'];

                    try{
                        var ospans=document.getElementsByName(data_type+"_"+class_code);
                        for(var i=0;i<ospans.length;i++){
                            var ospan=ospans[i];
                            $(ospan).empty().text(cno);
                        }
                    }catch(err){
                        var ospans=document.getElementsByName(data_type+"_"+class_code);
                        for(var i=0;i<ospans.length;i++){
                            var ospan=ospans[i];
                            $(ospan).empty().text(0);
                        }
                    }
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){

                    }else{

                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
        }
    }

    window.onload=function(){

        //班級陣列
        var arrys_class_code=<?php echo json_encode($arrys_class_code,true);?>;

        ajax_set(arrys_class_code);

        //套表格列奇偶色
        table_hover(tbl_id='mod_data_tbl',c_odd='#fff',c_even='#fff',c_on='#e6faff');

        //分頁列
        var cid         ="page";                        //容器id
        var numrow      =<?php echo (int)$numrow;?>;    //資料總筆數
        var psize       =<?php echo (int)$psize ;?>;    //單頁筆數,預設10筆
        var pnos        =<?php echo (int)$pnos  ;?>;    //分頁筆數
        var pinx        =<?php echo (int)$pinx  ;?>;    //目前分頁索引,預設1
        var sinx        =<?php echo (int)$sinx  ;?>;    //值域起始值
        var einx        =<?php echo (int)$einx  ;?>;    //值域終止值
        var list_size   =5;                             //分頁列顯示筆數,5
        var url_args    ={};                            //連結資訊
        url_args={
            'pinx_name' :'pinx',
            'psize_name':'psize',
            'page_name' :'reportF.php',
            'page_args' :{}
        }
        var opage=pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args);
    }

</script>
</Body>
