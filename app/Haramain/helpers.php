<?php

use App\Metronics\Adapters\Theme;
use App\Metronics\Adapters\Util;
use Illuminate\Contracts\Foundation\Application;

if (!function_exists('rupiah_format')){
    function rupiah_format($number): ?string
    {
        return number_format($number, 0, ",", ".") ?? null;
    }
}

if (!function_exists('diskon_format')){
    function diskon_format($value, $angkaBelakangKoma)
    {
        return number_format($value, $angkaBelakangKoma,",", ".");
    }
}

if (!function_exists('tanggalan_database_format')){
    function tanggalan_database_format($tanggal, $format): ?string
    {
        return \Carbon\Carbon::createFromFormat($format, $tanggal)->format('Y-m-d') ?? null;
    }
}

if (!function_exists('tanggalan_format')){
    function tanggalan_format($tanggal): ?string
    {
        return \Carbon\Carbon::parse($tanggal)->format('d-M-Y') ?? null;
    }
}

if (!function_exists('before_string_me')){
    function before_string_me ($char, $data)
    {
        return substr($data, 0, strpos($data, $char));
    };
}

if (!function_exists('terbilang')){
    function terbilang($angka) {
        $angka=abs($angka);
        $baca =array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

        $terbilang="";
        if ($angka < 12){
            $terbilang= " " . $baca[$angka];
        }
        else if ($angka < 20){
            $terbilang= terbilang($angka - 10) . " belas";
        }
        else if ($angka < 100){
            $terbilang= terbilang($angka / 10) . " puluh" . terbilang($angka % 10);
        }
        else if ($angka < 200){
            $terbilang= " seratus" . terbilang($angka - 100);
        }
        else if ($angka < 1000){
            $terbilang= terbilang($angka / 100) . " ratus" . terbilang($angka % 100);
        }
        else if ($angka < 2000){
            $terbilang= " seribu" . terbilang($angka - 1000);
        }
        else if ($angka < 1000000){
            $terbilang= terbilang($angka / 1000) . " ribu" . terbilang($angka % 1000);
        }
        else if ($angka < 1000000000){
            $terbilang= terbilang($angka / 1000000) . " juta" . terbilang($angka % 1000000);
        }
        return $terbilang;
    }

    /** Helpers From Metronics */
    if (!function_exists('get_svg_icon')) {
        function get_svg_icon($path, $class = null, $svgClass = null)
        {
            if (strpos($path, 'media') === false) {
                $path = theme()->getMediaUrlPath().$path;
            }

            $file_path = public_path($path);

            if (!file_exists($file_path)) {
                return '';
            }

            $svg_content = file_get_contents($file_path);

            if (empty($svg_content)) {
                return '';
            }

            $dom = new DOMDocument();
            $dom->loadXML($svg_content);

            // remove unwanted comments
            $xpath = new DOMXPath($dom);
            foreach ($xpath->query('//comment()') as $comment) {
                $comment->parentNode->removeChild($comment);
            }

            // add class to svg
            if (!empty($svgClass)) {
                foreach ($dom->getElementsByTagName('svg') as $element) {
                    $element->setAttribute('class', $svgClass);
                }
            }

            // remove unwanted tags
            $title = $dom->getElementsByTagName('title');
            if ($title['length']) {
                $dom->documentElement->removeChild($title[0]);
            }
            $desc = $dom->getElementsByTagName('desc');
            if ($desc['length']) {
                $dom->documentElement->removeChild($desc[0]);
            }
            $defs = $dom->getElementsByTagName('defs');
            if ($defs['length']) {
                $dom->documentElement->removeChild($defs[0]);
            }

            // remove unwanted id attribute in g tag
            $g = $dom->getElementsByTagName('g');
            foreach ($g as $el) {
                $el->removeAttribute('id');
            }
            $mask = $dom->getElementsByTagName('mask');
            foreach ($mask as $el) {
                $el->removeAttribute('id');
            }
            $rect = $dom->getElementsByTagName('rect');
            foreach ($rect as $el) {
                $el->removeAttribute('id');
            }
            $xpath = $dom->getElementsByTagName('path');
            foreach ($xpath as $el) {
                $el->removeAttribute('id');
            }
            $circle = $dom->getElementsByTagName('circle');
            foreach ($circle as $el) {
                $el->removeAttribute('id');
            }
            $use = $dom->getElementsByTagName('use');
            foreach ($use as $el) {
                $el->removeAttribute('id');
            }
            $polygon = $dom->getElementsByTagName('polygon');
            foreach ($polygon as $el) {
                $el->removeAttribute('id');
            }
            $ellipse = $dom->getElementsByTagName('ellipse');
            foreach ($ellipse as $el) {
                $el->removeAttribute('id');
            }

            $string = $dom->saveXML($dom->documentElement);

            // remove empty lines
            $string = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

            $cls = array('svg-icon');

            if (!empty($class)) {
                $cls = array_merge($cls, explode(' ', $class));
            }

            $asd = explode('/media/', $path);
            if (isset($asd[1])) {
                $path = 'assets/media/'.$asd[1];
            }

            $output = "<!--begin::Svg Icon | path: $path-->\n";
            $output .= '<span class="'.implode(' ', $cls).'">'.$string.'</span>';
            $output .= "\n<!--end::Svg Icon-->";

            return $output;
        }
    }

    if (!function_exists('theme')) {
        /**
         * Get the instance of Theme class core
         *
         * @return Theme|Application|mixed
         */
        function theme()
        {
            return app(Theme::class);
        }
    }

    if (!function_exists('util')) {
        /**
         * Get the instance of Util class core
         *
         * @return Util|Application|mixed
         */
        function util()
        {
            return app(Util::class);
        }
    }

    if (!function_exists('bootstrap')) {
        /**
         * Get the instance of Util class core
         *
         * @return Util|Application|mixed
         * @throws Throwable
         */
        function bootstrap()
        {
            $demo      = ucwords(theme()->getDemo());
            $bootstrap = "\App\Metronics\Bootstraps\Bootstrap$demo";

            if (!class_exists($bootstrap)) {
                abort(404, 'Demo has not been set or '.$bootstrap.' file is not found.');
            }

            return app($bootstrap);
        }
    }

    if (!function_exists('assetCustom')) {
        /**
         * Get the asset path of RTL if this is an RTL request
         *
         * @param $path
         * @return string
         */
        function assetCustom($path)
        {
            // Include rtl css file
            if (isRTL()) {
                return asset(theme()->getDemo().'/'.dirname($path).'/'.basename($path, '.css').'.rtl.css');
            }

            // Include dark style css file
            if (theme()->isDarkModeEnabled() && theme()->getCurrentMode() !== 'light') {
                $darkPath = str_replace('.bundle', '.'.theme()->getCurrentMode().'.bundle', $path);
                if (file_exists(public_path(theme()->getDemo().'/'.$darkPath))) {
                    return asset(theme()->getDemo().'/'.$darkPath);
                }
            }

            // Include default css file
            return asset(theme()->getDemo().'/'.$path);
        }
    }

    if (!function_exists('isRTL')) {
        /**
         * Check if the request has RTL param
         *
         * @return bool
         */
        function isRTL()
        {
            return request()->input('rtl') || setting('layout.rtl') === 'true';
        }
    }

    if (!function_exists('preloadCss')) {
        /**
         * Preload CSS file
         *
         * @return bool
         */
        function preloadCss($url)
        {
            return '<link rel="preload" href="'.$url.'" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" type="text/css"><noscript><link rel="stylesheet" href="'.$url.'"></noscript>';
        }
    }
}
