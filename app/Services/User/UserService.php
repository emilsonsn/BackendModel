<?php

namespace App\Services\User;

use Exception;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidateErrors;

class UserService
{

    public function paginate($request)
    {
        try {
            $pageSize = $request->take == '' ? 0 : $request->take;
            $orderField = $request->orderField ?? 'id';
            $order = $request->order ?? 'DESC';

            $users = User::orderBy($orderField, $order);

            if (isset($request->search_term)) {
                $searchTerm = $request->search_term;
                $users->where('name', 'like', "%$searchTerm%")
                    ->orWhere('email', 'like', "%$searchTerm%");
            }

            if ($pageSize == 0) {
                $users = ["data" => $users->get()];
            } else {
                $users = $users->paginate($pageSize);
            }

            return $users;
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function getById($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if (!isset($user)) {
                throw new Exception("Não foi possível localizar usuário de id $id", 400);
            }

            return ['status' => true, 'data' => $user];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function userInfo()
    {
        try {
            $user = JWTAuth::user();
            return ['status' => true, 'data' => $user];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function create($request)
    {
        try {
            $user = auth()->user();
            $validate = $this->validators($request->all());

            if ($validate->fails()) {
                $errors = ValidateErrors::toStr($validate->errors());
                throw new Exception($errors, 400);
            }

            $newUser = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'group' => $request->group
            ]);

            return ['status' => true, 'data' => $newUser, 'log' => "O usuário $user->id ($user->name) criou um usuário: $newUser->name | $newUser->group"];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function update($id, $request)
    {
        try {
            $user = auth()->user();
            $validate = $this->validators($request->all(), true);

            if ($validate->fails()) {
                $errors = ValidateErrors::toStr($validate->errors());
                throw new Exception($errors, 400);
            }

            $userUpdated = [
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'grupo' => $request->grupo
            ];

            if (isset($request->password) && !is_null($request->password)) {
                $userUpdated['password'] = bcrypt($request->password);
            }

            $userToUpdate = User::where('id', $id)->first();

            if (!isset($userToUpdate)) {
                throw new Exception('Usuário não encontrado', 400);
            }

            $userToUpdate->update($userUpdated);

            return ['status' => true, 'data' => $userToUpdate, 'log' => "O usuário $user->id ($user->name) atualizou um usuário: $user->name"];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function delete($id)
    {
        try {
            $user = auth()->user();
            $userToDelete = User::where('id', $id)->first();

            if (!$userToDelete) {
                throw new Exception('Usuário não encontrado', 400);
            }

            $userToDelete->delete();

            return ['status' => true, 'log' => "O usuário $user->id ($user->name) removeu usuário: $userToDelete->name"];
        } catch (\Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    private function validators($data, $update = false)
    {
        $validacao = [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'group' => 'required|in:MASTER,MANAGER,SUPPORT,FINANCIAL,INTEGRATION',
            'image' => 'nullable|string|max:255',
        ];

        if ($update) {
            $validacao['id'] = 'required|integer';
            $validacao['email'] = '';
            $validacao['password'] = '';
        }

        return Validator::make($data, $validacao);
    }
}
