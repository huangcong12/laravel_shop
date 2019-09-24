<?php

namespace App\Http\Controllers;

use App\CouponCode;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{
    public function show($code, Request $request)
    {
        // 不存在
        if (!$record = CouponCode::where('code', $code)->first()) {
            abort(404);
        }
        $record->checkAvailable($request->user());

        return $record;
    }
}
