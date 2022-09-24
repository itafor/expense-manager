<?php
namespace App\Services;

use App\Models\User;
use App\Traits\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UserAuthService
{

    use Response;

    public function signup($data)
    {
        try {

           
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();

            $access_token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $access_token,
            ];

            return $this->success(false, "User successfully registered!", $data, 200);

        } catch (\Exception$e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't registered user. $error ", 400);
        }
    }


    public function login($data)
    {
        try {
            if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return $this->fail(true, "Invalid login details!", $data, 400);
            }

            $user = User::where('email', $data['email'])->first();

            $token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $token,
            ];

            return $this->success(false, "User successfully logged in!", $data, 200);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't logged in user: $error !", $e->getMessage(), 400);
        }
    }

    public function userProfile()
    {
        try {

            $user = User::where('id', auth()->user()->id)->first();

            return $this->success(false, "User profile!", $user, 200);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't fetched user: $error!", $e->getMessage(), 400);
        }
    }
    

    public function logout()
    {
        try {
            $user = Auth::user('user')->currentAccessToken()->delete();

            return $this->success(false, "User successfully logged out!", $user, 200);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't logged out user: $error!", $e->getMessage(), 400);
        }
    }

}
