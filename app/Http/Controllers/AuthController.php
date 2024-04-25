<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $existingUser = User::where('email', $request->input('email'))->first();

        if ($existingUser) {
            return response()->json(['error' => 'Email đã tồn tại trong hệ thống. Vui lòng sử dụng một địa chỉ email khác.'], 422);
        }
        try {
            $user = User::create([
                'email' => $request->input('email'),
                'name' => $request->input('username'),
                'password' => Hash::make($request->input('password'))
            ]);

            return response()->json(['message' => 'Đăng ký thành công'], 200);
        } catch (Exception $e) {
            // Handle exceptions if any
            return response()->json(['error' => 'Đăng ký không thành công. Vui lòng thử lại sau.'], 500);
        }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $role = $user->role;
        $refreshToken = $this->createRefreshToken();

        return $this->respondWithToken($token, $refreshToken, $role);
    }

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback(): JsonResponse
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        /** @var User $user */
        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'email_verified_at' => now(),
                    'name' => $socialiteUser->getName(),
                    'google_id' => $socialiteUser->getId(),
                    'avatar' => $socialiteUser->getAvatar(),
                    'password' => null,
                ]
            );
        $token = auth()->login($user);

        return response()->json([
            'role' => $user->role,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function profile()
    {
        try {
            return response()->json(auth()->user());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    private function createRefreshToken()
    {
        $data = [
            'user_id' => auth()->user()->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refreh_ttl')
        ];

        $refreshToken = JWTAuth::getJWTProvider()->encode($data);

        return $refreshToken;
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $refreshToken = request()->refresh_token;

        try {
            $decode = JWTAuth::getJWTProvider()->decode($refreshToken);
            $user = User::find($decode['user_id']);

            if (!$user) {
                return response()->json(['error' => 'Không tìm thấy người dùng'], 404);
            }

            $currentTime = time();
            $refreshTokenExpiry = $decode['exp'];

            if ($currentTime < $refreshTokenExpiry) {
                // Nếu refresh_token chưa hết hạn, chỉ cập nhật access_token
                auth()->invalidate();
                $token = auth()->login($user);
                return $this->respondWithToken($token, $refreshToken);
            } else {
                auth()->invalidate();
                $token = auth()->login($user);
                $refreshToken = $this->createRefreshToken();
                return $this->respondWithToken($token, $refreshToken);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 500);
        }
    }


    protected function respondWithToken($token, $refreshToken, $role)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'role' => $role,
        ]);
    }
}
