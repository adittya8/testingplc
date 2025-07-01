<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class GroupingController extends Controller
{
    public function index()
    {
        return view('web.lists-page', [
            'components' => [
                [
                    'name' => 'group',
                    'permission' => 'View Groups',
                ],
                [
                    'name' => 'sub-group',
                    'permission' => 'View Sub-Groups',
                ],
            ],
            'pageTitle' => 'Grouping'
        ]);
    }
}
