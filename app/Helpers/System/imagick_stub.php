<?php

namespace StubOnly;
/**
 * Stub untuk bantu Intelephense kenal Imagick.
 * Tidak dipakai saat runtime, hanya buat editor.
 */

if (false) {
    class Imagick
    {
        public function __construct($filename = null) {}
        public function setResolution($x, $y) {}
        public function readImageBlob($blob) {}
        public function setImageFormat($format) {}
        public function resizeImage($width, $height, $filter, $blur) {}
        public function writeImage($filename) {}
    }
}
