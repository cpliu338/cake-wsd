<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseGroups Model
 *
 * @property \App\Model\Table\ApplicationFormsTable&\Cake\ORM\Association\HasMany $ApplicationForms
 * @property \App\Model\Table\CourseInstancesTable&\Cake\ORM\Association\HasMany $CourseInstances
 *
 * @method \App\Model\Entity\CourseGroup newEmptyEntity()
 * @method \App\Model\Entity\CourseGroup newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CourseGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\CourseGroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CourseGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CourseGroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CourseGroupsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('course_groups');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('ApplicationForms', [
            'foreignKey' => 'course_group_id',
        ]);
        $this->hasMany('CourseInstances', [
            'foreignKey' => 'course_group_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('application_close_at')
            ->maxLength('application_close_at', 30)
            ->requirePresence('application_close_at', 'create')
            ->notEmptyString('application_close_at');

        $validator
            ->scalar('recommendation_close_at')
            ->maxLength('recommendation_close_at', 30)
            ->requirePresence('recommendation_close_at', 'create')
            ->notEmptyString('recommendation_close_at');

        $validator
            ->scalar('approval_close_at')
            ->maxLength('approval_close_at', 30)
            ->requirePresence('approval_close_at', 'create')
            ->notEmptyString('approval_close_at');

        $validator
            ->scalar('nomination_close_at')
            ->maxLength('nomination_close_at', 30)
            ->requirePresence('nomination_close_at', 'create')
            ->notEmptyString('nomination_close_at');

        $validator
            ->scalar('remark')
            ->allowEmptyString('remark');

        $validator
            ->boolean('invited')
            ->notEmptyString('invited');

        $validator
            ->scalar('invited_ranks')
            ->maxLength('invited_ranks', 1024)
            ->notEmptyString('invited_ranks');

        $validator
            ->boolean('nominated')
            ->notEmptyString('nominated');

        $validator
            ->boolean('confirmed')
            ->notEmptyString('confirmed');

        $validator
            ->scalar('attachments')
            ->allowEmptyString('attachments');

        $validator
            ->notEmptyString('require_attachment');

        $validator
            ->scalar('division')
            ->maxLength('division', 16)
            ->notEmptyString('division');

        $validator
            ->notEmptyString('type');

        return $validator;
    }
}
