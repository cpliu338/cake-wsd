<div>
</div>
<?php
    echo $this->Form->create($courseGroup),
        $this->Form->control('title', ['label' =>'SQL', 'type'=>'textarea']),
        $this->Form->control('attachments', ['label' =>'pattern']),
        $this->Form->button('Check', ['class'=>'btn btn-success', 'id'=>'check']);
?>
<div id="check-result" class="bg-success"></div>
<?php
        echo $this->Form->submit(),
        $this->Form->end();
        /*
    echo $this->Url->build(['action'=>'index', 'controller'=>'CourseGroups', 'prefix'=>'Clerical'], ['fullBase'=>true]);
    */
        $this->append('script');
?>
<script>
    $("#check").click(function (ev){
        ev.preventDefault();
        $.ajax({
            url: "<?=$this->Url->build(['action'=>'view'])?>",
            method: "post",
            dataType: "json",
            data: $("#check").parent("form").serialize()
        })
        .done(function (data){
            $("#check-result").text(data.courseGroup);
        })
        .fail(function (jqXHR, statusText, errorThrown){
            $("#check-result").text(statusText);
        });
    });
</script>
<?php $this->end();