<?php

// 下記配列の値のHTTPRenderer オブジェクトがビュー、ルートのコールバックがコントローラ、データベースデータがモデル

require_once __DIR__ . '/../Exceptions/AuthenticationFailureException.php';
require_once __DIR__ . '/../Helpers/Authenticate.php';
// require_once __DIR__ . '/../Helpers/DatabaseHelper.php';
require_once __DIR__ . '/../Helpers/ValidationHelper.php';
require_once __DIR__ . '/../Response/HTTPRenderer.php';
require_once __DIR__ . '/../Response/Render/HTMLRenderer.php';
require_once __DIR__ . '/../Response/Render/JSONRenderer.php';
require_once __DIR__ . '/../Database/DataAccess/Implementations/ComputerPartDAOImpl.php';
require_once __DIR__ . '/../Types/ValueType.php';
require_once __DIR__ . '/../Models/ComputerPart.php';
require_once __DIR__ . '/../Models/DataTimeStamp.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Database/DataAccess/DAOFactory.php';
require_once __DIR__ . '/../Response/FlashData.php';
require_once __DIR__ . '/../Response/Render/RedirectRenderer.php';
require_once __DIR__ . '/../Routing/Route.php';
require_once __DIR__ . '/../Response/Render/MediaRenderer.php';

use Exceptions\AuthenticationFailureException;
use Helpers\Authenticate;
// use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Response\FlashData;
use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Types\ValueType;
use Models\ComputerPart;
use Models\DataTimeStamp;
use Models\User;
use Database\DataAccess\DAOFactory;
use Response\Render\RedirectRenderer;
use Routing\Route;
use Response\Render\MediaRenderer;

// AuthenticatedMiddleware を使用するルート: logout, update/part, form/update/part
// GuestMiddleware を使用するルート: login, form/login, register, form/register

return [
    'login' => Route::create('login', function (): HTTPRenderer {
        return new HTMLRenderer('page/login');
    })->setMiddleware(['guest']),
    'form/login' => Route::create('form/login', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'email' => ValueType::EMAIL,
                'password' => ValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            Authenticate::authenticate($validatedData['email'], $validatedData['password']);

            FlashData::setFlashData('success', 'Logged in successfully.');
            return new RedirectRenderer('update/part');
        } catch (AuthenticationFailureException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Failed to login, wrong email and/or password.');
            return new RedirectRenderer('login');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('login');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('login');
        }
    })->setMiddleware(['guest']),
    'register' => Route::create('register', function (): HTTPRenderer {
        return new HTMLRenderer('page/register');
    })->setMiddleware(['guest']),
    'form/register' => Route::create('form/register', function (): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'username' => ValueType::STRING,
                'email' => ValueType::EMAIL,
                'password' => ValueType::PASSWORD,
                'confirm_password' => ValueType::PASSWORD,
                'company' => ValueType::STRING,
            ];

            $userDao = DAOFactory::getUserDAO();

            // シンプルな検証
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if($validatedData['confirm_password'] !== $validatedData['password']){
                FlashData::setFlashData('error', 'Invalid Password!');
                return new RedirectRenderer('register');
            }

            // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
            if($userDao->getByEmail($validatedData['email'])){
                FlashData::setFlashData('error', 'Email is already in use!');
                return new RedirectRenderer('register');
            }

            // 新しいUserオブジェクトを作成します
            $user = new User(
                username: $validatedData['username'],
                email: $validatedData['email'],
                company: $validatedData['company']
            );

            // データベースにユーザーを作成しようとします
            $success = $userDao->create($user, $validatedData['password']);

            if (!$success) throw new Exception('Failed to create new user!');

            // ユーザーログイン
            Authenticate::loginAsUser($user);

            FlashData::setFlashData('success', 'Account successfully created.');
            return new RedirectRenderer('random/part');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('register');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('register');
        }
    })->setMiddleware(['guest']),
    'logout' => Route::create('logout', function (): HTTPRenderer {
        Authenticate::logoutUser();
        FlashData::setFlashData('success', 'Logged out.');
        return new RedirectRenderer('random/part');
    })->setMiddleware(['auth']),
    'random/part' => Route::create('random/part', function (): HTTPRenderer {
        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    }),
    'parts' => Route::create('parts', function (): HTTPRenderer {
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    }),
    'update/part' => Route::create('update/part', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();
        $part = null;
        $partDao = DAOFactory::getComputerPartDAO();
        if(isset($_GET['id'])){
            $id = ValidationHelper::integer($_GET['id']);
            $part = $partDao->getById($id);
            if($user->getId() !== $part->getSubmittedById()){
                FlashData::setFlashData('error', 'Only the author can edit this computer part.');
                return new RedirectRenderer('register');
            }
        }
        return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
    })->setMiddleware(['auth']),
    'form/update/part' => Route::create('form/update/part', function (): HTTPRenderer {
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

            $partDao = DAOFactory::getComputerPartDAO();

            // 入力に対する単純な検証。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $user = Authenticate::getAuthenticatedUser();

            // idが設定されている場合は、認証を行います
            if(isset($_POST['id'])){
                $validatedData['id'] = ValidationHelper::integer($_POST['id']);
                $currentPart = $partDao->getById($_POST['id']);
                if($currentPart === null || $user->getId() !== $currentPart->getSubmittedById()){
                    return new JSONRenderer(['status' => 'error', 'message' => 'Invalid Data Permissions!']);
                }
            } else {
                // `id` フィールドが設定されていない場合のデフォルト値を設定します
                $validatedData['id'] = null;
            }

            $validatedData['submitted_by_id'] = $user->getId();
            // $validatedData['timeStamp'] = new DataTimeStamp(date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));

            // デバッグ用に$validatedData配列の内容をログに出力
            error_log(print_r($validatedData, true));

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋スプレット構文による入力
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます
            // 別の方法として、createOrUpdateを実行することもできます
            if(isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());// エラーログは PHP のログや stdout から見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    })->setMiddleware(['auth']),
    'test/share/files/jpeg'=> Route::create('test/share/files/jpeg', function(): HTTPRenderer{
        // このURLは署名を必要とするため、URLが正しい署名を持つ場合にのみ、この最終ルートコードに到達します。
        $required_fields = [
            'user' => ValueType::STRING,
            'filename' => ValueType::STRING, // 本番環境では、有効なファイルパスに対してバリデーションを行いますが、ファイルパスの単純な文字列チェックを行います。
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        return new MediaRenderer(sprintf("private/shared/%s/%s", $validatedData['user'],$validatedData['filename']), 'jpeg');
    })->setMiddleware(['signature']),
    'test/share/files/jpeg/generate-url'=> Route::create('test/share/files/jpeg/generate-url', function(): HTTPRenderer{
        $required_fields = [
            'user' => ValueType::STRING,
            'filename' => ValueType::STRING, // 本番環境では、有効なファイルパスに対してバリデーションを行いますが、ファイルパスの単純な文字列チェックを行います。
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        if(isset($_GET['lasts'])){
            $validatedData['expiration'] = time() + ValidationHelper::integer($_GET['lasts']);
        }

        return new JSONRenderer(['url'=>Route::create('test/share/files/jpeg', function(){})->getSignedURL($validatedData)]);
    }),
];

// return [
//     // 'random/part'=>function(): HTTPRenderer{
//     //     $part = DatabaseHelper::getRandomComputerPart();

//     //     return new HTMLRenderer('component/random-part', ['part'=>$part]);
//     // },

//     // use DAO
//     // 'random/part'=>function(): HTTPRenderer{
//     //     $partDao = new ComputerPartDAOImpl();
//     //     $part = $partDao->getRandom();

//     //     if($part === null) throw new Exception('No parts are available!');

//     //     return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
//     // },

//     // use DAOFactory
//     'random/part'=>function(): HTTPRenderer{
//         $partDao = DAOFactory::getComputerPartDAO();
//         $part = $partDao->getRandom();

//         if($part === null) throw new Exception('No parts are available!');

//         return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
//     },
//     // 'parts'=>function(): HTTPRenderer{
//     //     // IDの検証, ??はNull合体演算子であり左辺の値が null もしくは存在しない場合に右辺の値を返す
//     //     $id = ValidationHelper::integer($_GET['id']??null);

//     //     $part = DatabaseHelper::getComputerPartById($id);
//     //     return new HTMLRenderer('component/parts', ['part'=>$part]);
//     // },
//     // use DAO
//     // 'parts'=>function(): HTTPRenderer{
//     //     // IDの検証
//     //     $id = ValidationHelper::integer($_GET['id']??null);

//     //     $partDao = new ComputerPartDAOImpl();
//     //     $part = $partDao->getById($id);

//     //     if($part === null) throw new Exception('Specified part was not found!');

//     //     return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
//     // },

//     // use DAOFactory
//     'parts'=>function(): HTTPRenderer{
//         // IDの検証
//         $id = ValidationHelper::integer($_GET['id']??null);

//         $partDao = DAOFactory::getComputerPartDAO();
//         $part = $partDao->getById($id);

//         if($part === null) throw new Exception('Specified part was not found!');

//         return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
//     },
//     'api/random/part'=>function(): HTTPRenderer{
//         $part = DatabaseHelper::getRandomComputerPart();
//         return new JSONRenderer(['part'=>$part]);
//     },
//     'api/parts'=>function(){
//         $id = ValidationHelper::integer($_GET['id']??null);
//         $part = DatabaseHelper::getComputerPartById($id);
//         return new JSONRenderer(['part'=>$part]);
//     },
//     'types' => function(): HTTPRenderer {
//         try {
//             $type = $_GET['type'] ?? null;
//             $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
//             $perPage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
    
//             if (!$type) {
//                 throw new \InvalidArgumentException('Type parameter is required');
//             }
    
//             $parts = DatabaseHelper::getComputerPartsByType($type, $page, $perPage);
//             return new HTMLRenderer('component/parts-list', [
//                 'type' => $type,
//                 'page' => $page,
//                 'perPage' => $perPage,
//                 'parts' => $parts,
//             ]);
//         } catch (\InvalidArgumentException $e) {
//             return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
//         } catch (Exception $e) {
//             return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
//         }
//     },
//     'parts/newest' => function(): HTTPRenderer {
//         try {
//             $page = ValidationHelper::integer($_GET['page'] ?? 1, 1);
//             $perPage = ValidationHelper::integer($_GET['perpage'] ?? 10, 1, 100);
    
//             $parts = DatabaseHelper::getNewestComputerParts($page, $perPage);
//             return new HTMLRenderer('component/parts-newest', [
//                 'page' => $page,
//                 'perPage' => $perPage,
//                 'parts' => $parts,
//             ]);
//         } catch (\InvalidArgumentException $e) {
//             return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
//         } catch (Exception $e) {
//             return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
//         }
//     },
//     'parts/performance' => function(): HTTPRenderer {
//         try {
//             $order = $_GET['order'] ?? 'desc';
//             $type = $_GET['type'] ?? null;

//             if (!$type) {
//                 throw new \InvalidArgumentException('Type parameter is required');
//             }

//             $parts = DatabaseHelper::getPerformanceComputerParts($type, $order);
//             return new HTMLRenderer('component/parts-perform', [
//                 'type' => $type,
//                 'order' => $order,
//                 'parts' => $parts,
//             ]);
//         } catch (\InvalidArgumentException $e) {
//             return new HTMLRenderer('errors/400', ['message' => $e->getMessage()]);
//         } catch (Exception $e) {
//             return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
//         }
//     },
//     'random/computer' => function(): HTTPRenderer {
//         try {
//             $cpu = DatabaseHelper::getRandomComputerPartByType('CPU');
//             $gpu = DatabaseHelper::getRandomComputerPartByType('GPU');
//             $ram = DatabaseHelper::getRandomComputerPartByType('RAM');
//             $ssd = DatabaseHelper::getRandomComputerPartByType('SSD');
    
//             return new HTMLRenderer('component/random-computer', [
//                 'cpu' => $cpu,
//                 'gpu' => $gpu,
//                 'ram' => $ram,
//                 'ssd' => $ssd,
//             ]);
//         } catch (Exception $e) {
//             return new HTMLRenderer('errors/500', ['message' => $e->getMessage()]);
//         }
//     },
//     // use DAO
//     // 'update/part' => function(): HTMLRenderer {
//     //     $part = null;
//     //     $partDao = new ComputerPartDAOImpl();
//     //     if(isset($_GET['id'])){
//     //         $id = ValidationHelper::integer($_GET['id']);
//     //         $part = $partDao->getById($id);
//     //     }
//     //     return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
//     // },
//     // use DAO
//     // 'form/update/part' => function(): HTTPRenderer {
//     //     try {
//     //         // リクエストメソッドがPOSTかどうかをチェックします
//     //         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     //             throw new Exception('Invalid request method!');
//     //         }

//     //         $required_fields = [
//     //             'name' => ValueType::STRING,
//     //             'type' => ValueType::STRING,
//     //             'brand' => ValueType::STRING,
//     //             'modelNumber' => ValueType::STRING,
//     //             'releaseDate' => ValueType::DATE,
//     //             'description' => ValueType::STRING,
//     //             'performanceScore' => ValueType::INT,
//     //             'marketPrice' => ValueType::FLOAT,
//     //             'rsm' => ValueType::FLOAT,
//     //             'powerConsumptionW' => ValueType::FLOAT,
//     //             'lengthM' => ValueType::FLOAT,
//     //             'widthM' => ValueType::FLOAT,
//     //             'heightM' => ValueType::FLOAT,
//     //             'lifespan' => ValueType::INT,
//     //         ];

//     //         $partDao = new ComputerPartDAOImpl();

//     //         // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
//     //         $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

//     //         if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

//     //         // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
//     //         $part = new ComputerPart(...$validatedData);

//     //         error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

//     //         // 新しい部品情報でデータベースの更新を試みます。
//     //         // 別の方法として、createOrUpdateを実行することもできます。
//     //         if(isset($validatedData['id'])) $success = $partDao->update($part);
//     //         else $success = $partDao->create($part);

//     //         if (!$success) {
//     //             throw new Exception('Database update failed!');
//     //         }

//     //         return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
//     //     } catch (\InvalidArgumentException $e) {
//     //         error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
//     //         return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
//     //     } catch (Exception $e) {
//     //         error_log($e->getMessage());
//     //         return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
//     //     }
//     // },
//     'update/part' => function(): HTMLRenderer {
//         $part = null;
//         $partDao = DAOFactory::getComputerPartDAO();
//         if(isset($_GET['id'])){
//             $id = ValidationHelper::integer($_GET['id']);
//             $part = $partDao->getById($id);
//         }
//         return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
//     },
//     'form/update/part' => function(): HTTPRenderer {
//         try {
//             // リクエストメソッドがPOSTかどうかをチェックします
//             if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//                 throw new Exception('Invalid request method!');
//             }

//             $required_fields = [
//                 'name' => ValueType::STRING,
//                 'type' => ValueType::STRING,
//                 'brand' => ValueType::STRING,
//                 'modelNumber' => ValueType::STRING,
//                 'releaseDate' => ValueType::DATE,
//                 'description' => ValueType::STRING,
//                 'performanceScore' => ValueType::INT,
//                 'marketPrice' => ValueType::FLOAT,
//                 'rsm' => ValueType::FLOAT,
//                 'powerConsumptionW' => ValueType::FLOAT,
//                 'lengthM' => ValueType::FLOAT,
//                 'widthM' => ValueType::FLOAT,
//                 'heightM' => ValueType::FLOAT,
//                 'lifespan' => ValueType::INT,
//             ];

//             $partDao = DAOFactory::getComputerPartDAO();

//             // 入力に対する単純な認証です。実際のシナリオでは、要件を満たす完全な認証が必要になることがあります。
//             $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

//             if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

//             // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋スプレッド演算子を用いて、配列の要素を別々の変数や関数の引数として展開
//             $part = new ComputerPart(...$validatedData);

//             error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

//             // 新しい部品情報でデータベースの更新を試みます。
//             // 別の方法として、createOrUpdateを実行することもできます。
//             if(isset($validatedData['id'])) $success = $partDao->update($part);
//             else $success = $partDao->create($part);

//             if (!$success) {
//                 throw new Exception('Database update failed!');
//             }

//             return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
//         } catch (\InvalidArgumentException $e) {
//             // エラーログはPHPのログやstdoutから見ることができます。
//             error_log($e->getMessage());
//             return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
//         } catch (Exception $e) {
//             error_log($e->getMessage());
//             return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
//         }
//     },
//     // 登録ページでありViewはregister.phpが対応
//     // 'register'=>function(): HTTPRenderer{
//     //     return new HTMLRenderer('page/register');
//     // },
//     'register'=>function(): HTTPRenderer{
//         if(Authenticate::isLoggedIn()){
//             FlashData::setFlashData('error', 'Cannot register as you are already logged in.');
//             return new RedirectRenderer('random/part');
//         }

//         return new HTMLRenderer('page/register');
//     },
//     'form/register' => function(): HTTPRenderer {
//         // ユーザが現在ログインしている場合、登録ページにアクセスすることはできません。
//         if(Authenticate::isLoggedIn()){
//             FlashData::setFlashData('error', 'Cannot register as you are already logged in.');
//             return new RedirectRenderer('random/part');
//         }

//         try {
//             // リクエストメソッドがPOSTかどうかをチェックします
//             if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

//             $required_fields = [
//                 'username' => ValueType::STRING,
//                 'email' => ValueType::EMAIL,
//                 'password' => ValueType::PASSWORD,
//                 'confirm_password' => ValueType::PASSWORD,
//                 'company' => ValueType::STRING,
//             ];

//             $userDao = DAOFactory::getUserDAO();

//             // シンプルな検証
//             $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

//             // デバッグ用：検証後のデータをログに出力
//             error_log(print_r($validatedData, true));

//             if($validatedData['confirm_password'] !== $validatedData['password']){
//                 error_log('Passwords do not match: ' . $validatedData['confirm_password'] . ' !== ' . $validatedData['password']);
//                 FlashData::setFlashData('error', 'Invalid Password!');
//                 return new RedirectRenderer('register');
//             }

//             // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
//             if($userDao->getByEmail($validatedData['email'])){
//                 FlashData::setFlashData('error', 'Email is already in use!');
//                 return new RedirectRenderer('register');
//             }

//             // 新しいUserオブジェクトを作成します
//             $user = new User(
//                 username: $validatedData['username'],
//                 email: $validatedData['email'],
//                 company: $validatedData['company']
//             );

//             // データベースにユーザーを作成しようとします
//             $success = $userDao->create($user, $validatedData['password']);

//             if (!$success) throw new Exception('Failed to create new user!');

//             // ユーザーログイン
//             Authenticate::loginAsUser($user);

//             FlashData::setFlashData('success', 'Account successfully created.');
//             return new RedirectRenderer('random/part');
//         } catch (\InvalidArgumentException $e) {
//             error_log($e->getMessage());

//             FlashData::setFlashData('error', 'Invalid Data.');
//             return new RedirectRenderer('register');
//         } catch (Exception $e) {
//             error_log($e->getMessage());

//             FlashData::setFlashData('error', 'An error occurred.');
//             return new RedirectRenderer('register');
//         }
//     },
//     'logout'=>function(): HTTPRenderer{
//         if(!Authenticate::isLoggedIn()){
//             FlashData::setFlashData('error', 'Already logged out.');
//             return new RedirectRenderer('random/part');
//         }

//         Authenticate::logoutUser();
//         FlashData::setFlashData('success', 'Logged out.');
//         return new RedirectRenderer('random/part');
//     },
//     'login'=>function(): HTTPRenderer{
//         if(Authenticate::isLoggedIn()){
//             FlashData::setFlashData('error', 'You are already logged in.');
//             return new RedirectRenderer('random/part');
//         }

//         return new HTMLRenderer('page/login');
//     },
//     'form/login'=>function(): HTTPRenderer{
//         if(Authenticate::isLoggedIn()){
//             FlashData::setFlashData('error', 'You are already logged in.');
//             return new RedirectRenderer('random/part');
//         }

//         try {
//             if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

//             $required_fields = [
//                 'email' => ValueType::EMAIL,
//                 'password' => ValueType::STRING,
//             ];

//             $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

//             Authenticate::authenticate($validatedData['email'], $validatedData['password']);

//             FlashData::setFlashData('success', 'Logged in successfully.');
//             return new RedirectRenderer('update/part');
//         } catch (AuthenticationFailureException $e) {
//             error_log($e->getMessage());

//             FlashData::setFlashData('error', 'Failed to login, wrong email and/or password.');
//             return new RedirectRenderer('login');
//         } catch (\InvalidArgumentException $e) {
//             error_log($e->getMessage());

//             FlashData::setFlashData('error', 'Invalid Data.');
//             return new RedirectRenderer('login');
//         } catch (Exception $e) {
//             error_log($e->getMessage());

//             FlashData::setFlashData('error', 'An error occurred.');
//             return new RedirectRenderer('login');
//         }
//     },
// ];