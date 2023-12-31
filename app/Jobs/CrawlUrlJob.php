<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UrlExtractorService;
use App\Models\Url;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CrawlUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $url;
    private $depth;
    private $session_id;

    public function __construct($url, $depth, $session_id)
    {
        $this->url = $url;
        $this->depth = $depth;
        $this->session_id = $session_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (strpos($this->url, '#')) {
            $this->url = substr($this->url, 0, strpos($this->url, '#'));
        }

        $response = Http::get($this->url);

        if ($response->ok()) {
            Url::firstOrCreate(['url' => $this->url]);

            // Emit a 'crawl-result' event with the crawl results
            $channel_name = "channel_". $this->session_id;
            
            Http::post("http://localhost:3000/broadcast/".$channel_name, ["url" => $this->url]);

            // Crawl the child URLs if the depth is not zero
            if ($this->depth > 0) {
                $domain = UrlExtractorService::extractDomain($this->url);
                $body = $response->body();
                $childUrls = UrlExtractorService::extractUrls($domain, $body);
                foreach ($childUrls as $childUrl) {
                    CrawlUrlJob::dispatch($childUrl, $this->depth - 1, $this->session_id);
                }
            }
        }
    }
}
