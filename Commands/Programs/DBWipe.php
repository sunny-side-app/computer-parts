<?php

namespace Commands\Programs;

require_once "Commands/Argument.php";

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;

class DBWipe extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'db-wipe';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
            (new Argument('backup'))->description('Create buckup. execute mysqldump -u username -p dbname > backup.sql')->required(false)->allowAsShort(true),
        ];
    }

    public function execute(): int
    {
        $backup = $this->getArgumentValue('backup');
        if($backup === false){
            $this->log("Starting wipe DB......");
            $this->wipe();
        }
        else{
            $this->log("Running create backup....");
            $this->backup();
        }
        return 0;
    }

    private function wipe(): void {
        $this->log("Dropping DB...");
        $this->log("Crear ended...\n");
    }

    private function backup(): void {
        $this->log("Create backup...\n");
    }

}



// $mysqli = new MySQLWrapper();

// $result = $mysqli->query("
//     CREATE TABLE IF NOT EXISTS cars (
//       id INT PRIMARY KEY AUTO_INCREMENT,
//       make VARCHAR(50),
//       model VARCHAR(50),
//       year INT,
//       color VARCHAR(20),
//       price FLOAT,
//       mileage FLOAT,
//       transmission VARCHAR(20),
//       engine VARCHAR(20),
//       status VARCHAR(10)
//     );
// ");

// if($result === false) throw new Exception('Could not execute query.');
// else print("Successfully ran all SQL setup queries.".PHP_EOL);