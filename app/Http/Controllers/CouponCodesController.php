<?php

namespace App\Http\Controllers;

use App\CouponCode;
use Illuminate\Support\Carbon;

class CouponCodesController extends Controller
{
    public function show($code)
    {
        // 不存在
        if (!$record = CouponCode::where('code', $code)->first()) {
            abort(404);
        }
        // 已被使用
        if (!$record->enabled) {
            abort(404);
        }
        if ($record->total - $record->used <= 0) {
            return response()->json(['msg' => '该优惠券已被兑换完'], 403);
        }

        if ($record->not_before && $record->not_before->gt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券还不能使用'], 403);
        }
        if ($record->not_after && $record->not_after->lt(Carbon::now())) {
            return response()->json(['msg' => '该优惠券已过期'], 403);
        }

        return $record;
    }
}
