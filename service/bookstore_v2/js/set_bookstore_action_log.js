		/***********************************************
		* 項PHP發射愛的宣..  不是!!!!
		*
		* 書店action_log使用每日拋
		* 須掛載JQ
		***********************************************/

		function set_action_bookstore_log(user_id,action_code,action_on)
		{
	
	
			var url = "/mssr/service/bookstore_v2/ajax/set_bookstore_action_log.php";
			$.post(url, {
					user_id:user_id,
					action_on:action_on,
					action_code:action_code
			}).success(function (data){
				
				
			}).error(function(e){
				
			}).complete(function(e){
				
			});
		}