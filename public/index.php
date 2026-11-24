<?php
// public/index.php — form + processing (PRG pattern)
error_reporting(E_ALL);

require_once __DIR__ . '/../src/validate.php';

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function field(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

$values = ['name' => '', 'email' => '', 'message' => ''];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($values as $k => $_) {
        $values[$k] = field($k);
    }

    if (!validate_required($values['name'])) {
        $errors['name'] = 'Name is required.';
    } elseif (!validate_length($values['name'], 2, 60)) {
        $errors['name'] = 'Name must be between 2 and 60 characters.';
    }

    if (!validate_required($values['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!validate_email($values['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (!validate_required($values['message'])) {
        $errors['message'] = 'Message is required.';
    } elseif (!validate_length($values['message'], 10, 1000)) {
        $errors['message'] = 'Message must be between 10 and 1000 characters.';
    }

    if (!$errors) {
        // Post/Redirect/Get: redirect to a success state.
        header('Location: index.php?success=1', true, 303);
        exit;
    }
    http_response_code(200);
}

$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 520px; margin: 2rem auto; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input, textarea { width: 100%; padding: .5rem; box-sizing: border-box; }
        .error { color: #b00020; font-size: .9rem; margin: .25rem 0 0; }
        .success { background: #e6f4ea; border: 1px solid #1e8e3e; padding: .75rem; border-radius: 4px; }
        button { margin-top: 1rem; padding: .6rem 1.2rem; }
    </style>
</head>
<body>
    <h1>Contact Us</h1>

    <?php if ($success): ?>
        <p class="success">Thank you! Your message has been sent.</p>
    <?php endif; ?>

    <form method="post" action="index.php" novalidate>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= e($values['name']) ?>">
        <?php if (isset($errors['name'])): ?>
            <p class="error"><?= e($errors['name']) ?></p>
        <?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($values['email']) ?>">
        <?php if (isset($errors['email'])): ?>
            <p class="error"><?= e($errors['email']) ?></p>
        <?php endif; ?>

        <label for="message">Message</label>
        <textarea id="message" name="message" rows="6"><?= e($values['message']) ?></textarea>
        <?php if (isset($errors['message'])): ?>
            <p class="error"><?= e($errors['message']) ?></p>
        <?php endif; ?>

        <button type="submit">Send</button>
    </form>
</body>
</html>
