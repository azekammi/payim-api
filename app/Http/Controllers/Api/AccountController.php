<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use DB;

class AccountController extends ApiController{

    function __construct(){

        parent::__construct();

    }

    public function replenish(){

        $response["status"] = 0;

        $input = Request::all();
        if(isset($input)) {
            $rules['code_sixteen'] = "required";
            $rules['cvv'] = "required";
            $rules['amount'] = "required";

            $validator = Validator::make($input, $rules);

            if (!$validator->fails()) {

                $user = DB::table("all_users")
                    ->where(["token" => $input["token"], "type" => 0])
                    ->select("id", "balance")
                    ->first();

                //
                //IT'S FOR TEST
                if($user && $input["code_sixteen"] == "0000000000000000"){

                    DB::table('all_users')
                        ->where(["id" => $user->id])
                        ->update(
                            [
                                'balance' => \DB::raw( 'balance + '.$input["amount"]*100 )
                            ]
                        );

                    $response["status"] = 1;
                }
            }
        }

        return response()->json($response);
    }
}
