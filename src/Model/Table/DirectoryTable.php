<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Directory Model
 *
 * @method \App\Model\Entity\Directory newEmptyEntity()
 * @method \App\Model\Entity\Directory newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Directory[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Directory get($primaryKey, $options = [])
 * @method \App\Model\Entity\Directory findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Directory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Directory[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Directory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Directory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Directory[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Directory[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Directory[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Directory[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DirectoryTable extends Table
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

        $this->setTable('directory');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
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
            ->notEmptyString('staffid');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('rank_code')
            ->maxLength('rank_code', 255)
            ->allowEmptyString('rank_code');

        $validator
            ->scalar('post_name')
            ->maxLength('post_name', 255)
            ->allowEmptyString('post_name');

        $validator
            ->scalar('tree_code')
            ->maxLength('tree_code', 45)
            ->allowEmptyString('tree_code');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 255)
            ->allowEmptyString('phone');

        $validator
            ->scalar('notesemail')
            ->maxLength('notesemail', 255)
            ->allowEmptyString('notesemail');

        $validator
            ->scalar('dposting_type')
            ->maxLength('dposting_type', 45)
            ->allowEmptyString('dposting_type');

        return $validator;
    }
}
