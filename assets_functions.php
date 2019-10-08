<?php
//动态tab选显卡操作
function assets_tabs($current_tab='documents') {
	global $config;
	$tabs = array(
		'documents'    => __('文档管理', 'assets'),
		'contract'=> __('合同管理', 'assets'),
		'equipment'      => __('设备管理', 'assets'),
		'type'=> __('类型管理', 'assets'),
		'ipaddress_group' => __('IP地址组管理', 'assets'),
		'ipaddress' => __('IP地址管理', 'assets')
	);
	$tabs = api_plugin_hook_function('assets_tabs', $tabs);//资产管理table
	get_filter_request_var('tab', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([a-zA-Z]+)$/')));
	load_current_session_value('tab', 'sess_assets_tab', 'general');
	//$current_tab = get_request_var('action');//得到当前选项卡
	/* draw the tabs */
	print "<div class='tabs'><nav><ul>\n";
	if (cacti_sizeof($tabs)) {//得到选项卡
		foreach (array_keys($tabs) as $tab_short_name) {
			print "<li><a class='tab" . (($tab_short_name == $current_tab) ? " selected'" : "'") .
				" href='" . html_escape($config['url_path'] .
				'plugins/assets/assets.php?' .
				'action=' . $tab_short_name) .
				"'>" . $tabs[$tab_short_name] . "</a></li>\n";
		}
	}
	print "</ul></nav></div>\n";
}
/**
 * 根据城市ID得到区域集合
 */
function get_area($city_id) {
	$area_array = db_fetch_assoc("SELECT code AS id, name FROM region where level=2 and pcode=" . $city_id);
	return $area_array;
}
/**
 * 参数192.168.1.0/24;192.168.2.0/24
 * 根据多个IP地址段得到具体IP字符串
 * 返回结果192.168.1.0,192.168.1.1
 */
function getIps($ip_range_string){
	$ip_range_array= explode(";", $ip_range_string);
	$ip_array=array();
	foreach ($ip_range_array as $ip_range) {
		 $ip_array=array_merge($ip_array,ip_range_parse($ip_range));//合并数组
	}
	return implode(";",$ip_array);
 }
 /**
  * 参数192.168.1.0/24
  * 根据单个个IP地址段得到具体IP字符串
  * 返回结果[192.168.1.0,192.168.1.1]
  */
 function ip_range_parse($ip_range) {
	 $mark_len = 32;
	 if (strpos($ip_range, "/") > 0) {
	  list($ip_range, $mark_len) = explode("/", $ip_range);
	 }
	 $ip = ip2long($ip_range);
	 $mark = 0xFFFFFFFF << (32 - $mark_len) & 0xFFFFFFFF;
	 $ip_start = $ip & $mark;
	 $ip_end = $ip | (~$mark) & 0xFFFFFFFF;
	 $ip_start=long2ip($ip_start);
	 $ip_end=long2ip($ip_end);
	 $ip_start_array=explode('.',$ip_start);
	 $ip_end_array=explode('.',$ip_end);
	 $ip_generate_array=$ip_start_array;
	 $ip_array=array();
	 if(cacti_sizeof($ip_start_array)==4){
		 for ($index=$ip_start_array[3]; $index<=$ip_end_array[3]; $index++) {
			 $ip_generate_array[3]=$index;
			 array_push($ip_array,implode(".",$ip_generate_array));
		 }
	 }
	 return $ip_array;
 }