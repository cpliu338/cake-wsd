<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Cake\I18n\FrozenTime;
use DateTime;

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
        switch ($model) {
            case 'Users': return $this->housekeepUsers($io); break;
            case 'Posts': return $this->housekeepPosts($io); break;
            case 'Test': return $this->housekeepTest($io); break;
            case 'CourseGroups': return $this->housekeepCourseGroups($io); break;
        }
    }

    private function housekeepTest(ConsoleIo $io) {
        $posts = new \App\Utils\PostsUtils();
        $usersTable = $this->getTableLocator()->get('Users');
        foreach ($usersTable->find('ranksWithPosts', ['ranks'=>" SEE, SME"])->order('name') as $u) {
            $io->out($u->name);
            foreach ($u->posts as $p)
                $io->out($p->title);
        }
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
            'Users', 'Posts', 'Test', 'CourseGroups'
        ], 'help'=>'Model to housekeep, Users | Posts | CourseGroups | Test' ,'required'=>true]);

        // add option dummyize

        return $parser;
    }
}