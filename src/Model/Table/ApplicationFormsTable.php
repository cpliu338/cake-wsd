<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;

/**
 * ApplicationForms Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CourseGroupsTable&\Cake\ORM\Association\BelongsTo $CourseGroups
 *
 * @method \App\Model\Entity\ApplicationForm newEmptyEntity()
 * @method \App\Model\Entity\ApplicationForm newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ApplicationForm[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApplicationForm get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApplicationForm findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ApplicationForm patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApplicationForm[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApplicationForm|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApplicationForm saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApplicationForm[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApplicationForm[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApplicationForm[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ApplicationForm[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ApplicationFormsTable extends Table
{
    public function beforeSave(EventInterface $event, $entity, $options)
    {
        switch ($entity->status_priority) {
            case -2: $entity->status = 'Not Approved'; break;
            case -1: $entity->status = 'Not Recommended'; break;
            case 0: $entity->status = 'Fresh'; break;
            case 1: $entity->status = 'Applied'; break;
            case 2: $entity->status = 'Recommended'; break;
            case 3: $entity->status = 'Approved'; break;
            case 4: $entity->status = 'Nominated'; break;
            case 5: $entity->status = 'Confirmed'; break;
            default: $entity->status = $entity->status_priority;
        }
    }
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('application_forms');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
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
            ->scalar('choice')
            ->maxLength('choice', 30)
            ->allowEmptyString('choice');

        $validator
            ->boolean('accept_other')
            ->notEmptyString('accept_other');

        $validator
            ->scalar('attachment')
            ->maxLength('attachment', 255)
            ->allowEmptyString('attachment');

        $validator
            ->scalar('safety_expired_day')
            ->maxLength('safety_expired_day', 30)
            ->allowEmptyString('safety_expired_day');

        $validator
            ->scalar('status')
            ->maxLength('status', 32)
            //->requirePresence('status', 'create') comment this out or beforeSave does not fire on create
            ->notEmptyString('status');

        $validator
            ->integer('status_priority')
            ->notEmptyString('status_priority')
            ->add('status_priority', 'validValue', [
                'rule' => ['range', -2, 5]
            ]);
    
        $validator
            ->integer('recommendation_priority')
            ->notEmptyString('recommendation_priority');

        $validator
            ->integer('approval_priority')
            ->notEmptyString('approval_priority');

        $validator
            ->integer('nomination_priority')
            ->notEmptyString('nomination_priority');

        $validator
            ->dateTime('modified_at')
            ->notEmptyDateTime('modified_at');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['course_group_id'], 'CourseGroups'), ['errorField' => 'course_group_id']);

        return $rules;
    }
}
