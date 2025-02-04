<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 日志记录中间件
namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\admin\UserLogService;

class UserLogMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $admin_user_id = admin_user_id();

        if ($admin_user_id) {
            $response_data = $response->getData();

            if (isset($response_data['code'])) {
                $admin_user_log['response_code'] = $response_data['code'];
            }
            if (isset($response_data['msg'])) {
                $admin_user_log['response_msg'] = $response_data['msg'];
            } else {
                if (isset($response_data['message'])) {
                    $admin_user_log['response_msg'] = $response_data['message'];
                }
            }

            $admin_user_log['admin_user_id'] = $admin_user_id;
            UserLogService::add($admin_user_log);
        }

        return $response;
    }
}
