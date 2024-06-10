<div class="container mt-3">
    <div class="row mb-3">
        <div class="col">
            <h2>Performance Computer Parts</h2>
            <p>Type: <?= htmlspecialchars($type) ?></p>
            <p>Order: <?= htmlspecialchars($order) ?></p>
        </div>
    </div>
    <div class="row">
        <?php if (empty($parts)): ?>
            <div class="col">
                <div class="alert alert-warning" role="alert">
                    No parts found for the specified criteria.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($parts as $part): ?>
                <div class="col-md-4 mb-3">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($part['name']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($part['type']) ?> - <?= htmlspecialchars($part['brand']) ?></h6>
                            <p class="card-text">
                                <strong>Model:</strong> <?= htmlspecialchars($part['model_number']) ?><br />
                                <strong>Release Date:</strong> <?= htmlspecialchars($part['release_date']) ?><br />
                                <strong>Description:</strong> <?= htmlspecialchars($part['description']) ?><br />
                                <strong>Performance Score:</strong> <?= htmlspecialchars($part['performance_score']) ?><br />
                                <strong>Market Price:</strong> $<?= htmlspecialchars($part['market_price']) ?><br />
                                <strong>RSM:</strong> $<?= htmlspecialchars($part['rsm']) ?><br />
                                <strong>Power Consumption:</strong> <?= htmlspecialchars($part['power_consumptionw']) ?>W<br />
                                <strong>Dimensions:</strong> <?= htmlspecialchars($part['lengthm']) ?>m x <?= htmlspecialchars($part['widthm']) ?>m x <?= htmlspecialchars($part['heightm']) ?>m<br />
                                <strong>Lifespan:</strong> <?= htmlspecialchars($part['lifespan']) ?> years<br />
                                <strong>Created at:</strong> <?= htmlspecialchars($part['created_at']) ?><br />
                            </p>
                            <p class="card-text"><small class="text-muted">Last updated on <?= htmlspecialchars($part['updated_at']) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

