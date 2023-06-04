<?php

namespace App\Services;

use App\Entities\Category;
use App\Entities\ChimneyAttribute;
use App\Helpers\MessagesHelper;
use App\Repositories\Categories;
use App\Repositories\ProductStockLogs;
use App\Traits\Paginatable;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use ParseError;

class ProductsService
{
    use Paginatable;

    /**
     * @throws Exception
     */
    public function getCategoryFromRequest($request)
    {
        foreach ($request->attr as $id => $value) {
            $attribute = ChimneyAttribute
                ::with(['category' => function ($q) {
                    $q->with(['chimneyAttributes' => function ($q) {
                        $q->with('options');
                    }]);
                    $q->with(['chimneyProducts' => function ($q) {
                        $q->with('replacements');
                    }]);
                }])
                ->find($id);
            if (!$attribute) {
                throw new Exception("Wrong attribute ID ($id)");
            }
        }

        return $attribute?->category;
    }

    /**
     * @throws Exception
     */
    public function getParamsFromRequest($request, $category): array
    {
        $params = [];

        foreach ($category->chimneyAttributes as $attribute) {
            if (empty($request->attr[$attribute->id])) {
                throw new Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
            }
            $value = null;
            foreach ($attribute->options as $option) {
                if ($request->attr[$attribute->id] == $option->id) {
                    $value = $option->name;
                    break;
                }
            }
            if (empty($value)) {
                if (count($attribute->options) > 0) {
                    throw new Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
                }
                $value = $request->attr[$attribute->id];
                $value = trim(str_replace(',', '.', $value));
                if (filter_var($value, FILTER_VALIDATE_FLOAT) === false || $value < 0) {
                    throw new Exception("Missing or incorrect attribute \"{$attribute->name}\" (ID {$attribute->id})");
                }
                $value = number_format($value, 2, '.', '');
            }
            $params[$attribute->column_number] = $value;
        }

        return $params;
    }

    public function getReplacements($category, $params): array
    {
        $out = [
            'replacements' => [],
            'products' => [],
            'products_replace' => []
        ];

        foreach ($category->chimneyProducts as $product) {
            if (count($product->replacements) == 0) {
                continue;
            }
            $id = count($out['replacements']) + 1;
            $replacements = [
                'description' => $product->replacement_description,
                'img' => $product->replacement_img,
                'id' => $id,
                'products' => []
            ];
            $exists = false;
            foreach ($product->replacements as $replacement) {
                $symbol = $this->replaceParams($replacement->product, $params);
                $quantity = $this->getQuantity($replacement->quantity, $params);
                if ($quantity == 0) {
                    continue;
                }
                $replacements['products'][$symbol] = $quantity;
                $out['products'][$this->replaceParams($product->product_code, $params)] = $id;
                $out['products_replace'][$symbol] = [
                    'quantity' => $quantity,
                    'id' => $id
                ];
                $exists = true;
            }
            if ($exists) {
                $out['replacements'][$id] = $replacements;
            }
        }
        return $out;
    }

    private function replaceParams($text, $params)
    {
        foreach ($params as $key => $value) {
            $text = str_replace("[$key]", $value, $text);
        }
        return $text;
    }

    private function getQuantity($formula, $params)
    {
        $formula = $this->replaceParams($formula, $params);
        $formula = str_replace(',', '.', $formula);
        $wrongChars = preg_replace('/(ceil|round|floor|\d|\.|\+|-|\*|\/|\(|\))/m', '', $formula);
        if (!empty($wrongChars)) {
            return 0;
        }
        try {
            return eval("return $formula;");
        } catch (ParseError $e) {
            return 0;
        }
    }

    public function getProductsFromParams($params, $category)
    {
        $productsData = [];
        foreach ($category->chimneyProducts as $product) {
            $code = $this->replaceParams($product->product_code, $params);
            $quantity = $this->getQuantity($product->formula, $params);
            if ($quantity == 0) {
                continue;
            }
            $productsData[$code] = [
                'quantity' => round($quantity, 2),
                'optional' => $product->optional
            ];
        }

        $products = Categories::getProductsForSymbols(array_keys($productsData));

        foreach ($products as $product) {
            $product->quantity = $productsData[$product->symbol]['quantity'];
            $product->optional = $productsData[$product->symbol]['optional'];
            $product->id = $product->product_id;
        }

        return $products;
    }

    /**
     * @throws Exception
     */
    public function attachReplaceParams($products, $replaceProducts, $replacements): void
    {
        foreach ($products as $product) {
            if (isset($replacements['products'][$product->symbol])) {
                $product->changer = $replacements['products'][$product->symbol];
            } else {
                $product->changer = 0;
            }
        }

        foreach ($replaceProducts as $product) {
            if (!isset($replacements['products_replace'][$product->symbol])) {
                throw new Exception('Unexpected unexisting replacement for symbol ' . $product->symbol);
            }
            $product->changer = $replacements['products_replace'][$product->symbol]['id'];
            $product->quantity = $replacements['products_replace'][$product->symbol]['quantity'];
        }
    }

    public function getCategory(array $request)
    {
        return Category::findOrFail((int)$request['category_id']);
    }

    public function getProducts($category): LengthAwarePaginator
    {
        $products = Categories::getProductsForCategory($category)
            ->paginate($this->getPerPage());
        $products->data = $products->items();

        return $products;
    }

    public function prepareProductData(&$products): void
    {
        foreach ($products->data as $productKey => $productValue) {
            $this->checkAndSetProductUrl($productKey, $productValue);
            $this->processMediaUrls($productKey, $productValue, $products);
            $this->getStockAndLogsData($productValue);
        }
    }

    protected function checkAndSetProductUrl($productKey, &$productValue): void
    {
        if (!empty($productValue['url_for_website']) && !File::exists(public_path($productValue['url_for_website']))) {
            $productValue->url_for_website = null;
        }
    }

    protected function processMediaUrls($productKey, &$productValue, $products): void
    {
        foreach ($productValue->media as $mediaKey => $mediaValue) {
            $this->checkAndSetMediaUrl($productKey, $mediaKey, $mediaValue, $products);
        }
    }

    protected function checkAndSetMediaUrl($productKey, $mediaKey, &$mediaValue, $products): void
    {
        $mediaData = explode('|', $mediaValue['url']);
        if (count($mediaData) == 3) {
            if (str_contains($mediaData[2], MessagesHelper::SHOW_FRONT)) {
                $products->data->$productKey->media->$mediaKey->url = null;
            } else {
                unset($products->data->$productKey->media->$mediaKey);
            }
        }
    }

    protected function getStockAndLogsData(&$productValue): void
    {
        $productValue->stock = $productValue->stock->where('quantity', '>', 0)->first();
        $productValue->selledInLastWeek = ProductStockLogs::getTotalQuantityForProductStockInLastDays($productValue->stock, 7);
    }
}
