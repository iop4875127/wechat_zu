<?php
require("database.php");
$openid = $_POST['openid'];
//获取该用户
$user_id = get("customer","openid",$openid);
if($user_id)
{
    $user_id = $user_id[0]['openid'];
    //获取该顾客的充值记录,按照时间倒序
    $order = sql_str("select * from `recharge_record` WHERE (`user_id` = '$user_id') ORDER BY `generated_time` DESC");//根据openid
    if($order)
	{
        for($i=0;$i<count($order);$i++)
		{
            //时间转换
            $order[$i]['generated_time'] = date("Y-m-d H:i:s",$order[$i]['generated_time']);
            //分转换为元表示
            $order[$i]['charge']/=100;
        }
        echo json_encode(['status' => 1,'data'=>$order]);
    }
    else
	{
		echo json_encode(['status' => -1]);
	}
}
else
{
    echo json_encode(['status' => 0]);
}
?>