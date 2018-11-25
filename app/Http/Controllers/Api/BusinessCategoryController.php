<?php

namespace App\Http\Controllers\Api;

use Request;
use Validator;
use Hash;
use DB;
use Config;

use App\Libraries\HelpFunctions;

class BusinessCategoryController extends ApiController{

    function __construct(){

        parent::__construct();

    }

    public function getBusinessCategories(){

        $response["status"] = 0;

        $categories = DB::table("business_categories")->get();

        $response = [
            "status" => 1,
            "categories" => []
        ];

        foreach ($categories as $category){
            $response["categories"][] = [
                "id" => $category->id,
                "name" => $category->name
            ];
        }

        return response()->json($response);
    }
}
