<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;

// Find Penalaran Umum Subtest
$subTest = SubTest::where('title', 'Penalaran Umum')->first();

if (!$subTest) {
    echo "Sub test not found.\n";
    exit;
}

// Clear old dummy questions
Question::where('sub_test_id', $subTest->id)->delete();

$questionsData = [
    [
        'question' => "Banyak pengusaha konveksi mengeluhkan turunnya pendapatan akibat membanjirnya pakaian impor bekas. Semua pengusaha yang mengeluhkan turunnya pendapatan berharap ada pembatasan kuota impor pakaian bekas dari pemerintah. \n\nKesimpulan yang benar adalah...",
        'options' => [
            ['text' => 'Semua pengusaha konveksi berharap ada pembatasan kuota impor pakaian bekas.', 'correct' => false],
            ['text' => 'Sebagian pengusaha konveksi berharap ada pembatasan kuota impor pakaian bekas.', 'correct' => true],
            ['text' => 'Semua pengusaha pakaian impor bekas mengalami penurunan pendapatan.', 'correct' => false],
            ['text' => 'Pemerintah akan segera membatasi kuota impor pakaian bekas.', 'correct' => false],
            ['text' => 'Tidak ada pengusaha yang tidak mengharapkan pembatasan kuota impor pakaian bekas.', 'correct' => false],
        ]
    ],
    [
        'question' => "Jika proyek jalan tol selesai tepat waktu, maka arus mudik akan lebih lancar. Jika arus mudik lebih lancar, maka angka kecelakaan lalu lintas dapat menurun. Namun, saat ini angka kecelakaan lalu lintas tidak menurun.\n\nKesimpulan yang paling tepat adalah...",
        'options' => [
            ['text' => 'Proyek jalan tol selesai tepat waktu dan arus mudik lancar.', 'correct' => false],
            ['text' => 'Arus mudik tidak lancar meskipun proyek jalan tol selesai.', 'correct' => false],
            ['text' => 'Proyek jalan tol tidak selesai tepat waktu.', 'correct' => true],
            ['text' => 'Masyarakat kurang disiplin dalam berlalu lintas.', 'correct' => false],
            ['text' => 'Pemerintah kurang optimal mengatur arus mudik.', 'correct' => false],
        ]
    ],
    [
        'question' => "Seorang dokter menyarankan pasiennya untuk mengurangi konsumsi gula agar terhindar dari risiko diabetes tipe 2. Selain itu, pasien juga disarankan rutin berjalan kaki selama 30 menit setiap hari untuk meningkatkan metabolisme.\n\nSimpulan logis dari pernyataan di atas adalah...",
        'options' => [
            ['text' => 'Jika pasien mengurangi gula tapi tidak berjalan kaki, pasien pasti terkena diabetes.', 'correct' => false],
            ['text' => 'Berjalan kaki lebih efektif dari mengurangi gula untuk mencegah diabetes.', 'correct' => false],
            ['text' => 'Pengurangan konsumsi gula dan olahraga rutin dapat menurunkan risiko diabetes.', 'correct' => true],
            ['text' => 'Semua pasien diabetes tipe 2 jarang berjalan kaki.', 'correct' => false],
            ['text' => 'Risiko diabetes tipe 2 hanya disebabkan oleh tingginya konsumsi gula.', 'correct' => false],
        ]
    ],
    [
        'question' => "Harga beras organik di pasar X selalu lebih tinggi daripada beras konvensional. Hal ini disebabkan proses penanaman beras organik yang membutuhkan perawatan lebih ekstra dan bebas pestisida kimia. Hari ini, beras milik Pak Budi harganya lebih murah daripada beras milik Pak Andi.\n\nPernyataan yang PALING MUNGKIN benar adalah...",
        'options' => [
            ['text' => 'Beras Pak Budi pasti bukan beras organik.', 'correct' => false],
            ['text' => 'Beras Pak Andi pasti beras organik.', 'correct' => false],
            ['text' => 'Jika beras Pak Budi organik, maka beras Pak Andi juga organik dari kualitas lebih tinggi.', 'correct' => true],
            ['text' => 'Beras Pak Budi banyak mengandung bahan kimia pestisida.', 'correct' => false],
            ['text' => 'Pasar X sedang mengalami penurunan harga beras konvensional.', 'correct' => false],
        ]
    ],
    [
        'question' => "Budi, Cici, Dedi, dan Eka sedang mengantre tiket bioskop. Dedi tidak mau berdiri di urutan ganjil. Budi berdiri tepat di depan Cici. Eka tidak mendaftar pada urutan pertama maupun terakhir.\n\nUrutan antrean dari depan ke belakang yang paling mungkin adalah...",
        'options' => [
            ['text' => 'Budi, Cici, Dedi, Eka', 'correct' => false],
            ['text' => 'Budi, Dedi, Cici, Eka', 'correct' => false],
            ['text' => 'Eka, Budi, Cici, Dedi', 'correct' => false],
            ['text' => 'Budi, Cici, Eka, Dedi', 'correct' => true],
            ['text' => 'Dedi, Budi, Cici, Eka', 'correct' => false],
        ]
    ]
];

foreach ($questionsData as $qData) {
    $question = Question::create([
        'exam_id' => $subTest->exam_id,
        'sub_test_id' => $subTest->id,
        'text' => $qData['question'],
        'type' => 'Multiple Choice',
        'timer_per_question' => 60,
    ]);

    $labels = ['A', 'B', 'C', 'D', 'E'];
    foreach ($qData['options'] as $idx => $opt) {
        Option::create([
            'question_id' => $question->id,
            'text' => $opt['text'],
            'point' => $opt['correct'] ? 10 : 0,
        ]);
    }
}

echo "Successfully injected authentic UTBK questions!";
