<?php

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Entities\Newsletter;
use App\Entities\Product;
use App\Http\Requests\CreateNewsletterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('newsletter.index', [
            'newsletters' => Newsletter::paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('newsletter.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateNewsletterRequest $request
     * @return RedirectResponse
     */
    public function store(CreateNewsletterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $request['description'] = nl2br($data['description']);
        Newsletter::create($data);

        return redirect()->route('newsletter.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Newsletter $newsletter
     * @return View
     */
    public function edit(Newsletter $newsletter): View
    {
        return view('newsletter.edit', [
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CreateNewsletterRequest $request
     * @param Newsletter $newsletter
     * @return RedirectResponse
     */
    public function update(CreateNewsletterRequest $request, Newsletter $newsletter): RedirectResponse
    {
        $data = $request->validated();
        $request['description'] = nl2br($data['description']);
        $newsletter->update($data);

        return redirect()->route('newsletter.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Newsletter $newsletter
     * @return RedirectResponse
     */
    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        $newsletter->delete();

        return redirect()->route('newsletter.index');
    }

    public function loadJson(Request $request): RedirectResponse
    {
        if ($request->file('file')) {
            $file = $request->file('file');
            $json = json_decode(file_get_contents($file->getRealPath()), true);

            foreach ($json as $category => $products) {
                foreach ($products as $product) {
                    Newsletter::create([
                        'category' => $category,
                        'product' => $product,
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function generate(string $category): View
    {
        $products = collect([Newsletter::where('category', $category)->first()])->each(function (&$product) {
            $product = Product::where('name', $product->product)->first();
        });


        return view('newsletter.generate', [
            'products' => $products,
        ]);
    }
}
