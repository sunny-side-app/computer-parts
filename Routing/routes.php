<?php

// 下記配列の値のHTTPRenderer オブジェクトがビュー、ルートのコールバックがコントローラ、データベースデータがモデル

require_once("Helpers/DatabaseHelper.php");
require_once("Helpers/ValidationHelper.php");
require_once("Response/HTTPRenderer.php");
require_once("Response/Render/HTMLRenderer.php");
require_once("Response/Render/JSONRenderer.php");
require_once __DIR__ . '/../Database/DataAccess/Implementations/ComputerPartDAOImpl.php';
require_once __DIR__ . '/../Types/ValueType.php';
require_once __DIR__ . '/../Models/ComputerPart.php';

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Types\ValueType;
use Models\ComputerPart;

return [
    // 'random/part'=>function(): HTTPRenderer{
    //     $part = DatabaseHelper::getRandomComputerPart();

    //     return new HTMLRenderer('component/random-part', ['part'=>$part]);
    // },
    'random/part'=>function(): HTTPRenderer{
        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    // 'parts'=>function(): HTTPRenderer{
    //     // IDの検証, ??はNull合体演算子であり左辺の値が null もしくは存在しない場合に右辺の値を返す
    //     $id = ValidationHelper::integer($_GET['id']??null);

    //     $part = DatabaseHelper::getComputerPartById($id);
    //     return new HTMLRenderer('component/parts', ['part'=>$part]);
    // },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = new ComputerPartDAOImpl();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'api/random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part'=>$part]);
    },
    'api/parts'=>function(){
        $id = ValidationHelper::integer($_GET['id']??null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part'=>$part]);
    },
    'types' => function(): HTTPRenderer {
        try {
            $type = $_GET['type'] ?? null;
            $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
            $perPage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
    
            if (!$type) {
                throw new \InvalidArgumentException('Type parameter is required');
            }
    
            $parts = DatabaseHelper::getComputerPartsByType($type, $page, $perPage);
            return new HTMLRenderer('component/parts-list', [
                'type' => $type,
                'page' => $page,
                'perPage' => $perPage,
                'parts' => $parts,
            ]);
        } catch (\InvalidArgumentException $e) {
            return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
        }
    },
    'parts/newest' => function(): HTTPRenderer {
        try {
            $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
            $perPage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
    
            $parts = DatabaseHelper::getNewestComputerParts($page, $perPage);
            return new HTMLRenderer('component/parts-newest', [
                'page' => $page,
                'perPage' => $perPage,
                'parts' => $parts,
            ]);
        } catch (\InvalidArgumentException $e) {
            return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
        }
    },
    'parts/performance' => function(): HTTPRenderer {
        try {
            $order = $_GET['order'] ?? 'desc';
            $type = $_GET['type'] ?? null;

            if (!$type) {
                throw new \InvalidArgumentException('Type parameter is required');
            }

            $parts = DatabaseHelper::getPerformanceComputerParts($type, $order);
            return new HTMLRenderer('component/parts-perform', [
                'type' => $type,
                'order' => $order,
                'parts' => $parts,
            ]);
        } catch (\InvalidArgumentException $e) {
            return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
        } catch (Exception $e) {
            return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
        }
    },
    'random/computer' => function(): HTTPRenderer {
        try {
            $cpu = DatabaseHelper::getRandomComputerPartByType('CPU');
            $gpu = DatabaseHelper::getRandomComputerPartByType('GPU');
            $ram = DatabaseHelper::getRandomComputerPartByType('RAM');
            $ssd = DatabaseHelper::getRandomComputerPartByType('SSD');
    
            return new HTMLRenderer('component/random-computer', [
                'cpu' => $cpu,
                'gpu' => $gpu,
                'ram' => $ram,
                'ssd' => $ssd,
            ]);
        } catch (Exception $e) {
            return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
        }
    },
    'update/part' => function(): HTMLRenderer {
        $part = null;
        $partDao = new ComputerPartDAOImpl();
        if(isset($_GET['id'])){
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
        }
        return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
    },
    'form/update/part' => function(): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = new ComputerPartDAOImpl();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if(isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
];