<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DistanceService
{
    private HttpClientInterface $client;

    // Coordonnées de Bordeaux Centre
    private const BORDEAUX_LAT = 44.837789;
    private const BORDEAUX_LON = -0.579180;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // Méthode qui récupère les coordonnées de la ville du client, en utilisant l'API de OpenStreetMap
    public function getCoordinates(string $ville): array
    {
        // Information demander à l'API
        $response = $this->client->request(
            'GET',
            'https://nominatim.openstreetmap.org/search',
            [
                'query' => [
                    'city' => $ville,
                    'country' => 'France',
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'ViteGourmand'
                ]
            ]
        );

        // Réponse sous forme de tableau, si elle existe
        $data = $response->toArray();

        if (
            empty($data) ||
            !isset($data[0]['address']['county']) ||
            !str_contains(
                strtolower($data[0]['address']['county']),
                'gironde'
            )
        ) {
            throw new \Exception('Ville hors Gironde.');
        }

        return [
            'lat' => (float) $data[0]['lat'],
            'lon' => (float) $data[0]['lon'],
        ];
    }

    // Calcule de la distance
    public function calculeDistance(float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad(self::BORDEAUX_LAT);
        $lonFrom = deg2rad(self::BORDEAUX_LON);

        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
                cos($latFrom) *
                cos($latTo) *
                pow(sin($lonDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 1);
    }

    // Méthode finale
    public function getDistance(string $ville): float
    {
        $coords = $this->getCoordinates($ville);

        return $this->calculeDistance(
            $coords['lat'],
            $coords['lon']
        );
    }
}
