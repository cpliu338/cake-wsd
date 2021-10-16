<?php $div_id = sprintf("%s-%d", $type, $user_id);?>
<div class="ajax-box ajax-box-<?=$type?>" id="<?=$div_id?>">
    <h4><?=$title?></h4>
    <p class="result"></p><p class="error"></p>
</div>
<?php $this->append('script');?>
<script>
    $(function (){
        $.ajax({
            url: "<?= $this->Url->build(['action'=>'count-applications', $type, $user_id])?>",
            dataType: 'json',
            headers: {"X-CSRF-Token": $('meta[name="csrfToken"]').attr('content')}
        })
        .done(function(data){
            $("#<?=$div_id?> .result").html(data.result.count);
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            $("#<?=$div_id?> .error").html(textStatus);
        });
        /* always() */
    });
</script>
<?php $this->end();?> 
