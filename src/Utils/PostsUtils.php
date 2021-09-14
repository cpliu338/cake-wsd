<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\Datasource\ConnectionManager;

class PostsUtils {

    public function findSubordinates(array $posts, int $limit) {
        $connection = ConnectionManager::get('default');

        $ar = array_map(function ($p) { return sprintf("JSON_CONTAINS(recommending_posts, ?)");}, $posts);
        $condition = implode(' or ', $ar);
        // select id,title from posts where json_contains(approving_posts,'"CWI(M)/S&T"');
        $statement = $connection->prepare(//"select id,title from posts where id=?");
            "select id,title from posts where $condition LIMIT $limit");
        for ($i=0; $i<count($posts); $i++)
            $statement->bindValue($i+1, sprintf('"%s"', $posts[$i]), 'string');
        $statement->execute();
   
        return $statement->fetchAll('assoc');
    }

}