<?php

namespace ZnLib\Rpc\Domain\Helpers;

class DocContentHelper
{

    public static function prepareHtml(string $docsHtml): string
    {
        $docsHtml = preg_replace('#<span class="parent">/(.+?)\s*</span>\s*/(.+?)#i', '<span class="parent">$1.</span>$2', $docsHtml);
        $docsHtml = preg_replace('#<h3 id="(.+?)" class="panel-title">/(.+?)</h3>#i', '<h3 id="$1" class="panel-title">$2</h3>', $docsHtml);
        $docsHtml = preg_replace('#<a\s+href="(.+?)">\/(.+?)<\/a>#i', '<a href="$1">$2</a>', $docsHtml);
        $docsHtml = str_replace('API documentation', '', $docsHtml);
        return $docsHtml;
    }
}
