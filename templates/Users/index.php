<?php
    echo $this->Form->create(null, ['valueSources' => 'query', 'class'=>'form-inline']);
    // You'll need to populate $authors in the template from your controller
    echo $this->Form->control('staffid');
    // Match the search param in your table configuration
    echo $this->Form->control('nameContains');
    echo $this->Form->button('Filter', ['type' => 'submit']);
    echo $this->Html->link('Reset', ['action' => 'index']);
    echo $this->Form->end();
?>
<table class="table table-bordered">
        <thead>
            <tr>
            <th><?= $this->Paginator->sort('staffid') ?></th>
            <th><?= $this->Paginator->sort('name') ?></th>
            <th><?= $this->Paginator->sort('rank') ?></th>
            <th><?= $this->Paginator->sort('tree_code') ?></th>
            </tr>
        </thead>
<?php foreach ($users as $user):?>
    <tr>
        <td><?= $user->staffid?></td>
        <td><?= $user->name?></td>
        <td><?= $user->rank?></td>
        <td><?= $user->tree_code?></td>
    </tr>
<?php endforeach;?>
</table>
