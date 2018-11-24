<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use Hash;
use DB;
use Config;

use App\Libraries\HelpFunctions;

class BusinessController extends ApiController{

    function __construct(){

        parent::__construct();

    }

    public function getBusinesses(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {

            $businesses = DB::table("all_users")
                ->join("users_businesses", "users_businesses.user_id", "=", "all_users.id", "left")
                ->join("business_categories", "business_categories.id", "=", "users_businesses.category_id", "left")
                ->where(["type" => 1])
                ->select("all_users.id", "account_id", "balance", "users_businesses.name as user_name", "description", "image", "logo", "discount", "business_categories.name as category_name")
                ->get();

            $response = [
                "status" => 1,
                "businesses" => []
            ];

            foreach ($businesses as $business){
                $response["businesses"][] = [
                    "id" => $business->id,
                    "account_id" => $business->account_id,
                    "balance" => $business->balance,
                    "user_name" => $business->user_name,
                    "description" => $business->description,
                    "image" => $business->image,
                    "logo" => $business->logo,
                    "discount" => $business->discount,
                    "category_name" => $business->category_name,
                ];
            }

        }

        return response()->json($response);
    }

    public function getBusiness(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input) && (isset($input["id"]) || isset($input["name"]))) {

            $filter = [];
            $filter["type"] = 1;

            if(isset($input["id"])) $filter["all_users.id"] = $input["id"];
            if(isset($input["name"])){
                $filter["users_businesses.name"] = "%".$input["name"]."%";
                $filter["users_businesses.description"] = "%".$input["name"]."%";
            }

            $business = DB::table("all_users")
                ->join("users_businesses", "users_businesses.user_id", "=", "all_users.id", "left")
                ->join("business_categories", "business_categories.id", "=", "users_businesses.category_id", "left")
                ->where($filter)
                ->select("all_users.id", "account_id", "users_businesses.name as user_name", "description", "image", "logo", "discount", "business_categories.name as category_name")
                ->first();

            $response = [
                "status" => 1,
                "business" => [
                    "id" => $business->id,
                    "account_id" => $business->account_id,
                    "user_name" => $business->user_name,
                    "description" => $business->description,
                    "image" => $business->image,
                    "logo" => $business->logo,
                    "discount" => $business->discount,
                    "category_name" => $business->category_name,
                ]
            ];

        }

        return response()->json($response);
    }
}
