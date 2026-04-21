<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\CustomPageCategory;
use App\Entities\CustomPage;

class CustomPagesController extends Controller
{

    public function getPages()
    {
        $pages = CustomPageCategory::orderBy('order', 'asc')->with('pages')->get()->keyBy('id');
        $pagesContent = CustomPage::all();
        $this->buildPages($pages);
        return ['tree' => $pages->values(), 'pages' => $pagesContent];
    }

    public function buildPages($pages)
    {
        foreach ($pages as $page) {
            $content = $this->addToContentArray($page->pages, $page);
            $page->content = $content;
            if (!empty($page->parent_id)) {
                $content = $this->addToContentArray($page, $pages[$page->parent_id]);
                $pages[$page->parent_id]->content = $content;
                unset($pages[$page->id]);
            }
        }
    }

    public function addToContentArray($content, $parent)
    {
        if (!isset($parent->content)) {
            if (is_array($content)) {
                $pc = $content;
            } else {
                $pc = $content;
            }
        } else if (is_array($content)) {
            $pc = array_merge($parent->content, $content);
        } else {
            $pc = $parent->content;
            $pc [] = $content;
        }
        return $pc;
    }

}
