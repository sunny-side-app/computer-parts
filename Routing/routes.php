<?php

// 下記配列の値のHTTPRenderer オブジェクトがビュー、ルートのコールバックがコントローラ、データベースデータがモデル

require_once("Helpers/DatabaseHelper.php");
require_once("Helpers/ValidationHelper.php");
require_once("Response/HTTPRenderer.php");
require_once("Response/Render/HTMLRenderer.php");
require_once("Response/Render/JSONRenderer.php");

use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    'random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();

        return new HTMLRenderer('component/random-part', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証, ??はNull合体演算子であり左辺の値が null もしくは存在しない場合に右辺の値を返す
        $id = ValidationHelper::integer($_GET['id']??null);

        $part = DatabaseHelper::getComputerPartById($id);
        return new HTMLRenderer('component/parts', ['part'=>$part]);
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
];