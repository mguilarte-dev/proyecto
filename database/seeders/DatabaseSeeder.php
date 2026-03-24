<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Áreas por defecto
        $this->call(AreaSeeder::class);
        $firstArea = \App\Models\Area::first();

        // Categorías por defecto
        Category::create([
            'name' => 'Inteligencia Artificial',
            'description' => 'Cursos relacionados con IA, Machine Learning y Automatización.'
        ]);

        Category::create([
            'name' => 'Elearning',
            'description' => 'Metodologías de enseñanza digital y gestión de plataformas de aprendizaje.'
        ]);

        // Administrador por defecto
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'area_id' => $firstArea->id
        ]);

        // Usuario de prueba (Empleado)
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'empleado',
            'area_id' => $firstArea->id
        ]);
    }
}
