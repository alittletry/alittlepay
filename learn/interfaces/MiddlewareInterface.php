<?php


namespace learn\interfaces;


use app\Request;

/**
 * Interface MiddlewareInterface
 * @package learn\interfaces
 */
interface MiddlewareInterface
{
    public function handle(Request $request, \Closure $next);
}