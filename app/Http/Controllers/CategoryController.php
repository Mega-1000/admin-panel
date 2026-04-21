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
        $roots = Category::with(['children.children'])
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
        if ($category->children()->exists()) {
            return redirect()->back()
                ->with(['message' => 'Nie można usunąć kategorii posiadającej podkategorie.', 'alert-type' => 'error']);
        }

        if ($category->products()->exists()) {
            return redirect()->back()
                ->with(['message' => 'Nie można usunąć kategorii posiadającej produkty.', 'alert-type' => 'error']);
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with(['message' => 'Kategoria została usunięta.', 'alert-type' => 'success']);
    }

    private function getSelectableParents(?Category $exclude = null): array
    {
        $query = Category::with('children')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->orderBy('name');

        if ($exclude) {
            $query->where('id', '!=', $exclude->id);
        }

        $roots = $query->get();
        $options = [];

        foreach ($roots as $root) {
            $options[$root->id] = $root->name;

            foreach ($root->children as $child) {
                if ($exclude && $child->id === $exclude->id) {
                    continue;
                }
                $options[$child->id] = '— ' . $child->name;
            }
        }

        return $options;
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
