<?php
/*
prefix: id prefix for the controls of this element, default upload.
    If you have more than one element in a page, you must do this
maxChunkSize: large files are uploaded in chunks of this size (check php.ini settings for max_upload_size, max_post_size)
onComplete: if defined, a function to be called on completion of upload
*/
    $prefix = 'upload';
    $tmpfolder = $this->request->getSession()->id() . '-' . rand(0, 100000);
?>
    <div class="bg-success" id="<?="$prefix-result"?>"></div>
    <div id="<?="$prefix-progress"?>"></div>
<?php
    echo $this->Form->create(null, ['id'=>"$prefix-form"]),
        $this->Form->hidden('tmpfolder', ['value'=>$tmpfolder]);
    foreach ($controls as $control) {
        if (is_string($control)) {
            echo $this->Form->control($control, ['type'=>'text']);
        }
        else {
            $name = $control['name'];
            unset($control['name']);
            echo $this->Form->control($name, $control);
        }
    }        
?>
    <label class="btn btn-primary">
        Browse <input type="file" hidden id="<?=$prefix?>-file" name="files" data-url="<?=$url?>">
    </label>
<?php
    $this->Form->end();
    $this->append('script');
?>
<script>
    $(function () {
        $('#<?=$prefix?>-file').fileupload({
            //dataType: 'json',
            maxChunkSize: <?= $maxChunkSize ?? 1000000?>, // default 1 MB
            done: function (e, data) {
                /*
                {
                    "upload":{
                        "name":"Templeton.pdf","type":"application/pdf",
                        "tmp_name":"/tmp/phpwd9aIA","error":0,"size":239330
                    },
                    "files":[
                        {
                            "name":"course_details-001.pdf","size":239330,
                            "type":"application/pdf","file_path":"/var/www/html/uploads/course_details-001.pdf",
                            "error":'File type not allowed' <-- optional
                        }
                    ],
                }
                */
                files = data.result.files;
                file = files[0];
                //result.text(`Received ${file.name} ${file.size} bytes`);
            },
            progressall: function (e, data) {
                // expects {"loaded":181352,"total":181352,"bitrate":145081600}
                $("#<?="$prefix-progress"?>").text(`Uploaded bytes: ${data.loaded} of ${data.total}`);
                $("#<?="$prefix-progress"?>").animate({opacity:1}, 10);
                $("#<?="$prefix-progress"?>").animate({opacity:0}, 500);
                <?php if ($onComplete):?>
                if (data.loaded == data.total) {
                    $.ajax({
                    url: "<?=$onComplete?>", //'/wsd/course-groups/uploadComplete',
                    method: 'post',
                    dataType: 'json',
                    data: objectifyForm($("#<?=$prefix?>-form").serializeArray())
                    })
                    .done(function(data) {
                        if (data.msg) {
                        result = $("#<?="$prefix-result"?>");
                        result.removeClass("bg-danger");
                        result.addClass("bg-success");
                        result.text(data.msg);
                        }
                        if (data.redirect) {
                            window.location = data.redirect;
                        }
                    });
                }
                <?php endif;?>
            }
        });
/*
image: /image\/(jpe?g|gif|png)/i
pdf: /application\/pdf/i
csv: /text\/csv/i
excel: /application\/vnd.+(excel|sheet)$/i
doc: /application\msword
docx: /vnd.+(document)$/i
ppt: /application\/vnd.+(powerpoint|presentation)$/i
txt: /text\/plain/i
video: /video\/.+/i
audio: /audio\/.+/i

        $('#<?=$prefix?>-file').bind('fileuploadsubmit', function (e, data) {
            data.formData = objectifyForm($("#<?=$prefix?>-form").serializeArray());
            if (!data.formData.title) {
                result = $("#<?=$prefix?>-result");
                result.removeClass("bg-success");
                result.addClass("bg-danger");
                result.text("Title cannot be empty");
                //input.focus();
                return false;
            }
        })
*/
    });
</script>
<?php $this->end(); ?>
    