<?php


namespace app\admin\controller\admin;

use app\common\service\admin\DataService;
use app\common\validate\admin\DataValidate;
use think\facade\Request;
use app\common\service\admin\MenuService;
use think\Request as Requests;



class Data
{


    /**
     * 后台首页统计
     * @return \json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info(Requests $request)
    {
        $param['start_time'] = Request::param('start_time/s', '');
        $param['end_time'] = Request::param('end_time/s', '');

        validate(DataValidate::class)->scene('info')->check($param);

        $start_time = $param['start_time'];
        $end_time = $param['end_time'];
        if($end_time){
            $end_time = strtotime($end_time)+(3600*24*1);
        }else{
            $end_time = strtotime(date('Y-m-d'))+(3600*24*1);
        }
        if($start_time){
            $start_time = strtotime($start_time);
        }else{
            $start_time = $end_time - (3600*24*7);
        }
        if($start_time >= $end_time){
            exception('结束时间必须大于结束时间' );
        }
        $where = [];
        if ($request->where){
            $where =  $request->where;
        }
        $data = DataService::info($start_time,$end_time,$where);
        return success($data);
    }


}
