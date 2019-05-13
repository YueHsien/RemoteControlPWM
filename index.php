<?php
/******************************Require list****************************/
require_once "./libs/autoload.php";
require_once "./libs/smarty/Smarty.class.php";
/******************************Method init*****************************/

@$devices = apw::get_devices(0);

@$tpl = new Smarty;
@$tpl->assign("dc", $devices);
//print_r($devices);
@$tpl->caching = 0;
@$tpl->force_compile = true;
@$tpl->display("./console.tpl");
?>