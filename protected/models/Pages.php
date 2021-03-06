<?php

/**
 * This is the model class for table "{{pages}}".
 *
 * The followings are the available columns in table '{{pages}}':
 * @property integer $id
 * @property string $page
 * @property string $title
 * @property string $text
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Pages extends ActiveRecord
{
    const PAGE_PATTERN = 'a-zA-Z0-9-_';



	public function tableName()
	{
		return '{{pages}}';
	}

	public function rules()
	{
		return array(
			array('title, page, text', 'required'),
			array('status', 'numerical', 'integerOnly' => TRUE),
			array('page, title, seo_title, seo_description, seo_keywords', 'length', 'max' => 255),
			array('page, title', 'length', 'min' => 4),
            array('text', 'length', 'min' => 15),
            array('page', 'unique', 'criteria' => array('condition' => 'status != :status', 'params' => array('status' => ActiveRecord::STATUS_DELETED)), 'message' => Yii::t('main', 'Страница уже существует.')),
			array('page', 'match', 'pattern' => '#^([' . self::PAGE_PATTERN . ']+)$#', 'message' => Yii::t('main', 'В поле «{attribute}» можно ввести следующие символы ":chars".', array(':chars' => self::PAGE_PATTERN))),

            array('status', 'in', 'range' => array_keys(parent::getStatusList())),

			array('id, page, title, status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'page'              => Yii::t('main', 'Ссылка на страницу'),
			'title'             => Yii::t('main', 'Название'),
			'text'              => Yii::t('main', 'Текст'),
			'seo_title'         => Yii::t('main', 'СЕО заголовок'),
			'seo_description'   => Yii::t('main', 'СЕО описание'),
			'seo_keywords'      => Yii::t('main', 'СЕО ключевые слова'),
			'created_at'        => Yii::t('main', 'Дата создания'),
			'updated_at'        => Yii::t('main', 'Дата обновления'),
			'status'            => Yii::t('main', 'Статус'),
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, TRUE);
		$criteria->compare('page', $this->page, TRUE);
		$criteria->compare('title', $this->title, TRUE);
		$criteria->compare('status', $this->status);

        $criteria->scopes = array('not_deleted');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'created_at DESC'
            ),
            'pagination' => array(
                'pageSize' => 20,
                'pageVar' => 'page',
            ),
        ));
	}

    public function getStatusList()
    {
        $data = parent::getStatusList();
        unset($data[ActiveRecord::STATUS_DELETED]);

        return $data;
    }
}
