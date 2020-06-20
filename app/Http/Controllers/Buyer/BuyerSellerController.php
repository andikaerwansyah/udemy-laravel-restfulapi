<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $sellers = $buyer->transaction()
                  ->with('product.seller')
                  ->get()
                  ->pluck('product.seller')
                  ->unique('id')
                  ->values();
        
        return $this->showAll('Success',$sellers);
    }
}