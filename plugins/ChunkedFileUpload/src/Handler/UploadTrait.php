<?php
declare(strict_types=1);

namespace ChunkedFileUpload\Handler;
use ChunkedFileUpload\Handler\UploadHandler;

trait UploadTrait {


    function handleUpload(array $options) {
        $options['upload_dir'] = $options['upload_base'] . $this->request->getData('tmpfolder') . DS;
        unset($options['upload_base']);
        //@mkdir($options['upload_dir']);
        $upload_handler = new UploadHandler($options);
        $result = $upload_handler->handle();
        $result['tmpfolder'] = $this->request->getData('tmpfolder');
        $result['upload_name'] = $result['upload']['name'];
        //$result['error'] = 0;
        $this->response = $this->response->withType('application/json');
        return $result;
    }

    function moveUploadedFile($upload_base, $dest_folder, $dest_name = null, $keep_extension=true) {
        $upload_dir = $upload_base . $this->request->getData('tmpfolder');
        $result = [];
        try {
            foreach (new \DirectoryIterator($upload_dir) as $file) {
                if (!$file->isDot()) {
                    if ($dest_name && $keep_extension) {
                        $dest_name = $dest_name . '.' . $file->getExtension();
                    }
                    rename($file->getPathname(), $dest_folder . DS . ($dest_name ?? $file->getFilename()));
                    $result['name'] =$file->getPathname();
                    $result['size'] =$file->getSize();
                    @rmdir($upload_dir);
                }
            }
        }
        catch (\Exception $ex) {
            $result['exception'] = ['class'=>get_class($ex), 'message'=>$ex->getMessage()];
        }
        return $result;
    }

    function garbageCollectTmpfolders($upload_base) {
        // clean stale directory under $upload_base
        foreach (new \DirectoryIterator($upload_base) as $file) {
            if (!$file->isDot() && $file->isDir()) {
                $stat = stat($file->getPathname());
                $this->log($file->getPathname(), 'info');
                $this->log('' . $stat['mtime'], 'info'); // integer, unix time in seconds
            }
        }
    }
}