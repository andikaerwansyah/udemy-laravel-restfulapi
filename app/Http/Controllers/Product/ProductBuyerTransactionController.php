<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;

class ProductBuyerTransactionController extends ApiController
{
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|min:1'
        ];

        $this->validate($request, $rules);

        // make sure seller is diffrent from buyer
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('The buyer must be diffrent from the seller', 409);
        }

        // check is user verified
        if (!$buyer->isVerified()) {
            return $this->errorResponse('The buyer must be a verified user', 409);
        }

        // check is seller verified
        if (!$product->seller->isVerified()) {
            return $this->errorResponse('The seller must be a verified user', 409);
        }

        // check is product are available
        if (!$product->isAvailable()) {
            return $this->errorResponse('This product is not available', 409);
        }

        // check if order qty not greater than available qty
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('Avaialble product quantity is not enough', 409);
        }

        //  if other user purchase same product
        return DB::transaction(
                    function() use ($request, $product, $buyer){
                        $product->quantity -= $request->quantity;
                        $product->save();

                        $transaction = Transaction::create([
                            'quantity' => $request->quantity,
                            'buyer_id' => $buyer->id,
                            'product_id' => $product->id,
                        ]);

                        return $this->showOne(201, 'Success create new transactions',$transaction, 201);
                    });
    }
}
