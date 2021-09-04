<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;

use Cake\View\View;
use CakeLte\View\CakeLteTrait;
/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    use CakeLteTrait;

    public $layout = 'cake_default';//'CakeLte.default';
  
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->initializeCakeLte($options = [    
            'appName' => '<b>Itnrs 4.0</b>', // [string] default='Cake<b>LTE</b>'
            'appLogo' => 'CakeLte.cake.icon.png', // [string] default='CakeLte.cake.icon.png'
        ]);
        /*
        If there is only one layout will be used in the entire site, you can set it here
        $this->layout = 'CakeLte.default'; 

        */
    }
}
