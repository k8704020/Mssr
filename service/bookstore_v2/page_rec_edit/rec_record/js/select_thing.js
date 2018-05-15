/***********************************************
		* Disable select-text script- © Dynamic Drive (www.dynamicdrive.com)
		* This notice MUST stay intact for legal use
		* Visit http://www.dynamicdrive.com/ for full source code
		***********************************************/
		
		//form tags to omit in NS6+:
		var omitformtags=["input", "textarea", "select"]
		
		omitformtags=omitformtags.join("|")
		
		function disableselect(e){
		if (omitformtags.indexOf(e.target.tagName.toLowerCase())==-1)
		return false
		}
		
		function reEnable(){
		return true
		}
		
		if (typeof document.onselectstart!="undefined")
		document.onselectstart=new Function ("return false")
		else{
		document.onmousedown=disableselect
		document.onmouseup=reEnable
		}