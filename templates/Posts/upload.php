<?php
    echo $this->Form->create(null, ['url'=>['action'=>'download']], ['id'=>'upload-form']),
    $this->Form->control('file', ['type'=>'file']),
    $this->Form->button('Upload', ['id'=>'upload']),
    $this->Form->end();
?>
<table id="upload-result" class="table">
    <thead>
        <tr><!--th></th><th>Title</th><th>Division</th-->
        <?php foreach ($columns as $col):?>
        <th style="text-transform: capitalize"><?=$col?></th>
        <?php endforeach;?>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr><td>
            <?=$this->Form->button('Confirm', ['id'=>'confirm'])?>
        </td></tr>
    </tfoot>
</table>
<?php
    $this->append('script');
?>
<script>
    $("#upload").click(function (ev) {
        ev.preventDefault();
        formData = new FormData($(this).closest('form')[0]);
        serial = $("#upload-result tr:last-child").data("serial");
        if (serial===undefined) serial = 0;
        formData.append("serial", serial);
        $.ajax({
            type: 'post',
            url: "<?=$this->Url->build(['action'=>'upload'])?>",
            contentType: false,
            processData: false,
            //dataType: "json",
            data: formData
        }).done(function(data){
            $("#upload-result tbody").append(data);
        });
    });
    $("#confirm").click(function (ev) {
        selector = "#upload-result";// td";
        data = confirm(selector, 2);/*
        for (let i=0; i<$(selector).length; i++){
            if (i%cols_per_row == 0) continue;
            array.push($(selector)[i].innerText);
        }
        data = {rows: array};*/
        //console.log(JSON.stringify(data)); return;
        $.ajax({
            type: 'post',
            url: "<?=$this->Url->build(['action'=>'confirm'])?>",
            headers: {"X-CSRF-TOKEN": $("meta[name=csrfToken").attr("content")},
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify({rows: data})
        }).done(function(data){
            console.log(JSON.stringify(data));
        });
    });
    /**
    * @param selector #upload-result
    */
    function confirm(selector, cols_per_row) {
        headers = <?=json_encode($columns, JSON_UNESCAPED_SLASHES)?>;
        select= selector + " thead tr:nth-child(1) ";
        cols_per_row = 2;
        array = []; 
        for (let i=0; i<$(selector + " tbody tr").length; i++){
            row = {};
            for (let j=0; j<headers.length; j++) {
                row[headers[j]] = 
                $(selector + " tbody tr:nth-child("+ (i+1) + ") td:nth-child("+ (j+1)+")").text();
            }
            array.push(row);
        }
        return array;
    }

    function confirm1(selector, cols_per_row) {
        array = []; row = [];
        for (let i=0; i<$(selector).length; i++){
            if (i % (cols_per_row+1) == 0) {
                if (row.length>0)
                    array.push(row);
                row = [];
                continue;
            }
            row.push($(selector)[i].innerText);
        }
        if (row.length>0)
            array.push(row);
        data = {rows: array};
        return data;
    }
</script>
<?php $this->end();