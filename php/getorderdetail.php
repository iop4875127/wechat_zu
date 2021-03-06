<?php
require("database.php");
$order_id = $_POST['id'];

$co = get("consumed_order","order_id",$order_id);
if(count($co)<=0)
{
    echo json_encode(['status'=>0]);
    exit();
}
$customer = get('customer','openid',$co[0]['user_id']);
if(count($customer)<=0)
{
    echo json_encode(['status'=>-1]);
    exit();
}
$co[0]['user_id'] = $customer[0]['name'];

$so = get("service_order","order_id",$order_id);
$shop = get("shop");
$logo = $shop[0]['logo'];
$shop = $shop[0]['phone'];
if(is_null($logo))$logo="/photo/wash-foot.jpg";

$info = [];

foreach($so as $svod)
{
    $tech = get("technician","job_number",$svod['job_number']);
    //如果是服务，则查找service_type表
    if($svod['service_type'] == 1)
	{
        $svs = get("service_type","ID",$svod['item_id']);
    }
    //如果是茶艺，则查找item_type表
    else if($svod['service_type'] == 3)
	{
        $svs = get("item_type","ID",$svod['item_id']);
    }
    $appo = ['appoint_time'=>$co[0]['appoint_time'],'people_num'=>$co[0]['user_num']];
    if($tech && $svs)
	{
        array_push($info,[
            "technician_name"=>$tech[0]['name'],
            "technician_head"=>$tech[0]['photo'],
            "job_number"=>$tech[0]['job_number'],
            "service_name"=>$svs[0]['name'],
            "service_id"=>$svs[0]['ID'],
            "price"=>$svs[0]['price']/100,
            "time"=>$co[0]['generated_time'],
            "pay_way"=>$co[0]['payment_method'],
            "user_id"=>$customer[0]['name'],
            "service_time"=>$svod['service_type']==1?$svs[0]['duration']:0,
            "appo"=>$appo,
            "phone"=>$shop,
            "logo"=>$logo,
        ]);
    }
	else
	{
        array_push($info,[
            "technician_name"=>"",
            "job_number"=>"",
            "service_name"=>"",
            "price"=>0,
            "time"=>"",
            "pay_way"=>"",
            "user_id"=>"",
            "appo"=>$appo,
            "phone"=>$shop,
            "logo"=>$logo,
        ]);
    }
}

//------------获取技师评价的tag--------------------
$tag = sql_str("select * from rate_tag");
if($tag)
{
    for($i=0;$i<count($tag);$i++)
	{
        $tag[$i] = array_merge($tag[$i],['check'=>false]);
    }
}

$tags = [];
for($i=0;$i<count($info);$i++)
{
    array_push($tags,$tag);
}

//--------------------------------------------------
echo json_encode(['data'=>$info,'co'=>$co,'tags'=>$tags]);
?>