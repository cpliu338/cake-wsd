<?php
?>
<div class="users form content">
<?= $this->Form->create() ?>
<fieldset>
    <legend><?= __('Please enter your staff id and password') ?></legend>
    <?= $this->Form->control('staffid') ?>
    <?= $this->Form->control('password') ?>
</fieldset>
<?= $this->Form->button(__('Login')); ?>
<?= $this->Form->end() ?>
</div>
