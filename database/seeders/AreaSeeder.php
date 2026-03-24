<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['name' => 'Tecnología y Desarrollo', 'description' => 'Ingeniería, Programación e Infraestructura.'],
            ['name' => 'Ventas y Comercial', 'description' => 'Ventas, Marketing y Estrategia Comercial.'],
            ['name' => 'Operaciones y Logística', 'description' => 'Gestión de operaciones y cadena de suministro.'],
            ['name' => 'Atención al Cliente', 'description' => 'Soporte y satisfacción del cliente.'],
            ['name' => 'Recursos Humanos', 'description' => 'Gestión de talento y cultura organizacional.'],
        ];

        foreach ($areas as $area) {
            \App\Models\Area::firstOrCreate(['name' => $area['name']], $area);
        }
    }
}
