<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Game;
class GamesSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            [
                'data' => [
                    'name' => 'DARK SOULS™: REMASTERED',
                    'short_description' => 'Entonces llegó el Fuego. Vuelve a disfrutar del aclamado juego que definió el género con el que empezó todo.',
                    'long_description' => 'Entonces llegó el Fuego. Vuelve a disfrutar del aclamado juego que definió el género con el que empezó todo. Gracias a una magnífica remasterización, podrás regresar a Lordran con unos impresionantes detalles en alta definición y a 60 fps.',
                    'price' => 39.99, 'count_boughts' => 0, 'discounts_id' => 1
                ],
                'genres' => [1,2,3,4]
            ],
            [
                'data' => [
                    'name' => 'Resident Evil Requiem',
                    'short_description' => 'Requiem for the dead. Nightmare for the living.',
                    'long_description' => 'Requiem for the dead. Nightmare for the living. A new era of survival horror arrives with Resident Evil Requiem.',
                    'price' => 69.99, 'count_boughts' => 0, 'discounts_id' => null
                ],
                'genres' => [5,6,7,8]
            ],
            [
                'data' => ['name' => 'Cyberpunk 2077', 'short_description' => 'RPG de acción en mundo abierto ambientado en Night City.', 'long_description' => 'Cyberpunk 2077 es una historia de acción y aventura en mundo abierto ambientada en Night City.', 'price' => 59.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [3, 8, 9, 10]
            ],
            [
                'data' => ['name' => "Baldur's Gate 3", 'short_description' => 'Reúne a tu grupo y regresa a los Reinos Olvidados.', 'long_description' => "Baldur's Gate 3 es un RPG basado en grupos ambientado en el universo de D&D 5E.", 'price' => 59.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [3, 11, 24]
            ],
            [
                'data' => ['name' => 'Stardew Valley', 'short_description' => 'Heredaste la vieja granja de tu abuelo en Stardew Valley.', 'long_description' => 'Acabas de empezar tu vida en el campo.', 'price' => 14.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [12, 13]
            ],
            [
                'data' => ['name' => 'Hades', 'short_description' => 'Desafía a los dioses del inframundo como Zagreo.', 'long_description' => 'Hades es un rogue-like de mazmorras de los creadores de Bastion y Transistor.', 'price' => 24.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [3, 14]
            ],
            [
                'data' => ['name' => 'The Witcher 3: Wild Hunt', 'short_description' => 'Eres Geralt de Rivia, cazador de monstruos a sueldo.', 'long_description' => 'Eres Geralt de Rivia, cazador de monstruos a sueldo.', 'price' => 39.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [3, 2, 10]
            ],
            [
                'data' => ['name' => 'DOOM Eternal', 'short_description' => 'Los ejércitos del Infierno han invadido la Tierra.', 'long_description' => 'Los ejércitos del Infierno han invadido la Tierra.', 'price' => 39.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [15, 16]
            ],
            [
                'data' => ['name' => 'Among Us', 'short_description' => 'Juego de fiesta online de trabajo en equipo y traición.', 'long_description' => 'Un juego de fiesta online y local de trabajo en equipo y traición.', 'price' => 4.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [17, 18]
            ],
            [
                'data' => ['name' => 'Grand Theft Auto V', 'short_description' => 'Los Santos: una metrópoli bañada por el sol.', 'long_description' => 'Cuando una banda callejera choca con un joven criminal.', 'price' => 29.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [10, 16, 19]
            ],
            [
                'data' => ['name' => 'Red Dead Redemption 2', 'short_description' => 'América, 1899. El fin de la era del oeste salvaje.', 'long_description' => 'América, 1899. El fin de la era del oeste salvaje ha comenzado.', 'price' => 59.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [10, 20, 21]
            ],
            [
                'data' => ['name' => 'God of War', 'short_description' => 'Kratos es un dios de la guerra, ex soldado espartano.', 'long_description' => 'Kratos es un dios de la guerra, ex soldado espartano brutalmente ejecutado por el guerrero Ares.', 'price' => 49.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [16, 22]
            ],
            [
                'data' => ['name' => 'Portal 2', 'short_description' => 'Eres un sujeto de pruebas en un juego mortal.', 'long_description' => 'Portal 2 es la secuela del aclamado juego de puzzles en primera persona.', 'price' => 9.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [23, 8]
            ],
            [
                'data' => ['name' => 'Half-Life 2', 'short_description' => 'Gordon Freeman está de vuelta.', 'long_description' => 'Gordon Freeman está de vuelta y listo para luchar contra la invasión alienígena.', 'price' => 9.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [15, 24]
            ],
            [
                'data' => ['name' => 'Celeste', 'short_description' => 'Madeline está escalando la Montaña Celeste.', 'long_description' => 'Celeste es un plataformas de precisión sobre una joven llamada Madeline.', 'price' => 19.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [25, 24]
            ],
            [
                'data' => ['name' => 'Hollow Knight', 'short_description' => 'Desciende a antiguos salones bajo una montaña.', 'long_description' => 'Desciende a antiguos salones bajo una montaña.', 'price' => 14.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [26, 16]
            ],
            [
                'data' => ['name' => 'Disco Elysium - The Final Cut', 'short_description' => 'RPG de detective sobre fracaso, drogas y hombre cangrejo.', 'long_description' => 'Disco Elysium es un revolucionario RPG de detective narrativo.', 'price' => 39.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [3, 24]
            ],
            [
                'data' => ['name' => "Marvel's Spider-Man Remastered", 'short_description' => '¡El trabajo de un héroe nunca termina!', 'long_description' => '¡El trabajo de un héroe nunca termina!', 'price' => 49.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [27, 16]
            ],
            [
                'data' => ['name' => "Assassin's Creed Valhalla", 'short_description' => '¡Lidera tus clanes vikingos hacia una nueva tierra!', 'long_description' => '¡Lidera tus clanes vikingos hacia una nueva tierra!', 'price' => 59.99, 'count_boughts' => 0, 'discounts_id' => 2],
                'genres' => [28, 16]
            ],
            [
                'data' => ['name' => 'Horizon Forbidden West', 'short_description' => '¡Aloy regresa en una aventura épica!', 'long_description' => '¡Aloy regresa en una aventura épica!', 'price' => 59.99, 'count_boughts' => 0, 'discounts_id' => null],
                'genres' => [29, 3]
            ],
        ];

        foreach ($games as $game) {
            $g = Game::updateOrCreate(['name' => $game['data']['name']], $game['data']);
            $g->genres()->sync($game['genres']);
        }
    }
}
