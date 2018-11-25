<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use Hash;
use DB;
use Config;

use App\Libraries\HelpFunctions;

class PaymentsController extends ApiController{

    function __construct(){

        parent::__construct();

    }

    public function generateCode(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {
            $rules['user_id'] = "required|exists:users_businesses,user_id";
            $rules['amount'] = "required";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $codeIsPresent = true;
                while ($codeIsPresent) {

                    $code = (new HelpFunctions())->getRandom(Config::get("my_config.generated_code_length"), 2);

                    $payments = DB::table("payments")
                        ->where(["generated_code" => $code])
                        ->select("id")
                        ->first();

                    if (!$payments) $codeIsPresent = false;
                }

                $business = DB::table("users_businesses")
                    ->where(["user_id" => $input["user_id"]])
                    ->select("discount")
                    ->first();

                DB::table('payments')->insert(
                    [
                        'generated_code' => $code,
                        'amount' => $input["amount"],
                        'business_user_id' => $input["user_id"],
                        'discount' => $business->discount,
                        'status' => 0
                    ]
                );

                $response = [
                    "status" => 1,
                    "generated_code" => $code
                ];

            }
        }

        return response()->json($response);
    }

    public function checkCode(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {
            $rules['user_id'] = "required|exists:users_customers,user_id";
            $rules['code'] = "required|exists:payments,generated_code";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $payments = DB::table("payments")
                    ->join("users_businesses", "users_businesses.user_id", "=", "payments.business_user_id", "left")
                    ->where(["generated_code" => $input["code"], "status" => 0])
                    ->select("amount", "users_businesses.user_id", "payments.discount", "name", "logo")
                    ->first();

                if($payments){
                    $response = [
                        "status" => 1,
                        "amount" => $payments->amount,
                        "discount" => $payments->discount,
                        "business" => [
                            "user_id" => $payments->user_id,
                            "name" => $payments->name,
                            "logo" => $payments->logo
                        ]
                    ];
                }
            }
        }

        return response()->json($response);
    }

    public function pay(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {
//            $rules['users'] = "required|array";
//            $rules['users.*'] = "required|array";
            $rules['code'] = "required";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $user = DB::table("all_users")
                    ->where(["token" => $input["token"], "type" => 0])
                    ->select("id")
                    ->first();

                $payment = DB::table("payments")
                    ->where(["generated_code" => $input["code"], "status" => 0])
                    ->select("id", "business_user_id")
                    ->first();

                if($user && $payment){
                    DB::table('transactions')->insert(
                        [
                            'from_user_id' => $user->id,
                            'to_user_id' => $payment->business_user_id,
                            'payment_id' => $payment->id,
                            'date' => date("Y-m-d H:i:s")
                        ]
                    );

                    DB::table('payments')
                        ->where(["id" => $payment->id])
                        ->update(
                            [
                                'status' => 1
                            ]
                        );

                    $response = [
                        "status" => 1
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
