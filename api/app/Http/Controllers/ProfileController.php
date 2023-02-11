<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Profile;
use App\Models\User;
use App\Models\Order_date;
use App\Models\Payment_mode;
use App\Models\Delivery_mode;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Profile as ProfileResource;


class ProfileController extends BaseController
{

    public function ListProfiles( Request $request)
    {

        $profiles = Profile::with(["user", "payment_mode", "delivery_mode"])->get();
        return $this->sendResponse(ProfileResource::collection($profiles), "Got every profile data!");

    }   

    public function UpdateProfile(Request $request, $id)
    {
        $profile = $request->all();

        $validator = Validator::make($profile, 
        [
            "surname" => "required",
            "lastname" => "required",
            "country" => "required",
            "city" => "required",
            "address" => "required",
        ],
        [
            "surname.required" => "Filling the surname field is required!",
            "lastname.required" => "Filling the lastname field is required!",
            "country.required" => "Filling the country field is required!",
            "city.required" => "Filling the city field is required!",
            "address.required" => "Filling the address field is required!",
        ]
    );

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $profile = Profile::find($id);

        if ($profile) 
        {
            $profile->update($request->all());
            return $this->sendResponse(new ProfileResource($profile), "Profile updated id:($id)!");
        } 
        else 
        {
            return $this->sendError("The profile was not found with this id: $id ");
        }

        $profile->save();

        return $this->sendResponse(new ProfileResource($profile), "The profile was successfully updated!");
    }

    public function DeleteProfile($id)
    {
        Profile::destroy($id);

        return $this->sendResponse([], "The profile was successfully deleted.");
    }
    
}
