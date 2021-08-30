<?= $this->Form->button("Log out", ['action'=>'logout'])?>
<?php 
    echo "Hello $users";
    var_export($users);
?>
<?= $this->Html->css('CakeLte./AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>