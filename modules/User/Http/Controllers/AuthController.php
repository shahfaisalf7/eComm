<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Modules\Page\Entities\Page;
use Modules\User\Entities\User;
use Modules\User\LoginProvider;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Storefront\Entities\UserActivationCode;
use Modules\User\Events\CustomerRegistered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseAuthController
{
    public function getLogin()
    {
        return redirect('/');
    }

    public function redirectToProvider($provider)
    {
        if (!LoginProvider::isEnable($provider)) {
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if (!LoginProvider::isEnable($provider)) {
            abort(404);
        }
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
        if (User::registered($user->getEmail())) {
            auth()->login(User::findByEmail($user->getEmail()));
            return redirect($this->redirectTo());
        }
        [$firstName, $lastName] = $this->extractName($user->getName());
        $registeredUser = $this->auth->registerAndActivate([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->getEmail(),
            'phone' => '',
            'password' => str_random(),
        ]);
        $this->assignCustomerRole($registeredUser);
        auth()->login($registeredUser);
        return redirect($this->redirectTo());
    }

    public function getRegister()
    {
        return redirect('/');
    }

    public function getReset()
    {
        return view('storefront::public.auth.reset.begin');
    }

    protected function redirectTo()
    {
        return route('account.dashboard.index');
    }

    protected function loginUrl()
    {
        return route('login');
    }

    protected function resetCompleteRoute($user, $code)
    {
        return route('reset.complete', [$user->email, $code]);
    }

    protected function resetCompleteView()
    {
        return view('storefront::public.auth.reset.complete');
    }

    private function extractName($name)
    {
        return explode(' ', $name, 2);
    }

    private function getPrivacyPageUrl()
    {
        return Cache::tags('settings')->rememberForever('privacy_page_url', function () {
            return Page::urlForPage(setting('storefront_privacy_page'));
        });
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'regex:/^(01[3-9]\d{8}|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/',
            ],
        ], [
            'phone.required' => 'The phone or email field is required.',
            'phone.regex' => 'Please enter a valid Bangladeshi phone number (e.g., 013XXXXXXXX) or a valid email address.',
        ]);

        $type = $request->input('type') ?? 'login';
        $phone = $request->input('phone');
        $user = User::where('phone', $phone)->first();
        if (!empty($user) && $type == 'register') {
            return responseInvalidRequest(__("This phone number is linked to another account, please enter another number."));
        }
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

        $API_TOKEN = config('sms.API_TOKEN');
        $SID = config('sms.SID');
        $url = config('sms.SMS_URL');

        $otp_content_from_config = config('sms.LOGIN_SIGNUP_OTP_CONTENT');
        $otp_content = str_replace('{otp}', $otp, $otp_content_from_config);

        $params = [
            "api_token" => $API_TOKEN,
            "sid" => $SID,
            "msisdn" => '88' . $phone,
            "sms" => $otp_content,
            "csms_id" => 'SETCOL' . date('Ymd'),
        ];
        $params = json_encode($params);
        $response = $this->sendSmsCurl($url, $params);
        return responseWithData(__("OTP Sent Successfully!"), json_decode($response));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric',
        ]);
        $phone = $request->phone;
        $otpRecord = UserActivationCode::where('phone', $phone)
            ->where('activation_code', $request->otp)
            ->where('expiry', '>=', Carbon::now())
            ->first();
        if ($otpRecord) {
            $exists_user = User::where('phone', $phone)->first();
            if (!empty($exists_user)) {
                Auth::login($exists_user);
                if (isAPI()) {
                    $authMethod = getAuthMethod();
                    if ($authMethod === 'jwt') {
                        $token = JWTAuth::fromUser($exists_user);
                    } else if ($authMethod === 'passport') {
                        $token = $exists_user->createToken('api')->accessToken;
                    } else {
                        return responseWithError(__("Invalid auth method!"));
                    }
                    $data = ['token' => $token, 'user' => $exists_user];
                    return responseWithData(__("Login Successfully!"), $data);
                }
                return responseSuccess(__("Login Successfully!"));
            } else {
                $data = [
                    'name' => '*******' . substr($phone, -3),
                    'first_name' => '*******' . substr($phone, -3),
                    'last_name' => ' ',
                    'email' => null,
                    'phone' => $phone,
                    'password' => $phone,
                ];
                $user = $this->auth->registerAndActivate($data);
                $this->assignCustomerRole($user);
                event(new CustomerRegistered($user));
                $otpRecord->update(['user_id' => $user->id]);
                Auth::login($user);
                if (isAPI()) {
                    $authMethod = getAuthMethod();
                    if ($authMethod === 'jwt') {
                        $token = JWTAuth::fromUser($user);
                    } else if ($authMethod === 'passport') {
                        $token = $user->createToken('api')->accessToken;
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'Invalid auth method'], 400);
                    }
                    return response()->json(['status' => 'success', 'token' => $token, 'user' => $user], 200);
                }
                return ['status' => "success", 'message' => __("Login successfully.")];
            }
        }
        return responseWithError(__("Invalid or Expired OTP!"));
    }

    public function sendSms(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'api_token' => 'required|string',
                'sid' => 'required|string',
                'phone' => 'required|regex:/^\+?[1-9]\d{1,14}$/',
                'sms' => 'required|string|max:160',
                'csms_id' => 'required|string',
                'domain' => 'required|url'
            ]);

            if ($validator->fails()) {
                return ['status' => 'error', 'message' => $validator->errors()->first()];
            }

            $data = $request->all();

            $params = [
                "api_token" => $data['api_token'],
                "sid" => $data['sid'],
                "msisdn" => $data['phone'],
                "sms" => $data['sms'],
                "csms_id" => $data['csms_id'],
            ];

            $url = trim($data['domain'], '/') . "/api/v3/send-sms";
            $params = json_encode($params);

            return $this->sendSmsCurl($url, $params);
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
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

    public function userLogin()
    {
        try {
            $request = request();
            $validator = Validator::make($request->all(), [
                'email_phone' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return responseInvalidRequest($validator->errors()->first());
            }

            $email_phone = $request->input('email_phone');
            if (preg_match('/^(?:\+8801\d{9}|01\d{9})$/', $email_phone)) {
                $column = 'phone';
                $email_phone = substr($email_phone, -11);
                $phone = $email_phone;
            } elseif (filter_var($email_phone, FILTER_VALIDATE_EMAIL)) {
                $column = 'email';
                $email = $email_phone;
            } else {
                return responseWithError(__("Invalid input format!"));
            }

            $oldSessionId = session()->getId();

            if (isAPI()) {
                $credentials = [$column => $email_phone, 'password' => $request->password];
                if ($column == 'phone') {
                    $user = User::where('phone', $phone)->first();
                    if ($user && Hash::check($request->password, $user->password)) {
                        Auth::login($user);
                        $user = auth()->user();
                        $authMethod = getAuthMethod();
                        if ($authMethod === 'jwt') {
                            $token = auth()->guard('api')->attempt($credentials);
                        } else if ($authMethod === 'passport') {
                            $token = $user->createToken('api')->accessToken;
                        } else {
                            return responseWithError(__("Invalid auth method!"));
                        }
                        $data = ['token' => $token, 'user' => $user];
                        return responseWithData(__("Login Successfully!"), $data);
                    }
                    return responseWithError(__("Invalid phone number or password!"));
                } else {
                    if (auth()->attempt($credentials)) {
                        $user = auth()->user();
                        $authMethod = getAuthMethod();
                        if ($authMethod === 'jwt') {
                            $token = auth()->guard('api')->attempt($credentials);
                        } else if ($authMethod === 'passport') {
                            $token = $user->createToken('api')->accessToken;
                        } else {
                            return responseWithError(__("Invalid auth method!"));
                        }
                        $data = ['token' => $token, 'user' => $user];
                        return responseWithData(__("Login Successfully!"), $data);
                    } else {
                        return responseWithError(__("Invalid email or password!"));
                    }
                }
            }

            if ($column == 'phone') {
                $user = User::where('phone', $phone)->first();
                if ($user && Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                    $this->mergeGuestCart($user, $oldSessionId);
                    return response()->json(['status' => 'success', 'message' => 'Login successfully.'], 200);
                }
                return response()->json(['status' => 'error', 'message' => __("Invalid phone number or password")]);
            } else {
                $loggedIn = $this->auth->login([
                    'email' => $email,
                    'password' => $request->password,
                ], (bool)$request->get('remember_me', false));
                if (!$loggedIn) {
                    return response()->json(['status' => 'error', 'message' => trans('user::messages.users.invalid_credentials')]);
                }
                $this->mergeGuestCart(auth()->user(), $oldSessionId);
                return response()->json(['status' => 'success', 'message' => 'Login successfully.'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function mergeGuestCart($user, $sessionId)
    {
        $userId = (string) $user->id;
        $guestCartId = $sessionId . '_cart_items';

        \Log::info("Merging guest cart", ['session_id' => $sessionId, 'guest_cart_id' => $guestCartId, 'user_id' => $userId]);
        $guestCart = \Modules\Cart\Entities\Cart::find($guestCartId);
        if ($guestCart) {
            \Log::info("Guest cart found", ['data' => $guestCart->data]);
            \Modules\Cart\Entities\Cart::updateOrCreate(
                ['id' => $userId . '_cart_items'],
                ['data' => $guestCart->data, 'updated_at' => now()]
            );
            $guestCart->delete();
            \Log::info("Guest cart merged and deleted");
        } else {
            \Log::warning("No guest cart found for session", ['guest_cart_id' => $guestCartId]);
        }
    }
}
