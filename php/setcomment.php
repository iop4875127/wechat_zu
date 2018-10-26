<?php
require("database.php");

$openid=$_POST['openid'];
$id=$_POST['order_id'];
$service_id = $_POST['service_id'];
$rate=$_POST['rate'];
$job_number = $_POST['job_number'];
$comment=$_POST['comment'];

$user=get('customer','openid',$openid);
if($user)
{

    foreach($service_id as $idx => $svid){
        $jbnb = $job_number[$idx];
        $arr_jbnb = ['job_number',$jbnb];

        $rt =$rate[$idx];
        $arr_rt = ['score',$rt];

        $cmt = $comment[$idx];
        $arr_cmt = ['comment',$cmt];

        $cus_id = $user[0]['ID'];
        $arr_cusid = ['customer_id',$cus_id];

        $arr_id = ['order_id',$id];

        $arr_svid = ['service_id',$svid];

        $arr_time = ['time',time()];
        add('rate',[$arr_id, $arr_jbnb, $arr_rt, $arr_cmt, $arr_svid, $arr_cusid, $arr_time]);
    }
    set('consumed_order','order_id',$id,[['state',5]]);
    echo(json_encode(['status'=>1]));
}
else
{
    echo(json_encode(['status'=>0]));
}