<?php

namespace Database\DataAccess\Implementations;

require_once __DIR__ . '/../Interfaces/ComputerPartDAO.php';
require_once __DIR__ . '/../../DatabaseManager.php';
require_once __DIR__ . '/../../../Models/ComputerPart.php';
require_once __DIR__ . '/../../../Models/DataTimeStamp.php';

use Database\DataAccess\Interfaces\ComputerPartDAO;
use Database\DatabaseManager;
use Models\ComputerPart;
use Models\DataTimeStamp;

class ComputerPartDAOImpl implements ComputerPartDAO
{
    public function create(ComputerPart $partData): bool
    {
        if($partData->getId() !== null) throw new \Exception('Cannot create a computer part with an existing ID. id: ' . $partData->getId());
        return $this->createOrUpdate($partData);
    }

    public function getById(int $id): ?ComputerPart
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $computerPart = $mysqli->prepareAndFetchAll("SELECT * FROM computer_parts WHERE id = ?",'i',[$id])[0]??null;

        return $computerPart === null ? null : $this->resultToComputerPart($computerPart);
    }

    public function update(ComputerPart $partData): bool
    {
        if($partData->getId() === null) throw new \Exception('Computer part specified has no ID.');

        $current = $this->getById($partData->getId());
        if($current === null) throw new \Exception(sprintf("Computer part %s does not exist.", $partData->getId()));

        return $this->createOrUpdate($partData);
    }

    public function delete(int $id): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        return $mysqli->prepareAndExecute("DELETE FROM computer_parts WHERE id = ?", 'i', [$id]);
    }

    public function getRandom(): ?ComputerPart
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        $computerPart = $mysqli->prepareAndFetchAll("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1",'',[])[0]??null;

        return $computerPart === null ? null : $this->resultToComputerPart($computerPart);
    }

    public function getAll(int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM computer_parts LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'ii', [$offset, $limit]);

        return $results === null ? [] : $this->resultsToComputerParts($results);
    }

    public function getAllByType(string $type, int $offset, int $limit): array
    {
        $mysqli = DatabaseManager::getMysqliConnection();

        $query = "SELECT * FROM computer_parts WHERE type = ? LIMIT ?, ?";

        $results = $mysqli->prepareAndFetchAll($query, 'sii', [$type, $offset, $limit]);
        return $results === null ? [] : $this->resultsToComputerParts($results);
    }

    public function createOrUpdate(ComputerPart $partData): bool
    {
        $mysqli = DatabaseManager::getMysqliConnection();
        // SQL ?:15, SQL row number: 15 
        // ON DUPLICATE KEY UPDATE句は、主に重複キーエラーが発生したときに実行されるため、この部分の行数が問題になることはありません。この部分では、重複が発生した場合に更新される列とその値を指定しています。ON DUPLICATE KEY UPDATE句の部分は、INSERT文がデータベースに存在する場合に使用されるため、バインドされる変数の数($resultの行数)とは直接関係ありません。VALUES句の部分(?の数)が正しくバインドされる変数の数（16個）と一致している場合、ON DUPLICATE KEY UPDATE句の行数が16であるかどうかは問題ありません。
        $query =
        <<<SQL
            INSERT INTO computer_parts (id, name, type, brand, model_number, release_date, description, performance_score, market_price, rsm, power_consumptionw, lengthm, widthm, heightm, lifespan, submitted_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            type = VALUES(type),
            brand = VALUES(brand),
            model_number = VALUES(model_number),
            release_date = VALUES(release_date),
            description = VALUES(description),
            performance_score = VALUES(performance_score),
            market_price = VALUES(market_price),
            rsm = VALUES(rsm),
            power_consumptionw = VALUES(power_consumptionw),
            lengthm = VALUES(lengthm),
            widthm = VALUES(widthm),
            heightm = VALUES(heightm),
            lifespan = VALUES(lifespan),
            submitted_by = VALUES(submitted_by);
        SQL;
        // i,s,d:16, paramater number: 16
        $result = $mysqli->prepareAndExecute(
            $query,
            'issssssidddddddi',
            [
                $partData->getId(), // on null ID, mysql will use auto-increment.
                $partData->getName(),
                $partData->getType(),
                $partData->getBrand(),
                $partData->getModelNumber(),
                $partData->getReleaseDate(),
                $partData->getDescription(),
                $partData->getPerformanceScore(),
                $partData->getMarketPrice(),
                $partData->getRsm(),
                $partData->getPowerConsumptionW(),
                $partData->getLengthM(),
                $partData->getWidthM(),
                $partData->getHeightM(),
                $partData->getLifespan(),
                $partData->getSubmittedById()
                // $partData->getId()
            ],
        );

        if(!$result) return false;

        // insert_id returns the last inserted ID.
        if($partData->getId() === null){
            $partData->setId($mysqli->insert_id);
            $timeStamp = $partData->getTimeStamp()??new DataTimeStamp(date('Y-m-h'), date('Y-m-h'));
            $partData->setTimeStamp($timeStamp);
        }

        return true;
    }

    public function convertResultsToComputerParts(array $results): array{
        return $this->resultsToComputerParts($results);
    }

    private function resultToComputerPart(array $data): ComputerPart{
        return new ComputerPart(
            name: $data['name'],
            type: $data['type'],
            brand: $data['brand'],
            id: $data['id'] ?? null,
            modelNumber: $data['model_number'],
            releaseDate: $data['release_date'],
            description: $data['description'],
            performanceScore: $data['performance_score'],
            marketPrice: $data['market_price'],
            rsm: $data['rsm'],
            powerConsumptionW: $data['power_consumptionw'],
            lengthM: $data['lengthm'],
            widthM: $data['widthm'],
            heightM: $data['heightm'],
            lifespan: $data['lifespan'],
            timeStamp: new DataTimeStamp($data['created_at'], $data['updated_at']),
            submitted_by_id: $data['submitted_by'],
        );
    }

    private function resultsToComputerParts(array $results): array{
        $computerParts = [];

        foreach($results as $result){
            $computerParts[] = $this->resultToComputerPart($result);
        }

        return $computerParts;
    }
}