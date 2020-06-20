<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductCategoryController extends ApiController
{
    /**
     * Show a category product using product ID as a parameter
     *
     * @param Product $product
     * @return void
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll('Success',$categories);
    }
    
    /**
     * Update category of the product
     *
     * @param Request $request
     * @param Product $product
     * @param Category $category
     * @return void
     */
    public function update(Request $request, Product $product, Category $category)
    {
        // Eloquent Method ManyToMany attach, sync, syncWitoutDetaching
        // $product->categories()->attach([$category->id]); // tidak cek kalau sudah ada id untuk categorynya
        // $product->categories()->sync([$category->id]); // malah ngapus id yang lainnya
        $product->categories()->syncWithoutDetaching([$category->id]); // malah ngapus id yang lainnya

        return $this->showAll('Success',$product->categories);

    }
    
    /**
     * Remove category of a product
     *
     * @param Product $product
     * @param Category $category
     * @return void
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse('The specified category is not the category of this product', 404);
        }

        $product->categories()->detach($category->id);

        return $this->showAll('Success',$product->categories);
    }
}
