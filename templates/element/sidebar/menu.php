<!-- Add icons to the links using the .nav-icon class
     with font-awesome or any other icon font library -->
<li class="nav-item has-treeview menu-open">
  <a href="#" class="nav-link active">
    <i class="nav-icon fas fa-tachometer-alt"></i>
    <p>
      Starter Pages
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="<?=$this->Url->build([
        'prefix'=>'Clerical', 'controller'=>'CourseGroups', 'action'=>'index'
      ])?>" class="nav-link active">
        <i class="far fa-circle nav-icon"></i>
        <p>Admin Courses</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Inactive Page</p>
      </a>
    </li>
  </ul>
</li>

<li class="nav-item">
    <?= $this->Form->postLink(
      __('Log out'), ['controller'=>'Users', 'action'=>'logout', 'prefix'=>false], ['class'=>'nav-link', 'confirm'=>'OK?'])
    ?>
</li>
