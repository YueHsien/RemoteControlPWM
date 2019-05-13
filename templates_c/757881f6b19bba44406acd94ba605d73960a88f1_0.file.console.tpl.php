<?php
/* Smarty version 3.1.30, created on 2019-05-10 17:11:47
  from "/var/www/html/apw_web/templates/console.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5cd540533c80a1_58464846',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '757881f6b19bba44406acd94ba605d73960a88f1' => 
    array (
      0 => '/var/www/html/apw_web/templates/console.tpl',
      1 => 1557469327,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5cd540533c80a1_58464846 (Smarty_Internal_Template $_smarty_tpl) {
?>
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
		<?php echo '<script'; ?>
 src="//code.jquery.com/jquery-3.1.1.min.js" type="text/javascript"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"><?php echo '</script'; ?>
>
		<?php echo '<script'; ?>
 src="./assets/ajax.js?<?php echo time();?>
"><?php echo '</script'; ?>
>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
		<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.min.css" rel="stylesheet">
		
		<?php echo '<script'; ?>
>
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
		<?php echo '</script'; ?>
>
		
	</head>
	<body>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['dc']->value, 'obj');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['obj']->value) {
?>
					<li class="list-group-item li_data" uuid="<?php echo $_smarty_tpl->tpl_vars['obj']->value['uuid'];?>
" did="<?php echo $_smarty_tpl->tpl_vars['obj']->value['id'];?>
" skey="<?php echo $_smarty_tpl->tpl_vars['obj']->value['skey'];?>
">
						<i class="fa fa-circle online_status" aria-hidden="true" style="color:#ccc;"></i> <?php echo $_smarty_tpl->tpl_vars['obj']->value['name'];?>

						<span class="pull-right">
							<input type="checkbox" class="m-checkbox" value="連線中">
							<input type="button" class="m-button" value="送出">
							<input type="text"  id="te1">
						</span>
					</li>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

	</body>
</html><?php }
}
