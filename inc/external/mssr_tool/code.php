<?php
//-------------------------------------------------------
//函式: mssr_tool()
//用途: 明日書店 外掛函式庫說明
//日期: 2014年11月30日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    //---------------------------------------------------
    //設置測試資料
    //---------------------------------------------------

        if(isset($_GET['show'])&&$_GET['show']='yes')echo mssr_tool();

    //---------------------------------------------------
    //函式
    //---------------------------------------------------

        function mssr_tool(){
        //-----------------------------------------------
        //函式: mssr_tool()
        //用途: 明日書店 外掛函式庫說明
        //-----------------------------------------------
        //
        //-----------------------------------------------

            //-------------------------------------------
            //初始化, 設定
            //-------------------------------------------

                $description='
                    <!DOCTYPE HTML>
                    <Html lang="zh_TW">
                    <Head>
                        <Title></Title>
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
                        <meta http-equiv="Content-Language" content="UTF-8">
                    </head>

                    <body style="margin:0;">
                        <table align="center" cellpadding="0" cellspacing="0" border="1" width="960px"/>
                            <tr valign="middle">
                                <td align="center" valign="middle" bgcolor="#ebebeb" style="color:#000;" colspan="2" height="100px">
                                    <h2><strong>歡迎使用明日書店外掛函式庫，以下將會列出您可使用的API。</strong></h2>
                                    <h3><strong>使用之前，請先掛載 $_SERVER["DOCUMENT_ROOT"]/mssr/inc/external/code.php。</strong></h3>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: object mobile_borrow()<br/><br/>
                                    用途: 行動裝置閱讀登記<br/><br/>
                                    說明: 本物件函式專門提供為行動裝置<br/>
                                        <span style="position:relative;left:40px;">進行閱讀登記所使用，回傳的型態</span><br/>
                                        <span style="position:relative;left:40px;">因應各 METHODS 不同</span><br/>
                                        <span style="position:relative;left:40px;">而有所調整。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $uid            使用者索引
    $school_code    學校代號
    $book_code      書本代號(ISBN號 OR 圖書館登錄號)
    $book_sid       書本識別碼(唯一)
    $book_name      書名
    $book_author    作者
    $book_publisher 出版社

  實際用法:

    //實體化物件
    $omobile_borrow =new mobile_borrow();

    //檢核書籍資訊
    //說明: 此 METHODS 將會回傳二維陣列。

        //設置參數
        $uid            =(int)5030;
        $book_code      =trim("9789862115503");
        $arrys_book_info=$omobile_borrow->get_books_info($uid,$book_code);
        print_r($arrys_book_info);

    //登記書籍
    //說明: 此 METHODS 將直接對書籍進行閱讀登記，結果將回傳TRUE | FALSE。

        //設置參數
        $uid            =(int)5030;
        $school_code    =trim("gcp");
        $book_sid       =trim("mbg5030201509052234325173");
        $borrow_book    =$omobile_borrow->borrow_book($uid,$school_code,$book_sid);

    //新增並登記書籍
    //說明: 此 METHODS 將建立書籍資訊，並直接對書籍進行閱讀登記，結果將回傳TRUE | FALSE。

        //設置參數
        $uid            =(int)5030;
        $school_code    =trim("gcp");
        $book_code      =trim("9789862115503");
        $book_name      =addslashes(strip_tags(trim("自建書籍")));
        $book_author    =addslashes(strip_tags(trim("自建作者")));
        $book_publisher =addslashes(strip_tags(trim("自建出版社")));
        $add_borrow_book=$omobile_borrow->add_borrow_book($uid,$school_code,$book_code,$book_name,$book_author,$book_publisher);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_user_rec_group_detail_info()<br/><br/>
                                    用途: 取得班級人員推薦本數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">班級人員的推薦本數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $arry_class_code  班級代號
    $start_time       起始時間
    $end_time         結束時間

  實際用法:

    //設置參數
    $arry_class_code =array("gcp_2014_2_3_4","gcp_2014_2_3_5");
    $start_time      =trim("2015-03-01 00:00:00");
    $end_time        =trim("2015-03-10 00:00:00");

    //呼叫函式
    print_r get_class_user_rec_group_detail_info($arry_class_code,$start_time,$end_time);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_user_read_group_detail_info()<br/><br/>
                                    用途: 取得班級人員閱讀本數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">班級人員的閱讀本數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $arry_class_code  班級代號
    $start_time       起始時間
    $end_time         結束時間

  實際用法:

    //設置參數
    $arry_class_code =array("gcp_2014_2_3_4","gcp_2014_2_3_5");
    $start_time      =trim("2015-03-01 00:00:00");
    $end_time        =trim("2015-03-10 00:00:00");

    //呼叫函式
    print_r get_class_user_read_group_detail_info($arry_class_code,$start_time,$end_time);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_user_read_frequency_detail_info()<br/><br/>
                                    用途: 取得班級人員閱讀次數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">班級人員的閱讀次數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $arry_class_code  班級代號
    $start_time       起始時間
    $end_time         結束時間

  實際用法:

    //設置參數
    $arry_class_code =array("gcp_2014_2_3_4","gcp_2014_2_3_5");
    $start_time      =trim("2015-03-01 00:00:00");
    $end_time        =trim("2015-03-10 00:00:00");

    //呼叫函式
    print_r get_class_user_read_frequency_detail_info($arry_class_code,$start_time,$end_time);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: object school_usage_info()<br/><br/>
                                    用途: 取得學校使用率<br/><br/>
                                    說明: 本物件函式將會回傳指定<br/>
                                          <span style="position:relative;left:40px;">時間內的學校使用率，型態</span><br/>
                                         <span style="position:relative;left:40px;">因應各 METHODS 不同</span><br/>
                                         <span style="position:relative;left:40px;">而有所調整。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $school_code    學校代號
    $time           時間條件

  實際用法:

    //設置參數
    $school_code=trim("gcp");
    $time       =trim("2014-11");

    //實體化物件
    $oschool_usage_info=new school_usage_info();

    //取得班級使用借閱的名單
    //說明: 此 METHODS 將會回傳一維陣列。
    $arry_class_code_info=$oschool_usage_info->get_class_code_borrow_info($school_code,$time);
    print_r($arry_class_code_info);

    //取得班級使用借閱的總數
    //說明: 此 vars 將會回傳 INT
    $class_code_borrow_cno=$oschool_usage_info->get_class_code_borrow_cno;
    echo $class_code_borrow_cno;
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_code_read_group_info()<br/><br/>
                                    用途: 取得班級平均閱讀本數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">的班級平均閱讀本數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $class_code   班級代號
    $arry_time    時間條件

  實際用法:

    //設置參數
    $class_code=trim("gcp_2014_1_3_5");
    $arry_time =array("2014-08","2014-09");

    //呼叫函式
    print_r get_class_code_read_group_info($class_code,$arry_time);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_code_rec_group_info()<br/><br/>
                                    用途: 取得班級平均推薦本數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">的班級平均推薦本數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $class_code   班級代號
    $arry_time    時間條件

  實際用法:

    //設置參數
    $class_code=trim("gcp_2014_1_3_5");
    $arry_time =array("2014-08","2014-09");

    //呼叫函式
    print_r get_class_code_rec_group_info($class_code,$arry_time);
</pre>
                                </td>
                            </tr>

                            <tr>
                                <td width="375px" align="left" bgcolor="#fff" style="color:#000;" height="50px">
                                    函式: get_class_code_comment_frequency_info()<br/><br/>
                                    用途: 取得班級平均指導次數<br/><br/>
                                    說明: 本函式將會回傳指定時間內<br/>
                                          <span style="position:relative;left:40px;">的班級平均指導次數，型態為 <br> ARRAY。</span>
                                </td>
                                <td width="" align="left" bgcolor="#fff" style="color:#000;" height="50px">
<pre>
  所需參數:

    $class_code   班級代號
    $arry_time    時間條件

  實際用法:

    //設置參數
    $class_code=trim("gcp_2014_1_3_5");
    $arry_time =array("2014-08","2014-09");

    //呼叫函式
    print_r get_class_code_comment_frequency_info($class_code,$arry_time);
</pre>
                                </td>
                            </tr>

                        </table>
                    </body>
                    </Html>
                ';

            //回傳
            return $description;
        }
?>