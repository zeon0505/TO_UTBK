<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Major;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['university' => 'Universitas Indonesia (UI)', 'name' => 'Kedokteran', 'passing_grade' => 740],
            ['university' => 'Universitas Indonesia (UI)', 'name' => 'Ilmu Hukum', 'passing_grade' => 680],
            ['university' => 'Universitas Indonesia (UI)', 'name' => 'Aktuaria', 'passing_grade' => 710],
            ['university' => 'Institut Teknologi Bandung (ITB)', 'name' => 'STEI-K', 'passing_grade' => 750],
            ['university' => 'Institut Teknologi Bandung (ITB)', 'name' => 'FTMD', 'passing_grade' => 690],
            ['university' => 'Universitas Gadjah Mada (UGM)', 'name' => 'Kedokteran', 'passing_grade' => 735],
            ['university' => 'Universitas Gadjah Mada (UGM)', 'name' => 'Sistem Informasi', 'passing_grade' => 670],
            ['university' => 'Universitas Gadjah Mada (UGM)', 'name' => 'Psikologi', 'passing_grade' => 660],
            ['university' => 'Universitas Padjadjaran (UNPAD)', 'name' => 'Ilmu Komunikasi', 'passing_grade' => 650],
            ['university' => 'Universitas Airlangga (UNAIR)', 'name' => 'Kedokteran Gigi', 'passing_grade' => 700],
            ['university' => 'ITS Surabaya', 'name' => 'Teknik Elektro', 'passing_grade' => 685],
        ];

        foreach ($data as $row) {
            Major::create($row);
        }
    }
}
