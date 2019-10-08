<?php

use Moontoast\Math\BigNumber;

function test_helper()
{
    return 'OK';
}

/**
 * 根据当前页面生成页面的 class
 * @return mixed
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 获取 Moontoast 的数字对象
 */
function big_number($number, $scale = 2)
{
    return new BigNumber($number, $scale);
}
