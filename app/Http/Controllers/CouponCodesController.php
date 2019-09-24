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
        $record->checkAvailable();

        return $record;
    }
}
