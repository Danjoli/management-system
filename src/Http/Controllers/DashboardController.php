<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Security\Auth;
use App\Security\Csrf;
use PDO;

final readonly class DashboardController
{
    public function __construct(private PDO $pdo, private Auth $auth, private Csrf $csrf)
    {
    }

    public function index(Request $request): Response
    {
        $metrics = $this->pdo->query(
            "SELECT
                (SELECT COUNT(*) FROM employees WHERE active = 1) employees,
                (SELECT COUNT(*) FROM departments WHERE active = 1) departments,
                (SELECT COUNT(*) FROM projects WHERE status = 'active') projects,
                (SELECT COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE -amount END),0)
                 FROM financial_transactions) balance"
        )->fetch();
        return View::render('dashboard/index', [
            'metrics' => is_array($metrics) ? $metrics : [],
            'user' => $this->auth->user(),
            'token' => $this->csrf->token(),
        ]);
    }
}
