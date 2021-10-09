<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\TableLocator;

class UsersUtils {

    use \Cake\Datasource\ModelAwareTrait;

    /* table objects 
    var $Posts;
    var $Users;

    public function __construct(TableLocator $tableLocator) {
        $this->Posts = $tableLocator->get('Posts');
        $this->Users = $tableLocator->get('Users'); 
    }*/

    /**
     * @param array $data is [username=>..., password=>...] although the form name for username is staffid
     */
    public function login(array $data) {
        $this->loadModel('Users');
        $user = $this->Users->find()->contain(['Posts'])->where([
            'staffid'=>$data['username'],
            'password'=>crypt($data['password'], $data['password'])
            ])->first();
        return $user ? $user->toArray() : [];
    }

}