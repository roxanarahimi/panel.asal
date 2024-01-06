<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
use App\Models\Redis;
use Kavenegar;

class AuthController extends Controller
{
    public function login(Request $request)
    {

//        return $request;
//        $http = new \GuzzleHttp\user;
//
//        try {
//            $response = $http->post;('http://127.0.0.1:8000/oauth/token', [
//                'form_params' => [
//                    'grant_type' => 'password',
//                    'client_id' => 2,
//                    'client_secret' => '8bEdKUmK73egEBlPZgKxTf3zYduzpwn24m71Y5X6',
//                    'username' => $request['username'],
//                    'password' => $request['password'],
//                    'scope' => null
//
//                ]
//            ]);
//            return $response->getBody();
//
//        } catch (BadResponseException $e) {
//
//            return $e->getMessage();
////            if ($e->getCode() === 400) {
////                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
////            } else if ($e->getCode() === 401) {
////                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
////
////            }
////            return response()->json('Something wnt wrong on the server', $e->getCode());

//        }
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|string|email|max:255',
//            'password' => 'required|string|min:6|confirmed',
//        ]);
//        if ($validator->fails())
//        {
//            return response(['errors'=>$validator->errors()->all()], 422);
//        }
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|string|email|max:255',
//            'password' => 'required|string|min:3',
//        ]);
//        if ($validator->fails())
//        {
//            return response(['errors'=>$validator->errors()->all()], 422);
//        }
        try {
            $user = User::where('email', $request->username)->first();
            if ($user) {
                if (auth()->attempt(['email' => request('username'), 'password' => request('password')])) {
                    $token = $user->createToken('user')->accessToken;

                    $date = new \DateTime();
                    $date->add(new \DateInterval('PT2H'));

                    $user->update(['last_activity'=> $date->format('Y-m-d H:i:s')]);

                    return response(['user' => new UserResource($user), 'access_token' => $token, 'expire' => date_format($date, 'Y-m-d H:i:s')], 200);
                } else {
                    $response = ["password" => ["کلمه عبور اشتباه است"]];
                    return response($response, 422);
                }
            } else {
                $response = ["email" =>['کاربر وجود ندارد']];
                return response($response, 422);
            }
        } catch (\Exception $exception) {
            return response($exception);

        }

    }

    public function getOtp(Request $request)
    {
//        Redis::set($request['mobile'], Random::generate(4, '0-9'));

        $user = User::where('mobile', $request->mobile)->where('scope',$request['scope'])->first();
        if ($user) {
            $characters = '1234567890';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 4; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
//            Redis::set($mobile, $randomString, 60);
            $Redis =  Redis::create(['key'=>$request['mobile'], 'value'=>  $randomString]);

            try {
                $sender = "";        //This is the Sender number
                $message = "به وبسایت عسل لذیذ خوش آمدید. کد تایید شما: " .$Redis['value'];  //Redis::get($mobile);        //The body of SMS
                $receptor = $request['mobile'];            //Receptors numbers
                Kavenegar::Send($sender, $receptor, $message);
//                $code = Redis::get($mobile);


                return response(['otp' => $randomString], 200);
            } catch (\Kavenegar\Exceptions\ApiException $e) {
                // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
                return $e->errorMessage();
            } catch (\Kavenegar\Exceptions\HttpException $e) {
                // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
                return $e->errorMessage();
            }
        } else {
            return response(['message'=>['شما عضو سامانه نیستید لطفا ابتدا ثبت نام کنید سپس وارد شوید']], 400);
        }



    }

    public function loginMobile(Request $request)
    {
        try {
            $user = User::where('mobile', $request->mobile)->where('scope','user')->first();
            $code = Redis::orderByDesc('id')->where('key', $request->mobile)->first();
            if ($user && $code) {

                if ((integer)$request['password'] == (integer)$code['value']) {

                    $token = $user->createToken('user')->accessToken;
                    $date = new \DateTime();
                    $date->add(new \DateInterval('PT2H'));
                    $user->update(['last_activity' => $date->format('Y-m-d H:i:s')]);
                    $code->delete();
                    return response(['user' => new UserResource($user), 'access_token' => $token, 'scope' => $request['scope'], 'expire' => date_format($date, 'Y-m-d H:i:s')], 200);
                } else {
                    $response = ["message" => ["کد وارد شده اشتباه است"]];
                    return response($response, 422);
                }

            }
        } catch (\Exception $exception) {
            return response($exception);

        }

    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all('mobile',),
            [
                'mobile' => 'unique:users',
                'national_code' => 'unique:users',
                'registration_number' => 'unique:users',
            ],
            [
                'mobile.unique' => 'این شماره موبایل قبلا ثبت شده',
                'national_code.unique' => 'این  کد قبلا ثبت شده',
                'registration_number.unique' => 'این شماره ثبت قبلا ثبت شده',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
        $user = User::create($request->except('img1','img2'));
        //event(new Registered($client));

        $accessToken = $user->createToken('authToken')->accessToken;
        if ($request['img1']){
            $name = 'user_' . $user['id'] . '_' . uniqid() . '.jpg';
            $image_path = (new ImageController)->uploadImage($request['img1'], $name, 'images/users/');
            $user->update(['image1' => '/' . $image_path]);
//            (new ImageController)->resizeImage('images/users/',$name);

        }
        if ($request['img2']){
            $name2 = 'user_' . $user['id'] . '_' . uniqid() . '.jpg';
            $image_path2 = (new ImageController)->uploadImage($request['img2'], $name2, 'images/users/');
            $user->update(['image2' => '/' . $image_path2]);
//            (new ImageController)->resizeImage('images/users/',$name2);

        }

        return response(['user' => new UserResource($user)], 201);


    }

    public function logout(User $user)
    {
        try {
//            return $user->tokens;
            $user->tokens->each->delete();
            foreach ($user->tokens as $token){
              return  $token->delete();
            }
            return response('successful logout', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());

        }

    }

    public function updateLastActivity(Request $request)
    {
//        $id = 1;
        // $client = auth()->user();
        $user = User::find($request['id']);
        if (!$user) {
            return response(['message' => 'user does not exist'], 401);
        }
        $date = date('Y-m-d H:i:s');
        if ($user['last_activity'] === null) {
            $latest = $date;
        } else {
            $latest = $user['last_activity'];
        }
//        $userExpire = date('Y-m-d H:i:s',strtotime('+1 minutes',strtotime($latest)));

        $userExpire = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($latest)));
        if ($date > $userExpire) {
            return response(['message' => 'token expired'], 401);
        } else {
            $expDate = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($date)));
            $user->update(['last_activity' => $date]);
            return response(['message' => 'token updated', 'user' => new UserResource($user), 'expire' => $expDate], 200);
        }

    }

    public function currentUser()
    {
        try {
            return response(Auth()->user(), 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), $exception->getCode());

        }

    }

//    public function adminLogin(Request $request)
//    {
////       return auth()->guard('admin')->user();
//        $user = Admin::where('email', $request->email)->first();
//        if ($user) {
//            if (auth()->guard('admin')->attempt(['email' => request('email'), 'password' => request('password')])) {
//                $token = $user->createToken('admin')->accessToken;
//
//                $date = new \DateTime();
//                $date->add(new \DateInterval('PT2H'));
//
////                $user->update(['last_activity' => $date->format('Y-m-d H:i:s')]);
//
//                return response()->json(['admin' => new UserResource($user), 'access_token' => $token, 'expire' => date_format($date, 'Y-m-d H:i:s')], 200);
//            } else {
//                $response = ["password" => ["کلمه عبور اشتباه است"]];
//                return response($response, 422);
//            }
//        } else {
//            $response = ["email" => ['کاربر وجود ندارد']];
//            return response($response, 422);
//        }
//    }
//
//    public function currentAdmin()
//    {
//
////        config(['auth.guards.api.provider' => 'admin']);
////        $admin = Admin::select('admins.*')->find(auth()->guard('admin')->user()->id);
////        return  $admin;
//        try {
////            config(['auth.guards.api.provider' => 'admin']);
//            $admin = Admin::select('admins.*')->find(auth()->guard('admin')->user()->id);
//
//            return response($admin, 200);
//        } catch (\Exception $exception) {
//            return response($exception->getMessage(), $exception->getCode());
//
//        }
//
//    }


}
