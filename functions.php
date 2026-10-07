<?php
// functions.php - Helpers & Database Queries
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function is_overdue(string $dueDate, int $completed): bool {
    return ($completed === 0) && (strtotime($dueDate) < strtotime(date('Y-m-d')));
}

function validate_task_input(array $data): array {
    $errors = [];
    if (empty(trim($data['title'] ?? ''))) {
        $errors[] = "Title is required.";
    }
    if (empty(trim($data['category'] ?? ''))) {
        $errors[] = "Category is required.";
    }
    if (!in_array($data['priority'] ?? '', ['Low', 'Medium', 'High'], true)) {
        $errors[] = "Invalid priority.";
    }
    if (empty($data['due_date']) || !strtotime($data['due_date'])) {
        $errors[] = "A valid due date is required.";
    }
    return $errors;
}

function getAllTasks(PDO $pdo, string $search = '', string $statusFilter = ''): array {
    $sql = "SELECT * FROM tasks WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (title LIKE :search1 OR category LIKE :search2)";
        $params['search1'] = '%' . $search . '%';
        $params['search2'] = '%' . $search . '%';
    }

    if ($statusFilter === 'pending') {
        $sql .= " AND completed = 0";
    } elseif ($statusFilter === 'completed') {
        $sql .= " AND completed = 1";
    }

    $sql .= " ORDER BY due_date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTaskById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

function createTask(PDO $pdo, array $data): bool {
    $sql = "INSERT INTO tasks (title, description, category, priority, due_date, completed) 
            VALUES (:title, :description, :category, :priority, :due_date, :completed)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'title'       => trim($data['title']),
        'description' => trim($data['description'] ?? ''),
        'category'    => trim($data['category']),
        'priority'    => $data['priority'],
        'due_date'    => $data['due_date'],
        'completed'   => !empty($data['completed']) ? 1 : 0
    ]);
}

function updateTask(PDO $pdo, int $id, array $data): bool {
    $sql = "UPDATE tasks 
            SET title = :title, description = :description, category = :category, 
                priority = :priority, due_date = :due_date, completed = :completed 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'id'          => $id,
        'title'       => trim($data['title']),
        'description' => trim($data['description'] ?? ''),
        'category'    => trim($data['category']),
        'priority'    => $data['priority'],
        'due_date'    => $data['due_date'],
        'completed'   => !empty($data['completed']) ? 1 : 0
    ]);
}

function deleteTask(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}