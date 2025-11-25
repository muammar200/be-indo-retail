<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\MetaPaginateResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        $users = User::latest()->where('name', 'LIKE', "%$search%")->orWhere('no_hp', 'LIKE', "%$search%")->orWhere('jabatan', 'LIKE', "%$search%")->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Users Success',
            'meta' => new MetaPaginateResource($users),
            'data' => UserResource::collection($users),
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            $data = [
                'status' => true,
                'message' => 'Create User Success',
                'data' => new UserResource($user),
            ];

            return response()->json($data, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $data = [
            'status' => true,
            'message' => 'Get User Success by Id',
            'data' => new UserResource($user),
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            $validatedData = $request->only(['name', 'no_hp', 'jabatan']);

            $user->update($validatedData);

            $data = [
                'status' => true,
                'message' => 'Update User Success',
                'data' => new UserResource($user),
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();

            $data = [
                'status' => true,
                'message' => 'Delete User Success',
            ];

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
