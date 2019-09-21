<?php
/**
 * IP管理主函数
 */
$ipaddress_actions = array(
    31 => __('删除')
);
//IP地址新增编辑页面
function ipaddress_edit(){
    assets_tabs('ipaddress');//IP地市管理选项卡
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_ipaddress WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => 'IP地址id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
        ),
        'group_id' => array(
            'friendly_name' => 'IP地址组',
            'class' => 'width280',
			'method' => 'drop_sql',
			'description' => '请选择接收合同到期告警的邮箱',
			'value' => isset($data['group_id']) ? $data['group_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => 'SELECT id, name FROM plugin_assets_ipaddress_group ORDER BY name'
		),
		'ip_range' => array(
			'friendly_name' => 'IP地址段',
			'method' => 'textarea',
			'textarea_rows' => '4',
            'textarea_cols' => '60',
            'max_length' => '1024',
            'description' =>'请正确填写IP地址段(192.168.1.0/24)',
            'class' =>'range',
			'value' => (isset($data['ip_range']) ? $data['ip_range']:'')
        ),
		'use_name' => array(
			'friendly_name' => '使用人',
            'method' => 'textbox',
            'max_length' => 32,
			'description' =>'请输入IP地址使用人',
			'value' => (isset($data['use_name']) ? $data['use_name']:'')
        ),
        'city_id' => array(
            'friendly_name' => '使用城市',
            'class' => 'width280',
			'method' => 'drop_sql',
			'description' => '请选择IP地址段使用的城市',
			'value' => isset($data['city_id']) ? $data['city_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => 'SELECT code AS id, name FROM region where level=1'
        ),
        'area_id' => array(
            'friendly_name' => '使用区域',
            'class' => 'width280',
			'method' => 'drop_sql',
			'description' => '请选择IP地址段使用的区域',
			'value' => isset($data['area_id']) ? $data['area_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => 'SELECT code AS id, name FROM region where level=2 and pcode=' . (isset($data['city_id']) ? $data['city_id'] : '0')
        ),
		'use_address' => array(
			'friendly_name' => '使用地址详情',
            'method' => 'textbox',
            'max_length' => 32,
			'description' =>'请输入IP地址段使用地址详情',
			'value' => (isset($data['use_address']) ? $data['use_address']:'')
		),
		'use_uso' => array(
			'friendly_name' => '使用用途',
            'method' => 'textbox',
            'max_length' => 32,
			'description' =>'请输入IP地址段使用用途',
			'value' => (isset($data['use_uso']) ? $data['use_uso']:'')
		),
		'description' => array(
			'friendly_name' => 'IP地址段备注',
			'method' => 'textbox',
			'max_length' => 50,
			'description' =>'请正确填写IP地址段备注',
			'value' => (isset($data['description']) ? $data['description']:'')
		)
	);
	form_start('assets.php', 'ipaddress_edit',true);//IP地址编辑form开始
	if (isset($data['id'])) {
		html_start_box(__('IP地址 [编辑: %s]', html_escape($data['ip_range'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box(__('IP地址 [新增]'), '100%', true, '3', 'center', '');
	}
	draw_edit_form(
		array(
			'config' => array('no_form_tag' => true),
			'fields' => $field_array
		)
    );
	html_end_box(true, true);
    ?>
    <!-- 操作按钮 -->
    <table style='width:100%;text-align:center;'>
		<tr>
			<td class='saveRow'>
                <input type="hidden" name="action" value="ipaddress_save">
                <input type="button" class="" onclick="window.location.href='assets.php?action=ipaddress';" value="返回" role="button">
                <input type="submit" id="submit" value="保存" role="button">
			</td>
		</tr>
    </table>
    <script>
    $(document).ready(function(){
         //市下拉框
         $("#city_id").change(function(){
            $("#area_id").empty();
            $("#area_id").append("<option value='0'>请选择</option>");
            $.ajax({
                url: 'assets.php?action=ajax_area&city_id=' + $("#city_id").val(),
                dataType: "json",
                success: function (data) {
                    data.forEach(function(ele){
                        $("#area_id").append("<option value='"+ele.id+"'>"+ele.name+"</option>");
                    });
                    $("#area_id").selectmenu("refresh");//一定要刷新
                }
            })
            $("#area_id").selectmenu("refresh");//一定要刷新
         });
         //区域下拉框
         $("#area_id").change(function(){
            var city_text=$("#city_id").find("option:selected").text();
            var area_text=$("#area_id").find("option:selected").text();
            var use_address_text="";
            if(city_text!='请选择'){
                use_address_text+=city_text;
            }
            if(area_text!='请选择'){
                use_address_text+=area_text;
            }
            $("#use_address").val(use_address_text);
         });
    })
    </script>

    <?php
    form_end(false);//表单编辑结束
}
//IP地址信息修改操作
function ipaddress_save(){
    global $config;
    $save['id']           = get_filter_request_var('id');
    $save['ip_range']         = form_input_validate(get_nfilter_request_var('ip_range'), 'ip_range', '', false, 3);
    $save['group_id']     = form_input_validate(get_nfilter_request_var('group_id'), 'group_id', '', true, 3);
    $save['use_name']     = form_input_validate(get_nfilter_request_var('use_name'), 'use_name', '', true, 3);
    $save['city_id']     = form_input_validate(get_nfilter_request_var('city_id'), 'city_id', '', true, 3);
    $save['area_id']     = form_input_validate(get_nfilter_request_var('area_id'), 'area_id', '', true, 3);
	$save['use_address']     = form_input_validate(get_nfilter_request_var('use_address'), 'use_address', '', true, 3);
	$save['use_uso']     = form_input_validate(get_nfilter_request_var('use_uso'), 'use_uso', '', true, 3);
    $save['description']     = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if (is_error_message()) {
        header('Location: assets.php?action=ipaddress_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        // if (!is_ipaddress($save['ip'])) {
        //     raise_message(2,"IP地址无效",MESSAGE_LEVEL_ERROR);
        //     header('Location: assets.php?action=ipaddress_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
        //     exit;
        // }
        $id=sql_save($save, 'plugin_assets_ipaddress');
        if ($id) {
            raise_message(1);
            header('Location: assets.php?action=ipaddress');
            exit;
        } else {
            raise_message(2);
            header('Location: assets.php?action=ipaddress_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
            exit;
        }
    }
}
//IP地市管理列表入口
function ipaddress(){
	global $config;
    global $ipaddress_actions,$item_rows;
    $ipaddress_group_list= db_fetch_assoc('SELECT * FROM plugin_assets_ipaddress_group  ORDER BY last_modified DESC');
    if(cacti_count($ipaddress_group_list)!=0){
        foreach($ipaddress_group_list as $ipaddress_group){
            $ipaddress_actions['_group_' . $ipaddress_group['id']]=$ipaddress_group['name'];
        }
    }
    /* ================= input validation and session storage ================= */
    $filters = array(
        'rows' => array(
            'filter' => FILTER_VALIDATE_INT,
            'pageset' => true,
            'default' => '-1'
        ),
        'page' => array(
            'filter' => FILTER_VALIDATE_INT,
            'default' => '1'
        ),
        'filter' => array(
            'filter' => FILTER_CALLBACK,
            'pageset' => true,
            'default' => '',
            'options' => array('options' => 'sanitize_search_string')
        ),
        'sort_column' => array(
            'filter' => FILTER_CALLBACK,
            'default' => 'id',
            'options' => array('options' => 'sanitize_search_string')
        ),
        'sort_direction' => array(
            'filter' => FILTER_CALLBACK,
            'default' => 'ASC',
            'options' => array('options' => 'sanitize_search_string')
        )
    );
    validate_store_request_vars($filters, 'sess_ipaddress');//
    /* ================= input validation ================= */
    /* if the number of rows is -1, set it to the default */
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    html_start_box("新增IP地址", '100%', '', '3', 'center', 'assets.php?action=ipaddress_edit');
    ?>
    <tr class='even'>
        <td>
            <form id='form_ipaddress' action='assets.php?action=ipaddress'>
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            IP地址组
                        </td>
                        <td>
                            <select id='group_id' onChange='applyFilter()'>
                            <option value='-1'<?php if (get_request_var('group_id') == '-1') {?> selected<?php }?>>请选择</option>
                                <?php
                                $assets_ipaddress_group_array = db_fetch_assoc("select * from plugin_assets_ipaddress_group");
                                if (cacti_sizeof($assets_ipaddress_group_array) > 0) {
									foreach ($assets_ipaddress_group_array as $assets_ipaddress_group) {
										print "<option value='" . $assets_ipaddress_group['id'] . "'"; if (get_request_var('group_id') == $assets_ipaddress_group['id']) { print ' selected'; } print '>' . $assets_ipaddress_group['name'] . "</option>\n";
									}
								}
                                ?>
                            </select>
                        </td>
                        <td>
                            IP地址记录
                        </td>
                        <td>
                            <select id='rows' onChange='applyFilter()'>
                                <option value='-1'<?php print (get_request_var('rows') == '-1' ? ' selected>':'>') . __('Default');?></option>
                                <?php
                                if (cacti_sizeof($item_rows)) {
                                    foreach ($item_rows as $key => $value) {
                                        print "<option value='" . $key . "'"; if (get_request_var('rows') == $key) { print ' selected'; } print '>' . html_escape($value) . "</option>\n";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td>
						<span>
							<input type='button' class='ui-button ui-corner-all ui-widget' id='refresh' value='<?php print __esc('Go');?>' title='<?php print __esc('Set/Refresh Filters');?>'>
							<input type='button' class='ui-button ui-corner-all ui-widget' id='clear' value='<?php print __esc('Clear');?>' title='<?php print __esc('Clear Filters');?>'>
						</span>
                        </td>
                    </tr>
                </table>
            </form>
            <script type='text/javascript'>
                //查询操作函数
                function applyFilter() {
                    strURL  = 'assets.php?action=ipaddress&header=false';
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    strURL += '&group_id='+$('#group_id').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=ipaddress&clear=1&header=false';
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_ipaddress').submit(function(event) {
                        event.preventDefault();
                        applyFilter();
                    });
                });
            </script>
        </td>
    </tr>
    <?php
    html_end_box();
    /* form the 'where' clause for our main sql query */
    $sql_where = '';
    if (get_request_var('filter') != '') {
        $sql_where = $sql_where . " AND (assets_ipaddress.ip_range LIKE '%" . get_request_var('filter') . "%' OR assets_ipaddress.use_uso like '%" . get_request_var('filter') . "%' OR assets_ipaddress.use_address like '%" . get_request_var('filter') . "%' OR assets_ipaddress.description like '%" . get_request_var('filter') . "%')";
    }
    if (get_request_var('group_id') != ''&&get_request_var('group_id') != '-1') {
        $sql_where =$sql_where . " AND assets_ipaddress.group_id=" . get_request_var('group_id');
    } 
    $total_rows = db_fetch_cell("SELECT count(*) FROM plugin_assets_ipaddress AS assets_ipaddress left join plugin_assets_ipaddress_group AS assets_ipaddress_group on assets_ipaddress.group_id=assets_ipaddress_group.id LEFT JOIN region as region_city on assets_ipaddress.city_id=region_city.code LEFT JOIN region as region_area on assets_ipaddress.area_id=region_area.code where 1=1 $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $ipaddress_list = db_fetch_assoc("SELECT assets_ipaddress.*,assets_ipaddress_group.name AS group_name,region_city.name AS city_name,region_area.name AS area_name FROM plugin_assets_ipaddress AS assets_ipaddress left join plugin_assets_ipaddress_group AS assets_ipaddress_group on assets_ipaddress.group_id=assets_ipaddress_group.id LEFT JOIN region as region_city on assets_ipaddress.city_id=region_city.code LEFT JOIN region as region_area on assets_ipaddress.area_id=region_area.code where 1=1 $sql_where $sql_order $sql_limit");
    cacti_log("SELECT assets_ipaddress.*,assets_ipaddress_group.name AS group_name,region_city.name AS city_name,region_area.name AS area_name FROM plugin_assets_ipaddress AS assets_ipaddress left join plugin_assets_ipaddress_group AS assets_ipaddress_group on assets_ipaddress.group_id=assets_ipaddress_group.id LEFT JOIN region as region_city on assets_ipaddress.city_id=region_city.code LEFT JOIN region as region_area on assets_ipaddress.area_id=region_area.code where 1=1 " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=ipaddress&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "IP地址", 'page', 'main');
    form_start('assets.php?action=ipaddress', 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => __('ID'),        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'ip_range'    => array('display' => "IP地址段", 'align' => 'left',  'sort' => 'ASC', 'tip' => "IP地址段"),
        'group_name'    => array('display' => "IP地址组", 'align' => 'left',  'sort' => 'ASC', 'tip' => "IP地址组"),
        'city_name'    => array('display' => "使用城市", 'align' => 'left',  'sort' => 'ASC', 'tip' => "使用城市"),
        'area_name'    => array('display' => "使用区域", 'align' => 'left',  'sort' => 'ASC', 'tip' => "使用区域"),
		'use_name'    => array('display' => "使用人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "使用人"),
		'use_address'    => array('display' => "使用地址详情", 'align' => 'left',  'sort' => 'ASC', 'tip' => "使用地址详情"),
		'use_uso'    => array('display' => "使用用途", 'align' => 'left',  'sort' => 'ASC', 'tip' => "使用用途"),
		'description'    => array('display' => "IP地址段备注", 'align' => 'left',  'sort' => 'ASC', 'tip' => "IP地址段备注"),
		'last_modified' => array('display' => __('最后编辑时间'), 'align' => 'left', 'sort' => 'ASC', 'tip' => "最后编辑时间"),
        'modified_by'    => array('display' => "最后编辑人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "最后编辑人")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=ipaddress');
    if (cacti_sizeof($ipaddress_list)) {
        foreach ($ipaddress_list as $ipaddress) {
            form_alternate_row('line' . $ipaddress['id'], true);
            form_alternate_row('line' . $ipaddress['use_uso'], true);
            form_selectable_cell($ipaddress['id'], $ipaddress['id'], '');
            form_selectable_cell(filter_value($ipaddress['ip_range'], get_request_var('filter'), 'assets.php?action=ipaddress_edit&id=' . $ipaddress['id']) , $ipaddress['id']);
            form_selectable_cell($ipaddress['group_name'],$ipaddress['id'],'');
            form_selectable_cell($ipaddress['city_name'],$ipaddress['id'],'');
            form_selectable_cell($ipaddress['area_name'],$ipaddress['id'],'');
            form_selectable_cell($ipaddress['use_name'],$ipaddress['id'],'');
            form_selectable_cell(filter_value($ipaddress['use_address'], get_request_var('filter')),$ipaddress['id'],'');
            form_selectable_cell(filter_value($ipaddress['use_uso'], get_request_var('filter')),$ipaddress['id'],'');
            form_selectable_cell(filter_value($ipaddress['description'], get_request_var('filter')),$ipaddress['id'],'');
            form_selectable_cell(substr($ipaddress['last_modified'],0,16), $ipaddress['id'], '');
            form_selectable_cell(get_username($ipaddress['modified_by']),$ipaddress['id'],'');
            form_checkbox_cell($ipaddress['ip_range'], $ipaddress['id']);
            form_end_row();
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($ipaddress_list)) {
        print $nav;
    }
    draw_actions_dropdown($ipaddress_actions);
    form_end();//分页form结束
}
