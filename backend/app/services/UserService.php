<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/JWT.php';

class UserService {
    private UserModel $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function register(array $input): array {
        $v = new Validator($input);
        $v->required('name')
          ->required('email')
          ->required('password')
          ->email('email')
          ->minLength('name', 2)
          ->minLength('password', 6);

        if ($v->fails()) {
            return ['ok' => false, 'error' => $v->getFirstError(), 'errors' => $v->getErrors(), 'code' => 422];
        }

        $email = strtolower(trim($input['email']));
        if ($this->model->emailExists($email)) {
            return ['ok' => false, 'error' => 'That email is already registered.', 'code' => 409];
        }

        $id    = $this->model->create(trim($input['name']), $email, $input['password'], trim($input['phone'] ?? ''));
        $user  = $this->model->findById($id);
        $token = JWT::generate(['user_id' => $id, 'email' => $user['email']]);

        return ['ok' => true, 'data' => ['token' => $token, 'user' => $user], 'code' => 201];
    }

    public function login(array $input): array {
        $v = new Validator($input);
        $v->required('email')->required('password')->email('email');

        if ($v->fails()) {
            return ['ok' => false, 'error' => $v->getFirstError(), 'errors' => $v->getErrors(), 'code' => 422];
        }

        $user = $this->model->findByEmail(strtolower(trim($input['email'])));
        if (!$user || !password_verify($input['password'], $user['password'])) {
            // intentionally vague — don't reveal which field is wrong
            return ['ok' => false, 'error' => 'Invalid credentials.', 'code' => 401];
        }

        $token = JWT::generate(['user_id' => $user['id'], 'email' => $user['email']]);
        unset($user['password']);
        return ['ok' => true, 'data' => ['token' => $token, 'user' => $user], 'code' => 200];
    }

    public function getProfile(int $userId): array {
        $user = $this->model->findById($userId);
        if (!$user) {
            return ['ok' => false, 'error' => 'User not found.', 'code' => 404];
        }
        return ['ok' => true, 'data' => $user];
    }
}
