<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{

    /**
     * @swg\Info(title="Udemy RestfulAPI", version="1")
     * 
     * @swg\Get(
     *     path="/users",
     *     tags={"users"},
     *     summary="Get All Users",
     *     description="Get list of Users (Buyer & Seller)",
     *     operationId="getAllUsers",
     *     @swg\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll(200, 'Success', $users);
    }

    /** 
     * @swg\Post(
     *     path="/users",
     *     tags={"users"},
     *     summary="Create new user",
     *     description="Create new user",
     *     operationId="createNewUser",
     *     @swg\Parameter(
     *          name="Create New User",
     *          description="Membuat user baru",
     *          required=true,
     *          in="body",
     *          @swg\Schema(
     *             @swg\Property(
     *              property="name",
     *              type="string",
     *             ),
     *             @swg\Property(
     *              property="email",
     *              type="string",
     *             ),
     *             @swg\Property(
     *              property="password",
     *              type="string",
     *             ),
     *              @swg\Property(
     *              property="password_confirmation",
     *              type="string",
     *             ),
     *          ),
     *     ),
     *     @swg\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // proteksi inputan dengan validasi
        // https://laravel.com/docs/7.x/validation#rule-confirmed
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        // validasi request
        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password); // enkripsi passowrd
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data); // massive assignment

        return $this->showOne(201, 'Created successfully' ,$user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) // implicit model binding
    {
        // $user = User::findOrFail($id); // dengan findOrFail

        return $this->showOne('Success', $user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) // Implicit Model Binding
    {
        // $user = User::findOrFail($id); 

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $this->validate($request, $rules);

        // menyimpan nama baru yang diterima 
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        // menyimpan email baru yang di terima
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }

        // menyimpan password
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        // cek apakah admin
        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse(
                    'Only verified users can modify the admin field',
                    409
                );
            }
            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse(
                'You need to specify a diffrent value to update',
                422
            );
        }

        $user->save();

        return $this->showOne(200, 'Success', $user);
    }

    /**
     * @swg\Delete(
     *     path="/users/{id}",
     *     tags={"users"},
     *     summary="Delete user",
     *     description="Delete user by id)",
     *     operationId="deleteUser",
     *     @swg\Parameter(
     *          name="id",
     *          description="insert user id",
     *          in="path",
     *          type="integer",
     *          required=true,
     *     ),
     *     @swg\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     */    
    public function destroy(User $user) // implicit model binding
    {
        //     $user = User::findOrFail($id);

        $user->delete();

        return $this->showOne(200, 'Success', $user);
    }
}
