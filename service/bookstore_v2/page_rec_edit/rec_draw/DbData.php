<?php
require_once("DbConnection.php");

class DbData {
    
    /*function GetAllStory() {
        $sql = "SELECT * FROM `draw_story` WHERE `enable` = 1";
        $data = array();

        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->story_id = $dataRow['story_id'];
            $rawData->unique_id = $dataRow['unique_id'];
            $rawData->story_name = $dataRow['story_name'];
            $rawData->section1 = $dataRow['section1'];
            $rawData->section2 = $dataRow['section2'];
            $rawData->section3 = $dataRow['section3'];
            $rawData->section4 = $dataRow['section4'];
            $rawData->word_count = $dataRow['word_count'];
            $rawData->record_time = $dataRow['record_time'];
            $data[] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }*/
    
    function GetClassStudents($school,$grade,$classroom){
        $sql = "SELECT * FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `classroom` = '%s' AND `member_IDENTITY` = 'S'";
        
        $sql = sprintf($sql,$school,$grade,$classroom);
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->nickname = $dataRow['nickname'];
            $rawData->member_ID = $dataRow['member_ID'];
            $rawData->unique_id = $dataRow['unique_id'];
            
            $data[$rawData->unique_id] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
	function GetClassStudentsT($school,$grade,$classroom){
        $sql = "SELECT * FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `classroom` = '%s' AND `member_IDENTITY` NOT LIKE 'F' AND `member_IDENTITY` NOT LIKE 'X'";
        
        $sql = sprintf($sql,$school,$grade,$classroom);
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->nickname = $dataRow['nickname'];
            $rawData->member_ID = $dataRow['member_ID'];
            $rawData->unique_id = $dataRow['unique_id'];
            
            $data[$rawData->unique_id] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
	    function GetClassParents($school,$grade,$classroom){
        $sql = "SELECT * FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `classroom` = '%s' AND `member_IDENTITY` = 'F'";
        
        $sql = sprintf($sql,$school,$grade,$classroom);
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->nickname = $dataRow['nickname'];
            $rawData->member_ID = $dataRow['member_ID'];
            $rawData->unique_id = $dataRow['unique_id'];
            
            $data[$rawData->unique_id] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
	 function GetClassA($school,$grade,$classroom){
        $sql = "SELECT * FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `classroom` = '%s' AND `member_IDENTITY` = 'A'";
        
        $sql = sprintf($sql,$school,$grade,$classroom);
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->nickname = $dataRow['nickname'];
            $rawData->member_ID = $dataRow['member_ID'];
            $rawData->unique_id = $dataRow['unique_id'];
            
            $data[$rawData->unique_id] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
    
    function GetSummaryData($school,$grade){
        $sql = "SELECT count(distinct a.`story_id`) AS stories,c.*,(COUNT(distinct a.story_id)/c.students)AS average ,(SUM(d.totalWord)/COUNT(distinct a.story_id)) AS avg_word_count,b.`classroom` FROM `draw_story` AS a LEFT JOIN `login_info` AS b on a.`unique_id` = b.`unique_id` LEFT JOIN( SELECT COUNT(`id`) AS students,classroom FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `member_IDENTITY` = 'S' GROUP by classroom )AS c ON b.`classroom`=c.`classroom` LEFT JOIN (SELECT SUM(`Wordcount`) AS totalWord,story_id FROM `draw_story_section` WHERE `enable`=1 group by `story_id`) AS d ON a.`story_id`= d.`story_id` WHERE b.`school`= '%s' AND b.`member_IDENTITY`= 'S'  AND b.`grade`= %s AND a.`academic` = %s AND a.`enable` = 1 AND a.`published` = 1 group by b.`classroom`";
        
        $sql = sprintf($sql,$school,$grade,$school,$grade,getAcademic());
             
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->stories = $dataRow['stories'];
            $rawData->classroom = $dataRow['classroom'];
            $rawData->average = $dataRow['average'];
			$rawData->avg_word_count= $dataRow['avg_word_count'];
            $rawData->students = $dataRow['students'];
            
            $data[$dataRow['classroom']] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
   function GetSummaryDataByGrade($school,$grade){
        $sql = "SELECT count(distinct a.`story_id`) AS stories,c.*,(COUNT(distinct a.story_id)/c.students)AS average FROM `draw_story` AS a LEFT JOIN `login_info` AS b on a.`unique_id` = b.`unique_id` LEFT JOIN( SELECT COUNT(`id`) AS students,grade FROM `login_info` WHERE `school` = '%s' AND `grade` = %s AND `member_IDENTITY` = 'S')AS c ON b.`grade`=c.`grade` WHERE b.`school`= '%s' AND b.`member_IDENTITY`= 'S'  AND b.`grade`= %s AND a.`academic` = %s AND a.`enable` = 1 AND a.`published` = 1 group by b.`grade";
        
        $sql = sprintf($sql,$school,$grade,$school,$grade,getAcademic());
             
        $data = array();
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
            $rawData->stories = $dataRow['stories'];
            $rawData->students = $dataRow['students'];
			$rawData->grade = $dataRow['grade'];
            $rawData->average = $dataRow['average'];
            
            $data[$dataRow['grade']] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }

function GetSummaryDataByUid($uid){
        $sql = "SELECT *, sum(`enable`) AS sumen ,sum(`published`) AS sumpub,sum(`shared`) AS sumshared FROM `draw_story` WHERE `unique_id` = %d AND `academic` = %s AND `enable` = 1";
         
		  $sql = sprintf($sql,$uid,getAcademic());
		  
		  $data = array();
     
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rawData = new stdClass();
			$rawData->sumen = $dataRow['sumen'];
			$rawData->sumpub = $dataRow['sumpub'];
			$rawData->sumshared = $dataRow['sumshared'];
			$data[$dataRow['unique_id']] = $rawData;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
	
	
    function GetStoryByUniqueId($uid) {
        $sql = "SELECT a.*,b.* FROM `draw_story` AS a LEFT JOIN `draw_story_section` AS b ON a.story_id = b.story_id WHERE a.`unique_id` = %d AND a.`academic` = %s AND a.`enable` = 1 AND b.`enable` = 1 ORDER BY a.`story_id` DESC";
        $data = array();
        
        $sql = sprintf($sql,$uid,getAcademic());
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            if(!isset($data[$dataRow['story_id']])){
                $rawData = new stdClass();
                $rawData->story_id = $dataRow['story_id'];
                $rawData->unique_id = $dataRow['unique_id'];
                $rawData->academic = $dataRow['academic'];
                $rawData->story_name = $dataRow['story_name'];
                $rawData->maxPage = $dataRow['maxPage'];
                $rawData->record_time = $dataRow['record_time'];
                $rawData->pub_time = $dataRow['pub_time'];
                $rawData->show_time = $dataRow['show_time'];
                $rawData->published = $dataRow['published'];
                $rawData->shared = $dataRow['shared'];
                $rawData->enable = $dataRow['enable'];
                $rawData->published = $dataRow['published'];
				$rawData->source = $dataRow['source'];
				$rawData->source_name = $dataRow['source_name'];
                $rawData->sections = array();
                $data[$dataRow['story_id']] = $rawData;
            }
            $sectionRow = new stdClass();
            $sectionRow->section_id = $dataRow['section_id'];
            $sectionRow->content = $dataRow['content'];  
            $sectionRow->isDraw = $dataRow['isDraw'];
            $sectionRow->wordcount = $dataRow['wordcount'];
            $sectionRow->modify_time = $dataRow['modify_time'];
            $sectionRow->modify_user = $dataRow['modify_user'];
            $data[$dataRow['story_id']]->sections[] = $sectionRow;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
    
    function GetStoryByStoryId($sid) {
        $sql = "SELECT a.*,b.*,c.nickname FROM `draw_story` AS a LEFT JOIN `draw_story_section` as b ON a.story_id = b.story_id LEFT JOIN `login_info` as c ON a.unique_id = c.unique_id WHERE a.`story_id` = %d AND b.enable = 1 ORDER BY b.`page_num` ASC";
        $rawData = new stdClass();
        
        $sql = sprintf($sql,$sid);
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            if(!isset($rawData->story_id)){
                $rawData->story_id = $dataRow['story_id'];
                $rawData->unique_id = $dataRow['unique_id'];
				$rawData->nickname = $dataRow['nickname'];
                $rawData->story_name = $dataRow['story_name'];
                $rawData->maxPage = $dataRow['maxPage'];
                $rawData->academic = $dataRow['academic'];
                $rawData->record_time = $dataRow['record_time'];
                $rawData->pub_time = $dataRow['pub_time'];
                $rawData->show_time = $dataRow['show_time'];
                $rawData->published = $dataRow['published'];
                $rawData->shared = $dataRow['shared'];
                $rawData->enable = $dataRow['enable'];
				$rawData->source = $dataRow['source'];
				$rawData->source_name = $dataRow['source_name'];
                $rawData->sections = array();
            }
            $sectionRow = new stdClass();
            $sectionRow->section_id = $dataRow['section_id'];
            $sectionRow->page_num = $dataRow['page_num'];
            $sectionRow->content = $dataRow['content'];
            $sectionRow->isDraw = $dataRow['isDraw'];
            $sectionRow->wordcount = $dataRow['wordcount'];
            $sectionRow->modify_time = $dataRow['modify_time'];
            $sectionRow->modify_user = $dataRow['modify_user'];
            $rawData->sections[$sectionRow->section_id] = $sectionRow;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $rawData;
    }
    
    function CreateStory($sname,$uid){
        $sql = "INSERT INTO `coach`.`draw_story` (`story_id`, `academic`, `unique_id`, `story_name`, `maxPage`, `record_time`, `pub_time`, `show_time`, `published`, `shared`, `enable`) VALUES (NULL, '%s', '%s', '%s', '4', CURRENT_TIMESTAMP, NULL, NULL, '0', '0', '1');";
        $sql = sprintf($sql,
                GetAcademic(),
                $uid,
                addslashes(htmlentities($sname, ENT_COMPAT | ENT_IGNORE, "UTF-8")));
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $sid = mysql_insert_id();
        
        //create section and pages
        for($i = 0; $i < 4; $i++){
            $sql = "INSERT INTO `coach`.`draw_story_section` (`section_id`, `story_id`, `page_num`, `content`, `wordcount`, `isDraw`, `lock`, `modify_time`, `modify_user`, `enable`) VALUES (NULL, '%d', '%d', NULL, NULL, '0', '0', CURRENT_TIMESTAMP, NULL, '1');";

            $result = mysql_query(sprintf($sql,$sid,$i+1));
        }
        //$data = $result;
        //mysql_free_result($result);
        $DbConnection->Close();
        return $sid;
    }
    
    function SaveStory($section,$content){
        $sql = "UPDATE `coach`.`draw_story_section` SET `content` = '%s', `modify_user` = '%s', `modify_time` = CURRENT_TIMESTAMP, `wordcount` = %d WHERE `draw_story_section`.`section_id` = %s;";
        $sql = sprintf($sql,        
                addslashes(htmlentities($content, ENT_COMPAT | ENT_IGNORE, "UTF-8")),
                $_COOKIE['nickname'],
                mb_strlen($content,"utf-8"),
                $section);
        $sql2 = "INSERT INTO `coach`.`draw_story_section_draft` SET `section_id` = %s,`content` = '%s', `modify_user` = '%s', `modify_time` = CURRENT_TIMESTAMP, `wordcount` = %d";
        $sql2 = sprintf($sql2,$section,        
                addslashes(htmlentities($content, ENT_COMPAT | ENT_IGNORE, "UTF-8")),
                $_COOKIE['nickname'],
                mb_strlen($content,"utf-8"),$section);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
		$result2 = mysql_query($sql2);
        //$data = $result;
        //mysql_free_result($result);
        $DbConnection->Close();
        return $result;
    }
    
    function AddNewSection($story,$section){
        $sql = "INSERT INTO `coach`.`draw_story_section` (`section_id`, `story_id`, `page_num`, `content`, `wordcount`, `isDraw`, `lock`, `modify_time`, `modify_user`, `enable`) VALUES (NULL, '%s', '%s', NULL, NULL, '0', '0', CURRENT_TIMESTAMP, NULL, '1');";
        $sql = sprintf($sql,$story,$section);
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $section_id = mysql_insert_id();
        
        $sql = sprintf("UPDATE `coach`.`draw_story` SET `maxPage` = `maxPage` + 1 WHERE `draw_story`.`story_id` = %s;",$story);
        $result = mysql_query($sql);
        //$data = $result;
        //mysql_free_result($result);
        $DbConnection->Close();
        return $section_id;
    }
    
    function DeleteStory($sid){
        $sql = "UPDATE `coach`.`draw_story` SET `enable` = '0' WHERE `draw_story`.`story_id` = %s AND `published` = 0;";
        $sql = sprintf($sql,$sid);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        
        $sql = "UPDATE `coach`.`draw_story_section` SET `enable` = '0' WHERE `draw_story_section`.`story_id` = %s;";
        $sql = sprintf($sql,$sid);
        //echo $sql;
        $result = mysql_query($sql);
        $affect += mysql_affected_rows();

        $DbConnection->Close();
        return $affect;
    }
    function UpdateDrawMark($section){        
        $sql = "UPDATE `coach`.`draw_story_section` SET `isDraw` = '1', `modify_user` = '%s', `modify_time` = CURRENT_TIMESTAMP WHERE `draw_story_section`.`section_id` = %s;";
        $sql = sprintf($sql,$_COOKIE['nickname'],$section);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        //$data = $result;
        //mysql_free_result($result);
        $DbConnection->Close();
        return $result; 
    }
    function DeleteSection($section,$story){
        $sql = "UPDATE `coach`.`draw_story_section` SET `enable` = '0' WHERE `draw_story_section`.`section_id` = %s;";
        $sql = sprintf($sql,$section);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        
        $sql = sprintf("UPDATE `coach`.`draw_story` SET `maxPage` = `maxPage` - 1 WHERE `draw_story`.`story_id` = %s;",$story);
        $result = mysql_query($sql);
        
        $DbConnection->Close();
        return $affect;
    }
    
    function PublishStory($sid){
        $sql = "UPDATE `coach`.`draw_story` SET `published` = '1',`pub_time` = CURRENT_TIMESTAMP WHERE `draw_story`.`story_id` = %s AND `enable` = '1';";
        $sql = sprintf($sql,$sid);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        $DbConnection->Close();
        return $affect;
    }
	
	    function SharedStory($sid){
        $sql = "UPDATE `coach`.`draw_story` SET `shared` = '1',`show_time` = CURRENT_TIMESTAMP WHERE `draw_story`.`story_id` = %s AND `enable` = '1' AND `published` = '1';";
        $sql = sprintf($sql,$sid);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        $DbConnection->Close();
        return $affect;
    }
		function SentScore($sid,$score){
        $sql = "UPDATE `coach`.`draw_story` SET `oralscore` = '{$score}' WHERE `draw_story`.`story_id` = '{$sid}' AND `enable` = '1' AND `published` = '1' ;";
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        $DbConnection->Close();
        return $affect;
    }
    
    function CancelPublish($sid){
        $sql = "UPDATE `coach`.`draw_story` SET `published` = '0',`pub_time` = NULL WHERE `draw_story`.`story_id` = %s AND `enable` = '1';";
        $sql = sprintf($sql,$sid);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        $DbConnection->Close();
        return $affect;
    }
    function ChangeStoryName($story,$name){
        $sql = "UPDATE  `coach`.`draw_story` SET  `story_name` =  '%s' WHERE  `draw_story`.`story_id` = %s;";
        $sql = sprintf($sql,
            addslashes(htmlentities($name, ENT_COMPAT | ENT_IGNORE, "UTF-8")),
            $story);
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        $affect = mysql_affected_rows();
        $DbConnection->Close();
        return $affect;        
    }
    function SaveOrder($obj){
        $sql = "UPDATE  `coach`.`draw_story_section` SET `page_num` = '%s' WHERE `draw_story_section`.`section_id` = %s;";                
        $DbConnection = new DbConnection();
        $DbConnection->Open();        
        foreach($obj as $k=>$v){
            $sqlx = sprintf($sql,$v,$k);
            mysql_query($sqlx);
        }        
        $DbConnection->Close();
    }
    function GetSectionCount($story){
        $sql = "SELECT COUNT(*) AS section_count FROM `draw_story_section` WHERE `story_id` = %s AND `enable` = 1;";
        $sql = sprintf($sql,$story);
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        $counts = 0;
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $counts = $dataRow['section_count'];
        }        
        $DbConnection->Close();
        return $counts; 
    }
    function GetPublishedStoryByUniqueId($uid){
        $sql = "SELECT a.*,b.* FROM `draw_story` AS a LEFT JOIN `draw_story_section` AS b ON a.story_id = b.story_id WHERE a.`unique_id` = %d AND a.`academic` = %s AND a.`enable` = 1 AND b.`enable` = 1  AND a.`published` = 1 ORDER BY a.`story_id` DESC";
        $data = array();
        
        $sql = sprintf($sql,$uid,getAcademic());
        
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            if(!isset($data[$dataRow['story_id']])){
                $rawData = new stdClass();
                $rawData->story_id = $dataRow['story_id'];
                $rawData->unique_id = $dataRow['unique_id'];
                $rawData->academic = $dataRow['academic'];
                $rawData->story_name = $dataRow['story_name'];
                $rawData->maxPage = $dataRow['maxPage'];
                $rawData->record_time = $dataRow['record_time'];
                $rawData->pub_time = $dataRow['pub_time'];
                $rawData->show_time = $dataRow['show_time'];
                $rawData->published = $dataRow['published'];
                $rawData->shared = $dataRow['shared'];
                $rawData->enable = $dataRow['enable'];
                $rawData->published = $dataRow['published'];
                $rawData->sections = array();
                $data[$dataRow['story_id']] = $rawData;
            }
            $sectionRow = new stdClass();
            $sectionRow->section_id = $dataRow['section_id'];
            $sectionRow->content = $dataRow['content'];  
            $sectionRow->isDraw = $dataRow['isDraw'];
            $sectionRow->wordcount = $dataRow['wordcount'];
            $sectionRow->modify_time = $dataRow['modify_time'];
            $sectionRow->modify_user = $dataRow['modify_user'];
            $data[$dataRow['story_id']]->sections[] = $sectionRow;
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $data;
    }
    function GetStoryByClass($school,$grade,$classroom){
        $sql = "SELECT * FROM `draw_story` AS a LEFT JOIN `login_info` AS b ON a.unique_id = b.unique_id LEFT JOIN `draw_story_section` AS c ON a.story_id = c.story_id WHERE b.school = '%s' AND b.grade = %s AND b.classroom = '%s' AND a.enable = 1 AND c.enable = 1 AND a.academic = '%s' AND b.member_IDENTITY = 'S' ORDER BY b.member_ID,record_time DESC";
        
        $sql = sprintf($sql,$school,$grade,$classroom,getAcademic());
        
        $data = array();
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            
            if(!isset($data[$dataRow['story_id']])){
                $rawData = new stdClass();
                $rawData->story_id = $dataRow['story_id'];
                $rawData->unique_id = $dataRow['unique_id'];
                $rawData->story_name = $dataRow['story_name'];
                $rawData->maxPage = $dataRow['maxPage'];
                $rawData->academic = $dataRow['academic'];
                $rawData->record_time = $dataRow['record_time'];
                $rawData->pub_time = $dataRow['pub_time'];
                $rawData->show_time = $dataRow['show_time'];
                $rawData->published = $dataRow['published'];
                $rawData->shared = $dataRow['shared'];
                $rawData->enable = $dataRow['enable'];
				$rawData->source = $dataRow['source'];
                $rawData->sections = array();
                $data[$dataRow['story_id']] = $rawData;
            }
            $sectionRow = new stdClass();
            $sectionRow->section_id = $dataRow['section_id'];
            $sectionRow->content = $dataRow['content'];
            $sectionRow->isDraw = $dataRow['isDraw'];
            $sectionRow->wordcount = $dataRow['wordcount'];
            $sectionRow->modify_time = $dataRow['modify_time'];
            $sectionRow->modify_user = $dataRow['modify_user'];
            $data[$dataRow['story_id']]->sections[$dataRow['section_id']] = $sectionRow;
        }
        
        $DbConnection->Close();
        return $data; 
    }
    function GetStudentByUniqueId($uid){
        $sql = "SELECT `nickname`,`grade`,`classroom`,`school` FROM `login_info` WHERE `unique_id` = %s";
        $student = new stdClass();
        
        $sql = sprintf($sql,$uid);
        //echo $sql;
        $DbConnection = new DbConnection();
        $DbConnection->Open();
        $result = mysql_query($sql);
        while ($dataRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $student->nickname = $dataRow['nickname'];
            $student->grade = $dataRow['grade'];
            $student->classroom = $dataRow['classroom'];
            $student->school = $dataRow['school'];
        }
        mysql_free_result($result);
        $DbConnection->Close();
        return $student;
    }
}
?>