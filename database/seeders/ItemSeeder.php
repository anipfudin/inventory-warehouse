<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();

        $items = [
            [
                'item_number' => 'ITM001',
                'name' => 'Resistor 1K Ohm',
                'description' => 'Resistor 1/4W 1K Ohm',
                'unit' => 'pcs',
                'unit_price' => 500,
                'minimum_stock' => 100,
                'supplier_id' => $suppliers->first()->id,
            ],
            [
                'item_number' => 'ITM002',
                'name' => 'Kapasitor 10uF',
                'description' => 'Kapasitor Elektrolit 10uF 50V',
                'unit' => 'pcs',
                'unit_price' => 1500,
                'minimum_stock' => 50,
                'supplier_id' => $suppliers->first()->id,
            ],
            [
                'item_number' => 'ITM003',
                'name' => 'LED Merah 5mm',
                'description' => 'LED 5mm Merah Bright',
                'unit' => 'pcs',
                'unit_price' => 2000,
                'minimum_stock' => 200,
                'supplier_id' => $suppliers->get(1)->id,
            ],
            [
                'item_number' => 'ITM004',
                'name' => 'IC 7404',
                'description' => 'IC NOT Gate DIP-14',
                'unit' => 'pcs',
                'unit_price' => 5000,
                'minimum_stock' => 20,
                'supplier_id' => $suppliers->get(1)->id,
            ],
            [
                'item_number' => 'ITM005',
                'name' => 'Kabel USB Tipe A-B 2m',
                'description' => 'Kabel USB 2.0 Grade A',
                'unit' => 'pcs',
                'unit_price' => 25000,
                'minimum_stock' => 10,
                'supplier_id' => $suppliers->get(2)->id,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
