<?php
//动态tab选显卡操作
function assets_tabs($current_tab='documents') {
	global $config;
	/* present a tabbed interface */
	$tabs = array(
		'documents'    => __('文档管理', 'assets'),
		'equipment'      => __('设备管理', 'assets'),
		'ipaddress_group' => __('IP地址组管理', 'assets'),
		'ipaddress' => __('IP地市管理', 'assets'),
		'contract'=> __('合同管理', 'assets'),
		'type'=> __('类型管理', 'assets')
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