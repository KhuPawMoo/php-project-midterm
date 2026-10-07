<?php
require_once 'db.php';
require_once 'functions.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$tasks = getAllTasks($pdo, $search, $statusFilter);
$totalTasks = count($tasks);
$completedCount = count(array_filter($tasks, fn($t) => $t['completed'] == 1));
$pendingCount = $totalTasks - $completedCount;

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Student Task Manager</h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?= sanitize($flash['type']); ?>">
                <?= sanitize($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="stats-bar">
            <span><strong>Total Shown:</strong> <?= $totalTasks; ?></span>
            <span><strong>Completed:</strong> <?= $completedCount; ?></span>
            <span><strong>Pending:</strong> <?= $pendingCount; ?></span>
        </div>

        <div class="controls-row">
            <a href="create.php" class="btn btn-primary">+ Add New Task</a>

            <form method="GET" action="index.php" class="search-form">
                <input type="text" name="search" placeholder="Search title or category..." value="<?= sanitize($search); ?>">
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending Only</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed Only</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                <?php if ($search !== '' || $statusFilter !== ''): ?>
                    <a href="index.php" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr><td colspan="6" style="text-align:center;">No tasks found.</td></tr>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <?php 
                            $overdue = is_overdue($task['due_date'], (int)$task['completed']);
                            $rowClass = $task['completed'] ? 'completed-row' : ($overdue ? 'overdue-row' : '');
                        ?>
                        <tr class="<?= $rowClass; ?>">
                            <td>
                                <span class="badge <?= $task['completed'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?= $task['completed'] ? 'Completed' : 'Pending'; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= sanitize($task['title']); ?></strong>
                                <?php if ($task['description']): ?>
                                    <p class="desc-text"><?= sanitize($task['description']); ?></p>
                                <?php endif; ?>
                            </td>
                            <td><?= sanitize($task['category']); ?></td>
                            <td><span class="priority-<?= strtolower($task['priority']); ?>"><?= sanitize($task['priority']); ?></span></td>
                            <td>
                                <?= sanitize($task['due_date']); ?>
                                <?php if ($overdue): ?>
                                    <span class="badge badge-danger">OVERDUE</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="edit.php?id=<?= $task['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <a href="delete.php?id=<?= $task['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this task?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>