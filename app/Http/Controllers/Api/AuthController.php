<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use Hash;
use DB;
use Session;
use Config;

//use App\Libraries\reCaptcha;
use App\Libraries\HelpFunctions;

class AuthController extends ApiController{

    function __construct(){

        parent::__construct();

    }

    public function login(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {
            $rules['username'] = "required";
            $rules['password'] = "required";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $user = DB::table("all_users")
                    ->where(["username" => $input["username"]])
                    ->select("id", "username", "password", "type", "account_id", "balance")
                    ->first();

                if($user){
                    if(Hash::check($input["password"], $user->password)){

                        $token = (new HelpFunctions())->getRandom(Config::get("my_config.token_length"));

                        DB::table("all_users")
                            ->where("id", $user->id)
                            ->update([
                                'token' => $token
                            ]);

                        $response = [
                            "status" => 1,
                            "username" => $user->username,
                            "type" => $user->type,
                            "account_id" => $user->account_id,
                            "balance" => $user->balance,
                            "token" => $token
                        ];

                        if($user->type == 0){
                            $userFullInfo = DB::table("users_customers")
                                ->where(["user_id" => $user->id])
                                ->select("name", "surname")
                                ->first();

                            $response["name"] = $userFullInfo->name;
                            $response["surname"] = $userFullInfo->surname;
                        }
                        else{
                            $userFullInfo = DB::table("users_partners")
                                ->join("partner_categories", "partner_categories.id", "=", "users_partners.category_id", "left")
                                ->where(["user_id" => $user->id])
                                ->select("users_partners.name", "description", "image", "logo", "discount", "partner_categories.name as category_name")
                                ->first();

                            $response["name"] = $userFullInfo->name;
                            $response["description"] = $userFullInfo->description;
                            $response["image"] = $userFullInfo->image;
                            $response["logo"] = $userFullInfo->logo;
                            $response["discount"] = $userFullInfo->discount;
                            $response["category_name"] = $userFullInfo->category_name;
                        }

                    }
                }
            }
        }

        return response()->json($response);
    }

    public function signup(){

        if(Request::isMethod('post')){
            $input = Request::except('_token');
            if(isset($input)) {
                $rules['username'] = "required|alpha|min:6|max:10|unique:users_clients,username";
                $rules['email'] = "required|email|max:30|unique:users_clients,email";
                $rules['phone'] = "digits:12|unique:users_clients,phone";
                $rules['privacy_policy'] = "accepted";
                $rules['secret_question'] = "required|min:1|max:50";
                $rules['secret_answer'] = "required|min:1|max:50";
                $rules['password'] = "required|min:8|max:40";
                $rules['password_confirm'] = "required|same:password";

                $validator = Validator::make($input, $rules);

                $botCheck = (isset($input["g-recaptcha-response"]) ? (new reCaptcha())->botCheck($input["g-recaptcha-response"]) : false);

                if (!$validator->fails() && $botCheck) {

                    $confirmingCode = (new HelpFunctions())->getRandom(Config::get("my_config.random_confirm_code_length"));

                    $user = DB::table('users_clients')->insert(
                        [
                            'username' => $input["username"],
                            'email' => $input["email"],
                            'phone' => ($input["phone"] ? $input["phone"] : null),
                            'secret_question' => $input["secret_question"],
                            'secret_answer' => $input["secret_answer"],
                            'password' => Hash::make($input["password"]),
                            'created_at' => date("Y-m-d H:i:s"),
                            'status' => -1,
                            'confirming_code' => $confirmingCode
                        ]
                    );

                    $mailData = [
                        "confirmingCode" => $confirmingCode
                    ];

                    $result = Mail::send('site.email.confirming', ['data' => $mailData], function ($message) use ($input) {
                        $mailAdmin = env('MAIL_USERNAME');

                        $message->to($input['email']);
                        $message->from($mailAdmin);

                        $message->subject(Lang::get('site.confirming'));
                    });

                    /*
                    if ($result) {
                        return redirect()->route('contact',['locale'=>Lang::getLocale()])->with('message', Lang::get('site.messageHasSent'));
                    }
                    */

                    return redirect()->route('login')
                        ->with("message", ["status" => "green", "text" => Lang::get("site.confirm_your_account")]);

                }

                return redirect()->route('signup')
                    ->withInput()
                    ->withErrors($validator);

            }
        }

        $breadcrumbs = [
            "javascript:void(0)" => Lang::get("site.signup")
        ];

        $data = [
            'mainDivClasses' => "main register-page",
            "breadcrumbs" => $breadcrumbs,
        ];

        return view('site.auth.signup.signup', $data);
    }

    public function logout(){
        if(Session::has("user")){
            Session::forget("user");
        }

        return redirect()->route('login');
    }

    public function rules(){
        $rules = DB::table("rules")->where("locale", Lang::getLocale())->first();

        $data = [
            'rules' => $rules,
            'mainDivClasses' => "main rules-page"
        ];

        return view('site.auth.rules.rules', $data);
    }

    public function howItWorks(){
        $howItWorks = DB::table("how_it_works")->where("locale", Lang::getLocale())->whereRaw("type_id in (1,2)")->get();

        $data = [
            'howItWorks' => $howItWorks,
            'mainDivClasses' => "main how-it-works-page"
        ];

        return view('site.auth.how_it_works.how_it_works', $data);
    }

    public function confirmingAccount(){
        $confirmingCode = Request::input("confirming_code");

        if($confirmingCode){
            $user = DB::table("users_clients")
                ->where(["confirming_code" => $confirmingCode])
                ->select("id")
                ->first();

            if($user){
                DB::table("users_clients")
                    ->where("id", $user->id)
                    ->update([
                        'status' => 1,
                        'confirming_code' => null,
                    ]);

                return redirect()->route('login')
                    ->with("message", ["status" => "green", "text" => Lang::get("site.account_confirmed")]);
            }
        }

        return redirect()->route('login');
    }

    public function forgotPassword(){

        if(Request::isMethod('post')){
            $input = Request::except('_token');
            if(isset($input)) {
                $rules['username'] = "required|min:6|max:10";
                $rules['secret_answer'] = "required|min:1|max:50";

                $validator = Validator::make($input, $rules);

                $botCheck = (isset($input["g-recaptcha-response"]) ? (new reCaptcha())->botCheck($input["g-recaptcha-response"]) : false);

                if (!$validator->fails()) {

                    $newPassword = (new HelpFunctions())->getRandom(Config::get("my_config.random_new_password_length"));

                    $userChanged = DB::table("users_clients")
                        ->where("username", $input['username'])
                        ->where("secret_answer", $input['secret_answer'])
                        ->update([
                            "password" => Hash::make($newPassword),
                        ]);

                    if($userChanged) {

                        $user = DB::table("users_clients")
                            ->where("username", $input['username'])
                            ->select("email")
                            ->first();

                        $mailData = [
                            "username" => $input['username'],
                            "newPassword" => $newPassword
                        ];

                        $dataMailExecute = [
                            "email" => $user->email
                        ];

                        $result = Mail::send('site.email.forgot_password', ['data' => $mailData], function ($message) use ($dataMailExecute) {
                            $mailAdmin = env('MAIL_USERNAME');

                            $message->to($dataMailExecute['email']);
                            $message->from($mailAdmin);

                            $message->subject(Lang::get('site.forgot_password'));
                        });

                        /*
                        if ($result) {
                            return redirect()->route('contact',['locale'=>Lang::getLocale()])->with('message', Lang::get('site.messageHasSent'));
                        }
                        */

                        return redirect()->route('login')
                            ->with("message", ["status" => "green", "text" => Lang::get("site.new_password_has_been_sent")]);
                    }
                    else{
                        return redirect()->route('forgotPassword')
                            ->with("message", ["status" => "red", "text" => Lang::get("site.incorrect_data")]);
                    }

                }

                return redirect()->route('forgotPassword')
                    ->withInput()
                    ->withErrors($validator);

            }
        }

        $breadcrumbs = [
            "javascript:void(0)" => Lang::get("site.forgot_password")
        ];

        $data = [
            'mainDivClasses' => "main login-page",
            "breadcrumbs" => $breadcrumbs,
        ];

        return view('site.auth.forgot_password.index', $data);
    }

    public function getUserSecretQuestion(){
        $input = Request::all();
        if(isset($input)) {

            $rules['username'] = "required|min:1|max:10";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $blog = DB::table("users_clients")
                    ->where("username", $input['username'])
                    ->select("secret_question")
                    ->first();

                if($blog){

                    $response = [
                        "status" => 1,
                        "secret_question" => $blog->secret_question,
                    ];
                }
                else {
                    $response = [
                        "status" => 0,
                        "message" => Lang::get("site.user_is_not_present")
                    ];
                }
            }
            else{
                $response = [
                    "status" => 0,
                    "message" => Lang::get("site.validation_error")
                ];
            }
        }
        else{
            $response = [
                "status" => 0,
                "message" => Lang::get("site.incorrect_data")
            ];
        }

        return $response;
    }
}
