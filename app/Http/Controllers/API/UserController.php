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
    /**
     * Menampilkan daftar pengguna dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', '');

        // Filter pencarian di kolom name, no_hp, dan jabatan
        $users = User::latest()
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('no_hp', 'LIKE', "%$search%")
            ->orWhere('jabatan', 'LIKE', "%$search%")
            ->paginate($perpage, ['*'], 'page', $page);

        $data = [
            'status' => true,
            'message' => 'Show Users Success',
            'meta' => new MetaPaginateResource($users),
            'data' => UserResource::collection($users),
        ];

        return response()->json($data, 200);
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(UserRequest $request)
    {
        try {
            // Membuat pengguna baru dengan data yang sudah tervalidasi
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
     * Menampilkan detail pengguna berdasarkan ID.
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
     * Mengupdate data pengguna yang sudah ada.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            // Mengambil data yang tervalidasi dan memperbarui data pengguna
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
     * Menghapus pengguna berdasarkan ID.
     */
    public function destroy(User $user)
    {
        try {
            // Menghapus pengguna berdasarkan ID
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
