<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Security\Auth;
use App\Security\Csrf;

final readonly class AuthController
{
    public function __construct(private Auth $auth, private Csrf $csrf, private Session $session)
    {
    }

    public function form(Request $request): Response
    {
        if ($this->auth->check()) {
            return Response::redirect('/dashboard');
        }
        return View::render('auth/login', ['token' => $this->csrf->token(), 'error' => $this->session->pullFlash('error')], 'layouts/guest');
    }

    public function login(Request $request): Response
    {
        $email = (string) $request->input('email', '');
        $password = (string) $request->input('password', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$this->auth->attempt($email, $password)) {
            $this->session->flash('error', 'Credenciais inválidas ou usuário inativo.');
            return Response::redirect('/login');
        }
        return Response::redirect('/dashboard');
    }

    public function logout(Request $request): Response
    {
        $this->auth->logout();
        return Response::redirect('/login');
    }
}
