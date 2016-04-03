<?php

namespace YoutubeThumbnailEnhancer;

class NetworkAdapter
{
    public function curlInit($url = null)
    {
        return curl_init($url);
    }

    public function curlSetOption($ch, $option, $value)
    {
        return curl_setopt ($ch, $option, $value);
    }

    public function curlExec($ch)
    {
        return curl_exec($ch);
    }

    public function curlGetInfo($ch, $opt = null)
    {
        return curl_getinfo ($ch, $opt);
    }

    public function curlClose($ch)
    {
        curl_close($ch);
    }
}