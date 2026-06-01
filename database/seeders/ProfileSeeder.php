<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Media;
use App\Models\Event;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar registros previos de prueba por correo para evitar errores de duplicidad (Unique Constraint)
        // Al borrar el usuario, la eliminación en cascada limpia sus perfiles, media y eventos automáticamente.
        User::whereIn('email', [
            'alejandro@kita.app',
            'sofia@kita.app',
            'carlos@kita.app',
            'valentina@kita.app',
            'mariachis@kita.app',
            'mateo@kita.app',
            'sunset@kita.app',
            'camila@kita.app'
        ])->delete();        $musicians = [
            [
                'name' => 'Alejandro Gómez',
                'email' => 'alejandro@kita.app',
                'slug' => 'alejandro-gomez-guitarrista',
                'instruments' => ['Guitarrista'],
                'coverage_area' => ['Bucerías', 'Nuevo Vallarta'],
                'bio' => 'Guitarrista profesional de Bucerías con más de 10 años de trayectoria tocando rock, blues y jazz instrumental. Disponible para eventos privados, hoteles y restaurantes.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Alejandro Gómez - Live at El Chiringuito'],
                    ['type' => 'photo', 'path' => 'images/profiles/alejandro_guitar.jpg', 'title' => 'Sesión de fotos en Bucerías']
                ],
                'events' => [
                    ['title' => 'Noche de Jazz & Blues', 'description' => 'Show en vivo en El Chiringuito Bucerías', 'start_time' => now()->addDays(2)->setHour(20)->setMinute(0), 'end_time' => now()->addDays(2)->setHour(23)->setMinute(0), 'type' => 'show', 'location' => 'El Chiringuito, Bucerías'],
                    ['title' => 'Clase Magistral de Guitarra', 'description' => 'Sesión privada reservada', 'start_time' => now()->addDays(4)->setHour(14)->setMinute(0), 'end_time' => now()->addDays(4)->setHour(16)->setMinute(0), 'type' => 'unavailable_block', 'location' => 'Estudio Privado']
                ]
            ],
            [
                'name' => 'Sofía Mora',
                'email' => 'sofia@kita.app',
                'slug' => 'sofia-mora-vocalista',
                'instruments' => ['Vocalista'],
                'coverage_area' => ['Sayulita', 'San Pancho'],
                'bio' => 'Cantautora y vocalista con base en Sayulita. Llevo covers acústicos, ritmos indie folk y vibras tropicales a los mejores spots del pueblo mágico. Ideal para bodas de destino.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'vimeo', 'url' => 'https://vimeo.com/76979871', 'title' => 'Sofía Mora - Acoustic Session in Sayulita'],
                    ['type' => 'photo', 'path' => 'images/profiles/sofia_singing.jpg', 'title' => 'Atardecer en Sayulita Beach']
                ],
                'events' => [
                    ['title' => 'Acoustic Sunset', 'description' => 'Sesión acústica al atardecer frente al mar', 'start_time' => now()->addDays(1)->setHour(18)->setMinute(30), 'end_time' => now()->addDays(1)->setHour(21)->setMinute(0), 'type' => 'show', 'location' => 'Coco\'s Beach Club, Sayulita'],
                    ['title' => 'Boda Privada - Ceremonia', 'description' => 'Acompañamiento musical de ceremonia nupcial', 'start_time' => now()->addDays(5)->setHour(16)->setMinute(0), 'end_time' => now()->addDays(5)->setHour(18)->setMinute(0), 'type' => 'show', 'location' => 'Sayulita Escape Resort']
                ]
            ],
            [
                'name' => 'Carlos Ramírez',
                'email' => 'carlos@kita.app',
                'slug' => 'carlos-ramirez-baterista',
                'instruments' => ['Baterista'],
                'coverage_area' => ['Centro', 'Zona Hotelera'],
                'bio' => 'Baterista y percusionista versátil para sesiones de grabación, ensambles y shows en vivo en Puerto Vallarta. Amplia experiencia en ritmos latinos, funk, rock y jazz.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Carlos Ramírez - Drum Solo Session'],
                ],
                'events' => [
                    ['title' => 'Presentación en Malecón', 'description' => 'Show nocturno en bar del Malecón', 'start_time' => now()->addDays(3)->setHour(22)->setMinute(0), 'end_time' => now()->addDays(3)->setHour(25)->setMinute(0), 'type' => 'show', 'location' => 'La Bodeguita del Medio, Centro'],
                ]
            ],
            [
                'name' => 'Valentina Herrera',
                'email' => 'valentina@kita.app',
                'slug' => 'valentina-herrera-bajista',
                'instruments' => ['Bajista'],
                'coverage_area' => ['Zona Hotelera', 'Marina Vallarta'],
                'bio' => 'Bajista profesional egresada del conservatorio de música. Ofrezco la base rítmica y armónica más sólida para agrupaciones de jazz, bossa nova, funk y pop rock.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'photo', 'path' => 'images/profiles/valentina_bass.jpg', 'title' => 'Grabación de disco de Jazz'],
                ],
                'events' => [
                    ['title' => 'Jazz Night Duo', 'description' => 'Noche de jazz instrumental en lobby del hotel', 'start_time' => now()->addDays(2)->setHour(19)->setMinute(0), 'end_time' => now()->addDays(2)->setHour(22)->setMinute(0), 'type' => 'show', 'location' => 'Sheraton Lobby Bar, Zona Hotelera'],
                ]
            ],
            [
                'name' => 'Los Mariachis del Puerto',
                'email' => 'mariachis@kita.app',
                'slug' => 'los-mariachis-del-puerto-banda-completa',
                'instruments' => ['Banda Completa'],
                'coverage_area' => ['Marina Vallarta', 'Centro', 'Nuevo Vallarta'],
                'bio' => 'Llevamos la tradición, alegría y folclor mexicano con la máxima calidad y elegancia. Especialistas en bodas, aniversarios, serenatas y banquetes corporativos en todo Vallarta.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Los Mariachis del Puerto - Popurrí de Jalisco'],
                    ['type' => 'photo', 'path' => 'images/profiles/mariachis.jpg', 'title' => 'Mariachis en Marina Vallarta']
                ],
                'events' => [
                    ['title' => 'Serenata del Día del Padre', 'description' => 'Show público al aire libre', 'start_time' => now()->addDays(6)->setHour(19)->setMinute(30), 'end_time' => now()->addDays(6)->setHour(21)->setMinute(0), 'type' => 'show', 'location' => 'Malecón de la Marina, Marina Vallarta'],
                ]
            ],
            [
                'name' => 'Mateo Fernández',
                'email' => 'mateo@kita.app',
                'slug' => 'mateo-fernandez-guitarrista',
                'instruments' => ['Guitarrista'],
                'coverage_area' => ['Nuevo Vallarta', 'Bucerías'],
                'bio' => 'Especialista en guitarra flamenca, clásica y fusión española. Música ambiental instrumental de alta gama diseñada exclusivamente para cenas de gala y cócteles VIP.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Mateo Fernández - Flamenco Fusión Live'],
                ],
                'events' => [
                    ['title' => 'Cóctel Sunset VIP', 'description' => 'Música instrumental para cóctel corporativo', 'start_time' => now()->addDays(3)->setHour(17)->setMinute(30), 'end_time' => now()->addDays(3)->setHour(19)->setMinute(30), 'type' => 'show', 'location' => 'Vidanta Beach Club, Nuevo Vallarta'],
                ]
            ],
            [
                'name' => 'Sunset Groove',
                'email' => 'sunset@kita.app',
                'slug' => 'sunset-groove-banda-completa',
                'instruments' => ['Banda Completa'],
                'coverage_area' => ['Bucerías', 'Sayulita'],
                'bio' => 'Banda de covers de formato completo de rock clásico, pop de los 80s/90s y éxitos radiales en inglés y español. Show dinámico con excelente juego de luces y energía sin igual.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => 'Sunset Groove - Live Concert Reel'],
                ],
                'events' => [
                    ['title' => 'Rock Legends Night', 'description' => 'Tributo al Rock de los 80s y 90s', 'start_time' => now()->addDays(4)->setHour(21)->setMinute(0), 'end_time' => now()->addDays(4)->setHour(24)->setMinute(0), 'type' => 'show', 'location' => 'The Shamrock Irish Pub, Bucerías'],
                ]
            ],
            [
                'name' => 'Camila Castro',
                'email' => 'camila@kita.app',
                'slug' => 'camila-castro-vocalista',
                'instruments' => ['Vocalista'],
                'coverage_area' => ['Centro', 'Marina Vallarta'],
                'bio' => 'Cantante solista de jazz, soul y R&B. Acompañada de pistas de alta definición o pianista en vivo para cenas románticas, pedidas de mano y eventos corporativos íntimos.',
                'widget_status' => ['agenda' => true, 'media' => true],
                'media' => [
                    ['type' => 'photo', 'path' => 'images/profiles/camila_sing.jpg', 'title' => 'Camila Castro - Session Photo Studio'],
                ],
                'events' => [
                    ['title' => 'Noche de Soul & Dinner', 'description' => 'Música en vivo para acompañar la cena gourmet', 'start_time' => now()->addDays(1)->setHour(20)->setMinute(0), 'end_time' => now()->addDays(1)->setHour(22)->setMinute(30), 'type' => 'show', 'location' => 'Café des Artistes, Centro'],
                ]
            ]
        ];

        foreach ($musicians as $musicianData) {
            $user = User::create([
                'name' => $musicianData['name'],
                'email' => $musicianData['email'],
                'password' => bcrypt('password123'),
            ]);

            $profile = $user->profile()->create([
                'name' => $musicianData['name'],
                'instruments' => $musicianData['instruments'],
                'coverage_area' => $musicianData['coverage_area'],
                'slug' => $musicianData['slug'],
                'bio' => $musicianData['bio'],
                'widget_status' => $musicianData['widget_status'],
            ]);

            foreach ($musicianData['media'] as $mediaData) {
                $profile->media()->create($mediaData);
            }

            foreach ($musicianData['events'] as $eventData) {
                $profile->events()->create($eventData);
            }
        }
    }
}
