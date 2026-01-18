<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Supplier Jaya',
                'email' => 'contact@supplierjaya.com',
                'phone' => '021-1234567',
                'address' => 'Jl. Merdeka No. 123',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
            ],
            [
                'name' => 'CV. Barang Berkualitas',
                'email' => 'info@barangberkualitas.com',
                'phone' => '031-9876543',
                'address' => 'Jl. Ahmad Yani No. 456',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60123',
            ],
            [
                'name' => 'Toko Grosir Elektronik',
                'email' => 'sales@togogrosir.com',
                'phone' => '0274-555666',
                'address' => 'Jl. Diponegoro No. 789',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'postal_code' => '55123',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
