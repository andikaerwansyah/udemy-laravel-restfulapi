<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Get all list of product with seller ID param
     *
     * @param Seller $seller
     * @return void
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }


    /**
     * Create new product
     *
     * @param Request $request
     * @param User $seller
     * @return void
     */
    public function store(Request $request, User $seller) 
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image']= '1.jpg';
        $data['seller_id'] = $seller->id;
        $product = Product::create($data);

        return $this->showOne($product);

    }


    /**
     * Update a product
     *
     * @param Request $request
     * @param Seller $seller
     * @param Product $product
     * @return void
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image' => 'image',
        ];

        $this->validate($request, $rules);

        // Check the product of seller
        $this->checkSeller($seller, $product);

        $product->fill($request->intersect([
            'name',
            'description',
            'quantity',
        ]));

        if($request->has('status')){
            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('Please insert a diffrent value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Delete Product
     *
     * @param Seller $seller
     * @param Product $product
     * @return void
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        $product->delete();

        return $this->showOne($product);
    }

    /**
     * Check if the product is owned by the seller
     *
     * @param Seller $seller
     * @param Product $product
     * @return void
     */
    public function checkSeller(Seller $seller, Product $product){
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422 ,"The specified seller is not the owner of this product");
        }
    }
}
