<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CBTTest;
use App\Models\Question;
use App\Models\Answer;

class StimulusExamplesSeeder extends Seeder
{
    public function run()
    {
        // Create a small test that demonstrates stimulus types
        $test = CBTTest::create([
            'id' => (string) Str::uuid(),
            'title' => 'Contoh Soal Stimulus (Image/Text/None)',
            'description' => 'Test singkat berisi contoh soal dengan stimulus gambar, teks, dan tanpa stimulus',
            'duration_minutes' => 20
        ]);

        // 1) Question with image stimulus
        $qImage = Question::create([
            'id' => (string) Str::uuid(),
            'test_id' => $test->id,
            'question' => 'Perhatikan gambar di bawah. Apa yang terlihat pada gambar tersebut?',
            'stimulus' => '/images/stimulus-sun.svg',
            'stimulus_type' => 'image',
            'explanation' => 'Gambar menunjukkan matahari dengan sinar di sekitarnya.'
        ]);

        foreach ([
            ['Matahari', true],
            ['Bulan', false],
            ['Bintang', false],
            ['Awan', false],
        ] as [$text, $correct]) {
            Answer::create([
                'question_id' => $qImage->id,
                'answer_text' => $text,
                'is_correct' => $correct
            ]);
        }

        // 2) Question with text stimulus
        $textStimulus = "Sebuah kebun di depan rumah memiliki banyak pohon yang rindang. Setiap pagi, burung-burung berkicau dan udara terasa sejuk.";

        $qText = Question::create([
            'id' => (string) Str::uuid(),
            'test_id' => $test->id,
            'question' => 'Berdasarkan teks tersebut, bagaimana suasana kebun pada pagi hari?',
            'stimulus' => $textStimulus,
            'stimulus_type' => 'text',
            'explanation' => 'Teks menyebutkan burung berkicau dan udara sejuk, sehingga suasana pagi di kebun terasa sejuk dan riang.'
        ]);

        foreach ([
            ['Gersang dan panas', false],
            ['Sejuk dan riang', true],
            ['Hening dan sunyi', false],
            ['Gelap dan dingin', false],
        ] as [$text, $correct]) {
            Answer::create([
                'question_id' => $qText->id,
                'answer_text' => $text,
                'is_correct' => $correct
            ]);
        }

        // 3) Question without stimulus
        $qNone = Question::create([
            'id' => (string) Str::uuid(),
            'test_id' => $test->id,
            'question' => 'Satuan panjang terkecil dari pilihan berikut adalah...?',
            'stimulus' => null,
            'stimulus_type' => null,
            'explanation' => 'Satuan panjang kecil yang umum adalah centimeter di antara pilihan yang tersedia.'
        ]);

        foreach ([
            ['Meter', false],
            ['Kilometer', false],
            ['Centimeter', true],
            ['Kilogram', false],
        ] as [$text, $correct]) {
            Answer::create([
                'question_id' => $qNone->id,
                'answer_text' => $text,
                'is_correct' => $correct
            ]);
        }
    }
}
