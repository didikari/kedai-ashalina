<?php

namespace App\Http\Controllers;

use Geocoder\Laravel\Facades\Geocoder;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DistanceController extends Controller
{

    public function index()
    {
        return view('jarak.index');
    }

    public function hitungJarak(Request $request)
    {
        // Lakukan validasi formulir jika diperlukan
        $request->validate([
            'purchase' => 'required|numeric',
            // Jika diperlukan, tambahkan validasi untuk clientLatitude dan clientLongitude
        ]);

        // Panggil method getDistance untuk mendapatkan hasilnya
        $distanceResult = $this->getDistance($request);

        // Tampilkan kembali view 'jarak.index' bersama dengan hasil perhitungan
        return view('jarak.index', ['distanceResult' => $distanceResult]);
    }

    public function calculateDistance($storeLatitude, $storeLongitude, $clientLatitude, $clientLongitude)
    {
        $baseURL = 'https://router.project-osrm.org/route/v1/driving/';
        $client = new Client();

        $response = $client->request(
            'GET',
            $baseURL . "$storeLongitude,$storeLatitude;$clientLongitude,$clientLatitude"
        );

        $data = json_decode($response->getBody(), true);

        // Ambil jarak dalam meter dari respons
        $distance = $data['routes'][0]['distance'];

        // Ubah jarak dari meter ke kilometer
        $distanceInKm = $distance / 1000;

        return $distanceInKm;
    }

    public function getDistance(Request $request)
    {
        // Koordinat toko
        $storeLatitude = -7.059926; // Ganti dengan latitude toko
        $storeLongitude = 111.852642; // Ganti dengan longitude toko
        // Koordinat pelanggan
        $clientLatitude = $request->input('clientLatitude'); // Ganti dengan latitude pelanggan
        $clientLongitude = $request->input('clientLongitude'); // Ganti dengan longitude pelanggan
        $purchaseAmount = $request->input('purchase'); // Ganti dengan nilai belanjaan untuk uji coba

        $distance = $this->calculateDistance($storeLatitude, $storeLongitude, $clientLatitude, $clientLongitude);

        // Biaya awal
        $initialCostLessThan1km = 1000; // Biaya untuk jarak kurang dari 1 km
        $initialCost = 5000; // Biaya untuk jarak pertama 1 km
        $additionalCostPerKm = 5000; // Biaya tambahan per km setelah 1 km
        $discountThreshold = 50000; // Ambang batas belanja untuk diskon
        $discountPercentage = 20; // Persentase diskon jika belanja mencapai ambang batas
        $discountDistance = 10; // Jarak di mana diskon berlaku

        // Hitung biaya tanpa diskon
        if ($distance < 1) {
            $totalCostWithoutDiscount = $initialCostLessThan1km;
        } elseif ($distance > 1) {
            $additionalDistance = $distance - 1;
            $additionalCost = $additionalDistance * $additionalCostPerKm;
            $totalCostWithoutDiscount = $initialCost + $additionalCost;
        } else {
            $totalCostWithoutDiscount = $initialCost;
        }

        // Hitung biaya dengan potensi diskon
        $totalCost = $totalCostWithoutDiscount;

        if ($distance >= $discountDistance && $purchaseAmount >= $discountThreshold) {
            $discountAmount = $totalCostWithoutDiscount * ($discountPercentage / 100);
            $totalCost = $totalCostWithoutDiscount - $discountAmount;
        }

        // Format ke Rupiah
        $formattedCost = "Rp " . number_format($totalCost, 0, ',', '.');

        return "Jarak antara Kedai Ashalina dan lokasi anda adalah: " . $distance . " kilometer. Biaya Ongkir: " . $formattedCost;
    }
}
