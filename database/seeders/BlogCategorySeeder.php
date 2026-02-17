<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;

        $pillars = [
            'IT Infrastructure','Cloud Computing','Artificial Intelligence',
            'VPS Hosting','Web Development','Real Estate Investment',
            'Business Strategy','Digital Marketing','Gaming Hardware',
            'SaaS Platforms','Cybersecurity','FinTech','EdTech',
            'Automation Technology','Startup Ecosystem'
        ];

        $buyerIntent = [
            'Best','Top','Affordable','Premium','Enterprise',
            'Recommended','High Performance'
        ];

        $contentTypes = [
            'Guide','Tips','Trends','Strategies','Tools',
            'Insights','Comparison','Reviews','Best Practices',
            'Market Analysis','Case Studies','Tutorials'
        ];

        $problems = [
            'Cost Reduction','Performance Optimization',
            'Security Improvements','Scaling Strategies',
            'Investment Planning','Growth Strategy'
        ];

        $categories = [];

        while (count($categories) < 600) {

            $pillar = $pillars[array_rand($pillars)];
            $type = rand(1, 5);

            switch ($type) {

                case 1: // Informational
                    $name = "$pillar " . $contentTypes[array_rand($contentTypes)];
                    break;

                case 2: // Buyer intent
                    $name = $buyerIntent[array_rand($buyerIntent)] . " $pillar";
                    break;

                case 3: // Comparison
                    $other = $pillars[array_rand($pillars)];
                    $name = "$pillar vs $other";
                    break;

                case 4: // Problem solving
                    $name = "$pillar " . $problems[array_rand($problems)];
                    break;

                default:
                    $name = "$pillar " . $contentTypes[array_rand($contentTypes)];
            }

            // 25% chance to add year
            if (rand(1,100) <= 25) {
                $year = rand(0,1) ? $currentYear : $nextYear;
                $name .= " $year";
            }

            $categories[] = $name;
        }

        $categories = array_unique($categories);

        foreach ($categories as $categoryName) {
            BlogCategory::updateOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
        }

        $this->command->info(count($categories) . ' ultra SEO-optimized categories seeded!');
    }
}
