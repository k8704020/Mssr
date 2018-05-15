<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無標題文件</title>
</head>
<div class="mssr_forum_create_group_box" >
	<form action="mssr_forum_create_group_A.php" method="post">
		
		<h3 id="mssr_forum_create_group_box_name">輸入討論區名稱與介紹</h3>
    	<p id="mssr_forum_create_group_box_title">討論區名稱：<br><input  id="haha" type="text"  name="mssr_forum_create_group_title" size="50" maxlength="40" /></p>
        <p id="">討論區介紹：</p>
        <textarea id="mssr_forum_create_group_box_content" name="mssr_forum_create_group_content" cols="40" rows="12"></textarea>
        
        <textarea name="user_id"  style="display:none" cols="3" rows="8"><?php echo $_GET["user_id"]?></textarea>
       
        <input id="mssr_forum_create_group_box_submit" type="submit" value="送出"/>
   	</form>
</div>
<body>
</body>
</html>