<?php

namespace Database\SeedsDao;

require_once __DIR__ . '/../AbstractSeeder.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../Database/DataAccess/DAOFactory.php';

use Database\AbstractSeeder;
use Database\DataAccess\DAOFactory;
use Faker\Factory as Faker;


class ComputerPartsDaoSeeder extends AbstractSeeder {
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
    public function createRowData(int $numberOfRows = 50): array {
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

            // $data[] = [
            //     $faker->word,
            //     $type,
            //     $brand,
            //     strtoupper($faker->bothify('??##??##')),
            //     $faker->date(),
            //     $description,
            //     $faker->numberBetween(80, 100),
            //     $faker->randomFloat(2, 50, 700),
            //     $faker->randomFloat(2, 0, 0.3),
            //     $faker->randomFloat(1, 1, 320),
            //     $faker->randomFloat(2, 0.01, 0.3),
            //     $faker->randomFloat(2, 0.01, 0.2),
            //     $faker->randomFloat(4, 0.001, 0.01),
            //     $faker->numberBetween(3, 10)
            // ];
            $data[] = [
                'name' => $faker->word,
                'type' => $type,
                'brand' => $brand,
                'model_number' => strtoupper($faker->bothify('??##??##')),
                'release_date' => $faker->date(),
                'description' => $description,
                'performance_score' => $faker->numberBetween(80, 100),
                'market_price' => $faker->randomFloat(2, 50, 700),
                'rsm' => $faker->randomFloat(2, 0, 0.3),
                'power_consumptionw' => $faker->randomFloat(1, 1, 320),
                'lengthm' => $faker->randomFloat(2, 0.01, 0.3),
                'widthm' => $faker->randomFloat(2, 0.01, 0.2),
                'heightm' => $faker->randomFloat(4, 0.001, 0.01),
                'lifespan' => $faker->numberBetween(3, 10),
                'created_at' => $faker->dateTimeThisDecade->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeThisDecade->format('Y-m-d H:i:s')
            ];
        }

        return $data;
    }
    public function seed(): void {
        $partDao = DAOFactory::getComputerPartDAO();
        $partArray = $this->createRowData();

        $computerParts = $partDao->convertResultsToComputerParts($partArray);

        foreach($computerParts as $result){
            $partDao->create($result);
        }
        // // ref: foreach block in AbstractSeeder.seed()
        // foreach ($partArray as $partRow) {
        //     $partData = new ComputerPart($partRow);
        //     // ref: create() in ComputerPartDAOImple.php
        //     $partDao->create($partData);
        // }
        
    }
}