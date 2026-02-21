<?php

namespace App\Support;

class GymLocationCatalog
{
    /**
     * @return array<string, array{label:string,states:array<string, array<int, string>>}>
     */
    public static function catalog(): array
    {
        return [
            'ar' => [
                'label' => 'Argentina',
                'states' => [
                    'Buenos Aires' => ['La Plata', 'Mar del Plata', 'Bahia Blanca'],
                    'Catamarca' => ['San Fernando del Valle de Catamarca', 'Belen'],
                    'Chaco' => ['Resistencia', 'Presidencia Roque Saenz Pena'],
                    'Chubut' => ['Rawson', 'Comodoro Rivadavia', 'Trelew'],
                    'Cordoba' => ['Cordoba', 'Villa Carlos Paz', 'Rio Cuarto'],
                    'Corrientes' => ['Corrientes', 'Goya'],
                    'Entre Rios' => ['Parana', 'Concordia', 'Gualeguaychu'],
                    'Formosa' => ['Formosa', 'Clorinda'],
                    'Jujuy' => ['San Salvador de Jujuy', 'Palpala'],
                    'La Pampa' => ['Santa Rosa', 'General Pico'],
                    'La Rioja' => ['La Rioja', 'Chilecito'],
                    'Mendoza' => ['Mendoza', 'San Rafael', 'Godoy Cruz'],
                    'Misiones' => ['Posadas', 'Obera', 'Eldorado'],
                    'Neuquen' => ['Neuquen', 'Cutral Co'],
                    'Rio Negro' => ['Viedma', 'Bariloche', 'General Roca'],
                    'Salta' => ['Salta', 'Tartagal'],
                    'San Juan' => ['San Juan', 'Pocito'],
                    'San Luis' => ['San Luis', 'Villa Mercedes'],
                    'Santa Cruz' => ['Rio Gallegos', 'Caleta Olivia'],
                    'Santa Fe' => ['Santa Fe', 'Rosario', 'Rafaela'],
                    'Santiago del Estero' => ['Santiago del Estero', 'La Banda'],
                    'Tierra del Fuego' => ['Ushuaia', 'Rio Grande'],
                    'Tucuman' => ['San Miguel de Tucuman', 'Tafi Viejo'],
                    'Ciudad Autonoma de Buenos Aires' => ['Buenos Aires'],
                ],
            ],
            'bo' => [
                'label' => 'Bolivia',
                'states' => [
                    'La Paz' => ['La Paz', 'El Alto'],
                    'Cochabamba' => ['Cochabamba', 'Quillacollo'],
                    'Santa Cruz' => ['Santa Cruz de la Sierra', 'Montero'],
                    'Oruro' => ['Oruro', 'Huanuni'],
                    'Potosi' => ['Potosi', 'Uyuni'],
                    'Chuquisaca' => ['Sucre', 'Monteagudo'],
                    'Tarija' => ['Tarija', 'Yacuiba'],
                    'Beni' => ['Trinidad', 'Riberalta'],
                    'Pando' => ['Cobija', 'Porvenir'],
                ],
            ],
            'br' => [
                'label' => 'Brasil',
                'states' => [
                    'Acre' => ['Rio Branco', 'Cruzeiro do Sul'],
                    'Alagoas' => ['Maceio', 'Arapiraca'],
                    'Amapa' => ['Macapa', 'Santana'],
                    'Amazonas' => ['Manaus', 'Parintins'],
                    'Bahia' => ['Salvador', 'Feira de Santana'],
                    'Ceara' => ['Fortaleza', 'Juazeiro do Norte'],
                    'Distrito Federal' => ['Brasilia'],
                    'Espirito Santo' => ['Vitoria', 'Vila Velha'],
                    'Goias' => ['Goiania', 'Anapolis'],
                    'Maranhao' => ['Sao Luis', 'Imperatriz'],
                    'Mato Grosso' => ['Cuiaba', 'Rondonopolis'],
                    'Mato Grosso do Sul' => ['Campo Grande', 'Dourados'],
                    'Minas Gerais' => ['Belo Horizonte', 'Uberlandia'],
                    'Para' => ['Belem', 'Santarem'],
                    'Paraiba' => ['Joao Pessoa', 'Campina Grande'],
                    'Parana' => ['Curitiba', 'Londrina'],
                    'Pernambuco' => ['Recife', 'Jaboatao dos Guararapes'],
                    'Piaui' => ['Teresina', 'Parnaiba'],
                    'Rio de Janeiro' => ['Rio de Janeiro', 'Niteroi'],
                    'Rio Grande do Norte' => ['Natal', 'Mossoro'],
                    'Rio Grande do Sul' => ['Porto Alegre', 'Caxias do Sul'],
                    'Rondonia' => ['Porto Velho', 'Ji-Parana'],
                    'Roraima' => ['Boa Vista', 'Rorainopolis'],
                    'Santa Catarina' => ['Florianopolis', 'Joinville'],
                    'Sao Paulo' => ['Sao Paulo', 'Campinas', 'Santos'],
                    'Sergipe' => ['Aracaju', 'Nossa Senhora do Socorro'],
                    'Tocantins' => ['Palmas', 'Araguaina'],
                ],
            ],
            'cl' => [
                'label' => 'Chile',
                'states' => [
                    'Arica y Parinacota' => ['Arica'],
                    'Tarapaca' => ['Iquique', 'Alto Hospicio'],
                    'Antofagasta' => ['Antofagasta', 'Calama'],
                    'Atacama' => ['Copiapo', 'Vallenar'],
                    'Coquimbo' => ['La Serena', 'Coquimbo'],
                    'Valparaiso' => ['Valparaiso', 'Vina del Mar'],
                    'Metropolitana de Santiago' => ['Santiago', 'Puente Alto'],
                    'O Higgins' => ['Rancagua', 'San Fernando'],
                    'Maule' => ['Talca', 'Curico'],
                    'Nuble' => ['Chillan', 'San Carlos'],
                    'Biobio' => ['Concepcion', 'Los Angeles'],
                    'La Araucania' => ['Temuco', 'Villarrica'],
                    'Los Rios' => ['Valdivia', 'La Union'],
                    'Los Lagos' => ['Puerto Montt', 'Osorno'],
                    'Aysen' => ['Coyhaique', 'Puerto Aysen'],
                    'Magallanes y la Antartica Chilena' => ['Punta Arenas', 'Puerto Natales'],
                ],
            ],
            'co' => [
                'label' => 'Colombia',
                'states' => [
                    'Amazonas' => ['Leticia'],
                    'Antioquia' => ['Medellin', 'Bello', 'Envigado'],
                    'Arauca' => ['Arauca'],
                    'Atlantico' => ['Barranquilla', 'Soledad'],
                    'Bolivar' => ['Cartagena', 'Magangue'],
                    'Boyaca' => ['Tunja', 'Duitama'],
                    'Caldas' => ['Manizales', 'La Dorada'],
                    'Caqueta' => ['Florencia'],
                    'Casanare' => ['Yopal'],
                    'Cauca' => ['Popayan', 'Santander de Quilichao'],
                    'Cesar' => ['Valledupar', 'Aguachica'],
                    'Choco' => ['Quibdo'],
                    'Cordoba' => ['Monteria', 'Sahagun'],
                    'Cundinamarca' => ['Soacha', 'Zipaquira', 'Chia'],
                    'Guainia' => ['Inirida'],
                    'Guaviare' => ['San Jose del Guaviare'],
                    'Huila' => ['Neiva', 'Pitalito'],
                    'La Guajira' => ['Riohacha', 'Maicao'],
                    'Magdalena' => ['Santa Marta', 'Cienaga'],
                    'Meta' => ['Villavicencio', 'Acacias'],
                    'Narino' => ['Pasto', 'Tumaco'],
                    'Norte de Santander' => ['Cucuta', 'Ocana'],
                    'Putumayo' => ['Mocoa'],
                    'Quindio' => ['Armenia'],
                    'Risaralda' => ['Pereira', 'Dosquebradas'],
                    'San Andres y Providencia' => ['San Andres'],
                    'Santander' => ['Bucaramanga', 'Floridablanca'],
                    'Sucre' => ['Sincelejo'],
                    'Tolima' => ['Ibague', 'Espinal'],
                    'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura'],
                    'Vaupes' => ['Mitu'],
                    'Vichada' => ['Puerto Carreno'],
                    'Bogota D.C.' => ['Bogota'],
                ],
            ],
            'ec' => [
                'label' => 'Ecuador',
                'states' => [
                    'Azuay' => ['Camilo Ponce Enriquez', 'Chordeleg', 'Cuenca', 'El Pan', 'Giron', 'Gualaceo', 'Guachapala', 'Nabon', 'Ona', 'Pucara', 'San Fernando', 'Santa Isabel', 'Sevilla de Oro', 'Sigsig'],
                    'Bolivar' => ['Caluma', 'Chillanes', 'Chimbo', 'Echeandia', 'Guaranda', 'Las Naves', 'San Miguel'],
                    'Canar' => ['Azogues', 'Biblian', 'Canar', 'Deleg', 'El Tambo', 'La Troncal', 'Suscal'],
                    'Carchi' => ['Bolivar', 'Espejo', 'Mira', 'Montufar', 'San Pedro de Huaca', 'Tulcan'],
                    'Chimborazo' => ['Alausi', 'Chambo', 'Chunchi', 'Colta', 'Cumanda', 'Guamote', 'Guano', 'Pallatanga', 'Penipe', 'Riobamba'],
                    'Cotopaxi' => ['La Mana', 'Latacunga', 'Pangua', 'Pujili', 'Salcedo', 'Saquisili', 'Sigchos'],
                    'El Oro' => ['Arenillas', 'Atahualpa', 'Balsas', 'Chilla', 'El Guabo', 'Huaquillas', 'Las Lajas', 'Machala', 'Marcabeli', 'Pasaje', 'Pinas', 'Portovelo', 'Santa Rosa', 'Zaruma'],
                    'Esmeraldas' => ['Atacames', 'Eloy Alfaro', 'Esmeraldas', 'Muisne', 'Quininde', 'Rioverde', 'San Lorenzo'],
                    'Galapagos' => ['Isabela', 'San Cristobal', 'Santa Cruz'],
                    'Guayas' => ['Alfredo Baquerizo Moreno', 'Balao', 'Balzar', 'Colimes', 'Daule', 'Duran', 'El Empalme', 'El Triunfo', 'General Antonio Elizalde', 'Guayaquil', 'Isidro Ayora', 'Lomas de Sargentillo', 'Marcelino Mariduena', 'Milagro', 'Naranjal', 'Naranjito', 'Nobol', 'Palestina', 'Pedro Carbo', 'Playas', 'Salitre', 'Samborondon', 'Santa Lucia', 'Simon Bolivar', 'Yaguachi'],
                    'Imbabura' => ['Antonio Ante', 'Cotacachi', 'Ibarra', 'Otavalo', 'Pimampiro', 'San Miguel de Urcuqui'],
                    'Loja' => ['Calvas', 'Catamayo', 'Celica', 'Chaguarpamba', 'Espindola', 'Gonzanama', 'Loja', 'Macara', 'Olmedo', 'Paltas', 'Pindal', 'Puyango', 'Quilanga', 'Saraguro', 'Sozoranga', 'Zapotillo'],
                    'Los Rios' => ['Baba', 'Babahoyo', 'Buena Fe', 'Mocache', 'Montalvo', 'Palenque', 'Puebloviejo', 'Quevedo', 'Quinsaloma', 'Urdaneta', 'Valencia', 'Ventanas', 'Vinces'],
                    'Manabi' => ['24 de Mayo', 'Bolivar', 'Chone', 'El Carmen', 'Flavio Alfaro', 'Jama', 'Jaramijo', 'Jipijapa', 'Junin', 'Manta', 'Montecristi', 'Olmedo', 'Pajan', 'Pedernales', 'Pichincha', 'Portoviejo', 'Puerto Lopez', 'Rocafuerte', 'San Vicente', 'Santa Ana', 'Sucre', 'Tosagua'],
                    'Morona Santiago' => ['Gualaquiza', 'Huamboya', 'Limon Indanza', 'Logrono', 'Morona', 'Pablo Sexto', 'Palora', 'San Juan Bosco', 'Santiago', 'Sucua', 'Taisha', 'Tiwintza'],
                    'Napo' => ['Archidona', 'Carlos Julio Arosemena Tola', 'El Chaco', 'Quijos', 'Tena'],
                    'Orellana' => ['Aguarico', 'Francisco de Orellana', 'Joya de los Sachas', 'Loreto'],
                    'Pastaza' => ['Arajuno', 'Mera', 'Pastaza', 'Santa Clara'],
                    'Pichincha' => ['Cayambe', 'Mejia', 'Pedro Moncayo', 'Pedro Vicente Maldonado', 'Puerto Quito', 'Quito', 'Ruminahui', 'San Miguel de los Bancos'],
                    'Santa Elena' => ['La Libertad', 'Salinas', 'Santa Elena'],
                    'Santo Domingo de los Tsachilas' => ['La Concordia', 'Santo Domingo'],
                    'Sucumbios' => ['Cascales', 'Cuyabeno', 'Gonzalo Pizarro', 'Lago Agrio', 'Putumayo', 'Shushufindi', 'Sucumbios'],
                    'Tungurahua' => ['Ambato', 'Banos de Agua Santa', 'Cevallos', 'Mocha', 'Patate', 'Pelileo', 'Pillaro', 'Quero', 'Tisaleo'],
                    'Zamora Chinchipe' => ['Centinela del Condor', 'Chinchipe', 'El Pangui', 'Nangaritza', 'Palanda', 'Paquisha', 'Yacuambi', 'Yantzaza', 'Zamora'],
                ],
            ],
            'gy' => [
                'label' => 'Guyana',
                'states' => [
                    'Barima-Waini' => ['Mabaruma'],
                    'Cuyuni-Mazaruni' => ['Bartica'],
                    'Demerara-Mahaica' => ['Georgetown'],
                    'East Berbice-Corentyne' => ['New Amsterdam'],
                    'Essequibo Islands-West Demerara' => ['Vreed-en-Hoop'],
                    'Mahaica-Berbice' => ['Fort Wellington'],
                    'Pomeroon-Supenaam' => ['Anna Regina'],
                    'Potaro-Siparuni' => ['Mahdia'],
                    'Upper Demerara-Berbice' => ['Linden'],
                    'Upper Takutu-Upper Essequibo' => ['Lethem'],
                ],
            ],
            'py' => [
                'label' => 'Paraguay',
                'states' => [
                    'Alto Paraguay' => ['Fuerte Olimpo'],
                    'Alto Parana' => ['Ciudad del Este', 'Hernandarias'],
                    'Amambay' => ['Pedro Juan Caballero'],
                    'Boqueron' => ['Filadelfia'],
                    'Caaguazu' => ['Coronel Oviedo'],
                    'Caazapa' => ['Caazapa'],
                    'Canindeyu' => ['Salto del Guaira'],
                    'Central' => ['San Lorenzo', 'Luque'],
                    'Concepcion' => ['Concepcion'],
                    'Cordillera' => ['Caacupe'],
                    'Guaira' => ['Villarrica'],
                    'Itapua' => ['Encarnacion'],
                    'Misiones' => ['San Juan Bautista'],
                    'Neembucu' => ['Pilar'],
                    'Paraguari' => ['Paraguari'],
                    'Presidente Hayes' => ['Villa Hayes'],
                    'San Pedro' => ['San Pedro de Ycuamandiyu'],
                    'Asuncion' => ['Asuncion'],
                ],
            ],
            'pe' => [
                'label' => 'Peru',
                'states' => [
                    'Amazonas' => ['Chachapoyas', 'Bagua Grande'],
                    'Ancash' => ['Huaraz', 'Chimbote'],
                    'Apurimac' => ['Abancay', 'Andahuaylas'],
                    'Arequipa' => ['Arequipa', 'Camana'],
                    'Ayacucho' => ['Ayacucho', 'Huanta'],
                    'Cajamarca' => ['Cajamarca', 'Jaen'],
                    'Callao' => ['Callao'],
                    'Cusco' => ['Cusco', 'Sicuani'],
                    'Huancavelica' => ['Huancavelica'],
                    'Huanuco' => ['Huanuco', 'Tingo Maria'],
                    'Ica' => ['Ica', 'Chincha Alta'],
                    'Junin' => ['Huancayo', 'Tarma'],
                    'La Libertad' => ['Trujillo', 'Chepen'],
                    'Lambayeque' => ['Chiclayo', 'Lambayeque'],
                    'Lima' => ['Lima', 'Huacho', 'Huaral'],
                    'Loreto' => ['Iquitos', 'Yurimaguas'],
                    'Madre de Dios' => ['Puerto Maldonado'],
                    'Moquegua' => ['Moquegua', 'Ilo'],
                    'Pasco' => ['Cerro de Pasco'],
                    'Piura' => ['Piura', 'Sullana'],
                    'Puno' => ['Puno', 'Juliaca'],
                    'San Martin' => ['Moyobamba', 'Tarapoto'],
                    'Tacna' => ['Tacna'],
                    'Tumbes' => ['Tumbes'],
                    'Ucayali' => ['Pucallpa'],
                ],
            ],
            'sr' => [
                'label' => 'Suriname',
                'states' => [
                    'Brokopondo' => ['Brokopondo'],
                    'Commewijne' => ['Nieuw Amsterdam'],
                    'Coronie' => ['Totness'],
                    'Marowijne' => ['Albina'],
                    'Nickerie' => ['Nieuw Nickerie'],
                    'Para' => ['Onverwacht'],
                    'Paramaribo' => ['Paramaribo'],
                    'Saramacca' => ['Groningen'],
                    'Sipaliwini' => ['Brownsweg'],
                    'Wanica' => ['Lelydorp'],
                ],
            ],
            'uy' => [
                'label' => 'Uruguay',
                'states' => [
                    'Artigas' => ['Artigas', 'Bella Union'],
                    'Canelones' => ['Canelones', 'Ciudad de la Costa'],
                    'Cerro Largo' => ['Melo', 'Rio Branco'],
                    'Colonia' => ['Colonia del Sacramento', 'Carmelo'],
                    'Durazno' => ['Durazno', 'Sarandi del Yi'],
                    'Flores' => ['Trinidad'],
                    'Florida' => ['Florida', 'Sarandi Grande'],
                    'Lavalleja' => ['Minas', 'Jose Pedro Varela'],
                    'Maldonado' => ['Maldonado', 'Punta del Este'],
                    'Montevideo' => ['Montevideo'],
                    'Paysandu' => ['Paysandu'],
                    'Rio Negro' => ['Fray Bentos', 'Young'],
                    'Rivera' => ['Rivera'],
                    'Rocha' => ['Rocha', 'Chuy'],
                    'Salto' => ['Salto'],
                    'San Jose' => ['San Jose de Mayo', 'Ciudad del Plata'],
                    'Soriano' => ['Mercedes', 'Dolores'],
                    'Tacuarembo' => ['Tacuarembo'],
                    'Treinta y Tres' => ['Treinta y Tres'],
                ],
            ],
            've' => [
                'label' => 'Venezuela',
                'states' => [
                    'Amazonas' => ['Puerto Ayacucho'],
                    'Anzoategui' => ['Barcelona', 'El Tigre'],
                    'Apure' => ['San Fernando de Apure'],
                    'Aragua' => ['Maracay', 'Turmero'],
                    'Barinas' => ['Barinas'],
                    'Bolivar' => ['Ciudad Bolivar', 'Ciudad Guayana'],
                    'Carabobo' => ['Valencia', 'Puerto Cabello'],
                    'Cojedes' => ['San Carlos'],
                    'Delta Amacuro' => ['Tucupita'],
                    'Distrito Capital' => ['Caracas'],
                    'Falcon' => ['Coro', 'Punto Fijo'],
                    'Guarico' => ['San Juan de los Morros', 'Calabozo'],
                    'La Guaira' => ['La Guaira', 'Catia La Mar'],
                    'Lara' => ['Barquisimeto', 'Cabudare'],
                    'Merida' => ['Merida', 'El Vigia'],
                    'Miranda' => ['Los Teques', 'Guarenas'],
                    'Monagas' => ['Maturin'],
                    'Nueva Esparta' => ['La Asuncion', 'Porlamar'],
                    'Portuguesa' => ['Guanare', 'Acarigua'],
                    'Sucre' => ['Cumana', 'Carupano'],
                    'Tachira' => ['San Cristobal'],
                    'Trujillo' => ['Trujillo', 'Valera'],
                    'Yaracuy' => ['San Felipe'],
                    'Zulia' => ['Maracaibo', 'Cabimas'],
                ],
            ],
        ];
    }

    public static function hasCountry(string $country): bool
    {
        return array_key_exists(strtolower(trim($country)), self::catalog());
    }

    public static function resolveState(string $country, string $state): ?string
    {
        $states = self::catalog()[strtolower(trim($country))]['states'] ?? [];
        $needle = strtolower(trim($state));
        foreach (array_keys($states) as $stateName) {
            if (strtolower($stateName) === $needle) {
                return $stateName;
            }
        }

        return null;
    }

    public static function resolveCity(string $country, string $state, string $city): ?string
    {
        $resolvedState = self::resolveState($country, $state);
        if ($resolvedState === null) {
            return null;
        }

        $cities = self::catalog()[strtolower(trim($country))]['states'][$resolvedState] ?? [];
        $needle = strtolower(trim($city));
        foreach ($cities as $cityName) {
            if (strtolower($cityName) === $needle) {
                return $cityName;
            }
        }

        return null;
    }

    public static function isValid(string $country, string $state, string $city): bool
    {
        return self::resolveState($country, $state) !== null
            && self::resolveCity($country, $state, $city) !== null;
    }

    public static function buildAddress(
        string $country,
        string $state,
        string $city,
        ?string $line = null
    ): string {
        $catalog = self::catalog();
        $countryKey = strtolower(trim($country));
        $countryLabel = $catalog[$countryKey]['label'] ?? trim($country);
        $stateLabel = self::resolveState($country, $state) ?? trim($state);
        $cityLabel = self::resolveCity($country, $state, $city) ?? trim($city);

        $pieces = array_filter([
            trim((string) $line),
            $cityLabel,
            $stateLabel,
            $countryLabel,
        ], static fn ($value): bool => $value !== '');

        return implode(', ', $pieces);
    }
}
