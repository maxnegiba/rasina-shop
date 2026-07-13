<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SamedayService
{
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $pickupPointId;

    public function __construct()
    {
        $env = config('services.sameday.env', 'demo');
        $this->apiUrl = $env === 'prod'
            ? 'https://api.sameday.ro'
            : 'https://sameday-api.demo.zitec.com';

        $this->username = config('services.sameday.username');
        $this->password = config('services.sameday.password');
        $this->pickupPointId = config('services.sameday.pickup_point_id');
    }

    /**
     * Obține token-ul JWT, din cache sau din API.
     */
    protected function getToken()
    {
        return Cache::remember('sameday_jwt_token', 3600, function () { // cache pt 1 oră
            $response = Http::withHeaders(['X-AUTH-USERNAME' => $this->username, 'X-AUTH-PASSWORD' => $this->password])
                ->post("{$this->apiUrl}/api/authenticate");

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            Log::error('Eroare autentificare Sameday', ['response' => $response->body()]);
            return null;
        });
    }

    /**
     * Generează AWB-ul pentru o comandă.
     */
    public function generateAwb(Order $order)
    {
        $token = $this->getToken();

        if (!$token) {
            Log::error("Sameday: Nu am putut obține token-ul JWT pentru comanda {$order->id}");
            return false;
        }

        if (!$this->pickupPointId) {
            Log::error("Sameday: Lipsește SAMEDAY_PICKUP_POINT_ID din .env pentru comanda {$order->id}");
            return false;
        }

        // Preluăm greutatea - aici putem aproxima la 1 kg
        $weight = 1;

        // Adresa destinatarului
        $customerDetails = $order->customer_details;

        // Dacă e easybox, Sameday are nevoie de anumite service-uri
        // Service ID trebuie sa fie corelat cu mediul din Sameday (Ex: Easybox are alt ID decat livrarea standard)
        // Mai jos este un exemplu generic. În mod normal, trebuie făcut un GET /api/client/services pentru a prelua IDs.
        // Totuși, putem folosi valorile cunoscute, sau cel puțin, setăm un fallback.
        // Pentru această implementare generală, trimitem doar un POST /api/awb conform documentatiei

        // Preluăm id-ul serviciului din API (Ideal ar fi ca aceste ID-uri să fie setate în config)
        $servicesResponse = Http::withToken($token)->get("{$this->apiUrl}/api/client/services");
        $services = [];
        if ($servicesResponse->successful()) {
            $services = collect($servicesResponse->json()['data'] ?? []);
        } else {
             Log::error("Sameday: Nu am putut prelua serviciile pentru comanda {$order->id}");
             return false;
        }

        // Identificăm ID-ul corect pentru serviciu (Locker Next Day vs Next Day/Standard)
        $isLocker = $order->shipping_method === 'locker';
        $serviceCode = $isLocker ? 'LN' : 'NN'; // LN = Locker Next Day, NN = Next Day (Depinde de contul creat)

        $service = $services->firstWhere('code', $serviceCode);
        if (!$service) {
             // Fallback dacă nu se găsește codul așteptat
             $service = $services->first();
             if(!$service) {
                 Log::error("Sameday: Nu am putut găsi niciun serviciu eligibil pentru comanda {$order->id}");
                 return false;
             }
        }
        $serviceId = $service['id'];

        $payload = [
            'pickupPointId' => $this->pickupPointId,
            'serviceId' => $serviceId,
            'packageType' => 0, // 0 = Parcel
            'awbPayment' => 1, // 1 = Client
            'awbRecipient' => [
                'name' => $customerDetails['name'] ?? 'Client',
                'phoneNumber' => $customerDetails['phone'] ?? '0000000000',
                'email' => $customerDetails['email'] ?? '',
                'address' => $customerDetails['address']['line1'] ?? 'Adresa necunoscută',
                'city' => $customerDetails['address']['city'] ?? 'Bucuresti',
                'county' => $customerDetails['address']['state'] ?? 'Bucuresti',
                'personType' => 0, // 0 = Fizică
            ],
            'parcels' => [
                [
                    'weight' => $weight,
                    'width' => 10,
                    'length' => 10,
                    'height' => 10,
                ]
            ],
            'insuredValue' => 0,
            'cashOnDelivery' => 0, // Plata s-a făcut online via Stripe
        ];

        // Dacă e livrare la Easybox, specificăm nodul de easybox
        // In API-ul curent Sameday, trebuie trimis un ID de nod specific, care ar trebui legat de serviciul Easybox
        if ($isLocker && $order->easybox_id) {
             // Setam awbRecipient la datele lockerului sau trimitem un ID de service adecvat
             // In documentatia noua, easybox-ul se trimite in campuri specifice sau prin service delivery point
             $payload['deliveryPointId'] = $order->easybox_id;
             // Unii cer setarea adresei fizice a easyboxului in recipient
             $payload['awbRecipient']['address'] = $order->easybox_name . ' - ' . $order->easybox_id;
        }

        $awbResponse = Http::withToken($token)->post("{$this->apiUrl}/api/awb", $payload);

        if ($awbResponse->successful()) {
            $data = $awbResponse->json();
            $awbNumber = $data['awbNumber'] ?? null;

            if ($awbNumber) {
                $order->update([
                    'awb_number' => $awbNumber,
                    'awb_status' => 'generated'
                ]);
                return true;
            }
        }

        Log::error("Sameday: A eșuat generarea AWB pentru comanda {$order->id}", ['response' => $awbResponse->body()]);
        return false;
    }
}
