<div class="container mt-3">
    <div class="row mb-3">
        <div class="col">
            <h2>Randomly Generated Computer</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    CPU
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($cpu['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($cpu['type']) ?> - <?= htmlspecialchars($cpu['brand']) ?></h6>
                    <p class="card-text">
                        <strong>Model:</strong> <?= htmlspecialchars($cpu['model_number']) ?><br>
                        <strong>Release Date:</strong> <?= htmlspecialchars($cpu['release_date']) ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($cpu['description']) ?><br>
                        <strong>Performance Score:</strong> <?= htmlspecialchars($cpu['performance_score']) ?><br>
                        <strong>Market Price:</strong> $<?= htmlspecialchars($cpu['market_price']) ?><br>
                        <strong>RSM:</strong> $<?= htmlspecialchars($cpu['rsm']) ?><br>
                        <strong>Power Consumption:</strong> <?= htmlspecialchars($cpu['power_consumptionw']) ?>W<br>
                        <strong>Dimensions:</strong> <?= htmlspecialchars($cpu['lengthm']) ?>m x <?= htmlspecialchars($cpu['widthm']) ?>m x <?= htmlspecialchars($cpu['heightm']) ?>m<br>
                        <strong>Lifespan:</strong> <?= htmlspecialchars($cpu['lifespan']) ?> years<br>
                        <strong>Created at:</strong> <?= htmlspecialchars($cpu['created_at']) ?><br>
                        <!-- 他のCPUの情報を追加 -->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    GPU
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($gpu['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($gpu['type']) ?> - <?= htmlspecialchars($gpu['brand']) ?></h6>
                    <p class="card-text">
                        <strong>Model:</strong> <?= htmlspecialchars($gpu['model_number']) ?><br>
                        <strong>Release Date:</strong> <?= htmlspecialchars($gpu['release_date']) ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($gpu['description']) ?><br>
                        <strong>Performance Score:</strong> <?= htmlspecialchars($gpu['performance_score']) ?><br>
                        <strong>Market Price:</strong> $<?= htmlspecialchars($gpu['market_price']) ?><br>
                        <strong>RSM:</strong> $<?= htmlspecialchars($gpu['rsm']) ?><br>
                        <strong>Power Consumption:</strong> <?= htmlspecialchars($gpu['power_consumptionw']) ?>W<br>
                        <strong>Dimensions:</strong> <?= htmlspecialchars($gpu['lengthm']) ?>m x <?= htmlspecialchars($gpu['widthm']) ?>m x <?= htmlspecialchars($gpu['heightm']) ?>m<br>
                        <strong>Lifespan:</strong> <?= htmlspecialchars($gpu['lifespan']) ?> years<br>
                        <strong>Created at:</strong> <?= htmlspecialchars($gpu['created_at']) ?><br>
                        <!-- 他のGPUの情報を追加 -->
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    RAM
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($ram['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ram['type']) ?> - <?= htmlspecialchars($ram['brand']) ?></h6>
                    <p class="card-text">
                        <strong>Model:</strong> <?= htmlspecialchars($ram['model_number']) ?><br>
                        <strong>Release Date:</strong> <?= htmlspecialchars($ram['release_date']) ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($ram['description']) ?><br>
                        <strong>Performance Score:</strong> <?= htmlspecialchars($ram['performance_score']) ?><br>
                        <strong>Market Price:</strong> $<?= htmlspecialchars($ram['market_price']) ?><br>
                        <strong>RSM:</strong> $<?= htmlspecialchars($ram['rsm']) ?><br>
                        <strong>Power Consumption:</strong
                        <?= htmlspecialchars($ram['power_consumptionw']) ?>W<br>
                        <strong>Dimensions:</strong> <?= htmlspecialchars($ram['lengthm']) ?>m x <?= htmlspecialchars($ram['widthm']) ?>m x <?= htmlspecialchars($ram['heightm']) ?>m<br>
                        <strong>Lifespan:</strong> <?= htmlspecialchars($ram['lifespan']) ?> years<br>
                        <strong>Created at:</strong> <?= htmlspecialchars($ram['created_at']) ?><br>
                        <!-- 他のRAMの情報を追加 -->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    SSD
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($ssd['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ssd['type']) ?> - <?= htmlspecialchars($ssd['brand']) ?></h6>
                    <p class="card-text">
                        <strong>Model:</strong> <?= htmlspecialchars($ssd['model_number']) ?><br>
                        <strong>Release Date:</strong> <?= htmlspecialchars($ssd['release_date']) ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($ssd['description']) ?><br>
                        <strong>Performance Score:</strong> <?= htmlspecialchars($ssd['performance_score']) ?><br>
                        <strong>Market Price:</strong> $<?= htmlspecialchars($ssd['market_price']) ?><br>
                        <strong>RSM:</strong> $<?= htmlspecialchars($ssd['rsm']) ?><br>
                        <strong>Power Consumption:</strong> <?= htmlspecialchars($ssd['power_consumptionw']) ?>W<br>
                        <strong>Dimensions:</strong> <?= htmlspecialchars($ssd['lengthm']) ?>m x <?= htmlspecialchars($ssd['widthm']) ?>m x <?= htmlspecialchars($ssd['heightm']) ?>m<br>
                        <strong>Lifespan:</strong> <?= htmlspecialchars($ssd['lifespan']) ?> years<br>
                        <strong>Created at:</strong> <?= htmlspecialchars($ssd['created_at']) ?><br>
                        <!-- 他のSSDの情報を追加 -->
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
