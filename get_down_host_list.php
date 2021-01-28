<?php

$data = exec("php /var/www/html/nagios-scripts/statusJson.php",$op,$int);
$data = json_decode(implode("", $op),TRUE);


$hosts = $data['hosts'];

$down_hosts = array();
$conditional_time = strtotime("-1 week");
$conditional_time = strtotime("-7 hour");
function get_days_hours_min($d1,$d2){
    $d1 = new DateTime(date("Y-m-d H:i:s", $d1));
    $d2 = new DateTime($d2);
    $interval = $d1->diff($d2);
    return $interval->d." days ".$interval->h." hours ".$interval->i." min";
}

foreach ($hosts as $key => $value) {
    $last_hard_state = isset($value['last_hard_state']) ? $value['last_hard_state'] : '';
    $scheduled_downtime_depth = isset($value['scheduled_downtime_depth']) ? $value['scheduled_downtime_depth'] : '';
    $problem_has_been_acknowledged = isset($value['problem_has_been_acknowledged']) ? $value['problem_has_been_acknowledged'] : '';
    $last_hard_state_change = isset($value['last_state_change']) ? $value['last_state_change'] : '';
    
    if ($last_hard_state !=0 && $scheduled_downtime_depth == 0 && $problem_has_been_acknowledged == 0 && $last_hard_state_change <=  $conditional_time ) {
        $down_hosts[$value['host_name']]['duration'] = get_days_hours_min($last_hard_state_change,date("Y-m-d H:i:s"));
    }
}

print_r($down_hosts);