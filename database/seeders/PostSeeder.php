<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUsers = User::where('role', 'admin')->get();
        $editorUsers = User::where('role', 'editor')->get();
        $faker = \Faker\Factory::create('id_ID');

        $posts = [
            [
                'title' => 'Teknologi AI Terbaru Mengubah Dunia Digital',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Panduan Lengkap Belajar Laravel untuk Pemula',
                'content' => $this->generateLongContent($faker),
                'published_at' => now()->subDays(5),
                'status' => 'published',
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Inovasi Terbaru dalam Pengembangan Web Modern',
                'content' => $this->generateLongContent($faker),
                'published_at' => now()->subDays(1),
                'status' => 'published',
                'user_id' => $adminUsers->last()->id,
            ],
            [
                'title' => 'Cara Meningkatkan Keamanan Website Anda',
                'content' => $this->generateLongContent($faker),
                'published_at' => now()->subDays(3),
                'status' => 'published',
                'user_id' => $adminUsers->last()->id,
            ],
            [
                'title' => 'Trend Teknologi 2024 yang Perlu Anda Ketahui',
                'content' => $this->generateLongContent($faker),
                'published_at' => now()->subDays(7),
                'status' => 'published',
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Mengoptimalkan Database untuk Performa Terbaik',
                'content' => $this->generateLongContent($faker),
                'published_at' => now()->subDays(4),
                'status' => 'published',
                'user_id' => $adminUsers->last()->id,
            ],
            [
                'title' => 'Belajar Vue.js: Framework JavaScript yang Powerful',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Tips dan Trik Menggunakan Tailwind CSS',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'user_id' => $adminUsers->last()->id,
            ],
            [
                'title' => 'Membangun RESTful API dengan Laravel',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Keamanan Aplikasi Web: Best Practices',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'user_id' => $adminUsers->last()->id,
            ],
            // Draft posts
            [
                'title' => 'Artikel Baru yang Masih dalam Proses',
                'content' => $this->generateLongContent($faker),
                'status' => 'draft',
                'published_at' => null,
                'user_id' => $adminUsers->first()->id,
            ],
            [
                'title' => 'Panduan Advanced Laravel Techniques',
                'content' => $this->generateLongContent($faker),
                'status' => 'draft',
                'published_at' => null,
                'user_id' => $adminUsers->last()->id,
            ],

             // Posts oleh editor
            [
                'title' => 'Trend Teknologi 2024 oleh Editor',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'user_id' => $editorUsers->first()->id,
            ],
            [
                'title' => 'Panduan Laravel untuk Pemula',
                'content' => $this->generateLongContent($faker),
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'user_id' => $editorUsers->last()->id,
            ],
        ];

        foreach ($posts as $postData) {
            Post::create($postData);
        }

        $this->command->info('Posts seeded successfully!');
    }

    private function generateLongContent($faker)
    {
        $content = '<h2>' . $faker->sentence(6) . '</h2>';
        $content .= '<p>' . $faker->paragraph(10) . '</p>';
        $content .= '<h3>' . $faker->sentence(4) . '</h3>';
        $content .= '<p>' . $faker->paragraph(8) . '</p>';
        $content .= '<ul>';
        for ($i = 0; $i < 5; $i++) {
            $content .= '<li>' . $faker->sentence(5) . '</li>';
        }
        $content .= '</ul>';
        $content .= '<p>' . $faker->paragraph(6) . '</p>';
        $content .= '<blockquote><p>' . $faker->paragraph(3) . '</p></blockquote>';
        $content .= '<p>' . $faker->paragraph(4) . '</p>';

        return $content;
    }
}
