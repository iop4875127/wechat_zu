<?php
require("database.php");
$openid = $_POST['openid'];
$cus = get("customer","openid",$openid);
if(!$cus)
{
    echo json_encode(['status'=>0]);
    exit();
}
$id = $cus[0]['openid'];
//获取充值总额
$str = "select sum(`recharge_record`.`charge`) AS `charge` from  `recharge_record` where ( '$id' = `recharge_record`.`user_id`)";//根据userid recharge_record 计算充进去的钱
$charge = sql_str($str);
//获取消费总额,仅计算用会员卡支付的订单
$str = "select sum(pay_amount) as pay from consumed_order where user_id='$id' and (state!=3 and state != 0) and payment_method=11";//根据userid consumed_order 计算微信会员花出去的钱
$use = sql_str($str);
//计算目前账户内余额
$charge=$charge[0]['charge']-$use[0]['pay'];

if($cus)
{
    $lv = get("vip_information","level",$cus[0]['level']);
    if($lv)
	{
        $lv = $lv[0]['name'];
    }
	else
	{
        $lv="非会员";
    }
	
    echo json_encode([
        'status'=>1,
        'name'=>$cus[0]['name'],
        'head'=>$cus[0]['head'],
        'phone_number'=>$cus[0]['phone_number'],
        'id_number'=>$cus[0]['id_number'],
        'level'=>$lv,
        'gender'=>$cus[0]['gender']==1?"男":"女",
        'cash'=>$charge
        
    ]);
}
else
{
    echo json_encode(['status'=>0]);
}
?>