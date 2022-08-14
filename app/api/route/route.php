<?php

use think\facade\Route;


Route::group(function () {
    //下单接口
    Route::get('submit', 'index/submit');
    Route::post('submit_api', 'index/submit_api');
    
    Route::get('pay/:order', 'index/pay');
    Route::get('get/:order', 'index/pagelisten');
    Route::post('listen_heart', 'listen/heart');
    Route::get('listen_test', 'listen/test');
    Route::post('listen_notify', 'listen/notify');
    
    
    //计划任务
    Route::get('overtime', 'async/overtime');
    Route::get('clean', 'async/clean');
    Route::get('limit', 'async/limit');
    Route::get('resets', 'async/resets');
    Route::get('notify', 'async/notify');

});

