<?php

namespace Modules\Account\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Modules\Storefront\Entities\UserActivationCode;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Support\Country;


class AccountProfileController
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit()
    {
        return view('storefront::public.account.profile.edit', [
            'account' => auth()->user(),
            'addresses' => auth()->user()->addresses->keyBy('id'),
            'defaultAddress' => auth()->user()->defaultAddress,
            'countries' => Country::supported(),
        ]);
    }

    public function apiEdit()
    {
        $final_data = ['account' => auth()->user()];
        return response()->json([
            'status' => 'success',
            'message' => trans('Account edit data'),
            'data' => $final_data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProfileRequest $request
     *
     * @return Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $data = $request->only('name', 'dob');
        // $request->bcryptPassword();

        auth()->user()->update($data);

        return back()->with('success', trans('account::messages.profile_updated'));
    }
    public function requestOtpCode(Request $request)
    {
        $request->validate([
            'email' => [
                Rule::requiredIf(!$request->has('phone')),
                Rule::unique('users')->ignore(auth()->id(), 'id'),
            ],
            'phone' => [
                Rule::requiredIf(!$request->has('email')),
                Rule::unique('users')->ignore(auth()->id(), 'id'),
            ],
            'otp_phone' => [
                'required',
                'regex:/^(01[3-9]\d{8}|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/',
            ],
        ], [
            'phone.required' => 'Either phone or email is required.',
            'phone.unique' => 'This phone number is already registered.',
            'email.required' => 'Either email or phone is required.',
            'email.unique' => 'This email is already registered.',
            'otp_phone.required' => 'You must provide a valid phone number or email.',
            'otp_phone.regex' => 'Please provide a valid Bangladeshi phone number (e.g., 013XXXXXXXX) or a valid email address.',
        ]);

        $phone = $request->input('otp_phone');
        $user = User::where('phone', $phone)->first();
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        $user_activation_data = [
            'phone' => $phone,
            'user_id' => $user->id ?? 0,
            'otp_media' => 'sms',
            'activation_code' => $otp,
            'expiry' => $expiresAt,
            'created_by' => 0,
            'updated_by' => 0,
        ];

        UserActivationCode::updateOrCreate(['phone' => $phone], $user_activation_data);

        $API_TOKEN =  config('sms.API_TOKEN');
        $SID =  config('sms.SID');
        $url =  config('sms.SMS_URL');
        $otp_content_from_config = config('sms.ACCOUNT_UPDATE_OTP_CONTENT');
        $otp_content = str_replace('{otp}', $otp, $otp_content_from_config);

        ### sms ###
        $params = [
            "api_token" => $API_TOKEN,
            "sid" => $SID,
            "msisdn" =>  '88' . $phone,
            "sms" => $otp_content,
            "csms_id" => 'SETCOL' . date('Ymd'),
        ];
        $params = json_encode($params);
        $response = $this->sendSmsCurl($url, $params);

        return response()->json([
            'status' => 'success',
            'message' => __("OTP sent successfully."),
            'details' => json_decode($response),
        ]);
    }

    private function sendSmsCurl($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),
            'accept:application/json'
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
            'email' => ['required', Rule::unique('users')->ignore(auth()->id(), 'id')],
        ], [
            'phone.required' => 'The phone field is required.',
            'otp.required' => 'The OTP field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email has already been taken.',
        ]);

        $otp = $request->input('otp');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $id = auth()->id();

        $user_activation = UserActivationCode::where('phone', $phone)
            ->where('activation_code', $otp)
            ->where('expiry', '>=', Carbon::now())
            ->first();

        if (!$user_activation) {
            return response()->json([
                'status' => 'error',
                'message' => __("Invalid OTP."),
            ]);
        }

        $user_update = User::where('id', $id)->update(['email' => $email]);
        if (!$user_update) {
            return response()->json([
                'status' => 'error',
                'message' => __("Email update failed."),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __("Email updated successfully."),
        ]);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'old_phone' => 'required',
            'otp' => 'required',
            'phone' => ['required', Rule::unique('users')->ignore(auth()->id(), 'id')],
        ], [
            'old_phone.required' => 'The old phone field is required.',
            'otp.required' => 'The OTP field is required.',
            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'The email has already been taken.',
        ]);

        $otp = $request->input('otp');
        $old_phone = $request->input('old_phone');
        $phone = $request->input('phone');
        $id = auth()->id();

        $user_activation = UserActivationCode::where('phone', $old_phone)
            ->where('activation_code', $otp)
            ->where('expiry', '>=', Carbon::now())
            ->first();

        if (!$user_activation) {
            return response()->json([
                'status' => 'error',
                'message' => __("Invalid OTP."),
            ]);
        }

        $user_update = User::where('id', $id)->update(['phone' => $phone]);
        if (!$user_update) {
            return response()->json([
                'status' => 'error',
                'message' => __("Phone update failed."),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __("phone updated successfully."),
        ]);
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                // 'otp' => 'required',
                // 'old_password' => 'required',
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
            ], [
                // 'otp.required' => 'The OTP field is required.',
                // 'old_password.required' => 'The old password field is required.',
                'new_password.required' => 'The new password field is required.',
                'confirm_password.required' => 'The confirm password field is required.',
                'confirm_password.same' => 'The confirm password and new password must match.',
            ]);


            // $otp = $request->input('otp');
            // $user_activation = UserActivationCode::where('phone', auth()->user()->phone)
            //     ->where('activation_code', $otp)
            //     ->where('expiry', '>=', Carbon::now())
            //     ->first();
            // if (!$user_activation) {
                //     return response()->json([
                    //         'status' => 'error',
            //         'message' => __("Invalid OTP."),
            //     ]);
            // }

            $id = auth()->id();
            $user = User::find($id);
            if (!$user) {
                return responseNotFound(__("User not found!"));
            }

            // $old_password = $request->input('old_password');
            // if (!Hash::check($old_password, $user->password)) {
            //     return responseWithFailed(__("Old password does not match."));
            // }

            $new_password = $request->input('new_password');
            $user->update(['password' => bcrypt($new_password)]);
            return responseSuccess(__("Password updated successfully."));
        } catch (\Exception $e) {
            return responseWithError($e->getMessage());
        }
    }
}
