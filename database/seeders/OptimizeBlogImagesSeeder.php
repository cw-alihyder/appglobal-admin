<?php

namespace Database\Seeders;

use App\Models\BlogArticle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptimizeBlogImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = BlogArticle::with('media')->get();

        foreach ($articles as $article) {
            foreach ($article->media as $media) {

                // Regenerate conversions
                $media->generateConversions();

                // Optimize original file
                $media->refresh();

                $this->command->info("Optimized: {$media->file_name}");
            }
        }

        $this->command->info('All images optimized successfully.');
    }
}
