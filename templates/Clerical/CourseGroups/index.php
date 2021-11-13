<table>
<?php foreach ($courseGroups as $cg):?>
    <tr>
        <td><?= $cg->title?></td>
    </tr>
<?php endforeach;?>
</table>
<div>
    <ul id="result"></ul>
    <div id="progress"></div>
    <label class="btn btn-primary">
        Browse <input type="file" hidden id="fileupload" name="files" data-url="<?=$this->Url->build([
            'action'=>'upload'
        ])?>">
    </label>
</div>
<?php
    echo $this->element('itnrs/ajax_box_style'),
    $this->element('itnrs/ajax_box', ['type'=>'approving', 'user_id'=>$user, 'title'=>'To approve']), 
    $this->element('itnrs/ajax_box', ['type'=>'recommending', 'user_id'=>$user, 'title'=>'To recommend']);
    $this->append('script');
    echo 
    $this->Html->script('jquery.ui.widget.js'),
    $this->Html->script('jquery.iframe-transport.js'),
    $this->Html->script('jquery.fileupload.js');
?>
<script>
$(function () {
    $('#fileupload').fileupload({
        dataType: 'json',
        maxChunkSize: 1000000, // 1 MB
        formData: {_csrfToken: $('meta[name="csrfToken"]').attr('content')},
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
            li = $('<li></li>').text(file.file_path);
            $("#result").append(li);
        },
        progressall: function (e, data) {
            // expects {"loaded":181352,"total":181352,"bitrate":145081600}
            $("#progress").text("Uploaded bytes: " + data.total);
            $("#progress").animate({opacity:1}, 100);
            $("#progress").animate({opacity:0}, 10000);
        }
    });
});
</script>
<?php $this->end();?>