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
            Log::warning('AI Generation skipped: No categories found.');
            return;
        }

        $client = OpenAI::client(env('OPENAI_API_KEY'));

        try {
            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Generate Blog Content (JSON)
            |--------------------------------------------------------------------------
            */
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a senior editorial blog writer. Return JSON only.'],
                    ['role' => 'user', 'content' => "Generate a complete blog package for category: {$category->name}.
                        Required JSON keys: title, excerpt, content, meta_title, meta_description, meta_keywords, image_prompt.
                        Rules: 900+ words, natural tone, clean HTML (h2, h3, p, strong, ul, li), no markdown."]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

            $output = $response->choices[0]->message->content ?? null;
            $data = json_decode($output, true);

            if (!$data || !isset($data['title'], $data['content'])) {
                throw new \Exception('Invalid JSON structure or empty response from OpenAI.');
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Prepare & Sanitize Data
            |--------------------------------------------------------------------------
            */
            $title = trim($data['title']);
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            $counter = 1;

            while (BlogArticle::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Sanitize HTML to prevent unwanted tags
            $allowedTags = '<h2><h3><p><strong><ul><li><ol><blockquote>';
            $sanitizedContent = strip_tags($data['content'], $allowedTags);

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Save Article
            |--------------------------------------------------------------------------
            */
            $article = BlogArticle::create([
                'title'            => $title,
                'excerpt'          => $data['excerpt'] ?? Str::limit(strip_tags($sanitizedContent), 160),
                'slug'             => $slug,
                'content'          => $sanitizedContent,
                'date'             => now(),
                'category_id'      => $category->id,
                'meta_title'       => $data['meta_title'] ?? $title,
                'meta_description' => $data['meta_description'] ?? ($data['excerpt'] ?? ''),
                'meta_keywords'    => $data['meta_keywords'] ?? $title,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ Generate & Attach AI Image (Spatie optimized)
            |--------------------------------------------------------------------------
            */
            $imagePrompt = $data['image_prompt'] ?? "Professional editorial blog cover about {$title}, modern, high detail, 16:9";

            $imageResponse = $client->images()->create([
                'model' => 'dall-e-3',
                'prompt' => $imagePrompt,
                'n' => 1,
                'size' => '1024x1024',
            ]);

            $imageUrl = $imageResponse->data[0]->url ?? null;

            if ($imageUrl) {
                // Using Spatie's built-in URL handler avoids manual downloads and temp files
                $article->addMediaFromUrl($imageUrl)
                    ->usingFileName($slug . '.png')
                    ->withCustomProperties(['alt' => $title])
                    ->toMediaCollection('image', 'public');

                // If you need a separate collection for thumbnails:
                $article->addMediaFromUrl($imageUrl)
                    ->usingFileName($slug . '-thumb.png')
                    ->toMediaCollection('thumbnail', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | 5️⃣ Attach Random Tags
            |--------------------------------------------------------------------------
            */
            $tags = BlogTag::inRandomOrder()->take(rand(2, 5))->pluck('id');
            $article->tags()->attach($tags);

            Log::info("Successfully generated blog: " . $title);

        } catch (\Exception $e) {
            Log::error('AI Blog generation failed: ' . $e->getMessage());
            // Rethrow if you want the queue to attempt a retry
            throw $e;
        }
    }
}
