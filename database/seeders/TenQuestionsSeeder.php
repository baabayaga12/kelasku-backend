<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CBTTest;
use App\Models\Question;
use App\Models\Answer;

class TenQuestionsSeeder extends Seeder
{
    public function run()
    {
        $test = CBTTest::create([
            'id' => (string) Str::uuid(),
            'title' => 'Soal Contoh 10 Butir (Image/Text/No Stimulus)',
            'description' => 'Kumpulan 10 soal contoh beragam stimulus untuk pengujian sistem',
            'duration_minutes' => 60
        ]);

        // We'll create 10 questions: 4 with image, 3 with text stimulus, 3 without stimulus

        // IMAGE questions (use existing images in public/images)
        $images = [
            '/images/stimulus-sun.svg',
            '/images/stimulus-tree.svg',
            '/images/gunung-stimulus.svg',
            '/images/persegi-panjang.svg'
        ];

        $imgQs = [
            ['Apa yang terlihat pada gambar?', ['Matahari','Bulan','Awan','Planet'], 0, 'Gambar menampilkan matahari.'],
            ['Gambar menunjukkan sebuah pohon. Manakah fungsi utama daun pada tumbuhan?', ['Menyerap air','Fotosintesis','Menopang batang','Menyimpan cadangan'], 1, 'Daun berfungsi untuk fotosintesis.'],
            ['Berdasarkan gambar, kenampakan alam yang ditunjukkan adalah?', ['Lembah','Gunung','Danau','Bukit'], 1, 'Gambar menunjukkan gunung yang menjulang.'],
            ['Perhatikan gambar bangun datar. Berapa luasnya jika panjang 8 dan lebar 5?', ['30 cm²','35 cm²','40 cm²','45 cm²'], 2, 'Luas = 8 × 5 = 40 cm².']
        ];

        foreach ($imgQs as $i => $qdata) {
            [$qtext, $opts, $correctIndex, $explain] = $qdata;
            $q = Question::create([
                'id' => (string) Str::uuid(),
                'test_id' => $test->id,
                'question' => $qtext,
                'stimulus' => $images[$i],
                'stimulus_type' => 'image',
                'explanation' => $explain
            ]);
            foreach ($opts as $j => $optText) {
                Answer::create([
                    'question_id' => $q->id,
                    'answer_text' => $optText,
                    'is_correct' => ($j === $correctIndex)
                ]);
            }
        }

        // TEXT stimulus questions
        $textQs = [
            ["Teks: Seorang petani memanen padi pada musim panen. Hasil panen melimpah dan cuaca cerah.", 'Apa kondisi cuaca saat panen?', ['Hujan','Cerah','Berkabut','Badai'], 1, 'Teks menyebutkan cuaca cerah.'],
            ["Teks: Sebuah kendaraan bergerak dari kota A ke kota B selama 2 jam dengan kecepatan konstan.", 'Jika jarak 120 km, berapa kecepatan rata-rata?', ['40 km/jam','50 km/jam','60 km/jam','80 km/jam'], 2, 'Kecepatan = 120 / 2 = 60 km/jam.'],
            ["Teks: Pada percobaan, larutan berubah warna menjadi merah menunjukkan reaksi asam.", 'Warna merah pada indikator menunjukkan apakah larutan?', ['Basa','Asam','Netral','Tidak bereaksi'], 1, 'Warna merah menandakan asam.']
        ];

        foreach ($textQs as $qdata) {
            [$stimText, $qtext, $opts, $correctIndex, $explain] = $qdata;
            $q = Question::create([
                'id' => (string) Str::uuid(),
                'test_id' => $test->id,
                'question' => $qtext,
                'stimulus' => $stimText,
                'stimulus_type' => 'text',
                'explanation' => $explain
            ]);
            foreach ($opts as $j => $optText) {
                Answer::create([
                    'question_id' => $q->id,
                    'answer_text' => $optText,
                    'is_correct' => ($j === $correctIndex)
                ]);
            }
        }

        // No-stimulus questions
        $noQs = [
            ['Satuan panjang terkecil dari pilihan berikut adalah?', ['Kilometer','Meter','Centimeter','Ton'], 2, 'Centimeter adalah satuan panjang kecil.'],
            ['Manakah yang merupakan hasil dari 12 × 8?', ['80','92','96','108'], 2, '12 × 8 = 96.'],
            ['Apa fungsi utama akar pada tumbuhan?', ['Fotosintesis','Menyerap air dan mineral','Menghasilkan bunga','Menyimpan energi'], 1, 'Akar menyerap air dan mineral.']
        ];

        foreach ($noQs as $qdata) {
            [$qtext, $opts, $correctIndex, $explain] = $qdata;
            $q = Question::create([
                'id' => (string) Str::uuid(),
                'test_id' => $test->id,
                'question' => $qtext,
                'stimulus' => null,
                'stimulus_type' => null,
                'explanation' => $explain
            ]);
            foreach ($opts as $j => $optText) {
                Answer::create([
                    'question_id' => $q->id,
                    'answer_text' => $optText,
                    'is_correct' => ($j === $correctIndex)
                ]);
            }
        }
    }
}
