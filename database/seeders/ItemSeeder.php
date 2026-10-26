<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lab      = Category::where('name', 'Alat Laboratorium')->first()->id;
        $olahraga = Category::where('name', 'Alat Olahraga')->first()->id;
        $elektronik = Category::where('name', 'Elektronik')->first()->id;

        $items = [
            // Alat Laboratorium (4 item)
            [
                'category_id' => $lab,
                'name'        => 'Mikroskop',
                'code'        => 'LAB-001',
                'stock'       => 5,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $lab,
                'name'        => 'Gelas Ukur 100ml',
                'code'        => 'LAB-002',
                'stock'       => 20,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $lab,
                'name'        => 'Tabung Reaksi',
                'code'        => 'LAB-003',
                'stock'       => 50,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $lab,
                'name'        => 'Bunsen Burner',
                'code'        => 'LAB-004',
                'stock'       => 8,
                'condition'   => 'rusak ringan',
            ],

            // Alat Olahraga (3 item)
            [
                'category_id' => $olahraga,
                'name'        => 'Bola Basket',
                'code'        => 'OLR-001',
                'stock'       => 10,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $olahraga,
                'name'        => 'Net Badminton',
                'code'        => 'OLR-002',
                'stock'       => 4,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $olahraga,
                'name'        => 'Matras Senam',
                'code'        => 'OLR-003',
                'stock'       => 6,
                'condition'   => 'rusak ringan',
            ],

            // Elektronik (3 item)
            [
                'category_id' => $elektronik,
                'name'        => 'Proyektor Epson',
                'code'        => 'ELK-001',
                'stock'       => 3,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $elektronik,
                'name'        => 'Laptop Acer',
                'code'        => 'ELK-002',
                'stock'       => 7,
                'condition'   => 'baik',
            ],
            [
                'category_id' => $elektronik,
                'name'        => 'Speaker Aktif',
                'code'        => 'ELK-003',
                'stock'       => 2,
                'condition'   => 'rusak berat',
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
