<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        return $this->showOne($user, 201);
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

        return $this->showOne($user);
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
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:'.User::ADMIN_USER.','.User::REGULAR_USER,
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
                    409);
            }
            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse(
                'You need to specify a diffrent value to update',
                422);
        }

        $user->save();

        return $this->showOne($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) // implicit model binding
    {
    //     $user = User::findOrFail($id);

        $user->delete();

        return $this->showOne($user);
    }
}
