<?php

/**
 * This is the model class for table "combo_relation".
 *
 * The followings are the available columns in table 'combo_relation':
 * @property integer $id
 * @property integer $combo_id
 * @property integer $rid
 * @property integer $qty
 * @property string $created
 * @property string $modified
 */
class ComboRelation extends CActiveRecord
{
    public $product_name ='';
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'combo_relation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('combo_id, rid, qty', 'required'),
			array('combo_id, rid, qty', 'numerical', 'integerOnly'=>true),
			array('created, modified, qty', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, combo_id, rid, qty, created, modified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'combo_id' => 'Combo',
			'rid' => 'Rid',
            'qty' => 'Quantity',
			'created' => 'Created',
			'modified' => 'Modified',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('combo_id',$this->combo_id);
		$criteria->compare('rid',$this->rid);
		$criteria->compare('qty',$this->quantity);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('modified',$this->modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ComboRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function afterSave(){
        $this->product_name = ContentProduct::model()->findByPk($this->rid)->name;
    }

    public function afterFind(){
        $this->product_name = ContentProduct::model()->findByPk($this->rid)->name;
    }
}
