<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\UserAddress;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAddressesController extends Controller
{
    /**
     * 收货地址列表页
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return view('user_addresses/index', [
            'addresses' => $request->user()->address
        ]);
    }

    /**
     * 创建收货地址
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        return view('user_addresses/create_and_edit', [
            'address' => new UserAddress()
        ]);
    }

    /**
     * 新增收货地址
     *
     * @param UserAddressRequest $request
     */
    public function store(UserAddressRequest $request)
    {
        $request->user()->address()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }
}
