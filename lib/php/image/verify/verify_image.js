function verify_image(lv_no,img_id,img_w,img_h){
//-------------------------------------------------------
//載入驗證圖片
//-------------------------------------------------------

    //參數檢驗
    if((lv_no==undefined)||(isNaN(lv_no))){
        return false;
    }
    if((img_id==undefined)||(img_id=='')){
        return false;
    }
    if((img_w==undefined)||(isNaN(img_w))){
        img_w=150;
    }
    if((img_h==undefined)||(isNaN(img_h))){
        img_h=60;
    }

    //路徑
    var url="";
    var page="lib/php/image/verify/verify_image.php";
    var rd=[];
    for(var i=1;i<=lv_no;i++){
        rd.push('../');
    }
    rd=rd.join('');
    url=rd+page;

    //設定
    var oimg=document.getElementById(img_id);
    oimg.style.width =img_w;
    oimg.style.height=img_h;
    oimg.src=url+'?seed='+Math.random();
}