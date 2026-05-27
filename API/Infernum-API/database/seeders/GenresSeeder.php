<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenresSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Tipo <<Dark Souls>>', 'Fantasia oscura', 'Rol', 'Dificiles',
            'Survival Horror', 'Zombies', 'Horror', 'Third-Person Shooter',
            'Ciencia ficción', 'Cyberpunk', 'Mundo abierto', 'Turnos',
            'Simulación', 'Granja', 'Rogue-like', 'FPS', 'Acción',
            'Deducción social', 'Multijugador', 'Crimen', 'Western',
            'Aventura', 'Mitología', 'Puzles', 'Narrativa', 'Plataformas',
            'Metroidvania', 'Superhéroes', 'Sigilo', 'Post-apocalíptico'
        ];
        foreach ($genres as $genre) {
            Genre::firstOrCreate(['type' => $genre]);
        }
    }
}
