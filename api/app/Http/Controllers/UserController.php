<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\User as UserResource;
use Validator;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserController extends BaseController
{
    public function UserRegister(Request $request)
    {
        $input = $request->all();

        DB::statement("ALTER TABLE users AUTO_INCREMENT = 1;");

        $validator = Validator::make($input, 
        [
            "username" => "required|unique:users|min:5",
            "email" => "required|email|unique:users",
            "password" => "required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/",
            "confirm_password" => "required|same:password",
        ],
        [
            "username.required" => "Filling the username field is required!",
            "username.unique" => "This username is already in use!",
            "username.min" => "The username should be more than 5 characters!",

            "email.required" => "Filling the email field is required!",
            "email.email" => "The email format is not acceptable! (@) ",
            "email.unique" => "Email address is already registered!",

            "password.required" => "Filling the password field is required!",
            "password.min" => "The password should be at least 6 character long!",
            "password.regex" => "The password should contain at least one upper and one lower case letter!",

            "confirm_password.required" => "Filling the password again is required!",
            "confirm_password.same" => "The field must be equal with the password!",
        ]
    );

        if($validator->fails())
        {
            return $this->sendError("Wrong registration data.", $validator->errors());
        }

        $input["password"] = bcrypt($input["password"]);
        $user = User::create($input);
        
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->save();

        return $this->sendResponse($user, "The register was successfull.");
    }

    public function UserLogin(Request $request)
    {
        if(Auth::attempt(["username"=> $request->username, "email" => $request->email, "password" => $request->password]))
        {
            $authUser = Auth::user();
            $success["token"] = $authUser->createToken("MyAuthApp")->plainTextToken;
            $success["username"] = $authUser->username;

            return $this->sendResponse($success, "The login was successfull!");
        }
        else
        {
            return $this->sendError("Unauthorized." . json_encode(["error"=>"Login failed."]));


        }
    }

    public function UserLogout(Request $request)
    {
        auth("sanctum")->user()->currentAccessToken()->delete();

        return response()->json("logout was successfull.");
    }

    public function ListUsers()
    {
        $users = User::all();

        return $this->sendResponse(UserResource::collection($users), "The registered user's list printed!");
    }

    public function UpdateUser(Request $request, $id)
    {
        $user = $request->all();

        $validator = Validator::make($user, 
        [
            "username" => "required|unique:users|min:5",
            "email" => "required|unique:users",
            "password" => "required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/",
        ],
        [
            "username.required" => "Filling the username field is required!",
            "username.unique" => "This username is already in use!",
            "username.min" => "The username should be more than 5 characters!",

            "email.required" => "Filling the email field is required!",
            "email.email" => "The email format is not acceptable! (@) ",
            "email.unique" => "Email address is already registered!",

            "password.required" => "Filling the password field is required!",
            "password.min" => "The password should be at least 6 character long!",
            "password.regex" => "The password should contain at least one upper and one lower case letter!",
        ]
    );

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = User::find($id);
        $user->update($request->all());
        $user["password"] = bcrypt($user["password"]);
        $user->save();


        return $this->sendResponse(new UserResource($user), "The user data updated!");
    }

    public function UpdateAdmin(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input,
        [
            "is_admin" => "required"
        ]
    );

    if ($validator->fails())
    {
        return $this->sendError($validator->errors());
    }

    $user = User::find($id);
    $user->update($request->all());
    $user->save();

    return $this->sendResponse(new UserResource($user), "Admin access granted!");
    }

    public function ShowUserById ($id)
    {
        $user = User::find($id);

        if(is_null($user))
        {
            return $this->sendError("There is no user with this id: $id!");
        }

        return $this->sendResponse(new UserResource($user), "$id. User data load is success");
        
    }

    public function DeleteUser($id)
    {
        User::destroy($id);
        
        $user = User::find($id);
        $users = User::where("id", ">", $id)->orderBy("id")->get();
        foreach($users as $user)
        {
            $user->id = $user->id -1;
            $user->save();
        }

        return $this->sendResponse([], "The user has been successfully deleted!");
    }

}