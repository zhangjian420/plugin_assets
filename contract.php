<?php
/**
 * 合同管理主函数
 */
//合同操作集合
$contract_actions = array(
    41 => __('删除'),
    42 => __('上传附件'),
    43 => __('下载附件')
);
//合同附件操作集合
$contract_accessory_actions = array(
    44 => __('删除')
);
//合同新增编辑页面
function contract_edit(){
    assets_tabs('contract');//合同管理选项卡
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_contract WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => '合同id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
		),
		'name' => array(
			'friendly_name' => '合同名称',
			'method' => 'textbox',
			'max_length' => 64,
			'description' =>'请正确填写合同名称',
			'value' => (isset($data['name']) ? $data['name']:'')
        ),
        'type_id' => array(
			'friendly_name' => '合同类型',
			'method' => 'drop_sql',
			'description' => '请选择合同类型',
			'value' => isset($data['type_id']) ? $data['type_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => "SELECT id, name FROM plugin_assets_type WHERE type='合同管理' ORDER BY id"
		),
		'number' => array(
			'friendly_name' => '合同编号',
			'method' => 'textbox',
			'max_length' => 64,
			'description' =>'请正确填写合同编号',
			'value' => (isset($data['number']) ? $data['number']:'')
		),
		'company' => array(
			'friendly_name' => '中标公司',
			'method' => 'textbox',
			'max_length' => 64,
			'description' =>'请正确填写中标公司名称',
			'value' => (isset($data['company']) ? $data['company']:'')
		),
		'signing_date' => array(
			'friendly_name' => '签订日期',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写签订日期',
			'value' => (isset($data['signing_date']) ? $data['signing_date']:'')
		),
		'due_date' => array(
			'friendly_name' => '到期日期',
			'method' => 'textbox',
			'max_length' => 32,
			'description' =>'请正确填写到期日期',
			'value' => (isset($data['due_date']) ? $data['due_date']:'')
		),
		'content_text' => array(
			'friendly_name' => '合同内容',
			'method' => 'textarea',
			'textarea_rows' => '4',
			'textarea_cols' => '80',
            'description' =>'请正确填写合同内容',
            'class' => 'contentText',
            'value' => (isset($data['content_text']) ? $data['content_text']:''),
		),
		// 'file_path' => array(
		// 	'friendly_name' => '合同附件',
        //     'method' => 'file',
        //     'size' => '500',
        //     'description' =>'请选择需要上传的文件'
		// ),
		'description' => array(
			'friendly_name' => '合同备注',
			'method' => 'textbox',
			'max_length' => 128,
			'description' =>'请正确填写合同备注',
			'value' => (isset($data['description']) ? $data['description']:'')
        ),
        'is_alarm' => array(
			'friendly_name' =>'是否开启告警',
			'method' => 'checkbox',
			'description' => '是否开启告警',
			'default' => '',
			'value' => (isset($data['is_alarm']) ? $data['is_alarm']:'')
        ),
        'alarm_advance_day' => array(
			'friendly_name' => '告警提前时间',
			'description' =>'请正确选择告警提前时间',
			'method' => 'radio',
			'value' => (isset($data['alarm_advance_day']) ? $data['alarm_advance_day']:''),
            'default' => '',
			'items' => array(
				0 => array(
					'radio_value' => '7',
					'radio_caption' => '提前1周',
					),
				1 => array(
					'radio_value' => '14',
					'radio_caption' => '提前2周',
					),
				2 => array(
					'radio_value' => '30',
					'radio_caption' =>'提前1个月'
                ),
                3 => array(
					'radio_value' => '60',
					'radio_caption' =>'提前2个月'
				)
			)
        ),
        'alarm_frequency' => array(
			'friendly_name' => '告警频率',
			'description' =>'请正确选择告警频率',
			'method' => 'radio',
			'value' => (isset($data['alarm_frequency']) ? $data['alarm_frequency']:''),
            'default' => '',
			'items' => array(
				0 => array(
					'radio_value' => '每天',
					'radio_caption' => '每天',
					),
				1 => array(
					'radio_value' => '每周',
					'radio_caption' => '每周',
					)
			)
		),
        'notification_id' => array(
			'friendly_name' => '告警邮箱',
			'method' => 'drop_sql',
			'description' => '请选择接收合同到期告警的邮箱',
			'value' => isset($data['notification_id']) ? $data['notification_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => 'SELECT id, name FROM plugin_notification_lists ORDER BY name'
		)
	);
	form_start('assets.php', 'contract_edit',true);//合同编辑form开始
	if (isset($data['id'])) {
		html_start_box(__('合同 [编辑: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box(__('合同 [新增]'), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="contract_save">
                <input type="button" onclick="window.location.href='assets.php?action=contract';" value="返回" role="button">
                <input type="submit" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
	<script>
		$(document).ready(function(){
            $("#signing_date").prop("readonly", true).datepicker({
                changeMonth: false,
                dateFormat: "yy-mm-dd",
                onClose: function(selectedDate) {

                }
            });
            $("#due_date").prop("readonly", true).datepicker({
                changeMonth: false,
                dateFormat: "yy-mm-dd",
                onClose: function(selectedDate) {

                }
            });
            $("div[style='formRadio']").css({float:'left',display:'flex'});
        });
	</script>
    <?php
    form_end(false);//表单编辑结束
}
//合同信息修改操作
function contract_save(){
    global $config;
    $save['id']           = get_filter_request_var('id');
    $save['name']         = form_input_validate(get_nfilter_request_var('name'), 'name', '', false, 3);
    $save['type_id']         = form_input_validate(get_nfilter_request_var('type_id'), 'type_id', '', false, 3);
	$save['number']         = form_input_validate(get_nfilter_request_var('number'), 'number', '', false, 3);
	$save['company']         = form_input_validate(get_nfilter_request_var('company'), 'company', '', false, 3);
	$save['signing_date']         = form_input_validate(get_nfilter_request_var('signing_date'), 'signing_date', '', false, 3);
	$save['due_date']         = form_input_validate(get_nfilter_request_var('due_date'), 'due_date', '', false, 3);
	$save['content_text']         = form_input_validate(get_nfilter_request_var('content_text'), 'content_text', '', false, 3);
    $save['description']     = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);
    $save['is_alarm'] = (isset_request_var('is_alarm') ? 'on':'');
    $save['alarm_advance_day']     = form_input_validate(get_nfilter_request_var('alarm_advance_day'), 'alarm_advance_day', '', true, 3);
    $save['alarm_frequency']     = form_input_validate(get_nfilter_request_var('alarm_frequency'), 'alarm_frequency', '', true, 3);
    $save['notification_id']     =form_input_validate(get_nfilter_request_var('notification_id'), 'notification_id', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if(strtotime($save['due_date'])>=strtotime(date('Y-m-d', time()))){//到期时间大于当前时间将告警状态修改为未告警
        $save['status']='未到期';
    }
    if (is_error_message()) {
        header('Location: assets.php?action=contract_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        // if (isset($_FILES["file_path"]) && !empty($_FILES["file_path"]["name"])) {
        //     $allowedExts = array("zip", "docx", "doc","xls", "xlsx", "rar","txt", "ppt", "pptx","log","pdf","jpg","png");
        //     $temp = explode(".", $_FILES["file_path"]["name"]);
        //     $extension = end($temp);//文件扩展名
        //     if (in_array($extension, $allowedExts)){
        //         if ($_FILES["file_path"]["error"] > 0){
        //             raise_message(2,"服务器文件上传错误",MESSAGE_LEVEL_ERROR);
        //         }else{
        //             /**文件上传记录上传路径 */
        //             $ext = pathinfo($_FILES["file_path"]["name"],PATHINFO_EXTENSION);
        //             $now = time();
        //             $file_path_dest = "plugins/assets/upload/contract/" . $now . "." . $ext;
        //             move_uploaded_file($_FILES["file_path"]["tmp_name"], $config['base_path']  . '/' . $file_path_dest);
        //             $save["file_path"] = $file_path_dest;
        //         }
        //     }
        //     else{
        //         raise_message(2,"文件类型不支持",MESSAGE_LEVEL_ERROR);
        //         header('Location: assets.php?action=contract_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
        //         exit;
        //     }
        // }
        /**文件信息保存 */
        $id=sql_save($save, 'plugin_assets_contract');
        if ($id) {
            raise_message(1);
            header('Location: assets.php?action=contract');
            exit;
        } else {
            raise_message(2);
            header('Location: assets.php?action=contract_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
            exit;
        }
    }
}
//合同管理列表入口
function contract(){
    global $config;
    global $contract_actions,$item_rows;
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
    validate_store_request_vars($filters, 'sess_contract');//
    /* ================= input validation ================= */
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    $buttons = array(
	    array(
	        'href'     => 'assets.php?action=contract_edit',
	        'callback' => true,
	        'title'    => "添加",
	        'class'    => 'fa fa-plus'
	    ),
	    array(
	        'href'     => 'assets.php?action=contract_import',
	        'callback' => true,
	        'title'    => "导入",
	        'class'    => 'fa fa-upload'
	    )
	);
    html_start_box("新增合同", '100%', '', '3', 'center', $buttons);
    //html_start_box("新增合同", '100%', '', '3', 'center', 'assets.php?action=contract_edit');
    ?>
    <tr class='even'>
        <td>
            <form id='form_contract' action='assets.php?action=contract'>
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            签订日期
                        </td>
                        <td>
                            <input type="text" id="signing_date" value='<?php print html_escape_request_var('signing_date');?>'/>
                        </td>
                        <td>
                            合同类型
                        </td>
                        <td>
                            <select id='type_id' onChange='applyFilter()'>
                            <option value='-1'<?php if (get_request_var('type_id') == '-1') {?> selected<?php }?>>请选择</option>
                                <?php
                                $assets_type_array = db_fetch_assoc("select * from plugin_assets_type where type='合同管理'");
                                if (cacti_sizeof($assets_type_array) > 0) {
									foreach ($assets_type_array as $assets_type) {
										print "<option value='" . $assets_type['id'] . "'"; if (get_request_var('type_id') == $assets_type['id']) { print ' selected'; } print '>' . $assets_type['name'] . "</option>\n";
									}
								}
                                ?>
                            </select>
                        </td>
                        <td>
                            合同记录
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
                    strURL  = 'assets.php?action=contract&header=false';
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    strURL += '&type_id='+$('#type_id').val();
                    strURL += '&signing_date='+$('#signing_date').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=contract&clear=1&header=false';
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_contract').submit(function(event) {
                        event.preventDefault();
                        applyFilter();
                    });
                    $("#signing_date").prop("readonly", true).datepicker({
                        changeMonth: false,
                        dateFormat: "yy-mm-dd",
                        onClose: function(selectedDate) {
                            applyFilter();
                        }
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
        $sql_where =$sql_where . " AND (assets_contract.name LIKE '%" . get_request_var('filter') . "%' OR assets_contract.number like '%" . get_request_var('filter') . "%' OR assets_contract.company like '%" . get_request_var('filter') . "%' OR user_auth.username like '%" . get_request_var('filter') . "%' or assets_contract.content_text like '%".get_request_var('filter')."%')";
    }
    if (get_request_var('type_id') != ''&&get_request_var('type_id') != '-1') {
        $sql_where =$sql_where . " AND assets_contract.type_id=" . get_request_var('type_id');
    } 
    if (get_request_var('signing_date') != '') {
        $sql_where =$sql_where . " AND assets_contract.signing_date like '%" . get_request_var('signing_date') . "%'";
    } 
    $total_rows = db_fetch_cell("SELECT count(*)  FROM plugin_assets_contract AS assets_contract LEFT JOIN user_auth AS user_auth ON assets_contract.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_contract.type_id=assets_type.id WHERE 1=1 $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $contract_list = db_fetch_assoc("SELECT assets_contract.*,user_auth.username AS modified_name,assets_type.name AS type_name  FROM plugin_assets_contract AS assets_contract LEFT JOIN user_auth AS user_auth ON assets_contract.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_contract.type_id=assets_type.id WHERE 1=1 $sql_where $sql_order $sql_limit");
    //cacti_log("SELECT assets_contract.*,user_auth.username AS modified_name,assets_type.name AS type_name  FROM plugin_assets_contract AS assets_contract LEFT JOIN user_auth AS user_auth ON assets_contract.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_contract.type_id=assets_type.id WHERE 1=1 " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=contract&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "合同", 'page', 'main');
    form_start('assets.php?action=contract', 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => __('ID'),        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'name'    => array('display' => "合同名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同名称"),
        'type_name'    => array('display' => "合同类型", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同类型"),
		'number'    => array('display' => "合同编号", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同编号"),
		'company'    => array('display' => "中标公司", 'align' => 'left',  'sort' => 'ASC', 'tip' => "中标公司"),
		'signing_date'    => array('display' => "签订日期", 'align' => 'left',  'sort' => 'ASC', 'tip' => "签订日期"),
        'due_date'    => array('display' => "到期日期", 'align' => 'left',  'sort' => 'ASC', 'tip' => "到期日期"),
        'status'    => array('display' => "合同状态", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同状态"),
		'description'    => array('display' => "合同备注", 'align' => 'left',  'sort' => 'ASC', 'tip' => "合同备注"),
        'modified_name'    => array('display' => "操作人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "操作人"),
        'last_modified' => array('display' => __('最后操作时间'), 'align' => 'left', 'sort' => 'ASC', 'tip' => "最操作时间")
		// 'file_path'    => array('display' => "附件", 'align' => 'left',  'sort' => 'ASC', 'tip' => "附件")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=contract');
    if (cacti_sizeof($contract_list)) {
        foreach ($contract_list as $contract) {
            form_alternate_row('line' . $contract['id'], true);
            form_selectable_cell($contract['id'], $contract['id'], '');
			form_selectable_cell(filter_value($contract['name'], get_request_var('filter'), 'assets.php?action=contract_edit&id=' . $contract['id']) , $contract['id']);
            form_selectable_cell($contract['type_name'],$contract['id'],'');
            form_selectable_cell(filter_value($contract['number'], get_request_var('filter')),$contract['id'],'');
            form_selectable_cell(filter_value($contract['company'], get_request_var('filter')),$contract['id'],'');
			form_selectable_cell(substr($contract['signing_date'],0,10), $contract['id'], '');
            form_selectable_cell(substr($contract['due_date'],0,10), $contract['id'], '');
            form_selectable_cell($contract['status'], $contract['id'], '');
            form_selectable_cell($contract['description'],$contract['id'],'');
            form_selectable_cell(filter_value($contract['modified_name'], get_request_var('filter')),$contract['id'],'');
            form_selectable_cell(substr($contract['last_modified'],0,16), $contract['id'], '');
			// $download_html = (isset($contract['file_path']) ? '<a href="'. $config['url_path'] . $contract['file_path'] .'" download="' . $contract['file_name']. '">下载</a> ':'-');
            // form_selectable_cell($download_html , $contract['id']);
            form_checkbox_cell($contract['name'], $contract['id']);
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($contract_list)) {
        print $nav;
    }
    draw_actions_dropdown($contract_actions);
    form_end();//分页form结束
}
//合同附件新增编辑页面
function contract_accessory_edit(){
    assets_tabs('contract');//合同管理选项卡
    $contract_id=get_request_var('contract_id');
    $contract= db_fetch_row_prepared('SELECT * FROM plugin_assets_contract WHERE id = ?', array($contract_id));
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_contract_accessory WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => '合同附件id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
        ),
        'contract_id' => array(
			'friendly_name' => '合同id',
			'method' => 'hidden',
			'value' => isset_request_var('contract_id') ? get_request_var('contract_id'):0
		),
		'name' => array(
			'friendly_name' => '合同附件名称(非必填项默认为附件的名称)',
			'method' => 'textbox',
			'max_length' => 50,
			'description' =>'请正确填写合同附件名称,非必填项默认为附件的名称',
			'value' => (isset($data['name']) ? $data['name']:'')
        ),
		'file_path' => array(
			'friendly_name' => '合同附件路径',
            'method' => 'file',
            'size' => '500',
            'description' =>'请选择需要上传的文件'
		),
		'description' => array(
			'friendly_name' => '合同附件描述',
			'method' => 'textbox',
			'max_length' => 100,
			'description' =>'请正确填写合同附件描述',
			'value' => (isset($data['description']) ? $data['description']:'')
		)
	);
	form_start('assets.php', 'contract_accessory_edit',true);//合同附件编辑form开始
	if (isset($data['id'])) {
		html_start_box('[' . $contract['name'] . ']' . __('合同附件 [编辑: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box('[' . $contract['name'] . ']' . __('合同附件 [新增]'), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="contract_accessory_save">
                <input type="button" class="" onclick="window.location.href='assets.php?action=contract';" value="返回" role="button">
                <input type="submit" class="" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
    <?php
    form_end(false);//表单编辑结束
}
//合同附件信息修改操作
function contract_accessory_save(){
    global $config;
    $save['id']           = get_filter_request_var('id');
    $save['contract_id']           = get_filter_request_var('contract_id');
    $save['name']         = form_input_validate(get_nfilter_request_var('name'), 'name', '', true, 3);
    $save['description']     = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if (is_error_message()) {
        header('Location: assets.php?action=contract_accessory_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        if (isset($_FILES["file_path"]) && !empty($_FILES["file_path"]["name"])) {
            $allowedExts = array("zip", "docx", "doc","xls", "xlsx", "rar","txt", "ppt", "pptx","log","pdf","jpg","png");
            $temp = explode(".", $_FILES["file_path"]["name"]);//临时文件路径
            $extension = end($temp);//文件扩展名
            if (in_array($extension, $allowedExts)){
                if ($_FILES["file_path"]["error"] > 0){
                    raise_message(2,"服务器文件上传错误",MESSAGE_LEVEL_ERROR);
                }else{
                    /**文件上传记录上传路径 */
                    $ext = pathinfo($_FILES["file_path"]["name"],PATHINFO_EXTENSION);
                    $now = time();
                    $file_path_dest = "plugins/assets/upload/contract/" . $now . "." . $ext;
                    move_uploaded_file($_FILES["file_path"]["tmp_name"], $config['base_path'] . '/' . $file_path_dest);
                    $save["file_name"] = $temp[count($temp)-2];
                    $save["file_path"] = $file_path_dest;
                    if($save['name']==''){
                        $save["name"] = $temp[count($temp)-2];
                    }
                    /**文件信息保存 */
                    $id=sql_save($save, 'plugin_assets_contract_accessory');
                    if ($id) {
                        raise_message(1);
                        //header('Location: assets.php?action=contract');
                        header('Location: assets.php?action=contract_accessory&contract_id=' . get_filter_request_var('contract_id') . '&header=true');
                        exit;
                    } else {
                        raise_message(2);
                        header('Location: assets.php?action=contract_accessory_edit&contract_id=' .get_filter_request_var('contract_id'). '&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                        exit;
                    }
                }
            }
            else{
                raise_message(2,"类型文件不支持",MESSAGE_LEVEL_ERROR);
                header('Location: assets.php?action=contract_accessory_edit&contract_id=' .get_filter_request_var('contract_id'). '&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                exit;
            }
        }else{//新增时上传文件为空
            if(!$save['id']){
                raise_message(2,"请选择需要上传的文件",MESSAGE_LEVEL_ERROR);
                header('Location: assets.php?action=contract_accessory_edit&contract_id=' .get_filter_request_var('contract_id'). '&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                exit;
            }
        }
    }
}
//合同附件记录页面
function contract_accessory(){
    global $config;
    global $contract_accessory_actions,$item_rows;
    $contract_id=get_request_var('contract_id');
    $contract= db_fetch_row_prepared('SELECT * FROM plugin_assets_contract WHERE id = ?', array($contract_id));
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
    validate_store_request_vars($filters, 'sess_contract_accessory');
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    html_start_box(__('[合同附件记录: %s]', html_escape($contract['name'])), '100%', false, '3', 'center', 'assets.php?action=contract_accessory_edit&contract_id=' . $contract_id);
    ?>
    <tr class='even'>
        <td>
            <form id='form_contract_accessory' action='assets.php?action=contract_accessory&contract_id=' . $contract_id>
                <input type="hidden" id="contract_id" value="<?php print $contract_id;?>">
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            合同附件记录
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
                    strURL  = 'assets.php?action=contract_accessory&header=false';
                    strURL += '&contract_id='+$('#contract_id').val();
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=contract_accessory&clear=1&header=false';
                    strURL += '&contract_id='+$('#contract_id').val();
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_contract_accessory').submit(function(event) {
                        event.preventDefault();
                        applyFilter();
                    });
                });
            </script>
        </td>
    </tr>
    <?php
    html_end_box();
    $sql_where=' AND assets_contract_accessory.contract_id='. $contract_id;
    if (get_request_var('filter') != '') {
        $sql_where =$sql_where . " AND (assets_contract_accessory.name LIKE '%" . get_request_var('filter') . "%' OR assets_contract_accessory.description like '%" . get_request_var('filter') . "%' OR user_auth.username like '%" . get_request_var('filter') . "%')";
    } 
    $total_rows = db_fetch_cell("SELECT count(*) FROM plugin_assets_contract_accessory AS assets_contract_accessory LEFT JOIN user_auth AS user_auth ON assets_contract_accessory.modified_by=user_auth.id WHERE 1=1 $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $contract_accessory_list = db_fetch_assoc("SELECT assets_contract_accessory.*,user_auth.username AS modified_name FROM plugin_assets_contract_accessory AS assets_contract_accessory LEFT JOIN user_auth AS user_auth ON assets_contract_accessory.modified_by=user_auth.id WHERE 1=1 $sql_where $sql_order $sql_limit");
    //cacti_log("SELECT assets_contract_accessory.*,user_auth.username AS modified_name FROM plugin_assets_contract_accessory AS assets_contract_accessory LEFT JOIN user_auth AS user_auth ON assets_contract_accessory.modified_by=user_auth.id WHERE 1=1 " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=contract_accessory&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "合同附件记录", 'page', 'main');
    form_start('assets.php?action=contract_accessory&contract_id=' . $contract_id, 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => 'ID',        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'name'    => array('display' => "文档名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文档名称"),
        'description'    => array('display' => "文档描述", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文档描述"),
        'modified_name'    => array('display' => "上传人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "上传人"),
        'last_modified' => array('display' => '上传时间', 'align' => 'left', 'sort' => 'ASC', 'tip' => "上传时间"),
        'file_path'    => array('display' => "下载", 'align' => 'left',  'sort' => 'ASC', 'tip' => "下载")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=contract_accessory&contract_id=' . $contract_id);
    if (cacti_sizeof($contract_accessory_list)) {
        foreach ($contract_accessory_list as $contract_accessory) {
            form_alternate_row('line' . $contract_accessory['id'], true);
            form_selectable_cell($contract_accessory['id'], $contract_accessory['id'], '');
            //form_selectable_cell(filter_value($contract_accessory['name'], get_request_var('filter'), 'assets.php?action=contract_accessory_edit&id=' . $contract_accessory['id'] . '&contract_id=' . $contract_accessory['contract_id']) , $contract_accessory['id']);
            form_selectable_cell(filter_value($contract_accessory['name'], get_request_var('filter')),$contract_accessory['id'],'');
            form_selectable_cell(filter_value($contract_accessory['description'], get_request_var('filter')),$contract_accessory['id'],'');
            form_selectable_cell(filter_value($contract_accessory['modified_name'], get_request_var('filter')),$contract_accessory['id'],'');
            form_selectable_cell(substr($contract_accessory['last_modified'],0,16), $contract_accessory['id'], '');
            $download_html = (isset($contract_accessory['file_path']) ? '<a href="'. $config['url_path'] . $contract_accessory['file_path'] .'" download="' . $contract_accessory['file_name']. '">下载</a> ':'-');
            form_selectable_cell($download_html , $contract_accessory['id']);
            form_checkbox_cell($contract_accessory['name'], $contract_accessory['id']);
            form_end_row();
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($contract_accessory_list)) {
        print $nav;
    }
    draw_actions_dropdown($contract_accessory_actions);
    form_end();//分页form结束
}
/**
 * 合同导入页面
 */
function contract_import(){
    $form_array = array(
        'description_1' => array(
            'friendly_name' => '导入说明:点击标题栏下载模版,数据列数要与模版一致',
            'method' => 'custom',
            'max_length' => 20,
            'value' => ''
        ),
        'file' => array(
            'friendly_name' => "导入CSV",
            'description' => "请选择需要导入的CSV文件",
            'size' => '50',
            'method' => 'file'
        ),
        // 'description_2' => array(
        //     'friendly_name' => '',
        //     'method' => 'custom',
        //     'max_length' => 20,
        //     'value' => ''
        // ),
        'do_import' => array(
            'method' => 'hidden',
            'value' => '1'
        ),
        'action' => array(
            'method' => 'hidden',
            'value' => 'contract_do_import'
        )
    );
    form_start('assets.php', 'contract_import',true);
    $buttons = array(
        array(
            'href'     => 'templates/contract.csv',
            'callback' => false,
            'title'    => "下载CSV模板",
            'class'    => 'fa fa-download'
        )
    );
    html_start_box("导入合同", '60%', '', '3', 'center', $buttons);
    draw_edit_form(
        array(
            'config' => array('no_form_tag' => true),
            'fields' => inject_form_variables($form_array)
        )
    );
    html_end_box(true, true);
    ?>
    <!-- 操作按钮 -->
    <table style='width:100%;text-align:center;'>
		<tr>
			<td class='saveRow'>
                <input type="button" class="" onclick="window.location.href='assets.php?action=contract';" value="取消" role="button">
                <input type="submit" id="submit" value="导入" role="button">
			</td>
		</tr>
    </table>
    <script type='text/javascript'>
        $(function() {
           
        });
    </script>
    <?php
}
/**
 * 合同导入操作
 */
function contract_do_import(){
    if (isset($_FILES["file"])&&!empty($_FILES["file"]["name"])) {
        $allowedExts = array("csv");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp);
        if ($_FILES["file"]["type"] == "application/vnd.ms-excel" && in_array($extension, $allowedExts)){
            if ($_FILES["file"]["error"] > 0){
                raise_message(2,"CSV上传错误",MESSAGE_LEVEL_ERROR);
            }else{
                $handle = fopen($_FILES['file']['tmp_name'],'r');
                setlocale(LC_ALL, 'zh_CN');
                $line_number = 0;
                while(($value = fgetcsv($handle)) !== FALSE){
                    if($line_number == 0){
                        $line_number++;
                        continue;
                    }
                    $value = eval('return '.iconv('gbk','utf-8',var_export($value,true)).';');
                    if(sizeof($value) != 7){
                        raise_message(2,"导入列数量不正确",MESSAGE_LEVEL_ERROR);
                        header('Location: assets.php?action=contract_import');
                        exit();
                    }
                    $count = db_fetch_row_prepared('SELECT * FROM plugin_assets_contract WHERE name = ? limit 1',array($value[0]));
                    if (!empty($count)) { //说明重复
                        continue;
                    }
                    $save = array();
                    $save['name'] = $value[0];
                    //$save['type_id'] = '';
                    $save['number'] = $value[1];
                    $save['company'] =  $value[2];
                    $save['signing_date'] = $value[3];
                    $save['due_date'] =  $value[4];
                    $save['content_text'] = $value[5];
                    $save['description'] =  $value[6];
                    //$save['is_alarm'] = ''
                    //$save['alarm_advance_day'] = '';
                    //$save['alarm_frequency'] = '';
                    //$save['notification_id'] ='';
                    $save['last_modified'] = date('Y-m-d H:i:s', time());
                    $save['modified_by'] = $_SESSION['sess_user_id'];
                    sql_save($save, 'plugin_assets_contract');
                }
                fclose($handle);
                header('Location: assets.php?action=contract');
                exit();
            }
        }else{
            raise_message(2,"请上传CSV类型文件",MESSAGE_LEVEL_ERROR);
            header('Location: assets.php?action=contract_import');
            exit();
        }
    }else{
        raise_message(2,"请选择需要导入的文件",MESSAGE_LEVEL_ERROR);
        header('Location: assets.php?action=contract_import');
        exit;
    }
}