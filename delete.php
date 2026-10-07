<?php
require_once 'db.php';
require_once 'functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id && deleteTask($pdo, $id)) {
    set_flash('success', 'Task deleted successfully.');
} else {
    set_flash('danger', 'Invalid task request or deletion failed.');
}

header('Location: index.php');
exit;