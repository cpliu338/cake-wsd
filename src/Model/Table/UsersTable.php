<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ApplicationForms', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('Posts', [
            'foreignKey' => 'user_id',
        ]);
        $this->hasMany('TrainingRecords', [
            'foreignKey' => 'user_id',
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
            ->integer('staffid')
            ->requirePresence('staffid', 'create')
            ->notEmptyString('staffid')
            ->add('staffid', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('name_chi')
            ->maxLength('name_chi', 20)
            ->requirePresence('name_chi', 'create')
            ->notEmptyString('name_chi');

        $validator
            ->scalar('notesmail')
            ->maxLength('notesmail', 255)
            ->requirePresence('notesmail', 'create')
            ->notEmptyString('notesmail');

        $validator
            ->scalar('rank')
            ->maxLength('rank', 128)
            ->requirePresence('rank', 'create')
            ->notEmptyString('rank');

        $validator
            ->scalar('rank_code')
            ->maxLength('rank_code', 255)
            ->requirePresence('rank_code', 'create')
            ->notEmptyString('rank_code');

        $validator
            ->scalar('tree_code')
            ->maxLength('tree_code', 45)
            ->requirePresence('tree_code', 'create')
            ->notEmptyString('tree_code');

        $validator
            ->scalar('password')
            ->maxLength('password', 160)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('telephone')
            ->maxLength('telephone', 255)
            ->allowEmptyString('telephone');

        $validator
            ->integer('mobile')
            ->allowEmptyString('mobile');

        $validator
            ->scalar('id_prefix')
            ->maxLength('id_prefix', 16)
            ->notEmptyString('id_prefix');

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
        $rules->add($rules->isUnique(['staffid']), ['errorField' => 'staffid']);

        return $rules;
    }
}