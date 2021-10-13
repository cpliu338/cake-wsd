<?php
declare(strict_types=1);

namespace App\Utils;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Psr\Http\Message\UploadedFileInterface;
use App\Model\Entity\CourseGroup;

class AttachmentsUtils {

    use \Cake\Datasource\ModelAwareTrait;
    use \Cake\Log\LogTrait;

    var $base;
    var $mime_types = [
        'image/jpeg' => '.jpg',
        'application/vnd.ms-excel' => '.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        'application/vnd.ms-word' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
        'application/pdf' => '.pdf',
    ];

    public function __construct() {
        $this->base = Configure::read('Path.attachment');
    }

    private function getAttachmentArray(string $dest_path) : array {
        try {
            $folder = new Folder($dest_path);
            return $folder->find('.+');
        }
        catch (\Exception $ex) {
            return [];
        }
    }

    /**
     *  Delete a file in {base}/course_id/course_details and update the model
     * @param CourseGroup $courseGroup
     * @param string $filename
     * @return if successful, the courseGroup with updated "attachments" to be saved by the caller
     * if fail, the reason
     */
    public function delCourseDetailFile(CourseGroup $courseGroup, string $filename) {
        $dest_path = $this->getCourseDetailsPath($courseGroup);
        $this->log($dest_path);
        try {
        $result = (new File($dest_path . DS . $filename))->delete();
        if ($result) {
            $attachments = $this->getAttachmentArray($dest_path);
            $courseGroup->attachments = json_encode($attachments, JSON_UNESCAPED_SLASHES);
            return $courseGroup;
        }
        else {
            return "Cannot delete " . $dest_path . DS . $filename;
        }
        }
        catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getCourseDetailsPath(CourseGroup $courseGroup)
    {
        $folder = new Folder($this->base);
        $suffix = $courseGroup->id . DS . 'course_details';
        $folder->create($suffix);
        return $this->base . DS . $suffix;
    }

    /**
     * Save a blank application form, name without extension hard coded to be application-form_001
     * @param CourseGroup $courseGroup
     * @param UploadedFileInterface $uploadedFile
     * @return if successful, the courseGroup with updated "attachments" to be saved by the caller
     * if fail, the reason
     */
    public function saveApplicationForm(CourseGroup $courseGroup, UploadedFileInterface $uploadedFile) {
        //$folder = new Folder($this->base);
        $ext = $this->mime_types[$uploadedFile->getClientMediaType()];
        $filename = 'application-form_001' . $ext;
        $dest_path = $this->getCourseDetailsPath($courseGroup);
        try {
            // if cannot move to dest, use @ to suppress error
            $fullname = $dest_path . DS . $filename;
            $folder = new Folder($dest_path);
            foreach ($folder->find('application-form_001.*') as $f) {
                if (!@unlink($dest_path . DS . $f)) {
                    $this->log("Failed to delete $f");
                }
            }
            $exists = (new File($fullname))->exists();
            @$uploadedFile->moveTo($fullname);
            if ($exists) {
                return "$filename replaced";
            }
            $attachments = $this->getAttachmentArray($dest_path);
            $courseGroup->attachments = json_encode($attachments, JSON_UNESCAPED_SLASHES);
            return $courseGroup;
        }
        catch (\Exception $ex) { 
            //Laminas\Diactoros\Exception\UploadedFileErrorException thrown
            $this->log($ex->getMessage());
            return "Cannot move upload to " . $dest_path;
            //return false;
        }
    }
}