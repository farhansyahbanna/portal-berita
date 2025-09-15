<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishedPosts = Post::whereNotNull('published_at')->get();
        $users = User::where('role', 'user')->get();
        $faker = \Faker\Factory::create('id_ID');

        $comments = [];

        foreach ($publishedPosts as $post) {
            // Generate 3-8 comments per post
            $commentCount = rand(3, 8);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $user = $users->random();
                
                $comments[] = [
                    'post_id' => $post->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'content' => $this->generateCommentContent($faker),
                    'status' => 'approved',
                    'created_at' => $faker->dateTimeBetween($post->published_at, 'now'),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all comments
        Comment::insert($comments);

        $this->command->info('Comments seeded successfully!');
    }

    private function generateCommentContent($faker)
    {
        $comments = [
            'Artikel yang sangat informatif! Terima kasih sudah berbagi.',
            'Sangat membantu untuk pemula seperti saya.',
            'Apakah ada rencana untuk membuat tutorial lanjutan?',
            'Saya sudah mencoba dan berhasil! Terima kasih.',
            'Penjelasannya sangat jelas dan mudah dipahami.',
            'Bisa tolong dijelaskan lebih detail tentang poin ketiga?',
            'Artikel yang tepat waktu, sedang membutuhkan informasi ini.',
            'Apakah metode ini juga bekerja untuk versi terbaru?',
            'Sangat inspiratif! Memberikan perspektif baru.',
            'Terima kasih atas tipsnya, sangat berguna untuk project saya.',
            'Ada saran untuk troubleshooting jika mengalami error?',
            'Kualitas konten yang sangat baik, ditunggu artikel selanjutnya!',
            'Sangat relevan dengan perkembangan teknologi saat ini.',
            'Apakah ada best practice lain yang bisa direkomendasikan?',
            'Penulisan yang sangat profesional dan mudah diikuti.',
        ];

        return $faker->randomElement($comments);
    }

}
