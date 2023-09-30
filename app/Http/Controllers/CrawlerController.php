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
            'session_id' => 'string|required'
        ]);

        $url = $request->input('url');
        $depth = $request->input('depth') ?? 10;
        $session_id = $request->input('session_id');

        CrawlUrlJob::dispatch($url, $depth, $session_id);

        return response()->json(['ok'=>true,'message' => 'Crawling started successfully']);
    }

    public function get_session_id(Request $request)
    {
        return response()->json(['ok'=>true,'session_id' => $request->session()->getId()]);
    }
}
