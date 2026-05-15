<?php

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryAdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $roots = Category::with('allChildren')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->orderBy('priority')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('roots'));
    }

    public function create(): View
    {
        $parents = $this->getSelectableParents();
        return view('categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['rewrite'] = Str::slug($data['name']);
        $data['img'] = $data['img'] ?? '';
        $data['description'] = $data['description'] ?? '';
        $data['priority'] = $data['priority'] ?? 0;
        $data['is_visible'] = $data['is_visible'] ?? true;
        $data['youtube'] = $this->filterYoutube($data['youtube'] ?? null);

        Category::create($data);

        return redirect()->route('categories.index')
            ->with(['message' => 'Kategoria została utworzona.', 'alert-type' => 'success']);
    }

    public function edit(Category $category): View
    {
        $parents = $this->getSelectableParents($category);
        $youtube = $category->youtube ?? [];
        $category->load('chimneyAttributes.options');

        return view('categories.edit', compact('category', 'parents', 'youtube'));
    }

    public function update(UpdateCategoryAdminRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['name']) && $category->rewrite === Str::slug($category->name)) {
            $data['rewrite'] = Str::slug($data['name']);
        }

        $data['youtube'] = $this->filterYoutube($data['youtube'] ?? null);

        $category->update($data);

        return redirect()->route('categories.edit', $category->id)
            ->with(['message' => 'Zmiany zostały zapisane.', 'alert-type' => 'success']);
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($this->treeHasProducts($category)) {
            return redirect()->back()
                ->with(['message' => 'Nie można usunąć — kategoria lub jej podkategorie posiadają produkty.', 'alert-type' => 'error']);
        }

        $this->deleteTree($category);

        return redirect()->route('categories.index')
            ->with(['message' => 'Kategoria i wszystkie podkategorie zostały usunięte.', 'alert-type' => 'success']);
    }

    private function treeHasProducts(Category $category): bool
    {
        if ($category->products()->exists()) {
            return true;
        }

        foreach ($category->children as $child) {
            if ($this->treeHasProducts($child)) {
                return true;
            }
        }

        return false;
    }

    private function deleteTree(Category $category): void
    {
        foreach ($category->children as $child) {
            $this->deleteTree($child);
        }

        $category->delete();
    }

    private function getSelectableParents(?Category $exclude = null): array
    {
        $roots = Category::with('allChildren')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->orderBy('priority')
            ->orderBy('name')
            ->get();

        $options = [];

        foreach ($roots as $root) {
            if ($exclude && $root->id === $exclude->id) {
                continue;
            }
            $options[$root->id] = $root->name;
            $this->flattenChildrenForSelect($root->allChildren, $options, 1, $exclude);
        }

        return $options;
    }

    private function flattenChildrenForSelect($children, array &$options, int $depth, ?Category $exclude): void
    {
        foreach ($children as $child) {
            if ($exclude && $child->id === $exclude->id) {
                continue;
            }
            $options[$child->id] = str_repeat('— ', $depth) . $child->name;
            $this->flattenChildrenForSelect($child->allChildren, $options, $depth + 1, $exclude);
        }
    }

    private function filterYoutube(?array $youtube): ?array
    {
        if (empty($youtube)) {
            return null;
        }

        $filtered = array_values(array_filter($youtube, fn($item) => !empty($item['link'])));

        return empty($filtered) ? null : $filtered;
    }
}
