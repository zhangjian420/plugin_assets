<?php
/**
 * 文档管理主函数
 */
ini_set('max_execution_time', '0');
$documents_actions = array(
    11 => __('删除')
);
//文档新增编辑页面
function documents_edit(){
    assets_tabs('documents');//文档管理选项卡
    $data = array();//页面显示data
    if (!isempty_request_var('id')) {
        $data= db_fetch_row_prepared('SELECT * FROM plugin_assets_documents WHERE id = ?', array(get_request_var('id')));
    }
	$field_array = array(
		'id' => array(
			'friendly_name' => '文档id',
			'method' => 'hidden',
			'value' => isset_request_var('id') ? get_request_var('id'):0
		),
		'name' => array(
			'friendly_name' => '文档名称',
			'method' => 'textbox',
			'max_length' => 50,
			'description' =>'请正确填写文档名称',
			'value' => (isset($data['name']) ? $data['name']:'')
        ),
        'type_id' => array(
			'friendly_name' => '文档类型',
			'method' => 'drop_sql',
			'description' => '请选择文档类型',
			'value' => isset($data['type_id']) ? $data['type_id'] : '0',
            'none_value' =>'请选择',
			'default' => '0',
			'sql' => "SELECT id, name FROM plugin_assets_type WHERE type='文档管理' ORDER BY id"
		),
		'path' => array(
			'friendly_name' => '文档路径',
            'method' => 'file',
            'size' => '500',
            'description' =>'请选择需要上传的文件'
		),
		'description' => array(
			'friendly_name' => '文档描述',
			'method' => 'textbox',
			'max_length' => 100,
			'description' =>'请正确填写文档描述',
			'value' => (isset($data['description']) ? $data['description']:'')
		)
	);
	form_start('assets.php', 'documents_edit',true);//文档编辑form开始
	if (isset($data['id'])) {
		html_start_box(__('文档 [编辑: %s]', html_escape($data['name'])), '100%', true, '3', 'center', '');
	} else {
		html_start_box(__('文档 [新增]'), '100%', true, '3', 'center', '');
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
                <input type="hidden" name="action" value="documents_save">
                <input type="button" class="" onclick="window.location.href='assets.php?action=documents';" value="返回" role="button">
                <input type="submit" class="" id="submit" value="保存" role="button">
			</td>
		</tr>
	</table>
    <?php
    form_end(false);//表单编辑结束
}
//文档信息修改操作
function documents_save(){
    global $config;
    $save['id']           = get_filter_request_var('id');
    $save['name']         = form_input_validate(get_nfilter_request_var('name'), 'name', '', false, 3);
    $save['type_id']     =form_input_validate(get_nfilter_request_var('type_id'), 'type_id', '', true, 3);
    $save['description']     = form_input_validate(get_nfilter_request_var('description'), 'description', '', true, 3);
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    $save['modified_by']   = $_SESSION['sess_user_id'];
    if (is_error_message()) {
        header('Location: assets.php?action=documents_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
		exit;
	}else{
        if (isset($_FILES["path"]) && !empty($_FILES["path"]["name"])) {
            $allowedExts = array("zip", "docx", "doc","xls", "xlsx", "rar","txt", "ppt", "pptx","log","pdf");
            $temp = explode(".", $_FILES["path"]["name"]);//临时文件路径
            $extension = end($temp);//文件扩展名
            if (in_array($extension, $allowedExts)){
                if ($_FILES["path"]["error"] > 0){
                    raise_message(2,"服务器文件上传错误",MESSAGE_LEVEL_ERROR);
                }else{
                    /**文件上传记录上传路径 */
                    $ext = pathinfo($_FILES["path"]["name"],PATHINFO_EXTENSION);
                    $now = time();
                    $file_path_dest = "plugins/assets/upload/documents/" . $now . "." . $ext;
                    move_uploaded_file($_FILES["path"]["tmp_name"], $config['base_path'] . '/' . $file_path_dest);
                    $save["path"] = $file_path_dest;
                }
            }
            else{
                raise_message(2,"类型文件不支持",MESSAGE_LEVEL_ERROR);
                header('Location: assets.php?action=documents_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                exit;
            }
        }else{//新增时上传文件为空
            if(!$save['id']){
                raise_message(2,"请选择需要上传的文件",MESSAGE_LEVEL_ERROR);
                header('Location: assets.php?action=documents_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
                exit;
            }
        }
        /**文件信息保存 */
        $id=sql_save($save, 'plugin_assets_documents');
        if ($id) {
            raise_message(1);
            header('Location: assets.php?action=documents');
            exit;
        } else {
            raise_message(2);
            header('Location: assets.php?action=documents_edit&id=' . (empty($id) ? get_nfilter_request_var('id') : $id));
             exit;
        }
    }
}
//文档管理列表入口
function documents(){
    global $config;
    global $documents_actions,$item_rows;
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
    validate_store_request_vars($filters, 'sess_documents');//
    /* ================= input validation ================= */
    /* if the number of rows is -1, set it to the default */
    if (get_request_var('rows') == -1) {
        $rows = read_config_option('num_rows_table');
    } else {
        $rows = get_request_var('rows');
    }
    html_start_box("新增文档", '100%', '', '3', 'center', 'assets.php?action=documents_edit');
    ?>
    <tr class='even'>
        <td>
            <form id='form_documents' action='assets.php?action=documents'>
                <table class='filterTable'>
                    <tr>
                        <td>
                            <?php print __('Search');?>
                        </td>
                        <td>
                            <input type='text' class='ui-state-default ui-corner-all' id='filter' size='25' value='<?php print html_escape_request_var('filter');?>'>
                        </td>
                        <td>
                            上传日期
                        </td>
                        <td>
                            <input type="text" id="last_modified" value='<?php print html_escape_request_var('last_modified');?>'/>
                        </td>
                        <td>
                            文档类型
                        </td>
                        <td>
                            <select id='type_id' onChange='applyFilter()'>
                            <option value='-1'<?php if (get_request_var('type_id') == '-1') {?> selected<?php }?>>请选择</option>
                                <?php
                                $assets_type_array = db_fetch_assoc("select * from plugin_assets_type where type='文档管理'");
                                if (cacti_sizeof($assets_type_array) > 0) {
									foreach ($assets_type_array as $assets_type) {
										print "<option value='" . $assets_type['id'] . "'"; if (get_request_var('type_id') == $assets_type['id']) { print ' selected'; } print '>' . $assets_type['name'] . "</option>\n";
									}
								}
                                ?>
                            </select>
                        </td>
                        <td>
                            文档记录
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
                    strURL  = 'assets.php?action=documents&header=false';
                    strURL += '&filter='+$('#filter').val();
                    strURL += '&rows='+$('#rows').val();
                    strURL += '&type_id='+$('#type_id').val();
                    strURL += '&last_modified='+$('#last_modified').val();
                    loadPageNoHeader(strURL);
                }
                //重置查询函数
                function clearFilter() {
                    strURL = 'assets.php?action=documents&clear=1&header=false';
                    loadPageNoHeader(strURL);
                }
                $(function() {
                    $('#refresh').click(function() {
                        applyFilter();
                    });
                    $('#clear').click(function() {
                        clearFilter();
                    });
                    $('#form_documents').submit(function(event) {
                        event.preventDefault();
                        applyFilter();
                    });
                    $("#last_modified").prop("readonly", true).datepicker({
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
        $sql_where =$sql_where . " AND (assets_documents.name LIKE '%" . get_request_var('filter') . "%' OR assets_documents.description like '%" . get_request_var('filter') . "%' OR user_auth.username like '%" . get_request_var('filter') . "%')";
    } 
    if (get_request_var('type_id') != ''&&get_request_var('type_id') != '-1') {
        $sql_where =$sql_where . " AND assets_documents.type_id=" . get_request_var('type_id');
    } 
    if (get_request_var('last_modified') != '') {
        $sql_where =$sql_where . " AND assets_documents.last_modified like '%" . get_request_var('last_modified') . "%'";
    } 
    $total_rows = db_fetch_cell("SELECT count(*)  FROM plugin_assets_documents AS assets_documents LEFT JOIN user_auth AS user_auth ON assets_documents.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_documents.type_id=assets_type.id WHERE 1=1 $sql_where");
    $sql_order = get_order_string();
    $sql_limit = ' LIMIT ' . ($rows*(get_request_var('page')-1)) . ',' . $rows;
    $documents_list = db_fetch_assoc("SELECT assets_documents.*,user_auth.username AS modified_name,assets_type.name AS type_name  FROM plugin_assets_documents AS assets_documents LEFT JOIN user_auth AS user_auth ON assets_documents.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_documents.type_id=assets_type.id WHERE 1=1 $sql_where $sql_order $sql_limit");
    cacti_log("SELECT assets_documents.*,user_auth.username AS modified_name,assets_type.name AS type_name  FROM plugin_assets_documents AS assets_documents LEFT JOIN user_auth AS user_auth ON assets_documents.modified_by=user_auth.id LEFT JOIN plugin_assets_type AS assets_type ON assets_documents.type_id=assets_type.id WHERE 1=1 " . $sql_where . $sql_order . $sql_limit);
    $nav = html_nav_bar('assets.php?action=documents&filter=' . get_request_var('filter'), MAX_DISPLAY_PAGES, get_request_var('page'), $rows, $total_rows, 5, "文档", 'page', 'main');
    form_start('assets.php?action=documents', 'chk');//分页表单开始
    print $nav;
    html_start_box('', '100%', '', '3', 'center', '');
    $display_text = array(
        'id'      => array('display' => __('ID'),        'align' => 'left', 'sort' => 'ASC', 'tip' => "ID"),
        'name'    => array('display' => "文档名称", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文档名称"),
        'type_name'    => array('display' => "文档类型", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文档类型"),
        'description'    => array('display' => "文档描述", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文档描述"),
        'modified_by'    => array('display' => "上传人", 'align' => 'left',  'sort' => 'ASC', 'tip' => "上传人"),
        'last_modified' => array('display' => __('上传时间'), 'align' => 'left', 'sort' => 'ASC', 'tip' => "上传时间"),
        'path'    => array('display' => "文件", 'align' => 'left',  'sort' => 'ASC', 'tip' => "文件")
    );
    html_header_sort_checkbox($display_text, get_request_var('sort_column'), get_request_var('sort_direction'), false,'assets.php?action=documents');
    if (cacti_sizeof($documents_list)) {
        foreach ($documents_list as $documents) {
            form_alternate_row('line' . $documents['id'], true);
            form_selectable_cell($documents['id'], $documents['id'], '');
            form_selectable_cell(filter_value($documents['name'], get_request_var('filter'), 'assets.php?action=documents_edit&id=' . $documents['id']) , $documents['id']);
            form_selectable_cell($documents['type_name'],$documents['id'],'');
            form_selectable_cell(filter_value($documents['description'], get_request_var('filter')),$documents['id'],'');
            form_selectable_cell(filter_value($documents['modified_name'], get_request_var('filter')),$documents['id'],'');
            form_selectable_cell(substr($documents['last_modified'],0,16), $documents['id'], '');
            $download_html = (isset($documents['path']) ? '<a href="'. $config['url_path'] . $documents['path'] .'" download="' . $documents['name']. '">下载</a> ':'-');
            form_selectable_cell($download_html , $documents['id']);
            form_checkbox_cell($documents['name'], $documents['id']);
            form_end_row();
        }
    } else {
        print "<tr class='tableRow'><td colspan='" . (cacti_sizeof($display_text)+1) . "'><em>" . "没有数据" . "</em></td></tr>\n";
    }
    html_end_box(false);//与谁对应
    if (cacti_sizeof($documents_list)) {
        print $nav;
    }
    draw_actions_dropdown($documents_actions);
    form_end();//分页form结束
}
