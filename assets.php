<?php
$guest_account=true;
chdir('../../');
include_once('./include/global.php');
include_once('./include/auth.php');
include_once($config['base_path'] . '/plugins/assets/assets_functions.php');//资产管理公共函数文件
include_once($config['base_path'] . '/plugins/assets/documents.php');//文档管理主函数
include_once($config['base_path'] . '/plugins/assets/equipment.php');//设备管理主函数
include_once($config['base_path'] . '/plugins/assets/ipaddress_group.php');//IP地址组管理主函数
include_once($config['base_path'] . '/plugins/assets/ipaddress.php');//IP地市管理主函数
include_once($config['base_path'] . '/plugins/assets/contract.php');//合同管理主函数
include_once($config['base_path'] . '/plugins/assets/type.php');//类型管理主函数
switch(get_request_var('action')) {
	case 'documents'://文档列表入口
		general_header();
		assets_tabs('documents');
		documents();
		bottom_footer();
		break;
	case 'documents_edit'://文档新增编辑页面
		general_header();
		documents_edit();
		bottom_footer();
		break;
	case 'documents_save'://文档保存
		documents_save();
		break;
	case 'equipment'://设备列表入口
		general_header();
		assets_tabs('equipment');
		equipment();
		bottom_footer();
		break;
	case 'equipment_edit'://设备编辑页面
		general_header();
		equipment_edit();
		bottom_footer();
		break;
	case 'equipment_save'://设备保存
		equipment_save();
		break;
	case 'equipment_almacenar_edit'://设备出入库页面
		general_header();
		equipment_almacenar_edit();
		bottom_footer();
		break;
	case 'equipment_almacenar_save'://出入库
		equipment_almacenar_save();
		break;	
	case 'equipment_almacenar_list'://出入库记录
		general_header();
		equipment_almacenar_list();
		bottom_footer();
		break;
	case 'ipaddress_group':
		general_header();
		assets_tabs('ipaddress_group');
		ipaddress_group();//ip地址组管理
		bottom_footer();
		break;
	case 'ipaddress_group_edit'://IP地址组新增编辑页面
		general_header();
		ipaddress_group_edit();
		bottom_footer();
		break;
	case 'ipaddress_group_save'://IP地址组保存
		ipaddress_group_save();
		break;
	case 'ipaddress':
		general_header();
		assets_tabs('ipaddress');
		ipaddress();//IP地市管理
		bottom_footer();
		break;
	case 'ipaddress_edit'://IP地址新增编辑页面
		general_header();
		ipaddress_edit();
		bottom_footer();
		break;
	case 'ipaddress_save'://IP地址保存
		ipaddress_save();
		break;
	case 'contract'://合同列表入库
		general_header();
		assets_tabs('contract');
		contract();
		bottom_footer();
		break;
	case 'contract_edit'://合同新增编辑页面
		general_header();
		contract_edit();
		bottom_footer();
		break;
	case 'contract_save'://合同保存
		contract_save();
		break;
	case 'actions':
		form_actions();
		break;
	case 'ajax_area':
		ajax_area();
		break;
	case 'type':
		general_header();
		assets_tabs('type');
		type();//类型管理
		bottom_footer();
		break;
	case 'type_edit'://类型新增编辑页面
		general_header();
		type_edit();
		bottom_footer();
		break;
	case 'type_save'://类型保存
		type_save();
		break;
	default:
        general_header();
        assets_tabs('documents');
        documents();
        bottom_footer();
		break;
}
 /**
 * ajax得到区域集合
 */
function ajax_area(){
	$city_id=get_filter_request_var('city_id');
    $data=get_area($city_id);
    print json_encode($data);
}
/**
 * form_actions
 */
function form_actions() {
	global $config;
    global $documents_actions,$equipment_actions,$ipaddress_actions,$ipaddress_group_actions,$contract_actions;
    /* ================= input validation ================= */
    get_filter_request_var('drp_action', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([a-zA-Z0-9_]+)$/')));
	/***********************文档管理操作begin ************************/
	if(get_nfilter_request_var('drp_action') == '11'){
		//文档删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '11') { /* delete */
					db_execute('DELETE FROM plugin_assets_documents WHERE ' . array_to_sql_or($selected_items, 'id'));
				}
			}
			header('Location: assets.php?action=documents&header=false');
			exit;
		}
		//文档删除操作end
        //文档删除确认页面begin
		$documents_html = ''; $i = 0; $documents_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$documents_name=html_escape(db_fetch_cell_prepared('SELECT name FROM plugin_assets_documents WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '11'){//删除
					$documents_html .= '<li>' . $documents_name  . '</li>';
				}
				$documents_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($documents_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($documents_id_list) && cacti_sizeof($documents_id_list)) {
			if (get_nfilter_request_var('drp_action') == '11') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下文档</p>
						<div class='itemlist'><ul>$documents_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除文档'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=documents&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($documents_id_list) ? serialize($documents_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//文档删除确认页面end
	}
	/***********************文档管理操作end ************************/

	/***********************合同管理操作begin ************************/
	if(get_nfilter_request_var('drp_action') == '41'){
		//合同删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '41') { /* delete */
					db_execute('DELETE FROM plugin_assets_contract WHERE ' . array_to_sql_or($selected_items, 'id'));
				}
			}
			header('Location: assets.php?action=contract&header=false');
			exit;
		}
		//合同删除操作end
        //合同删除确认页面begin
		$contract_html = ''; $i = 0; $contract_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$contract_name=html_escape(db_fetch_cell_prepared('SELECT name FROM plugin_assets_contract WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '41'){//删除
					$contract_html .= '<li>' . $contract_name  . '</li>';
				}
				$contract_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($contract_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($contract_id_list) && cacti_sizeof($contract_id_list)) {
			if (get_nfilter_request_var('drp_action') == '41') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下合同</p>
						<div class='itemlist'><ul>$contract_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除合同'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=contract&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($contract_id_list) ? serialize($contract_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//合同删除确认页面end
	}
	/***********************合同管理操作end ************************/

	/***********************IP地址组管理操作begin ************************/
	if(get_nfilter_request_var('drp_action') == '51'){
		//IP地址组删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '51') { /* delete */
					db_execute('DELETE FROM plugin_assets_ipaddress_group WHERE ' . array_to_sql_or($selected_items, 'id'));
				    db_execute('UPDATE plugin_assets_ipaddress SET group_id = 0 WHERE ' . array_to_sql_or($selected_items, 'group_id'));
				}
			}
			header('Location: assets.php?action=ipaddress_group&header=false');
			exit;
		}
		//IP地址组删除操作end
        //IP地址组删除确认页面begin
		$ipaddress_group_html = ''; $i = 0; $ipaddress_group_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$ipaddress_group_name=html_escape(db_fetch_cell_prepared('SELECT name FROM plugin_assets_ipaddress_group WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '51'){//删除
					$ipaddress_group_html .= '<li>' . $ipaddress_group_name  . '</li>';
				}
				$ipaddress_group_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($ipaddress_group_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($ipaddress_group_id_list) && cacti_sizeof($ipaddress_group_id_list)) {
			if (get_nfilter_request_var('drp_action') == '51') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下IP地址组</p>
						<div class='itemlist'><ul>$ipaddress_group_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除IP地址组'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=ipaddress_group&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($ipaddress_group_id_list) ? serialize($ipaddress_group_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//IP地址组删除确认页面end
	}
    /***********************IP地址组管理操作end ************************/

	/***********************IP地市管理操作begin ************************/
	if(get_nfilter_request_var('drp_action') == '31'){
		//IP地址删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '31') { /* delete */
					db_execute('DELETE FROM plugin_assets_ipaddress WHERE ' . array_to_sql_or($selected_items, 'id'));
				}
			}
			header('Location: assets.php?action=ipaddress&header=false');
			exit;
		}
		//IP地址删除操作end
        //IP地址删除确认页面begin
		$ipaddress_html = ''; $i = 0; $ipaddress_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$ipaddress_name=html_escape(db_fetch_cell_prepared('SELECT ip_range FROM plugin_assets_ipaddress WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '31'){//删除
					$ipaddress_html .= '<li>' . $ipaddress_name  . '</li>';
				}
				$ipaddress_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($ipaddress_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($ipaddress_id_list) && cacti_sizeof($ipaddress_id_list)) {
			if (get_nfilter_request_var('drp_action') == '31') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下IP地址段</p>
						<div class='itemlist'><ul>$ipaddress_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除IP地址段'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=ipaddress&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($ipaddress_id_list) ? serialize($ipaddress_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
			
		html_end_box();
		form_end();
		bottom_footer();
		//IP地址删除确认页面end
	}
	/***********************IP地市管理操作end ************************/

	/***********************IP地址分组管理操作begin ************************/
	if(strpos(get_nfilter_request_var('drp_action'),"group_") > 0){
		//IP地址分组操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (strpos(get_nfilter_request_var('drp_action'),"group_") > 0) { /* delete */
					$group_id=explode("_",get_nfilter_request_var('drp_action'))[2];
					db_execute('UPDATE plugin_assets_ipaddress SET group_id = '.$group_id.' WHERE ' . array_to_sql_or($selected_items, 'id'));
				}
			}
			header('Location: assets.php?action=ipaddress&header=false');
			exit;
		}
		//IP地址分组操作end
        //IP地址分组操作确认页面begin
		$ipaddress_html = ''; $i = 0; $ipaddress_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$ipaddress_name=html_escape(db_fetch_cell_prepared('SELECT ip_range FROM plugin_assets_ipaddress WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(strpos(get_nfilter_request_var('drp_action'),"group_") > 0){//删除
					$ipaddress_html .= '<li>' . $ipaddress_name  . '</li>';
				}
				$ipaddress_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		$ipaddress_group_list= db_fetch_assoc('SELECT * FROM plugin_assets_ipaddress_group  ORDER BY last_modified DESC');
		if(cacti_count($ipaddress_group_list)!=0){
			foreach($ipaddress_group_list as $ipaddress_group){
				$ipaddress_actions['ipaddressgroup_' . $ipaddress_group['id']]=$ipaddress_group['name'];
			}
		}
		html_start_box($ipaddress_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($ipaddress_id_list) && cacti_sizeof($ipaddress_id_list)) {
			if (strpos(get_nfilter_request_var('drp_action'),"group_") > 0) { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'分组以下IP地址</p>
						<div class='itemlist'><ul>$ipaddress_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除IP地址'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=ipaddress&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($ipaddress_id_list) ? serialize($ipaddress_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//IP地址分组操作确认页面end
	}
	/***********************IP地址分组管理操作end ************************/

	/***********************设备管理操作begin ************************/
	/***************************************设备删除操作begin*************************************** */
	if (get_nfilter_request_var('drp_action') == '24') {//删除操作
		//设备删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '24') { /* delete */
					db_execute('DELETE FROM plugin_assets_equipment WHERE ' . array_to_sql_or($selected_items, 'id'));
					db_execute('DELETE FROM plugin_assets_equipment_almacenar WHERE ' . array_to_sql_or($selected_items, 'equipment_id'));
				}
			}
			header('Location: assets.php?action=equipment&header=false');
			exit;
		}
		//设备删除操作end
		//设备删除确认页面begin
		$equipment_html = ''; $i = 0; $equipment_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$equipment_name=html_escape(db_fetch_cell_prepared('SELECT name FROM plugin_assets_equipment WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '24'){//删除
					$equipment_html .= '<li>' . $equipment_name  . '</li>';
				}
				$equipment_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($equipment_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($equipment_id_list) && cacti_sizeof($equipment_id_list)) {
			if (get_nfilter_request_var('drp_action') == '24') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下设备</p>
						<div class='itemlist'><ul>$equipment_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除设备'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=equipment&header=false');
			exit;
		}
		print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($equipment_id_list) ? serialize($equipment_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			</tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//设备删除确认页面end
	}
	/***************************************设备删除操作end*************************************** */
	if(get_nfilter_request_var('drp_action') == '21'||get_nfilter_request_var('drp_action') == '22'||get_nfilter_request_var('drp_action') == '23'){
		$equipment_id_list = ''; $i = 0;
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				/* ==================================================== */
				$equipment_id_list[$i] = $matches[1];
				$i++;
			}
		}
		if (isset($equipment_id_list) && cacti_sizeof($equipment_id_list)) {
			if (get_nfilter_request_var('drp_action') == '21') {//入库操作
				if(cacti_sizeof($equipment_id_list)>1){
					raise_message(2,'只能选择一条数据操作',MESSAGE_LEVEL_ERROR);
					header('Location: assets.php?action=equipment&header=false');
					exit;
				}
				header('Location: assets.php?action=equipment_almacenar_edit&equipment_id=' . $equipment_id_list[0] . '&operation_type=入库');
			}
			if (get_nfilter_request_var('drp_action') == '22') {//出库操作
				if(cacti_sizeof($equipment_id_list)>1){
					raise_message(2,'只能选择一条数据操作',MESSAGE_LEVEL_ERROR);
					header('Location: assets.php?action=equipment&header=false');
					exit;
				}
				header('Location: assets.php?action=equipment_almacenar_edit&equipment_id=' . $equipment_id_list[0] . '&operation_type=出库');
			}
			if (get_nfilter_request_var('drp_action') == '23') {//记录操作
				if(cacti_sizeof($equipment_id_list)>1){
					raise_message(2,'只能选择一条数据操作',MESSAGE_LEVEL_ERROR);
					header('Location: assets.php?action=equipment&header=false');
					exit;
				}
				header('Location: assets.php?action=equipment_almacenar_list&equipment_id=' . $equipment_id_list[0]);
				exit;
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=equipment&header=false');
			exit;
		}
	}
	/***********************设备管理操作end ************************/

	/***********************类型管理操作begin ************************/
	if(get_nfilter_request_var('drp_action') == '61'){
		//类型删除操作begin
		if (isset_request_var('selected_items')) {
			$selected_items = sanitize_unserialize_selected_items(get_nfilter_request_var('selected_items'));
			if ($selected_items != false) {
				if (get_nfilter_request_var('drp_action') == '61') { /* delete */
					db_execute('DELETE FROM plugin_assets_type WHERE ' . array_to_sql_or($selected_items, 'id'));
				}
			}
			header('Location: assets.php?action=type&header=false');
			exit;
		}
		//类型删除操作end
        //类型删除确认页面begin
		$type_html = ''; $i = 0; $type_id_list='';
		foreach ($_POST as $var => $val) {
			if (preg_match('/^chk_([0-9]+)$/', $var, $matches)) {
				/* ================= input validation ================= */
				input_validate_input_number($matches[1]);
				$type_name=html_escape(db_fetch_cell_prepared('SELECT name FROM plugin_assets_type WHERE id = ?', array($matches[1])));
				/* ==================================================== */
				if(get_nfilter_request_var('drp_action') == '61'){//删除
					$type_html .= '<li>' . $type_name  . '</li>';
				}
				$type_id_list[$i] = $matches[1];
				$i++;
			}
		}
		top_header();
		form_start('assets.php');
		html_start_box($type_actions[get_nfilter_request_var('drp_action')], '60%', '', '3', 'center', '');
		if (isset($type_id_list) && cacti_sizeof($type_id_list)) {
			if (get_nfilter_request_var('drp_action') == '61') { /* delete */
				print "<tr>
					<td class='textArea' class='odd'>
						<p>点击'继续'删除以下类型</p>
						<div class='itemlist'><ul>$type_html</ul></div>
					</td>
				</tr>\n";
				$save_html = "<input type='button' class='ui-button ui-corner-all ui-widget' value='" . __esc('Cancel') . "' onClick='cactiReturnTo()'>&nbsp;<input type='submit' class='ui-button ui-corner-all ui-widget' value='" . __esc('Continue') . "' title='删除类型'>";
			}
		} else {
			raise_message(40);
			header('Location: assets.php?action=type&header=false');
			exit;
		}
        print "<tr>
					<td class='saveRow'>
						<input type='hidden' name='action' value='actions'>
						<input type='hidden' name='selected_items' value='" . (isset($type_id_list) ? serialize($type_id_list) : '') . "'>
						<input type='hidden' name='drp_action' value='" . html_escape(get_nfilter_request_var('drp_action')) . "'>
						$save_html
					</td>
			   </tr>\n";
		html_end_box();
		form_end();
		bottom_footer();
		//类型删除确认页面end
	}
    /***********************类型管理操作end ************************/
}
?>