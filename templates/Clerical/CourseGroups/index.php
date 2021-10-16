<table>
<?php foreach ($courseGroups as $cg):?>
    <tr>
        <td><?= $cg->title?></td>
    </tr>
<?php endforeach;?>
</table>
<?php
    echo $this->element('itnrs/ajax_box_style'),
    $this->element('itnrs/ajax_box', ['type'=>'approving', 'user_id'=>$user, 'title'=>'To approve']), 
    $this->element('itnrs/ajax_box', ['type'=>'recommending', 'user_id'=>$user, 'title'=>'To recommend']);