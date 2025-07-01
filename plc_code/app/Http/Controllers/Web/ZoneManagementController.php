<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ZoneManagementController extends Controller
{
    public function index()
    {
        hasPermissionTo(['View Zones', 'View Roads']);

        return view('web.lists-page', [
            'components' => [
                [
                    'name' => 'zone',
                    'permission' => 'View Zones',
                ],
                [
                    'name' => 'road',
                    'permission' => 'View Roads',
                ],
            ],
            'pageTitle' => 'Zone Management',
        ]);
    }
}
