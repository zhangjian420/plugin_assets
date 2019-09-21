<?php
chdir(dirname(__FILE__));
chdir('../../');
include_once('./include/global.php');
cacti_log("<<<<<<<<<<<<<<<<合同到期告警定时任务执行>>>>>>>>>>>>>>>> " . date('Y-m-d H:i:s', time()));
//$assets_contract_array = db_fetch_assoc("select assets_contract.*,notification_lists.emails from plugin_assets_contract as assets_contract left join plugin_notification_lists as notification_lists on assets_contract.notification_id=notification_lists.id where 1=1 and assets_contract.is_alarm='on' and notification_lists.emails is not null and assets_contract.signing_date<=now() and assets_contract.due_date >=now()");
$assets_contract_array = db_fetch_assoc("select assets_contract.*,notification_lists.emails from plugin_assets_contract as assets_contract left join plugin_notification_lists as notification_lists on assets_contract.notification_id=notification_lists.id where 1=1 and assets_contract.is_alarm='on' and assets_contract.signing_date<=now() and assets_contract.due_date >=now()");
foreach($assets_contract_array as $assets_contract) {
     $due_date=$assets_contract['due_date'];//合同到期日期
     $alarm_advance_day=$assets_contract['alarm_advance_day'];//告警提前天数
     $alarm_frequency=$assets_contract['alarm_frequency'];//告警频率每天每周
     $current_date=date('Y-m-d', time());//今天
     if(strtotime($current_date)>=strtotime("-" . $alarm_advance_day . " day",strtotime($due_date))&&strtotime($current_date)<=strtotime($due_date)){
        if($alarm_frequency=='每天'){//每天告警
            doAlarm($assets_contract,$current_date);
        }
        if($alarm_frequency=='每周'){
            $week=date("l",strtotime($current_date));//当前周几
            if($week=='Monday'){
                doAlarm($assets_contract,$current_date);
            }
        }
        if(strtotime($current_date)==strtotime($assets_contract['due_date'])){//没有配置当天发送邮件告警即可
                doAlarm($assets_contract,$current_date);
        }
     }
}
//已经到期合同状态修改
$assets_contract_due_array = db_fetch_assoc("select * from plugin_assets_contract where 1=1 and status!='已到期' and due_date <now()");
foreach($assets_contract_due_array as $assets_contract) {
    $save=array();
    $save['id']=$assets_contract['id'];
    $save['status']='已到期';
    $save['last_modified'] = date('Y-m-d H:i:s', time());
    sql_save($save, 'plugin_assets_contract');
}
/**
 * 执行告警操作
 */
function doAlarm($assets_contract,$alram_date){
    /************************************发送邮件begin*************************************/
    $assets_contract_alarm= db_fetch_row_prepared("SELECT * FROM plugin_assets_contract_alarm WHERE contract_id = " . $assets_contract['id'] . " and alram_date = '" . $alram_date . "'");
    if(isset($assets_contract_alarm['id'])){//数据库中已经存在记录
        return;
    }
    $assets_contract_alarm['contract_id']=$assets_contract['id'];
    $assets_contract_alarm['alram_date']=$alram_date;
    if(isset($assets_contract['emails'])){
        $msg='[' . $assets_contract['name'] . ']将在' . $assets_contract['due_date'] . '到期！请查看合同详情！';
        $msg='<h3>' .$msg . '</h3>';
        $assets_contract_alarm['subject']=$assets_contract['name'] . '告警';
        $assets_contract_alarm['body']=$msg;
        $assets_contract_alarm['to_emails']=$assets_contract['emails'];
        $errors = send_mail($assets_contract['emails'],"",$assets_contract_alarm['subject'],$assets_contract_alarm['body'],"","",true);
        if($errors == ''){
            $assets_contract_alarm['status']='邮件发送成功';
        }else{
            $assets_contract_alarm['status']='邮件发送失败';
        }
    }
    $assets_contract_alarm['description']=$assets_contract['name'] . '告警';
    $assets_contract_alarm['last_modified'] = date('Y-m-d H:i:s', time());
    sql_save($assets_contract_alarm, 'plugin_assets_contract_alarm');
    /************************************发送邮件end*************************************/
    if(strtotime($alram_date)==strtotime($assets_contract['due_date'])){
        $save=array();
        $save['id']=$assets_contract['id'];
        $save['status']='已到期';
        $save['last_modified'] = date('Y-m-d H:i:s', time());
        sql_save($save, 'plugin_assets_contract');
    }
    if(strtotime($alram_date)<strtotime($assets_contract['due_date'])){
        $save=array();
        $save['id']=$assets_contract['id'];
        $save['status']='即将到期';
        $save['last_modified'] = date('Y-m-d H:i:s', time());
        sql_save($save, 'plugin_assets_contract');
    }
}