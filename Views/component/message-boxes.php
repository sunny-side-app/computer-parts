<?php

// HTMLRender が成功/エラーメッセージをフラッシュデータを使用して表示するためのビュー

require_once __DIR__ . '/../../Response/FlashData.php';

use Response\FlashData;

$success = \Response\FlashData::getFlashData('success');
$error = \Response\FlashData::getFlashData('error');
?>

<div class="container mt-5 mb-5">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
</div>