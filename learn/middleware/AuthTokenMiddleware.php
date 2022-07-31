<?php


namespace learn\middleware;


use app\api\model\user\User;
use app\Request;
use learn\exceptions\AuthException;
use learn\interfaces\MiddlewareInterface;

/**
 * token验证
 * Class AuthTokenMiddleware
 * @package learn\middleware
 */
class AuthTokenMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @param bool $force
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, bool $force = true)
    {
        $authInfo = null;
        $token = $request->header('Authori-zation');
        if(!$token)  $token = $request->header('Authorization');
        try {
            $authInfo = User::parseToken($token);
        } catch (AuthException $e) {
            if ($force)
                return app('json')->make($e->getCode(), $e->getMessage());
        }
        if (!is_null($authInfo)) {
            Request::macro('user', function () use (&$authInfo) {
                return $authInfo;
            });
        }
        Request::macro('isLogin', function () use (&$authInfo) {
            return !is_null($authInfo);
        });
        Request::macro('uid', function () use (&$authInfo) {
            return is_null($authInfo) ? 0 : $authInfo['uid'];
        });
        return $next($request);
    }
}