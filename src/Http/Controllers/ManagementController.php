<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Security\Auth;
use App\Security\Csrf;
use PDO;

final readonly class ManagementController
{
    /** @var array<string, array{title:string,permission:string,query:string}> */
    private const RESOURCES = [
        'departments' => ['title' => 'Departamentos', 'permission' => 'departments.manage', 'query' => 'SELECT id,name,cost_center,budget,active,created_at FROM departments ORDER BY name'],
        'employees' => ['title' => 'Colaboradores', 'permission' => 'employees.manage', 'query' => 'SELECT e.id,e.name,e.email,e.position,e.salary,e.hired_at,d.name department FROM employees e JOIN departments d ON d.id=e.department_id ORDER BY e.name'],
        'projects' => ['title' => 'Projetos', 'permission' => 'projects.manage', 'query' => 'SELECT p.id,p.name,d.name department,p.status,p.budget,p.starts_at,p.ends_at FROM projects p JOIN departments d ON d.id=p.department_id ORDER BY p.created_at DESC'],
        'transactions' => ['title' => 'Financeiro', 'permission' => 'transactions.manage', 'query' => 'SELECT f.id,f.type,f.category,f.description,f.amount,f.occurred_on,d.name department FROM financial_transactions f JOIN departments d ON d.id=f.department_id ORDER BY f.occurred_on DESC,f.id DESC'],
    ];

    public function __construct(
        private PDO $pdo,
        private Auth $auth,
        private Csrf $csrf,
        private Session $session,
    ) {
    }

    public function index(Request $request, string $resource): Response
    {
        $config = self::RESOURCES[$resource] ?? null;
        if ($config === null) {
            return new Response('Not Found', 404);
        }
        if (!$this->auth->can($config['permission'])) {
            return new Response('Acesso negado', 403);
        }
        return View::render('management/index', [
            'resource' => $resource,
            'title' => $config['title'],
            'rows' => $this->pdo->query($config['query'])->fetchAll(),
            'departments' => $this->pdo->query('SELECT id,name FROM departments WHERE active=1 ORDER BY name')->fetchAll(),
            'projects' => $this->pdo->query("SELECT id,name FROM projects WHERE status IN ('planned','active') ORDER BY name")->fetchAll(),
            'token' => $this->csrf->token(),
            'message' => $this->session->pullFlash('success'),
            'error' => $this->session->pullFlash('error'),
        ]);
    }

    public function store(Request $request, string $resource): Response
    {
        $config = self::RESOURCES[$resource] ?? null;
        if ($config === null || !$this->auth->can($config['permission'])) {
            return new Response('Acesso negado', 403);
        }
        try {
            match ($resource) {
                'departments' => $this->department($request),
                'employees' => $this->employee($request),
                'projects' => $this->project($request),
                'transactions' => $this->transaction($request),
                default => throw new \InvalidArgumentException('Recurso inválido'),
            };
            $this->session->flash('success', 'Registro criado com sucesso.');
        } catch (\Throwable $exception) {
            $this->session->flash('error', 'Não foi possível salvar: ' . $exception->getMessage());
        }
        return Response::redirect('/manage/' . $resource);
    }

    private function department(Request $request): void
    {
        $statement = $this->pdo->prepare('INSERT INTO departments (name,cost_center,budget) VALUES (:name,:cost_center,:budget)');
        $statement->execute($this->required($request, ['name', 'cost_center', 'budget']));
    }

    private function employee(Request $request): void
    {
        $statement = $this->pdo->prepare('INSERT INTO employees (department_id,name,email,position,salary,hired_at) VALUES (:department_id,:name,:email,:position,:salary,:hired_at)');
        $statement->execute($this->required($request, ['department_id', 'name', 'email', 'position', 'salary', 'hired_at']));
    }

    private function project(Request $request): void
    {
        $statement = $this->pdo->prepare('INSERT INTO projects (department_id,name,description,status,budget,starts_at,ends_at) VALUES (:department_id,:name,:description,:status,:budget,:starts_at,:ends_at)');
        $statement->execute($this->required($request, ['department_id', 'name', 'description', 'status', 'budget', 'starts_at', 'ends_at']));
    }

    private function transaction(Request $request): void
    {
        $user = $this->auth->user();
        Database::transaction($this->pdo, function (PDO $pdo) use ($request, $user): void {
            $data = $this->required($request, ['department_id', 'project_id', 'type', 'category', 'description', 'amount', 'occurred_on']);
            $data['project_id'] = $data['project_id'] === '' ? null : $data['project_id'];
            $data['user_id'] = $user['id'] ?? 0;
            $statement = $pdo->prepare('INSERT INTO financial_transactions (department_id,project_id,user_id,type,category,description,amount,occurred_on) VALUES (:department_id,:project_id,:user_id,:type,:category,:description,:amount,:occurred_on)');
            $statement->execute($data);
        });
    }

    /**
     * @param list<string> $fields
     * @return array<string, mixed>
     */
    private function required(Request $request, array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $value = $request->input($field);
            if ($value === null || (is_string($value) && trim($value) === '' && !in_array($field, ['project_id', 'description', 'starts_at', 'ends_at'], true))) {
                throw new \InvalidArgumentException("O campo {$field} é obrigatório.");
            }
            $data[$field] = is_string($value) ? trim($value) : $value;
        }
        return $data;
    }
}
