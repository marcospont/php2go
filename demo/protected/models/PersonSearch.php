<?php

class PersonSearch extends SearchModel
{
	public function attributeLabels() {
		return array(
			'name' => 'Nome',
			'id_country' => 'País',
			'birth_date' => 'Nascimento',
			'is_user' => 'É usuário?'
		);
	}

	public function filters() {
		return array(
			'name' => array(
				'type' => 'string',
				'operator' => 'select'
			),
			'id_country' => array(
				'type' => 'string',
				'operator' => 'eq'
			),
			'birth_date' => array(
				'type' => 'date',
				'interval' => true,
				'startLabel' => 'Data de Nascimento Inicial',
				'endLabel' => 'Data de Nascimento Final'
			),
			'is_user' => array(
				'callback' => array($this, 'isUserCallback')
			)
		);
	}

	public function rules() {
		return array(
			array('birth_date[start],birth_date[end]', 'required'),
			array('name[val]', 'length', 'min' => 2),
 			array('birth_date[start]', 'compare', 'operator' => 'loet', 'peerAttribute' => 'birth_date[end]', 'dataType' => 'date')
		);
	}

	protected function isUserCallback($value) {
		if ($value == 1)
			return 'exists(select id_user from users where id_user = people.id_person)';
		return '';
	}
}