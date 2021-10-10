<table>
<?php foreach ($courseGroups as $cg):?>
    <tr>
        <td><?= $cg->title?></td>
    </tr>
<?php endforeach;?>
</table>
<?php
    $tree = \Cake\Core\Configure::read('Division.tree');
    echo $this->Form->create($courseGroup, ['url'=>'/admin/course-groups/add-dummy-course']),
        $this->Form->control('division', [
            'type'=>'select', 'options'=>array_combine(array_keys($tree), array_keys($tree))
        ]),
        $this->Form->submit('New dummy course'),
        $this->Form->end();
?>
<div class="container">
    <form method="post" action="" enctype="multipart/form-data" id="myform">
        <div class='preview'>
            <span id="preview"></span>
        </div>
        <div >
            <input type="file" id="file" name="file" />
            <input type="button" class="btn btn-default" value="Upload" id="but_upload">
        </div>
    </form>
</div>
<?php $this->start('script'); ?>
<script>

    var csrfToken = $('meta[name="csrfToken"]').attr('content');

$(function(){
    $("#but_upload").click(function(){

        var fd = new FormData();
        var files = $('#file')[0].files;
        
        // Check file selected or not
        if(files.length > 0 ){
            fd.append('file',files[0]);

            $.ajax({
                url: '<?= $this->Url->build(['action'=>'upload'])?>',
                type: 'post',
                data: fd,
                /* following 2 lines stop jQuery from processing the multipart data */
                contentType: false,
                processData: false,
                headers: {'X-CSRF-Token': csrfToken, 'Accept': 'application/json'},
                success: function(response){
                    $("#preview").html(JSON.stringify(response.result));
                },
                error: function (jqXHR, exception) {
                    $("#preview").html("Error "+jqXHR.status);
                }
            });
        }
        else {
            alert("Please select a file.");
        }
    });
});
</script>
<?php $this->end(); ?>