<?php

namespace Response\Render;

require_once __DIR__ . '/../HTTPRenderer.php';

use Response\HTTPRenderer;

class MediaRenderer implements HTTPRenderer {
    public function __construct(private string $filepathBase, private string $type) {}

    public function getFields(): array {
        return [
            'Content-Type' => $this->getTypeDetails()['content_type']
        ];
    }

    public function getFileName(): string{
        $base = __DIR__ . '/../..';
        $filename = sprintf("%s/%s.%s", $base, $this->filepathBase, $this->getTypeDetails()['extension']);
        if(file_exists($filename)) return $filename;
        else return sprintf("%s/public/images/file-not-found.jpeg", $base);
    }

    // public function getContent(): string {
    //     ob_start();
    //     readfile($this->getFileName());
    //     return ob_get_clean();
    // }
    public function getContent(): string {
        $filename = $this->getFileName();
        if (file_exists($filename)) { // ファイルの存在確認
            ob_start();
            readfile($filename);
            $content = ob_get_clean();
            error_log("Serving file: " . $filename); // デバッグ情報をログに出力
            return $content;
        } else {
            error_log("File not found: " . $filename); // エラーログに出力
            throw new \Exception("File not found: " . $filename);
        }
    }

    private function getTypeDetails(): array{
        $supportedContentTypes = [
            'jpg' => [
                'content_type' => 'image/jpeg',
                'extension' => 'jpg',
            ],
            'jpeg' => [
                'content_type' => 'image/jpeg',
                'extension' => 'jpeg',
            ],
            'png' => [
                'content_type' => 'image/png',
                'extension' => 'png',
            ],
            'gif' => [
                'content_type' => 'image/gif',
                'extension' => 'gif',
            ],
            'mp3' => [
                'content_type' => 'audio/mpeg',
                'extension' => 'mp3',
            ],
            'mp4' => [
                'content_type' => 'video/mp4',
                'extension' => 'mp4',
            ],
        ];

        if (isset($supportedContentTypes[$this->type])) {
            return $supportedContentTypes[$this->type];
        } else {
            throw new \InvalidArgumentException(sprintf("Media type %s is an invalid type", $this->type));
        }
    }
}