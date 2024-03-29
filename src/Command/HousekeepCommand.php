<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Collection\Collection;
use DateTime;
use App\Model\Entity\CourseGroup;

/**
 * Simple console wrapper around Psy\Shell.
 */
class HousekeepCommand extends Command
{
    /**
     * Housekeeping for the database
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $model = $args->getArgument('model');
        /*
        switch ($model) {
            case 'Users': return $this->housekeepUsers($io); break;
            case 'Posts': return $this->housekeepPosts($io); break;
            case 'Test': return $this->housekeepTest($io); break;
            case 'CourseGroups': return $this->housekeepCourseGroups($io); break;
        }
        */
        $method = 'housekeep' . $model;
        $this->{$method}($io);
    }

    private function housekeepTest(ConsoleIo $io) {
        $this->log('notice', 'notice');
        $this->log('info', 'info'); 
        $this->log('debug','debug');
    }

    private function housekeepCourseGroups(ConsoleIo $io) {
        $courseGroupsTable = $this->getTableLocator()->get('CourseGroups');
        $now = (new DateTime())->format('YmdHis');
        $io->out($now);
        $todayAt9 = (new FrozenTime())->hour(9)->minute(0);
        $cg = $courseGroupsTable->newEntity([
            'title' => "Dummy course at $now",
            'application_close_at' => $todayAt9->addDays(10)
        ]);
        $io->out($todayAt9->nice());
    }

    private function housekeepInvite(ConsoleIo $io) {
        $cgid = 1892;
        //$courseGroupsTable = $this->getTableLocator()->get('CourseGroups');
        $usersTable = $this->getTableLocator()->get('Users');
        //$cg = $courseGroupsTable->get(1892);
        $posts_util = new \App\Utils\PostsUtils($this->getTableLocator());
        $posts = ['EE/M(NTE1)'];
        $course_util = new \App\Utils\CoursesUtils();
        $result2 = [];
        try {
            $subordinates = $posts_util->findSubordinates($posts, 'recommending');
            //$subordinate_ids = array_unique(array_map(function ($post) {return $post['user_id'];}, $subordinates));
            $result2 = $course_util->invite($cgid, $subordinates);
        }
        catch (\Exception $ex) {
            $io->out($ex->getMessage());
        }
        $io->out(var_export($result2, true));
    }

    private function housekeepUsers(ConsoleIo $io) {
        $users = $this->getTableLocator()->get('Users');
        $result = $users->updateAll(['password'=>'12IbR.gJ8wcpc'],[]);
        $io->out(var_export($result));
    }

    private function housekeepPosts(ConsoleIo $io) {
        // TODO trim leading and trailing spaces for post_name too? (got from directory), some posts are no good
        $posts = $this->getTableLocator()->get('Posts');
        $users = $this->getTableLocator()->get('Users');
        $vacant = $users->findByName('vacant')->first();
        if (empty($vacant)) {
            $io->out("User vacant not found");
            return 1;
        }
        $cnt = 0;
        $io->out("Start with rows count: " . $posts->find()->count());
        foreach ($posts->find()->order('id') as $p) {
            try {
                $users->get($p->user_id);
            }
            catch (\Exception $e) {
                $p->user_id = $vacant->id;
            }
            // trim title
            if (trim($p->title)!=$p->title) {
                $io->out(sprintf("Changed %s to %s", $p->post_name, trim($p->post_name)));
                $p->title = trim($p->title);
            }
            $p->approving_posts = json_encode(json_decode($p->approving_posts), JSON_UNESCAPED_SLASHES);
            $p->recommending_posts = json_encode(json_decode($p->recommending_posts), JSON_UNESCAPED_SLASHES);
            if ($posts->save($p))
                $cnt++;
            else {
                $io->out($p->id, $p->recommending_posts);
                break;
            }
        }
        $io->out("Done after rows $cnt");
    }

    public function getOptionParser(): ConsoleOptionParser
    {
        // Get an empty parser from the framework.
        $parser = parent::getOptionParser();

        $parser->addArgument('model', ['choices'=>[
            'Users', 'Posts', 'Test', 'CourseGroups', 'Invite'
        ], 'help'=>'Model to housekeep, Users | Posts | CourseGroups | ApplicationForms | Test' ,'required'=>true]);

        // add option dummyize

        return $parser;
    }
}