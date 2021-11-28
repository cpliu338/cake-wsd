<?= $this->element('ChunkedFileUpload.script')?>
<div>
    <?= $this->element('ChunkedFileUpload.form', [
        'url' => $this->Url->build(['action'=>'upload']),
        'onComplete' => '/wsd/course-groups/uploadComplete',
        'controls' => [
            'title'=>'text'
        ]
    ])?>
</div>
<?php
echo $this->Url->build(['action'=>'index', 'controller'=>'CourseGroups', 'prefix'=>'Clerical'], ['fullBase'=>true]);