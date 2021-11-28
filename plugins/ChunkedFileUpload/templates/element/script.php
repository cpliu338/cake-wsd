<?php
    if (!defined('JQUPLOADSCRIPTLOADED')) {
        $this->append('script');
        echo 
        $this->Html->script('ChunkedFileUpload.jquery.ui.widget.js'),
        $this->Html->script('ChunkedFileUpload.jquery.iframe-transport.js'),
        $this->Html->script('ChunkedFileUpload.jquery.fileupload.js');
?>
<script>
    function objectifyForm(formArray) {
    /* Credit https://stackoverflow.com/questions/1184624/convert-form-data-to-javascript-object-with-jquery?page=1&tab=votes#tab-top
    serialize data function, formArray is from something like $("#form-id").serializeArray()
    */
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
    }
</script>
<?php
        $this->end();
        define('JQUPLOADSCRIPTLOADED', 1);
    }
?>
