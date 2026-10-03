<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Security\Auth;
use App\Security\Csrf;
use PDO;

final readonly class ReportController
{
    public function __construct(private PDO $pdo, private Auth $auth, private Csrf $csrf)
    {
    }

    public function index(Request $request): Response
    {
        if (!$this->auth->can('reports.view')) {
            return new Response('Acesso negado', 403);
        }
        $from = (string) $request->input('from', date('Y-01-01'));
        $to = (string) $request->input('to', date('Y-m-d'));
        $rows = $this->financialRows($from, $to);
        return View::render('reports/index', compact('rows', 'from', 'to') + ['token' => $this->csrf->token()]);
    }

    public function csv(Request $request): Response
    {
        if (!$this->auth->can('reports.view')) {
            return new Response('Acesso negado', 403);
        }
        $from = (string) $request->input('from', date('Y-01-01'));
        $to = (string) $request->input('to', date('Y-m-d'));
        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            return new Response('Export unavailable', 500);
        }
        fputcsv($stream, ['Departamento', 'Receitas', 'Despesas', 'Saldo', 'Posição'], ';');
        foreach ($this->financialRows($from, $to) as $row) {
            fputcsv($stream, $row, ';');
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return new Response("\xEF\xBB\xBF" . $csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="relatorio-financeiro.csv"',
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function financialRows(string $from, string $to): array
    {
        $statement = $this->pdo->prepare(
            "WITH totals AS (
                SELECT d.id, d.name department,
                    COALESCE(SUM(CASE WHEN f.type='income' THEN f.amount ELSE 0 END),0) income,
                    COALESCE(SUM(CASE WHEN f.type='expense' THEN f.amount ELSE 0 END),0) expense
                FROM departments d
                LEFT JOIN financial_transactions f ON f.department_id=d.id AND f.occurred_on BETWEEN :from AND :to
                GROUP BY d.id,d.name
            )
            SELECT department,income,expense,(income-expense) balance,
                DENSE_RANK() OVER (ORDER BY (income-expense) DESC) ranking
            FROM totals ORDER BY ranking,department"
        );
        $statement->execute(['from' => $from, 'to' => $to]);
        return $statement->fetchAll();
    }
}
