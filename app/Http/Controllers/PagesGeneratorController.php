<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesGeneratorController extends Controller
{
    public function getPages()
    {
        $pages = [
            ['title' => 'test',
                'childs' => [
                    ['title' => 'test1',
                        'childs' => []],
                    ['title' => 'test3',
                        'childs' => []],

                ]
            ],
            ['title' => 'test4',
                'childs' => []],
        ];
        return view('pages.index')->withPages($pages);
    }

    public function createPage()
    {
        return view('pages.index');
    }
}
