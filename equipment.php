<?php
/**
 * 设备相关操作函数
 */
$equipment_actions = array(
    24 => __('删除'),
    21 => __('入库'),
    22 => __('出库'),
    23 => __('记录')
);
 //设备管理列表入口
function equipment(){
	global $equipment_actions,$item_rows;
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
    validate_store_request_vars($filters, 'sess_equipment');//
    /* ================= input validation ================= */
    /* if the number of rows is -1, set it to the default */
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    html_start_box("新增设备", '100%', '', '3', 'center', 'assets.php?action=equipment_edit');
    ?>
    <tr class='even'>
        <td>
            <form id='form_equipment' action='assets.php?action=equipment'>
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            设备记录
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
                    strURL  = 'assets.php?action=equipment&header=false';
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=equipment&clear=1&header=false';
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_equipment').submit(function(event) {
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
        $sql_where =$sql_where . " AND (name LIKE '%" . get_request_var('filter') . "%' OR model like '%" . get_request_var('filter') . "%' OR contract_number like '%" . get_request_var('filter') . "%')";
    } 
    $total_rows = db_fetch_cell("SELECT COUNT(*) FROM plugin_assets_equipment WHERE 1=1 $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $equipment_list = db_fetch_assoc("SELECT * FROM plugin_assets_equipment WHERE 1=1 $sql_where $sql_order $sql_limit");
    cacti_log("SELECT * FROM plugin_assets_equipment WHERE 1=1 " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=equipment&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "设备", 'page', 'main');
    form_start('assets.php?action=equipment', 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => __('ID'),        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'name'    => array('display' => "设备名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "设备名称"),
        'model'    => array('display' => "设备型号", 'align' => 'left',  'sort' => 'ASC', 'tip' => "设备型号"),
        'contract_number'    => array('display' => "合同编号", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同编号"),
        'purpose'    => array('display' => "设备用途", 'align' => 'left',  'sort' => 'ASC', 'tip' => "设备用途"),
        'total'    => array('display' => "设备数量", 'align' => 'left',  'sort' => 'ASC', 'tip' => "设备数量"),
        'last_modified' => array('display' => __('最后编辑时间'), 'align' => 'left', 'sort' => 'ASC', 'tip' => "最后编辑时间")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=equipment');
    if (cacti_sizeof($equipment_list)) {
        foreach ($equipment_list as $equipment) {
            form_alternate_row('line' . $equipment['id'], true);
            form_selectable_cell($equipment['id'], $equipment['id'], '', 'left');
            form_selectable_cell(filter_value($equipment['name'], get_request_var('filter'), 'assets.php?action=equipment_edit&id=' . $equipment['id']), $equipment['id'],'', 'left');
            form_selectable_cell(filter_value($equipment['model'], get_request_var('filter')),$equipment['id'],'','left');
            form_selectable_cell(filter_value($equipment['contract_number'], get_request_var('filter')),$equipment['id'],'','left');
            form_selectable_cell($equipment['purpose'],$equipment['id'],'','left');
            form_selectable_cell($equipment['total'],$equipment['id'],'','left');
            form_selectable_cell(substr($equipment['last_modified'],0,16), $equipment['id'], '', 'left');
            form_checkbox_cell($equipment['name'], $equipment['id']);
            form_end_row();
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($equipment_list)) {
        print $nav;
    }
    draw_actions_dropdown($equipment_actions);
    form_end();//分页form结束
}
//设备信息修改操作
function equipment_save(){
    $save['id']           = get_filter_request_var('id');
    $save['name']         = form_input_validate(get_nfilter_request_var('name'), 'name', '', false, 3);
    $save['model']     = form_input_validate(get_nfilter_request_var('model'), 'model', '', false, 3);
    $save['contract_number']     = form_input_validate(get_nfilter_request_var('contract_number'), 'contract_number', '', false, 3);
    $save['purpose']     = form_input_validate(get_nfilter_request_var('purpose'), 'purpose', '', true, 3);
    $save['total']     = form_input_validate(get_nfilter_request_var('total'), 'total', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if (is_error_message()) {
        header('Location: assets.php?action=equipment_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        $is_repeat=false;//默认不重复
        $assets_equipment_array = db_fetch_assoc("select * from plugin_assets_equipment where name='" . $save['name'] . "' and model='" . $save['model'] . "' and contract_number='" . $save['contract_number'] . "'");
        if (cacti_sizeof($assets_equipment_array) > 0) {
            foreach ($assets_equipment_array as $assets_equipment) {
                if($assets_equipment['id']!=$save['id']){
                    $is_repeat=true;//重复
                    break;
                }
            }
        }
        if($is_repeat){
            raise_message(2," 名称、型号、合同编号同时重复",MESSAGE_LEVEL_ERROR);
            header('Location: assets.php?action=equipment_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
            exit;
        }else{
            $id=sql_save($save, 'plugin_assets_equipment');
            if ($id) {
                raise_message(1);
                header('Location: assets.php?action=equipment');
                exit;
            } else {
                raise_message(2);
                header('Location: assets.php?action=equipment_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                exit;
            }
        }
    }
}
//设备出入库信息操作
function equipment_almacenar_save(){
    //设备信息表
    $equipment['id']=get_filter_request_var('equipment_id');//设备ID
    $equipment['total']=get_filter_request_var('equipment_total');//设备数量
    $equipment['last_modified'] = date('Y-m-d H:i:s', time());
    $equipment['modified_by']   = $_SESSION['sess_user_id'];
    //设备设备表
    $equipment_almacenar['equipment_id'] = get_filter_request_var('equipment_id');//设备ID
    $equipment_almacenar['operation_type'] = get_nfilter_request_var('operation_type');//操作类型：入库-出库
    $equipment_almacenar['count'] = form_input_validate(get_nfilter_request_var('count'), 'count', '^[0-9]+$', false, 3);//数量
    if($equipment_almacenar['operation_type']=='入库'){
        $equipment_almacenar['contract_number'] = form_input_validate(get_nfilter_request_var('contract_number'), 'contract_number', '', true, 3);//合同编号
    }
    if($equipment_almacenar['operation_type']=='出库'){
        $equipment_almacenar['lend_person'] = form_input_validate(get_nfilter_request_var('lend_person'), 'lend_person', '', false, 3);//借出人出库时填写
    }
    $equipment_almacenar['description'] = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);//备注说明
    $equipment_almacenar['last_modified'] = date('Y-m-d H:i:s', time());//最后修改时间
    $equipment_almacenar['modified_by'] = $_SESSION['sess_user_id'];//修改人
    if (is_error_message()) {
        header('Location: assets.php?action=equipment_almacenar_edit&equipment_id=' . $equipment_almacenar['equipment_id'] . '&operation_type=' . $equipment_almacenar['operation_type']);
		exit;
	}else{
        if($equipment_almacenar['operation_type']=='入库'){
            $equipment['total']=$equipment['total']+$equipment_almacenar['count'];
        }
        if($equipment_almacenar['operation_type']=='出库'){
            if($equipment_almacenar['count']>$equipment['total']){
                raise_message(2,'出库量大于设备数量',MESSAGE_LEVEL_ERROR);
                header('Location: assets.php?action=equipment_almacenar_edit&equipment_id=' . $equipment_almacenar['equipment_id'] . '&operation_type=' . $equipment_almacenar['operation_type']);
                exit;
            }else{
                $equipment['total']=$equipment['total']-$equipment_almacenar['count'];
            }
        }
        $id=sql_save($equipment_almacenar, 'plugin_assets_equipment_almacenar');
        if ($id) {
            sql_save($equipment, 'plugin_assets_equipment');//更新设备总量
            raise_message(1);
            header('Location: assets.php?action=equipment');
            exit;
        } else {
            raise_message(2);
            header('Location: assets.php?action=equipment_almacenar_edit&equipment_id=' . $equipment_almacenar['equipment_id'] . '&operation_type=' . $equipment_almacenar['operation_type']);
            exit;
        }
    }
}
//设备新增编辑页面
function equipment_edit(){
    assets_tabs('equipment');//设备管理选项卡
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_equipment WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => '设备id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
		),
		'name' => array(
			'friendly_name' => '设备名称',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写设备名称',
			'value' => (isset($data['name']) ? $data['name']:'')
		),
		'model' => array(
			'friendly_name' => '设备型号',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写设备型号',
			'value' => (isset($data['model']) ? $data['model']:'')
        ),
        'contract_number' => array(
			'friendly_name' => '合同编号',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写合同编号',
			'value' => (isset($data['contract_number']) ? $data['contract_number']:'')
        ),
		'purpose' => array(
			'friendly_name' => '设备用途',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写设备用途',
			'value' => (isset($data['purpose']) ? $data['purpose']:'')
        ),
        'total' => array(
			'friendly_name' => '设备数量',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写设备数量',
			'value' => (isset($data['total']) ? $data['total']:'')
        ),
	);
	form_start('assets.php', 'equipment_edit');//设备编辑form开始
	if (isset($data['id'])) {
		html_start_box(__('设备 [编辑: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box(__('设备 [新增]'), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="equipment_save">
                <input type="button" onclick="window.location.href='assets.php?action=equipment';" value="返回" role="button">
                <input type="submit" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
    <?php
    form_end();//表单编辑结束
}
//设备出入库编辑页面
function equipment_almacenar_edit(){
    $equipment_id=get_request_var('equipment_id');
    $operation_type=get_request_var('operation_type');
    assets_tabs('equipment');//设备管理选项卡
    $data = array();//页面显示data
    //cacti_log('==============>$equipment_id=' . $equipment_id . '==============>$operation_type='+$operation_type);
    if ($equipment_id) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_equipment WHERE id = ?', array($equipment_id));
    }
    $field_array = array(
		'equipment_id' => array(
			'friendly_name' => '设备id',
			'method' => 'hidden',
			'value' => $equipment_id
		),
		'name' => array(
			'friendly_name' => '设备名称',
			'method' => 'custom',
			'max_length' => 20,
			'value' => (isset($data['name']) ? $data['name']:'')
		),
		'model' => array(
			'friendly_name' => '设备型号',
			'method' => 'custom',
			'max_length' => 20,
			'value' => (isset($data['model']) ? $data['model']:'')
        ),
        'assets_equipment_contract_number' => array(
			'friendly_name' => '合同编号',
			'method' => 'custom',
			'max_length' => 20,
			'value' => (isset($data['contract_number']) ? $data['contract_number']:'')
		),
		'purpose' => array(
			'friendly_name' => '设备用途',
			'method' => 'custom',
			'max_length' => 20,
			'value' => (isset($data['purpose']) ? $data['purpose']:'')
        ),
        'total' => array(
			'friendly_name' => '设备数量',
			'method' => 'custom',
			'max_length' => 20,
			'value' => (isset($data['total']) ? $data['total']:'')
        ),
        'equipment_total' => array(
			'friendly_name' => '设备数量',
			'method' => 'hidden',
			'max_length' => 20,
			'value' => (isset($data['total']) ? $data['total']:'0')
        )
    );
    if ($operation_type=='入库') {
        $operation_type_field=array(
            'friendly_name' => '操作类型：入库',
            'method' => 'hidden',
            'value' => '入库'
        );
        $field_array['operation_type']=$operation_type_field;
        $contract_number=array(
            'friendly_name' => '合同编号',
            'method' => 'hidden',
            'max_length' => 32,
            'value' => (isset($data['contract_number']) ? $data['contract_number']:'')
        );
        $field_array['contract_number']=$contract_number;
        $count=array(
            'friendly_name' => '入库数量',
            'method' => 'textbox',
            'max_length' => 10,
            'description' =>'请正确填写入库数量',
            'value' => ''
        );
        $field_array['count']=$count;
    } 
    if ($operation_type=='出库') {
        $operation_type_field=array(
            'friendly_name' => '操作类型：出库',
            'method' => 'hidden',
            'value' => '出库'
        );
        $field_array['operation_type']=$operation_type_field;
        $lend_person=array(
            'friendly_name' => '借出人',
            'method' => 'textbox',
            'max_length' => 32,
            'description' =>'请正确填写借出人',
            'value' => ''
        );
        $field_array['lend_person']=$lend_person;
        $count=array(
            'friendly_name' => '出库数量',
            'method' => 'textbox',
            'max_length' => 10,
            'description' =>'请正确填写出库数量',
            'value' => ''
        );
        $field_array['count']=$count;
    } 
    $description=array(
        'friendly_name' => '备注',
        'method' => 'textbox',
        'max_length' => 32,
        'description' =>'请正确填写备注信息',
        'value' => ''
    );
    $field_array['description']=$description;
    form_start('assets.php', 'equipment_almacenar_edit');//设备出入库编辑form开始
    if (isset($data['id'])&&$operation_type=='入库') {
		html_start_box(__('[设备入库操作: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} 
	if (isset($data['id'])&&$operation_type=='出库') {
		html_start_box(__('[设备出库操作: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="equipment_almacenar_save">
                <input type="button" onclick="window.location.href='assets.php?action=equipment';" value="返回" role="button">
                <input type="submit" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
    <?php
    form_end();//表单编辑结束
}
//设备出入库记录页面
function equipment_almacenar_list(){
    assets_tabs('equipment');//设备管理选项卡
    $equipment_id=get_request_var('equipment_id');
    $equipment= db_fetch_row_prepared('SELECT * FROM plugin_assets_equipment WHERE id = ?', array($equipment_id));
    $equipment_almacenar_list= db_fetch_assoc('SELECT * FROM plugin_assets_equipment_almacenar WHERE equipment_id = '. $equipment_id . ' ORDER BY last_modified DESC');
    html_start_box(__('[设备出入库记录: %s]', html_escape($equipment['name'])), '100%', true, '3', 'center', '');
    $equipment_almacenar_html="";
    $equipment_almacenar_html.="<div class='dataTitle'>";
    $equipment_almacenar_html.="<div class='dataCloumn borderRight'>操作类型</div>";
    $equipment_almacenar_html.="<div class='dataCloumn borderRight'>数量</div>";
    $equipment_almacenar_html.="<div class='dataCloumn borderRight'>操作人</div>";
    $equipment_almacenar_html.="<div class='dataCloumn'>操作时间</div>";
    $equipment_almacenar_html.="</div>";
    if(cacti_count($equipment_almacenar_list)==0){
        $equipment_almacenar_html.="<div class='dataRow dataOdd'>暂无数据</div>";
    }else{
        foreach($equipment_almacenar_list as $equipment_almacenar){
            $equipment_almacenar_html.="<div class='dataRow dataOdd'>";
            $equipment_almacenar_html.="<div class='dataCloumn'>" .$equipment_almacenar['operation_type'] . "</div>";
            $equipment_almacenar_html.="<div class='dataCloumn'>" .$equipment_almacenar['count'] . "</div>";
            $equipment_almacenar_html.="<div class='dataCloumn'>" .get_username($equipment_almacenar['modified_by']). "</div>";
            $equipment_almacenar_html.="<div class='dataCloumn'>" .$equipment_almacenar['last_modified'] . "</div>";
            $equipment_almacenar_html.="</div>";
        }
    }
    print "$equipment_almacenar_html";
    ?>
    <!-- 操作按钮 -->
    <table style='width:100%;text-align:center;'>
		<tr>
			<td class='saveRow'>
                <input type="button" onclick="window.location.href='assets.php?action=equipment';" value="返回" role="button">
			</td>
		</tr>
	</table>
    <?php
}