<?php

namespace Database\Seeds;

require_once "Database/AbstractSeeder.php";
require_once "vendor/autoload.php";

use Database\AbstractSeeder;
use Faker\Factory as Faker;

class ComputerPartsSeeder extends AbstractSeeder {
    protected ?string $tableName = 'computer_parts';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'type'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'brand'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model_number'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'release_date'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'performance_score'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'market_price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'rsm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'power_consumptionw'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'lengthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'widthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'heightm'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'lifespan'
        ]
    ];
    public function createRowData(int $numberOfRows = 1000): array {
        $faker = Faker::create();
        $data = [];

        $types = ['CPU', 'GPU', 'SSD', 'RAM'];
        $brands = ['AMD', 'NVIDIA', 'Samsung', 'Corsair'];
        $descriptions = [
            'A high-performance 12-core processor.',
            'A powerful gaming GPU with ray tracing support.',
            'A fast NVMe M.2 SSD with 500GB storage.',
            'A DDR4 memory kit operating at 3200MHz.'
        ];

        for ($i = 0; $i < $numberOfRows; $i++) {
            $type = $faker->randomElement($types);
            $brand = $faker->randomElement($brands);
            $description = $faker->randomElement($descriptions);

            $data[] = [
                $faker->word,
                $type,
                $brand,
                strtoupper($faker->bothify('??##??##')),
                $faker->date(),
                $description,
                $faker->numberBetween(80, 100),
                $faker->randomFloat(2, 50, 700),
                $faker->randomFloat(2, 0, 0.3),
                $faker->randomFloat(1, 1, 320),
                $faker->randomFloat(2, 0.01, 0.3),
                $faker->randomFloat(2, 0.01, 0.2),
                $faker->randomFloat(4, 0.001, 0.01),
                $faker->numberBetween(3, 10)
            ];
        }

        return $data;
    }

    // public function createRowData(): array {
    //     return [
    //         [
    //             'Ryzen 9 5900X',
    //             'CPU',
    //             'AMD',
    //             '100-000000061',
    //             '2020-11-05',
    //             'A high-performance 12-core processor.',
    //             90,
    //             549.99,
    //             0.05,
    //             105.0,
    //             0.04,
    //             0.04,
    //             0.005,
    //             5
    //         ],
    //         [
    //             'GeForce RTX 3080',
    //             'GPU',
    //             'NVIDIA',
    //             '10G-P5-3897-KR',
    //             '2020-09-17',
    //             'A powerful gaming GPU with ray tracing support.',
    //             93,
    //             699.99,
    //             0.04,
    //             320.0,
    //             0.285,
    //             0.112,
    //             0.05,
    //             5
    //         ],
    //         [
    //             'Samsung 970 EVO SSD',
    //             'SSD',
    //             'Samsung',
    //             'MZ-V7E500BW',
    //             '2018-04-24',
    //             'A fast NVMe M.2 SSD with 500GB storage.',
    //             88,
    //             79.99,
    //             0.02,
    //             5.7,
    //             0.08,
    //             0.022,
    //             0.0023,
    //             5
    //         ],
    //         [
    //             'Corsair Vengeance LPX 16GB',
    //             'RAM',
    //             'Corsair',
    //             'CMK16GX4M2B3200C16',
    //             '2015-08-10',
    //             'A DDR4 memory kit operating at 3200MHz.',
    //             85,
    //             69.99,
    //             0.03,
    //             1.2,
    //             0.137,
    //             0.03,
    //             0.007,
    //             7
    //         ]
    //     ];
    // }
}