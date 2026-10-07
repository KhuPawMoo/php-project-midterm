<?php
require_once 'db.php';
require_once 'functions.php';

$errors = [];
$formData = [
    'title'       => '',
    'description' => '',
    'category'    => '',
    'priority'    => 'Medium',
    'due_date'    => date('Y-m-d'),
    'completed'   => 0
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'title'       => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'category'    => $_POST['category'] ?? '',
        'priority'    => $_POST['priority'] ?? 'Medium',
        'due_date'    => $_POST['due_date'] ?? '',
        'completed'   => isset($_POST['completed']) ? 1 : 0
    ];

    $errors = validate_task_input($formData);

    if (empty($errors)) {
        if (createTask($pdo, $formData)) {
            set_flash('success', 'Task created successfully!');
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "An error occurred while saving.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container container-small">
        <h1>Add New Task</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= sanitize($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="create.php">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?= sanitize($formData['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?= sanitize($formData['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" value="<?= sanitize($formData['category']); ?>" required>
            </div>

            <div class="form-group">
                <label for="priority">Priority *</label>
                <select id="priority" name="priority">
                    <option value="Low" <?= $formData['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                    <option value="Medium" <?= $formData['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="High" <?= $formData['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date *</label>
                <input type="date" id="due_date" name="due_date" value="<?= sanitize($formData['due_date']); ?>" required>
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" id="completed" name="completed" value="1" <?= !empty($formData['completed']) ? 'checked' : ''; ?>>
                <label for="completed">Mark as Completed</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Task</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>