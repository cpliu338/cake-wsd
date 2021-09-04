<ul>
<?php foreach ($users as $user):?>
    <li><?= $user->username?></li>
<?php endforeach;?>
    <li><?=$identity['hash']?></li>
</ul>
