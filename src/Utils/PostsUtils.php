<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\Locator\TableLocator;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;

class PostsUtils {

    /* table objects */
    var $Posts;
    var $ApplicationForms;

    public function __construct(TableLocator $tableLocator) {
        $this->Posts = $tableLocator->get('Posts');
        $this->ApplicationForms = $tableLocator->get('ApplicationForms'); 
    }

    /**
     * Find all the users recommend/approv -able courses, i.e. my subordinates' applied courses
     * @param int $user_id of the supervisor
     * @param string $action recommending | approving
     * @return Query the resulting query (ApplicationForms containing CourseGroups)
     */
    public function findMyActionableCourses($user_id, $action) : Query {
        //$user_id = $options['user_id'] ?? 0;
        $now = FrozenTime::now();
        $posts = $this->Posts->find()->where(['user_id'=>$user_id])->extract('title')->toArray();
        $subordinates = $this->findSubordinates($posts, $action);
        
        $users = array_map(function($elem){ return $elem['user_id'];}, $subordinates);
        $conditions = (empty($users)) ? ['ApplicationForms.user_id' => -1] : ['ApplicationForms.user_id IN' => $users];
        return $this->ApplicationForms->find()->contain(['CourseGroups'])->where(array_merge(
            $conditions,
            ['CourseGroups.application_close_at >' => $now]
        ));

        /*
        return $this->find()->contain(['CourseGroups'])->where([
            'ApplicationForms.user_id' => $user_id,
            'CourseGroups.application_close_at >' => $now
        ]);
        */
    }

    /**
     * Find subordinates, i.e. posts whose recommding or approving posts of the given posts
     * @param $posts the given posts
     * @param $mode recommending | approving, nothing else
     * @param $limit the max rows return, 0 means no limit
     * @return array of found posts: [[id=xx, user_id=>yyy, title=>'STO'], [id=xx, user_id=>yyy, title=>'TO'], ...]
     */
    public function findSubordinates(array $posts, string $mode, int $limit=0) {
        if (!in_array($mode, ["recommending","approving"])) return [];
        $connection = ConnectionManager::get('default');

        $ar = array_map(function ($p) use ($mode) { return sprintf("JSON_CONTAINS(%s_posts, ?)", $mode);}, $posts);
        $condition = implode(' or ', $ar);
        // select id,title from posts where json_contains(approving_posts,'"CWI(M)/S&T"');
        $sql = "select id,user_id,title from posts where $condition ORDER BY title" . (($limit <1) ? "" : " LIMIT $limit");
        $statement = $connection->prepare($sql);
        //$ar2 = [0 => $sql];
        for ($i=0; $i<count($posts); $i++) {
            $statement->bindValue($i+1, sprintf('"%s"', $posts[$i]), 'string');
            //$ar2[$i+1] = sprintf('"%s"', $posts[$i]);
        }
            //return $ar2;
        $statement->execute();
   
        return $statement->fetchAll('assoc');
    }

}