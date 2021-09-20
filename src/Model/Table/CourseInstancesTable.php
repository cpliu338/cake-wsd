<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseInstances Model
 *
 * @property \App\Model\Table\CourseGroupsTable&\Cake\ORM\Association\BelongsTo $CourseGroups
 *
 * @method \App\Model\Entity\CourseInstance newEmptyEntity()
 * @method \App\Model\Entity\CourseInstance newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CourseInstance[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseInstance get($primaryKey, $options = [])
 * @method \App\Model\Entity\CourseInstance findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CourseInstance patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CourseInstance[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseInstance|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseInstance saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseInstance[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseInstance[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseInstance[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CourseInstance[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CourseInstancesTable extends Table
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

        $this->setTable('course_instances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CourseGroups', [
            'foreignKey' => 'course_group_id',
            'joinType' => 'INNER',
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
            ->scalar('code')
            ->maxLength('code', 30)
            ->notEmptyString('code');

        $validator
            ->scalar('start_at')
            ->maxLength('start_at', 30)
            ->requirePresence('start_at', 'create')
            ->notEmptyString('start_at');

        $validator
            ->scalar('end_at')
            ->maxLength('end_at', 30)
            ->requirePresence('end_at', 'create')
            ->notEmptyString('end_at');

        $validator
            ->scalar('venue')
            ->allowEmptyString('venue');

        $validator
            ->scalar('remark')
            ->allowEmptyString('remark');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['course_group_id'], 'CourseGroups'), ['errorField' => 'course_group_id']);

        return $rules;
    }
}
