<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisSeeder extends Seeder
{
    public function run()
    {
        $paises = [
            'Afganistán', 'Albania', 'Alemania', 'Andorra', 'Angola',
            'Antigua y Barbuda', 'Arabia Saudita', 'Argelia', 'Argentina', 'Armenia',
            'Australia', 'Austria', 'Azerbaiyán', 'Bahamas', 'Barbados',
            'Baréin', 'Bélgica', 'Belice', 'Bielorrusia', 'Bolivia',
            'Bosnia y Herzegovina', 'Botsuana', 'Brasil', 'Brunéi', 'Bulgaria',
            'Burkina Faso', 'Burundi', 'Cabo Verde', 'Camboya', 'Camerún',
            'Canadá', 'Catar', 'Chile', 'China', 'Chipre',
            'Colombia', 'Comoras', 'Congo', 'Corea del Norte', 'Corea del Sur',
            'Costa de Marfil', 'Costa Rica', 'Croacia', 'Cuba', 'Dinamarca',
            'Dominica', 'República Dominicana', 'Ecuador', 'Egipto', 'El Salvador',
            'Emiratos Árabes Unidos', 'Eslovenia', 'España', 'Estonia', 'Eswatini',
            'Estados Unidos', 'Etiopía', 'Filipinas', 'Finlandia', 'Francia',
            'Gambia', 'Georgia', 'Ghana', 'Grecia', 'Granada',
            'Guatemala', 'Guyana', 'Haití', 'Honduras', 'Hungría',
            'India', 'Indonesia', 'Irán', 'Irak', 'Irlanda',
            'Isla de Man', 'Islas Marshall', 'Islas Salomón', 'Israel', 'Italia',
            'Jamaica', 'Japón', 'Jordania', 'Kazajistán', 'Kenia',
            'Kirguistán', 'Kiribati', 'Kuwait', 'Laos', 'Lesoto',
            'Letonia', 'Líbano', 'Lituania', 'Luxemburgo', 'Madagascar',
            'Malasia', 'Malaui', 'Maldivas', 'Malta', 'Marruecos',
            'Mauricio', 'Mauritania', 'México', 'Moldavia', 'Mónaco',
            'Mongolia', 'Mozambique', 'Namibia', 'Nauru', 'Nepal',
            'Nicaragua', 'Níger', 'Nigeria', 'Noruega', 'Nueva Zelanda',
            'Omán', 'Países Bajos', 'Pakistán', 'Palaos', 'Panamá',
            'Papúa Nueva Guinea', 'Paraguay', 'Perú', 'Polonia', 'Portugal',
            'Reino Unido', 'República Centroafricana', 'República Checa', 'República del Congo',
            'República Dominicana', 'Rumania', 'Rusia', 'Rwanda', 'San Cristóbal y Nieves',
            'San Marino', 'San Vicente y las Granadinas', 'Santo Tomé y Príncipe',
            'Senegal', 'Serbia', 'Seychelles', 'Singapur', 'Siria',
            'Somalia', 'Sudáfrica', 'Sudán', 'Suecia', 'Suiza',
            'Tailandia', 'Tanzania', 'Timor Oriental', 'Togo', 'Tonga',
            'Trinidad y Tobago', 'Túnez', 'Turkmenistán', 'Turquía', 'Tuvalu',
            'Ucrania', 'Uganda', 'Uruguay', 'Uzbekistán', 'Vanuatu',
            'Vaticano', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabue',
        ];

        foreach ($paises as $nombre) {
            DB::table('pais')->insert([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
