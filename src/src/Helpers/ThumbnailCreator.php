<?php
    class ThumbnailCreator {

        public static function createThumbnail($src, $targetWidth, $targetHeight) {

            $fileNameNoExt = pathinfo($src, PATHINFO_FILENAME);
            $thumbnailName = $fileNameNoExt . "_thumb" . ".png";

            $dest = Utils::combinePaths(array(Config::getPublicPath(), "thumbnails", $thumbnailName));
            $src = Utils::combinePaths(array(Config::getUploadsPath(), $src));

            $type = exif_imagetype($src);
            if (!$type || !self::IMAGE_HANDLERS[$type]) {
                return null;
            }

            $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src);
            if (!$image) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

            if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
                imagecolortransparent(
                    $thumbnail,
                    imagecolorallocate($thumbnail, 0, 0, 0)
                );

                if ($type == IMAGETYPE_PNG) {
                    imagealphablending($thumbnail, false);
                    imagesavealpha($thumbnail, true);
                }
            }

            imagecopyresampled(
                $thumbnail,
                $image,
                0, 0, 0, 0,
                $targetWidth, $targetHeight,
                $width, $height
            );

            call_user_func(
                self::IMAGE_HANDLERS[$type]['save'],
                $thumbnail,
                $dest,
                self::IMAGE_HANDLERS[$type]['quality']
            );

            return Utils::combinePaths(array("thumbnails", $thumbnailName));
        }

        const IMAGE_HANDLERS = [
            IMAGETYPE_JPEG => [
                'load' => 'imagecreatefromjpeg',
                'save' => 'imagejpeg',
                'quality' => 100
            ],
            IMAGETYPE_PNG => [
                'load' => 'imagecreatefrompng',
                'save' => 'imagepng',
                'quality' => -1
            ],
            IMAGETYPE_GIF => [
                'load' => 'imagecreatefromgif',
                'save' => 'imagegif'
            ]
        ];

    }
?>