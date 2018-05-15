<!DOCTYPE HTML>
<Html>
<Head>
    <Title></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="Content-Language" content="UTF-8">

    <script type="text/javascript" src=""></script>
    <link rel="stylesheet" href=""/>
</head>
<body>

    <p>计数: <output id="result"></output></p>
    <button onclick="startWorker()">开始 Worker</button>
    <button onclick="stopWorker()">停止 Worker</button>
    <br /><br />

<script>
    var w;

    function startWorker(){
        if(typeof(Worker)!=="undefined"){
            if(typeof(w)=="undefined"){
                w=new Worker("demo_workers.js");
            }
            w.onmessage=function(e){
                document.getElementById("result").innerHTML=e.data;
            };
        }else{
            document.getElementById("result").innerHTML="Sorry, your browser does not support Web Workers...";
        }
    }

    function stopWorker(){
        w.terminate();
    }
</script>

</body>
</Html>
