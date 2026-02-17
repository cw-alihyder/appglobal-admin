<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use OpenAI;

class GenerateBlogArticle implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        $category = BlogCategory::inRandomOrder()->first();
        if (!$category) {
            return;
        }

        $client = OpenAI::client(env('OPENAI_API_KEY'));

        try {
            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Generate Blog Content (JSON Structured)
            |--------------------------------------------------------------------------
            */

            $response = $client->responses()->create([
                'model' => 'gpt-4o-mini',
                'text' => [
                    'format' => [
                        'type' => 'json_object',
                    ],
                ],
                'input' => "
You are a senior editorial blog writer.

Generate a complete blog package for category: {$category->name}.

Return JSON:

{
  \"title\": \"\",
  \"excerpt\": \"\",
  \"content\": \"\",
  \"meta_title\": \"\",
  \"meta_description\": \"\",
  \"meta_keywords\": \"\",
  \"image_prompt\": \"\"
}

Rules:
- Minimum 900 words
- Natural human tone
- Slightly opinionated
- Vary sentence length
- Avoid generic AI phrases
- Clean HTML only
- Use <h2>, <h3>, <p>, <strong>, <ul><li>
- No markdown
- No inline CSS
- No <html> or <body>
- Image prompt must describe a professional 16:9 blog cover
",
            ]);

            // Extract text safely from new API structure
            $output = $response->output[0]->content[0]->text ?? null;

            if (!$output) {
                Log::error('OpenAI returned empty response.');
                return;
            }

            $data = json_decode($output, true);

            if (!$data || !isset($data['title'], $data['content'])) {
                Log::error('Invalid JSON structure from OpenAI.');
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Prepare Article Data
            |--------------------------------------------------------------------------
            */

            $title = trim($data['title']);
            $excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 160);

            // Unique slug
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            $counter = 1;

            while (BlogArticle::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Sanitize HTML
            $allowedTags = '<h2><h3><p><strong><ul><li><ol><blockquote>';
            $content = strip_tags($data['content'], $allowedTags);

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Save Article
            |--------------------------------------------------------------------------
            */

            $article = BlogArticle::create([
                'title' => $title,
                'excerpt' => $excerpt,
                'slug' => $slug,
                'content' => $content,
                'date' => now(),
                'category_id' => $category->id,
                'meta_title' => $data['meta_title'] ?? $title,
                'meta_description' => $data['meta_description'] ?? $excerpt,
                'meta_keywords' => $data['meta_keywords'] ?? $title,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ Generate AI Image
            |--------------------------------------------------------------------------
            */

            /*
|--------------------------------------------------------------------------
| 4️⃣ Generate AI Image
|--------------------------------------------------------------------------
*/

            $imagePrompt = $data['image_prompt'] ?? "Professional editorial blog cover about {$title}, modern lighting, high detail, realistic, no text, 16:9 ratio";

            $imageResponse = $client->images()->create([
                'model' => 'dall-e-3',
                'prompt' => $imagePrompt,
                'n' => 1,
                'size' => '1024x1024',
                'response_format' => 'url', // We are using URL
            ]);

            if (!isset($imageResponse->data[0]->url)) {
                Log::error('Image generation failed.');
                return;
            }

            $imageUrl = $imageResponse->data[0]->url;

            /*
|--------------------------------------------------------------------------
| Download Image From URL
|--------------------------------------------------------------------------
*/

            $imageBinary = file_get_contents($imageUrl);

            \Log::info('Generated image URL: ' . $imageUrl);

            if (!$imageBinary) {
                Log::error('Failed to download generated image.');
                return;
            }

            $fileName = $slug . '.png';
            $tempDirectory = storage_path('app/public/temp');

            if (!file_exists($tempDirectory)) {
                mkdir($tempDirectory, 0755, true);
            }

            $tempPath = $tempDirectory . '/' . $fileName;

            file_put_contents($tempPath, $imageBinary);

            /*
|--------------------------------------------------------------------------
| 5️⃣ Attach Image via Spatie
|--------------------------------------------------------------------------
*/

            $article
                ->addMedia($tempPath)
                ->withCustomProperties([
                    'alt' => $title,
                ])
                ->toMediaCollection('image', 'public');

            $article->addMedia($tempPath)->toMediaCollection('thumbnail', 'public');

            // Delete temp file
            unlink($tempPath);

            /*
            |--------------------------------------------------------------------------
            | 6️⃣ Attach Random Tags
            |--------------------------------------------------------------------------
            */

            $tags = BlogTag::inRandomOrder()->take(rand(2, 5))->pluck('id');

            $article->tags()->attach($tags);
        } catch (\Exception $e) {
            Log::error('AI Blog generation failed: ' . $e->getMessage());
        }
    }
}
