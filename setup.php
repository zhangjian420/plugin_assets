<?php

/**
 * 安装时的方法
 */
function plugin_assets_install() {
    /* core plugin functionality */
    api_plugin_register_hook('assets', 'top_header_tabs', 'assets_show_tab', 'setup.php');
    api_plugin_register_hook('assets', 'top_graph_header_tabs', 'assets_show_tab', 'setup.php');
    // Breadcrums
    api_plugin_register_hook("assets", 'draw_navigation_text', 'assets_draw_navigation_text', 'setup.php');
    api_plugin_register_hook('assets', 'page_head', 'plugin_assets_page_head', 'setup.php');
    api_plugin_register_realm('assets', 'assets.php', '资产管理', 1);
    assets_setup_table();
}

/**
 * 卸载时候的方法
 */
function plugin_assets_uninstall() {

}

/**
 * 用于检查插件的版本，并提供更多信息
 * @return mixed
 */
function plugin_assets_version() {
    global $config;
    $info = parse_ini_file($config['base_path'] . '/plugins/assets/INFO', true);
    return $info['info'];
}

/**
 * 用于确定您的插件是否已准备好在安装后启用
 */
function plugin_assets_check_config() {
    return true;
}

/**
 * 显示顶部选项卡
 */
function assets_show_tab() {
    global $config;
    print '<a href="' . $config['url_path'] . 'plugins/assets/assets.php"><img src="' . $config['url_path'] . 'plugins/monitor/images/tab_monitor.gif" alt="资产管理"></a>';
}

/**
 * 面包屑
 */
function assets_draw_navigation_text ($nav) {
    $nav['assets:'] = array('title' => "资产管理", 'mapping' => '', 'url' => 'assets.php', 'level' => '0');

    $nav['assets.php:'] = array('title' => "文档管理", 'mapping' => 'assets:', 'url' => 'assets.php', 'level' => '1');
    $nav['assets.php:documents'] = array('title' => "文档管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=documents', 'level' => '1');
    $nav['assets.php:documents_edit'] = array('title' => "文档编辑", 'mapping' => 'assets:,assets.php:documents', 'url' => 'assets.php?action=documents_edit', 'level' => '2');

    $nav['assets.php:equipment'] = array('title' => "设备管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=equipment', 'level' => '1');
    $nav['assets.php:equipment_edit'] = array('title' => "设备编辑", 'mapping' => 'assets:,assets.php:equipment', 'url' => 'assets.php?action=equipment_edit', 'level' => '2');
    $nav['assets.php:equipment_almacenar_edit'] = array('title' => "设备出入库", 'mapping' => 'assets:,assets.php:equipment', 'url' => 'assets.php?action=equipment_almacenar_edit', 'level' => '2');
    $nav['assets.php:equipment_almacenar_list'] = array('title' => "设备出入库记录", 'mapping' => 'assets:,assets.php:equipment', 'url' => 'assets.php?action=equipment_almacenar_list', 'level' => '2');

    $nav['assets.php:ipaddress_group'] = array('title' => "IP地址组管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=ipaddress_group', 'level' => '1');
    $nav['assets.php:ipaddress_group_edit'] = array('title' => "IP地质组编辑", 'mapping' => 'assets:,assets.php:ipaddress_group', 'url' => 'assets.php?action=ipaddress_group_edit', 'level' => '2');

    $nav['assets.php:ipaddress'] = array('title' => "IP地市管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=ipaddress', 'level' => '1');
    $nav['assets.php:ipaddress_edit'] = array('title' => "IP地市编辑", 'mapping' => 'assets:,assets.php:ipaddress', 'url' => 'assets.php?action=ipaddress_edit', 'level' => '2');

    $nav['assets.php:contract'] = array('title' => "合同管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=contract', 'level' => '1');
    $nav['assets.php:contract_edit'] = array('title' => "合同编辑", 'mapping' => 'assets:,assets.php:contract', 'url' => 'assets.php?action=contract_edit', 'level' => '2');

    $nav['assets.php:type'] = array('title' => "类型管理", 'mapping' => 'assets:', 'url' => 'assets.php?action=type', 'level' => '1');
    $nav['assets.php:type_edit'] = array('title' => "类型编辑", 'mapping' => 'assets:,assets.php:type', 'url' => 'assets.php?action=type_edit', 'level' => '2');

    return $nav;
}

/**
 * 自定义js
 */
function plugin_assets_page_head() {
     print get_md5_include_css('plugins/assets/include/css/assets.css') . PHP_EOL;
}

function assets_setup_table() {
    
}