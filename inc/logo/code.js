//-------------------------------------------------------
//函式: logo()
//用途: 首頁logo
//日期: 2013年8月12日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function logo(rd,page,arg){
    //---------------------------------------------------
    //函式: logo()
    //用途: 首頁logo
    //---------------------------------------------------
    //rd    層級
    //page  首頁名稱,預設 index.php
    //arg   參數物件
    //---------------------------------------------------

        //參數檢驗
        var rd  =rd || 0;
        var page=page || 'index.php';
        var arg =arg || {};

        //層級處理
        var _rd="";
        for(var i=0;i<rd;i++){
            _rd="../"+_rd;
        }

        //網址處理
        var url ="";
        var _arg=[];
        for(var key in arg){
            var val=arg[key];
            _arg.push(key+"="+encodeURI(val));
        }
        if(_arg.length!==0){
            _arg=_arg.join("&");
            url =_rd+page+"?"+_arg;
        }else{
            url =_rd+page;
        }

        //設定
        var o_logo=document.getElementById("logo");

        if(o_logo){
            o_logo.onmouseover=function(){
                this.style.cursor="pointer";
            }
            o_logo.onmouseout=function(){
                this.style.cursor="";
            }
            o_logo.onclick=function(){
                self.location.href=url;
            }
        }
    }