<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserProfileResource;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/v1/profile/show',
        operationId: 'show',
        tags: ['Profile'],
        security: [['sanctum' => []]],
        summary: 'Get authenticated user profile (TOKEN REQUIRED)',
        description: '**NO TOKEN = 401!**\n\n1. **POST /v1/login** → copy token\n2. **Authorize** → paste `Bearer {token}`. **Execute this route** ✅',
        responses: [
            new OA\Response(
                response: 200,
                description: 'User OK',
                content: new OA\JsonContent(ref: '#/components/schemas/UserProfileResource')
            ),
            new OA\Response(
                response: 401,
                description: 'TOKEN required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Unauthenticated'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Failure'),
                        new OA\Property(property: 'message', type: 'string', example: 'User not authenticated'),
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
        ]
    )]
    public function show(Request $request): JsonResponse
    {

        $user = $request->user()->load('profile');


        return response()->json([
            'status' => 'Succesful',
            'data' => UserProfileResource::make($user),
        ]);
    }

    #[OA\Put(
        path: '/v1/profile/update',
        operationId: 'updateProfile',
        tags: ['Profile'],
        security: [['sanctum' => []]],
        summary: 'Update authenticated user profile (TOKEN REQUIRED)',
        description: '**NO TOKEN = 401!**\n\n1. **POST /v1/login** → copy token\n2. **Authorize** → paste `Bearer {token}`. **Execute this route** ✅',
        requestBody: new OA\RequestBody(
            description: 'Data of the profile updated',
            required: false,
            content: new OA\JsonContent(
                ref: '#/components/schemas/ProfileRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated',
                content: new OA\JsonContent(ref: '#/components/schemas/ProfileResource')
            ),
            new OA\Response(
                response: 401,
                description: 'TOKEN required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Unauthenticated'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation Failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'Error: Failure'),
                        new OA\Property(property: 'message', type: 'string', example: 'User not authenticated'),
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ]
                )
            ),
        ]
    )]
    public function updateProfile(ProfileRequest $request): JsonResponse
    {
        $profile = Auth::user()->profile;
        $data = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $data['profile_picture'] = "storage/" . $file->store('profiles_images', 'public');
        }

        $profile->update($data);

        return response()->json([
            'status' => 'Succesful',
            'profile' => ProfileResource::make($profile->refresh()),
        ]);
    }
}
