<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\Datasource\ConnectionManager;

class PostsUtils {

    /**
     * Find subordinates, i.e. posts whose recommding or approving posts of the given posts
     * @param $posts the given posts
     * @param $mode recommending | approving, nothing else
     * @param $limit the max rows return, 0 means no limit
     * @return array of found posts: [[id=xx, title=>'STO'], [id=xx, title=>'TO'], ...]
     */
    public function findSubordinates(array $posts, string $mode, int $limit=0) {
        if (!in_array($mode, ["recommending","approving"])) return [];
        $connection = ConnectionManager::get('default');

        $ar = array_map(function ($p) use ($mode) { return sprintf("JSON_CONTAINS(%s_posts, ?)", $mode);}, $posts);
        $condition = implode(' or ', $ar);
        // select id,title from posts where json_contains(approving_posts,'"CWI(M)/S&T"');
        $sql = "select id,title from posts where $condition ORDER BY title" . (($limit <1) ? "" : " LIMIT $limit");
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