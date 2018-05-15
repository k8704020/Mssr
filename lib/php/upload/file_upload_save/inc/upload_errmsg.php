<?php
function upload_errmsg($_FILE){
//-------------------------------------------------------
//上傳狀態代碼
//-------------------------------------------------------
//$_FILE    上傳檔案物件
//
//0    文件上傳成功.
//1    大小超出PHP.ini裡upload_max_filesize限制
//2    大小超出表單裡MAX_FILE_SIZE限制
//3    文件只有部分被上傳.
//4    沒有文件被上傳.
//6    找不到臨時文件夾.
//7    文件寫入失敗.
//-------------------------------------------------------

	if(!isset($_FILE)){
        return false;
    }

    $out='';
	switch(intval($_FILE["error"])){
		case 0:
			$out="文件上傳成功";
			break;
		case 1:
			$out="大小超出限制";
			break;
		case 2:
			$out="大小超出限制";
			break;
		case 3:
			$out="文件只有部分被上傳";
			break;
		case 4:
			$out="沒有文件被上傳";
			break;
		case 5:
			$out="找不到臨時文件夾";
			break;
		case 6:
			$out="文件寫入失敗";
			break;
	}
	return $out;
}
?>