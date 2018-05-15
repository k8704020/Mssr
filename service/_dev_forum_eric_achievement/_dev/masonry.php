<!DOCTYPE HTML>
<Html lang="zh_TW">
<Head>
    <Title></Title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

    <!-- 通用 -->
    <link href="../../../lib/framework/bootstrap/css/code.css" rel="stylesheet" type="text/css">

    <!-- 通用 -->
    <script type="text/javascript" src="../../../lib/jquery/basic/code.js"></script>
    <script type="text/javascript" src="../../../lib/framework/bootstrap/js/code.js"></script>
    <script src="http://photo.minwt.com/file/sampleView/jQuery/jquery-masonry/jquery.masonry.min.js"></script>
    <style>
        @media screen and (min-width: 992px) and (max-width: 1200px){
            .col{
                display: inline-block;
                width: 345px;
                margin: 5px 5px 5px 0;
                background-color: #ffffcc;
                overflow: hidden;
            }
        }
        @media screen and (min-width: 1200px){
            .col{
                display: inline-block;
                width: 445px;
                margin: 5px 5px 5px 0;
                background-color: #ffffcc;
                overflow: hidden;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">

            <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <?php for($i=0;$i<15;$i++):?>
                            <div class="media col">
                                <a class="pull-left" href="#">
                                    <img class="media-object" src="http://www.runoob.com/wp-content/uploads/2014/06/64.jpg"
                                    alt="媒体对象">
                                </a>
                                <div class="media-body">
                                    <h4 class="media-heading">媒体标题</h4>
                                    这是一些示例文本。这是一些示例文本。
                                    这是一些示例文本。这是一些示例文本。
                                    这是一些示例文本。这是一些示例文本。
                                    这是一些示例文本。这是一些示例文本。
                                    这是一些示例文本。这是一些示例文本。
                                </div>
                            </div>

                            <div class='media col' style='margin:10px 0;'>
                                <a class='pull-left' href='user.php?user_id=5031&tab=1'>
                                    <img class='media-object' src='../img/default/user_girl.png' width='64' height='64' alt='Media'>
                                </a>
                                <div class='media-body'>
                                    <h5 class='media-heading' style='position:relative;left:0px'>
                                        你的朋友『<a href='user.php?user_id=5031&tab=1'>錢明日</a>』
                                        <br>在 【<a href='article.php?get_from=1&book_sid=mbl1201310091649438948578'>哈利波特4死神的聖物上</a>】 說
                                    </h5>
                                    <p style='position:relative;top:5px;'>
                                        <a target='_blank' href='reply.php?get_from=1&article_id=3'>
                                            哈
                                        </a>
                                    </p>
                                        <div class='submedia'>
                                            <a class='pull-left' href='article.php?get_from=1&book_sid=mbl1201310091649438948578'>
                                                <img class='media-object' src='../img/default/book.png' width='64' height='64' alt='Media'>
                                            </a>
                                            <div class='media-body'>
                                                <h5 class='media-heading submedia-heading'>哈利波特4死神的聖物上</h5>
                                                <p class='submedia-heading'>作者：</br/>出版社：皇冠</p>
                                            </div>
                                        </div>
                                </div>
                                <p style='position:relative;top:5px;' class='pull-right'>2015-07-29 16:06:35</p>
                            </div>
                        <?php endfor;?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                右側內容
            </div>

        </div>
    </div>
</body>
<script type="text/javascript">
//-------------------------------------------------------
//範例
//-------------------------------------------------------

    var browserwidth=parseInt(getbrowserwidth());
    var columnwidth =355;

    //解析度 md
    if(browserwidth>=992 && browserwidth<1200){
        columnwidth =355;
    }
    //解析度 lg
    if(browserwidth>=1200){
        columnwidth =455;
    }

    function getbrowserwidth(){
        if ($.browser.msie){
            return document.compatMode == "CSS1Compat" ? document.documentElement.clientWidth :
            document.body.clientWidth;
        }else{
            return self.innerWidth;
        }
    }

    $(function(){
        $('.row').imagesLoaded(function(){
            $('.row').masonry({
                itemSelector: '.col',
                columnWidth: columnwidth
            });
        });
    });

</script>
</Html>