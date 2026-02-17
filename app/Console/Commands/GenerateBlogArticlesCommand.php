<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerateBlogArticle;

class GenerateBlogArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate blog articles using OpenAI';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        GenerateBlogArticle::dispatch();
        $this->info('Blog article generation job dispatched!');
    }
}
