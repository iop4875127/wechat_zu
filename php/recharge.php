<?php
require("database.php");
require("getpromotion.php");
$user = $_GET['user_id'];
$us = get("customer","openid",$user);
$shop = sql_str("select * from shop where ID=1");

if($us)
{
    $val = intval($_GET['charge']);
    $back = intval($_GET['back']);
	//$job_number = null;
    //if(isset($_POST['job_number']))$job_number = $_GET['job_number'];
	$job_number = $_GET['job_number'];
    
	//支付方式
    $pay = $_GET['pay'];

    $dict=['1','2','3','4','5','6','7','8','9','0'];
    $rnd = "";
    $rnd.=date("YmdHis");
    
	for($i=0;$i<2;$i++)
	{
        $rnd.=$dict[rand(0,count($dict)-1)];
    }
    for($i=0;$i<4;$i++)
	{
        $rnd.=$dict[rand(0,count($dict)-1)];
    }
	
	//--------------------------------------------------------------------------
	//计算技师的充卡提成
	/*
    $tech = sql_str("select * from technician where job_number='$job_number'");
    $ticheng = 0;
    if($tech && $tech[0]['type']==1)
	{
        $ticheng = $shop[0]['recharge_income'] * $val;
    }
	else
	{
        $ticheng = $shop[0]['recharge_income2'] * $val;
    }
	*/
	//--------------------------------------------------------------------------


    add("recharge_record",[
        ["record_id",$rnd],
        ["user_id",$us[0]['openid']],
        ['charge',$val],
        ['payment_method',$pay],
        ['job_number',$job_number],
        ['generated_time',time()],
        ['note','充值'],
        ['type',1],
        ]);
        
	add("recharge_record",[
            ["record_id",$rnd.'b'],
            ["user_id",$us[0]['openid']],
            ['charge',$back],
            ['payment_method',$pay],
            ['job_number',$job_number],
            ['generated_time',time()],
            ['note','充值送钱'],
            ['type',2],
    ]);

    $all_recharge = get('recharge_record','user_id',$user);
    $all_level = get('vip_information');
    $total_recharge = 0;
    $new_level = 0;
    
	foreach($all_recharge as $re)
	{
        $total_recharge+=$re['charge'];
    }
    foreach($all_level as $lv)
	{
        if($lv['necessary_charge_to_level_up']>$total_recharge)
		{
            $new_level = $lv['level'];
            break;
        }
    }
    set("customer","ID",$user,[['cash',$us[0]['cash']+$val],['level',$new_level]]);

    echo json_encode(['status'=>1]);
}
else
{
    echo json_encode(['status'=>0]);
}
?>