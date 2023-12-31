<?php

namespace App\Http\Controllers;

use App\Models\Cache;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Kavenegar;


class UserController extends Controller

{
    public function indexUsers(Request $request)
    {
        try {
            $perPage = $request['perPage'];
            $data = User::latest()->where('scope', 'user')->where('name', 'Like', '%' . $request['search'] . '%')->paginate($perPage);
            $pages_count = ceil($data->total() / $perPage);
            $labels = [];
            for ($i = 1; $i <= $pages_count; $i++) {
                (array_push($labels, $i));
            }
            return response([
                "data" => UserResource::collection($data),
                "pages" => $pages_count,
                "total" => $data->total(),
                "labels" => $labels,
                "title" => 'کاربر ها',
                "tooltip_new" => '',

            ], 200);
        } catch (\Exception $exception) {
            return $exception;
        }

    }

    public function indexAdmins(Request $request)
    {
        try {
            $perPage = 4;
            $data = User::latest()->where('scope', 'admin')->where('name', 'Like', '%' . $request['search'] . '%')->paginate($perPage);
            $pages_count = ceil($data->total() / $perPage);
            $labels = [];
            for ($i = 1; $i <= $pages_count; $i++) {
                (array_push($labels, $i));
            }
            return response([
                "data" => UserResource::collection($data),
                "pages" => $pages_count,
                "total" => $data->total(),
                "labels" => $labels,
                "title" => 'مدیر ها',
                "tooltip_new" => '',

            ], 200);
        } catch (\Exception $exception) {
            return $exception;
        }

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all('name', 'email', 'password'),
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'phone'=>'required',
                'mobile'=>'required|starts_with:09|min:11|max:11',


//                'password' => 'required|min:3',
            ],
            [
                'name.required' => 'لطفا نام را وارد کنید',
                'name.max' => 'نام بیش از حد طولانی است',
                'email.required' => 'لطفا ایمیل را وارد کنید',
                'email.unique' => 'این ایمیل قبلا ثبت شده',
//                'password.required' => 'لطفا کلمه عبور را وارد کنید',
//                'password.min' => 'لطفا حد اقل 3 کاراکتر وارد کنید',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        try {

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
//                'password' => Hash::make($request['password'])
            ]);
            return response(new UserResource($user), 201);
        } catch (\Exception $exception) {
            return response($exception);

        }

    }

    public function show(User $user)
    {
        try {
            return response(new UserResource($user), 200);
        } catch (\Exception $exception) {
            return response($exception);

        }
    }

    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all('name', 'email', 'password', 'new_password'),
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user['id'],
                'new_password' => 'nullable|min:3',
            ],
            [
                'name.required' => 'لطفا نام را وارد کنید',
                'name.max' => 'نام بیش از حد طولانی است',
                'email.required' => 'لطفا ایمیل را وارد کنید',
                'email.unique' => 'این ایمیل قبلا ثبت شده',
                'new_password.min' => 'لطفا حد اقل 3 کاراکتر وارد کنید',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        try {
            $user->update($request->all());
            if ($request['password']) {
                $user->update([
                    'password' => Hash::make($request['password'])
                ]);
            }
            return response(new UserResource($user), 200);

        } catch (\Exception $exception) {
            return response($exception);
        }

    }

    public function destroy(User $user)
    {
        try {
            $user->sizes->each->delete();
            $user->delete();
            return response('deleted', 200);
        } catch (\Exception $exception) {
            return response($exception);
        }

    }

    public function updateProfile(Request $request)
    {
        $user = User::where('id',$request['id'])->first();
//        return $user;
//        $validator = Validator::make($request->all('name', 'email', 'password'),
//            [
//                'name' => 'required|max:255',
//                'email' => 'required|email|max:255|unique:users,email,' . $user['id'],
//                'new_password' => 'nullable|min:3',
////            'password' => 'required|string|min:3|confirmed',
//            ],
//            [
//                'name.required' => 'لطفا نام را وارد کنید',
//                'name.max' => 'نام بیش از حد طولانی است',
//                'email.required' => 'لطفا ایمیل را وارد کنید',
//                'email.unique' => 'این ایمیل قبلا ثبت شده',
//                'new_password.min' => 'لطفا حد اقل 3 کاراکتر وارد کنید',
//            ]
//        );
//        if ($validator->fails()) {
//            return response()->json($validator->messages(), 422);
//        }

        try {
            if ($request['current_password'] != '' && $request['new_password'] != '' && $request['new_password_repeat'] != '') {

                if (Hash::check($request['current_password'], $user['password'])) {
                    if (strlen($request['new_password']) < 3) {
                        return response()->json(['new_password' => ['لطفا حد اقل 3 کاراکتر وارد کنید']], 422);

                    } else if ($request['new_password'] == $request['new_password_repeat']) {

                        if (!Hash::check($request['new_password'], $user['password'])) {
                            $user->update([
                                'name' => $request['name'],
                                'email' => $request['email'],
                                'password' => Hash::make($request['new_password'])
                            ]);
                        }

                    } else {
                        return response()->json(['new_password_repeat' => ['کلمه عبور جدید با تکرار آن برابر نیست']], 422);
                    }


                } else {
                    return response()->json(['current_password' => ['کلمه عبور فعلی درست نیست']], 422);

                }


            } else {
                $user->update([
                    'name' => $request['name'],
                    'email' => $request['email'],
                ]);
            }

            return response(['user' => $user], 200);
        } catch (\Exception $exception) {
            return response($exception);

        }

    }



}

