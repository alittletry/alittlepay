<?php

use think\facade\Route;


Route::group(function () {


    //下单接口
    Route::get('notify', 'index/testnotify');


});

