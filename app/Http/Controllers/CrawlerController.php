<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\CrawlUrlJob;

class CrawlerController extends Controller
{
    public function crawl(Request $request)
    {
        $request->validate([
            'url' => 'bail|required|url',
            'depth' => 'integer',
        ]);

        $url = $request->input('url');
        $depth = $request->input('depth') ?? 10;

        CrawlUrlJob::dispatch($url, $depth);

        return response()->json(['ok'=>true,'message' => 'Crawling completed successfully']);
    }
}
