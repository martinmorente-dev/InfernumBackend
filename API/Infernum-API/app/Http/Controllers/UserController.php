<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    #[OA\Post(
        path: '/v1/login',
        operationId: 'loginUser',
        tags: ['Auth'],
        summary: 'Log in user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserLoginRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful login',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Successfull'),
                        new OA\Property(property: 'user', ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123...')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Incorrect credentials',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Incorrect credentials')
                    ]
                )
            )
        ]
    )]
    public function login(UserLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password))
            return response()->json(['status' => 'Error: Incorrect credentials'], 401);

        Auth::login($user);
        if ($user->role != 'admin')
            $token = $user->createToken('client', ['buy', 'cart', 'library', 'view-profile'], now()->addHour(3))->plainTextToken;
        else
            $token = $user->createToken('admin', ['admin'], now()->addHour(3))->plainTextToken;

        return response()->json([
            'status' => 'Successfull',
            'user' => UserResource::make($user),
            'expires_in_hours' => 3,
            'token' => $token
        ], 200);
    }

    #[OA\Post(
        path: '/v1/register',
        operationId: 'registerUser',
        tags: ['Auth'],
        summary: 'Register new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UserRegisterRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Successfull'),
                        new OA\Property(property: 'created-user', ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'expires_in_minutes', type: 'string', example: '3'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123...')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Bad data'),
                        new OA\Property(property: 'errors', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['role'] = 'client';

        $user = User::create($data);
        $token = $user->createToken('client', ['buy', 'cart', 'library', 'view-profile'], now()->addHour(3))->plainTextToken;

        return response()->json([
            'status' => 'Successfull',
            'created_user' => UserResource::make($user),
            'expires_in_minutes' => 3,
            'token' => $token
        ], 201);
    }

    #[OA\Get(
        path: '/v1/user',
        operationId: 'getAuthenticatedUser',
        tags: ['User'],
        security: [['sanctum' => []]],
        summary: 'Get authenticated user (TOKEN REQUIRED)',
        description: '**NO TOKEN = 401!**\n\n1. **POST /v1/login** → copy token\n2. **Authorize** → paste `Bearer {token}`. **Execute this route** ✅',
        responses: [
            new OA\Response(
                response: 200,
                description: 'User OK',
                content: new OA\JsonContent(ref: '#/components/schemas/UserResource')
            ),
            new OA\Response(
                response: 401,
                description: 'TOKEN required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Unauthenticated')
                    ]
                )
            )
        ]
    )]
    public function getUserAuthenticated(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'Succesfull',
            'user' => $request->user()
        ]);
    }
}
