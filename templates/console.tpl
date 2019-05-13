<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta http-equiv="Cache-Control" content="no-cache"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<meta name="robots" content="index, follow"/>
		<title>智慧家電 - CuMi</title>
		<!-- cdn javascript and css -->
		<script src="//code.jquery.com/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>
		<script src="./assets/ajax.js?{time()}"></script>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.min.css" rel="stylesheet">
		{literal}
		<script>
		var uuid_list = [];
		var websocket = null;
		
		$(function() {
			$("li.li_data").each(function(i) {
				uuid_list.push($(this).attr("uuid"));
			});
			if(uuid_list.length > 0) {
				set_switch(websocket);
				start_websocket(websocket);
			}
		});
		</script>
		{/literal}
	</head>
	<body>
					{foreach $dc as $obj}
					<li class="list-group-item li_data" uuid="{$obj.uuid}" did="{$obj.id}" skey="{$obj.skey}">
						<i class="fa fa-circle online_status" aria-hidden="true" style="color:#ccc;"></i> {$obj.name}
						<span class="pull-right">
							<input type="checkbox" class="m-checkbox" value="連線中">
							<input type="button" class="m-button" value="送出">
							<input type="text"  id="te1">
						</span>
					</li>
					{/foreach}
	</body>
</html>