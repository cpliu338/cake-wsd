<?php
declare(strict_types=1);

namespace App\Utils;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Psr\Http\Message\UploadedFileInterface;

class AttachmentsUtils {

    use \Cake\Datasource\ModelAwareTrait;

    public function saveBlankApplication(int $courseGroupId, UploadedFileInterface $uploadedFile) {
        $base = new Folder(Configure::read('Path.attachment'));
        $base->create(strval($courseGroupId));
        $ext = '.bin';
        switch ($uploadedFile->getClientMediaType()) {
            case 'image/jpeg': $ext = '.jpg'; break;
            case 'application/vnd.ms-excel': $ext = '.xls'; break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': $ext = '.xlsx'; break;
            case 'application/vnd.ms-word': $ext = '.doc'; break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': $ext = '.docx'; break;
        }
        return $uploadedFile->moveTo(Configure::read('Path.attachment') . DS . 
            $courseGroupId . DS . 'application-form_001' . $ext);
    }
}