<?php
function getshop($id){
    $shop=get('shop');
    return [
        'status'=>1,
        'data'=>[
            'name'=>$shop[0]['name'],
            'position'=>$shop[0]['position'],
            'location'=>'../html/map.html?location='.$shop[0]['position'],
            'open_time'=>$shop[0]['open_time'],
            'close_time'=>$shop[0]['close_time']
            ]
        ];
}