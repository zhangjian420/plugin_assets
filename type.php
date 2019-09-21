<?php
/**
 * 类型管理主函数
 */
$type_actions = array(
    61 => __('删除')
);
//类型新增编辑页面
function type_edit(){
    assets_tabs('type');//类型管理选项卡
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_type WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => '类型id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
		),
		'name' => array(
			'friendly_name' => '类型名称',
			'method' => 'textbox',
			'max_length' => 36,
			'description' =>'请正确填写类型名称',
			'value' => (isset($data['name']) ? $data['name']:'')
        ),
        'type' => array(
			'friendly_name' => '模块名称',
			'method' => 'drop_sql',
			'description' => '请选择该类型需要被使用的模块名称',
			'value' => isset($data['type']) ? $data['type'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => "SELECT name as id, name FROM plugin_assets_type where type='资产管理' ORDER BY id"
		),
		'description' => array(
			'friendly_name' => '类型描述',
			'method' => 'textbox',
			'max_length' => 36,
			'description' =>'请正确填写类型描述',
			'value' => (isset($data['description']) ? $data['description']:'')
		)
	);
	form_start('assets.php', 'type_edit',true);//类型编辑form开始
	if (isset($data['id'])) {
		html_start_box(__('类型 [编辑: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box(__('类型 [新增]'), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="type_save">
                <input type="button" onclick="window.location.href='assets.php?action=type';" value="取消" role="button">
                <input type="submit" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
    <?php
    form_end(false);//表单编辑结束
}

//类型信息修改操作
function type_save(){
    global $config;
    $save['id']           = get_filter_request_var('id');
    $save['name']         = form_input_validate(get_nfilter_request_var('name'), 'name', '', false, 3);
    $save['type']         = form_input_validate(get_nfilter_request_var('type'), 'type', '', true, 3);
    $save['description']     = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if (is_error_message()) {
        header('Location: assets.php?action=type_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        $id=sql_save($save, 'plugin_assets_type');
        if ($id) {
            raise_message(1);
            header('Location: assets.php?action=type');
            exit;
        } else {
            raise_message(2);
            header('Location: assets.php?action=type_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
            exit;
        }
    }
}
//类型管理列表入口
function type(){
	global $config;
    global $type_actions,$item_rows;
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
    validate_store_request_vars($filters, 'sess_type');//
    /* ================= input validation ================= */
    /* if the number of rows is -1, set it to the default */
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    html_start_box("新增类型", '100%', '', '3', 'center', 'assets.php?action=type_edit');
    ?>
    <tr class='even'>
        <td>
            <form id='form_type' action='assets.php?action=type'>
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            类型记录
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
                    strURL  = 'assets.php?action=type&header=false';
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=type&clear=1&header=false';
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_type').submit(function(event) {
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
    $sql_where='';
    if (get_request_var('filter') != '') {
        $sql_where =$sql_where . " AND (name LIKE '%" . get_request_var('filter') . "%' OR type like '%" . get_request_var('filter') . "%' OR description like '%" . get_request_var('filter') . "%')";
    } 
    $total_rows = db_fetch_cell("SELECT COUNT(*) FROM plugin_assets_type WHERE 1=1 AND type!='资产管理' $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $type_list = db_fetch_assoc("SELECT * FROM plugin_assets_type WHERE 1=1 AND type!='资产管理' $sql_where $sql_order $sql_limit");
    cacti_log("SELECT * FROM plugin_assets_type WHERE 1=1 AND type!='资产管理' " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=type&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "类型", 'page', 'main');
    form_start('assets.php?action=type', 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => __('ID'),        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'name'    => array('display' => "类型名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "类型名称"),
        'type'    => array('display' => "模块名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "模块名称"),
		'description'    => array('display' => "类型描述", 'align' => 'left',  'sort' => 'ASC', 'tip' => "类型描述"),
		'last_modified' => array('display' => __('最后编辑时间'), 'align' => 'left', 'sort' => 'ASC', 'tip' => "最后编辑时间"),
        'modified_by'    => array('display' => "最后编辑人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "最后编辑人")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=type');
    if (cacti_sizeof($type_list)) {
        foreach ($type_list as $type) {
            form_alternate_row('line' . $type['id'], true);
            form_selectable_cell($type['id'], $type['id'], '');
            form_selectable_cell(filter_value($type['name'], get_request_var('filter'), 'assets.php?action=type_edit&id=' . $type['id']) , $type['id']);
            form_selectable_cell(filter_value($type['type'], get_request_var('filter')),$type['id'],'');
            form_selectable_cell(filter_value($type['description'], get_request_var('filter')),$type['id'],'');
            form_selectable_cell(substr($type['last_modified'],0,16), $type['id'], '');
            form_selectable_cell(get_username($type['modified_by']),$type['id'],'');
            form_checkbox_cell($type['name'], $type['id']);
            form_end_row();
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($type_list)) {
        print $nav;
    }
    draw_actions_dropdown($type_actions);
    form_end();//分页form结束
    print '<br/>';
}
