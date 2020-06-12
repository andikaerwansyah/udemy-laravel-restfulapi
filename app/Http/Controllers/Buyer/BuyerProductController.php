<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $products = $buyer
                    ->transaction() // Laravel Eager loading
                    ->with('product')
                    ->get()
                    ->pluck('product'); // ekstrak spesifik key dari object yang akan di gunakan


        return $this->showAll($products);
    }
}
