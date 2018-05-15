<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
<title>無標題文件</title>
</head>
  
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/mssr_forum.css" rel="stylesheet">
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  
<body>

<!--========================navbar=====================================-->
<div class="navbar-top">
      <nav class="navbar navbar-default navbar" role="navigation">
        <div class="navbar-header">
           <a class="navbar-brand" href="#"></a>
        </div>
        
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li>
              <a href="mssr_forum_people_shelf.php"><em class="glyphicon glyphicon-book"></em>書櫃</a>
            </li>
            <li>
              <a href="mssr_forum_people_myreply.php"><em class="glyphicon glyphicon-comment"></em>討論</a>
            </li>
            <li>
              <a href="mssr_forum_people_group.php"><em class="glyphicon glyphicon-star"></em>聊書小組</a>
            </li>
            <li>
              <a href="mssr_forum_people_friend.php"><em class="glyphicon glyphicon-user"></em>朋友</a>
            </li>
            
            
          </ul>
          
          <ul class="nav navbar-nav navbar-right">
            <form class="navbar-form navbar-left" >
            <div class="form-group">
              <input type="text" class="input-medium search-query">
				<select name="search_type" size="1">
				
				  <option>人</option>
				  <option>書</option>
				  <option>群</option>
				</select>
			<button type="submit" class="btn btn-default btn-xs">搜尋</button>
            </div>
			
          </form>
          </ul>
        </div>        
      </nav>
   
  </div>


<!--========================root=====================================-->
  <div class="root" >
  
      <ul class="breadcrumb">
    目前位置：
		  <li>
            <a href="#">首頁</a> <span class="divider">/</span>
          </li>
          <li>
            <a href="#">XXX書籍</a> <span class="divider">/</span>
          </li>
          <li class="active">
           看過這本書的人也看過
          </li>
        </ul>
  </div>

<!--========================group header=====================================-->
  <div class="group_header">
        <div class="group_image" >
          <img  src="image/book.jpg" class="img-thumbnail" width="100" height="100">
        </div>
      
        <div class="group_info1">
          
            <p>
              書籍名稱<P>
              作者：<P>
			  出版社：<P><P><P>
          <a href="#" class="btn" type="button">追蹤書籍</a>
          
           
    
        </div>

        <div class="group_info2">
         
            <p>
              書籍討論資訊：<P>
              幾篇發文<br>
              幾篇回復<br>
              幾位看過這本書<br>
			  幾位好友看過這本書<br>
          
        </div>
    
 </div>
 

<!--========================tab_bar=====================================-->     
    <div class="tab_bar">
		
		  <div class="tabbable" id="tabs-215204">
			<ul class="nav nav-tabs">
			  <li>
				<a href="mssr_forum_book_discussion.php">討論</a>
			  </li>
			  <li class="active">
				<a href="mssr_forum_book_shelf.php" >看過這本書的人也看過</a>
			  </li>
			  <li>
				<a href="mssr_forum_book_member.php">誰也看過這本書</a>
			  </li>
			   <li>
				<a href="mssr_forum_book_group.php">哪些小組討論這本書</a>
			  </li>
			  </ul>
		  </div>
		
	</div>
 
 
 
<!--========================content=====================================--> 
<div class="content">

    <div class="left_content">
 
    
     
	  
      
    
	<p>
      <table class="table table-hover table-striped">
        <thead>
                    <tr>
                        <th style="width:15%"></th>
                        <th style="width:40%">書籍名稱</th>
                        <th style="width:15%">書籍分類</th>
                        <th style="width:15%">閱讀日期</th>
                        <th style="width:15%">追蹤狀態</th>
                       
                    </tr>
                </thead>
                <tbody>
                     <tr class="even">
                        <td align="center"><img src="image/book.jpg" alt=""  width="50" height="50"/></td>
                        <td><a href="#">XXX</a></td>
                        <td>分類</td>
                        <td>2014-10-02 12:32:52</td>
                        <td>未追蹤</td>
                    
                    </tr>
          </tbody>
                     <tbody>
                     <tr class="even2">
                         <td align="center"><img src="image/book.jpg" alt=""  width="50" height="50"/></td>
                        <td><a href="#">XXX</a></td>
                        <td>分類</td>
                        <td>2014-10-02 12:32:52</td>
                        <td>未追蹤</td>
                    </tr>
                 </tbody>
                  <tbody>
                      <tr class="even">
                        <td align="center"><img src="image/book.jpg" alt=""  width="50" height="50"/></td>
                        <td><a href="#">XXX</a></td>
                        <td>分類</td>
                        <td>2014-10-02 12:32:52</td>
                        <td>未追蹤</td>
                    
                    </tr>
          </tbody>
      </table>
    </div>
	
	

	
    <div class="aside">
    
      <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>看過這本書的人也看過</TD>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
             <img src="image/boy.jpg" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr class="active">
            <td>
              <img src="image/boy.jpg" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
          </tr>
          <tr>
            <td>
              <img src="image/boy.jpg" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr class="active">
            <td>
              <img src="image/boy.jpg" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr>
            <td>
              <img src="image/boy.jpg" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
          </tr>
          
        </tbody>
      </table>
          
      <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>哪些小組討論這本書</TD>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <img src="image/group.png" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr class="active">
            <td>
             <img src="image/group.png" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
          </tr>
          <tr>
            <td>
              <img src="image/group.png" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr class="active">
            <td>
              <img src="image/group.png" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
            
          </tr>
          <tr>
            <td>
              <img src="image/group.png" alt=""  width="40" height="40"/>
            </td>
            <td>
              XXX
            </td>
          </tr>
          
        </tbody>
      </table>
    </div>
  </div>



</body>

</html>