<ul>
<?php foreach ($users as $user):?>
    <li><?= $user->name?></li>
<?php endforeach;?>
    <li><?=$identity['usename']?></li>
    <li><?=$identity['password']?></li>
    <li><?=$identity['hash']?></li>
</ul>
