<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class LuminariesConfigController extends Controller
{
    public function index()
    {
        hasPermissionTo(['View Brands', 'View Luminary-Types']);

        return view('web.lists-page', [
            'components' => [
                [
                    'name' => 'brand',
                    'permission' => 'View Brands',
                ],
                [
                    'name' => 'luminary-type',
                    'permission' => 'View Luminary-Types',
                ],
            ],
            'pageTitle' => 'Luminaries Config'
        ]);
    }
}
