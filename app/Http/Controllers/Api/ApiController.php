<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//use Illuminate\Http\Request;

use Request;
use Lang;
use View;
use DB;
use Session;
use Cookie;

class ApiController extends Controller
{

    protected $contact;

    function __construct(){

//        $this->headerData();
//
//        $this->setLocale();
    }

    private function headerData(){

        //profile data
        $profileData = DB::table("users_clients")
            ->where(["id" => Session::get("user.id")])
            ->select("id", "username", "first_name", "last_name", "profile_pic", "balance", "balance_reserved")
            ->first();

        //notifications data
        $notificationsData = DB::table("notifications")
            ->where(["user_client_id" => Session::get("user.id")])
            ->limit(4)
            ->select("id", "type", "parameters", "read")
            ->orderBy("read","asc")
            ->orderBy("created_at", "desc")
            ->get();

        $notificationsGroupedByRead["count"] = count($notificationsData);
        foreach($notificationsData as $key=>$notification) {
            $notificationsData[$key]->parameters = json_decode($notificationsData[$key]->parameters, true);
            if($notification->read) $notificationsGroupedByRead[1][] = $notificationsData[$key];
            else $notificationsGroupedByRead[0][] = $notificationsData[$key];
        }

        // current uri
        $currentPath= url()->current();

        View::share("profileData", $profileData);
        View::share("notificationsData", $notificationsGroupedByRead);
        View::share("currentPath", $currentPath);
    }

    private function setLocale(){
        $newLang = Request::input("lang");
        if($newLang&&in_array($newLang, ["az", "ru"])){
            Cookie::queue("lang", $newLang);
            Lang::setLocale($newLang);
        }
        else {

            $lang = Cookie::get("lang");

            if (in_array($lang, ["az", "ru"])) {
                Lang::setLocale($lang);
            }
        }
    }
}
