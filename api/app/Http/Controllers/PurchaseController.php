<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Payment_Mode;
use App\Models\Delivery_Mode;
use App\Http\Resources\Profile as ProfileResource;
use Validator;
use Carbon\Carbon;

class PurchaseController extends BaseController
{
    public function Purchase(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) 
        {
            return $this->sendError("Profile was not found with id: $id "); 
        }

        $validator = Validator::make($request->all(), 
        [
            "payment_mode" => "required",
            "delivery_mode" => "required"
        ], 
        [
            "payment_mode.required" => "Filling the payment_mode field is required!",
            "delivery_mode.required" => "Filling the delivery_mode field is required!"
        ]);

        if ($validator->fails()) 
        {
            return $this->sendError("There was an error with the provided data.", $validator->errors());
        }

        $payment_mode = Payment_Mode::where("payment_mode", $request->payment_mode)->first();

        if (!$payment_mode) 
        {
            return $this->sendError("Invalid payment mode has been choosen!");
        }

        $delivery_mode = Delivery_Mode::where("delivery_mode", $request->delivery_mode)->first();

        if (!$delivery_mode) 
        {
            return $this->sendError("Invalid delivery mode has been choosen!");
        }

        $profile->payment_mode_id = $payment_mode->id;
        $profile->delivery_mode_id = $delivery_mode->id;
        $profile->order_date = Carbon::now();
        $profile->update();

        return $this->sendResponse(new ProfileResource($profile), "The purchase was successfull!");
    }
}
