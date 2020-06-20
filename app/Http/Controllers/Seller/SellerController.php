<?php

namespace App\Http\Controllers\Seller;

use App\Seller; // import seller model 
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::has('products')->get();

        return $this->showAll('Success',$sellers);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller) // implicit model binding
    {
        // $seller = Seller::has('products')->findOrFail($id); // tidak lagi digunakan 
        
        return $this->showOne(200,'Success get seller by id',$seller);
    }
}
