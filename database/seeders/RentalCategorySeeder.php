<?php

namespace Database\Seeders;

use App\Models\RentalCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lapangan',
                'slug' => 'lapangan',
                'description' => 'Tempat ideal untuk olahraga atau acara komunitas. Lapangan kami dirawat rutin agar tetap bersih, rata, dan siap untuk futsal, sepak bola mini, atau kegiatan outdoor lain. Sistem pemesanan fleksibel—mulai dari jam latihan singkat sampai penyewaan harian—memudahkan tim dan komunitas menyesuaikan jadwal.'
            ],
            [
                'name' => 'Gedung',
                'slug' => 'gedung',
                'description' => 'Ruang serbaguna untuk pesta, seminar, hingga pameran. Gedung dilengkapi pendingin ruangan, tata cahaya yang bisa diatur, dan fasilitas parkir luas. Tim kami siap membantu dekorasi dan kebutuhan teknis agar acara berlangsung lancar dan profesional.'
            ],
            [
                'name' => 'Kendaraan',
                'slug' => 'kendaraan',
                'description' => 'Pilihan armada lengkap dari mobil keluarga hingga kendaraan niaga. Semua unit mendapatkan perawatan berkala dan asuransi dasar. Layanan tersedia untuk perjalanan harian, wisata, atau kebutuhan logistik, dengan opsi sopir berpengalaman atau lepas kunci sesuai kenyamanan penyewa.'
            ]
        ];

        foreach ($categories as $category) {
            RentalCategory::firstOrCreate([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => $category['description']
            ]);
        }
    }
}
