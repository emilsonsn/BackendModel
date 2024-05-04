<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\User\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function paginate(Request $request)
    {
        $data = $this->userService->paginate($request);

        return response()->json($data, 200);
    }

    public function getById($id)
    {
        $data = $this->userService->getById($id);

        if ($data['status']) {
            return $this->response(
                $data['status'],
                data: $data['data'],
                message: 'Usuário localizado',
                httpStatusCode: 200,
            );
        }

        return $this->response(
            $data['status'],
            message: $data['error'],
            httpStatusCode: 400
        );
    }

    public function userInfo()
    {
        $data = $this->userService->userInfo();

        if ($data['status']) {
            return $this->response(
                $data['status'],
                data: $data['data'],
                message: 'Usuário localizado',
                httpStatusCode: 200
            );
        }

        return $this->response(
            $data['status'],
            message: $data['error'],
            httpStatusCode: 400
        );
    }

    public function create(Request $request)
    {
        $data = $this->userService->create($request);

        if ($data['status']) {
            return $this->response(
                $data['status'],
                data: $data['data'],
                message: 'Usuário criado',
                log: $data['log'],
                httpStatusCode: 200
            );
        }

        return $this->response(
            $data['status'],
            message: $data['error'],
            httpStatusCode: 400
        );
    }

    public function update($id, Request $request)
    {
        $data = $this->userService->update($id, $request);

        if ($data['status']) {
            return $this->response(
                $data['status'],
                data: $data['data'],
                message: 'Usuário atualizado',
                log: $data['log'],
                httpStatusCode: 200
            );
        }

        return $this->response(
            $data['status'],
            message: $data['error'],
            httpStatusCode: 400
        );
    }

    public function delete($id)
    {
        $data = $this->userService->delete($id);

        if ($data['status']) {
            return $this->response(
                $data['status'],
                message: 'Usuário removido',
                log: $data['log'],
                httpStatusCode: 200
            );
        }

        return $this->response(
            $data['status'],
            message: $data['error'],
            httpStatusCode: 400
        );
    }
}
