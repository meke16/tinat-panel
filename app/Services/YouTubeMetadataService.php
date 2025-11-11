<?php

namespace App\Services;

use Google_Client;
use Google_Service_YouTube;
class YouTubeMetadataService
{
    protected $youtube;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setDeveloperKey(config('services.youtube.key'));
        $this->youtube = new Google_Service_YouTube($client);
    }

    public function getVideoInfo(string $url): ?array
    {
        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        $videoId = $matches[1] ?? null;
        if (!$videoId) return null;

        $response = $this->youtube->videos->listVideos('snippet,contentDetails', ['id' => $videoId]);
        $video = $response->items[0] ?? null;
        if (!$video) return null;

        // Convert ISO 8601 duration to seconds
        $interval = new \DateInterval($video->contentDetails->duration);
        $seconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

        return [
            'title' => $video->snippet->title,
            'description' => $video->snippet->description,
            'author' => $video->snippet->channelTitle,
            'duration' => $seconds,
        ];
    }
}
