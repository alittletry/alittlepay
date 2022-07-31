<?php


namespace learn\middleware;


use app\Request;
use learn\interfaces\MiddlewareInterface;
use think\Response;

/**
 * Class AllowOriginMiddleware
 * @package app\http\middleware
 */
class AllowOriginMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed|Response
     */
    public function handle(Request $request, \Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authori-zation, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        header('Access-Control-Max-Age: 1728000');
        if ($request->isOptions()) {
            $response = Response::create("ok");
        } else {
            $response = $next($request);
        }
        return $response;
    }
}