<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use Hash;
use DB;
use Config;

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
                            $userFullInfo = DB::table("users_businesses")
                                ->join("business_categories", "business_categories.id", "=", "users_businesses.category_id", "left")
                                ->where(["user_id" => $user->id])
                                ->select("users_businesses.name", "description", "image", "logo", "discount", "business_categories.name as category_name")
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
}
