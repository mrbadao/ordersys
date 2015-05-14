<?php

/**
 * This is the model class for table "delivery_staff".
 *
 * The followings are the available columns in table 'delivery_staff':
 * @property integer $id
 * @property string $login_id
 * @property string $pasword
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $created
 * @property string $modified
 */
class DeliveryStaff extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'delivery_staff';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, login_id, pasword, phone, email, address', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('login_id', 'length', 'max'=>20),
			array('login_id, email', 'unique', 'message'=> 'Login id đã tồn tại'),
			array('pasword, address', 'length', 'max'=>128),
			array('phone', 'length', 'max'=>15),
			array('email', 'length', 'max'=>60),
			array('email', 'email', 'message'=>'Email không hợp lệ'),
			array('created, modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login_id, pasword, phone, email, address, created, modified', 'safe', 'on'=>'search'),
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
			'login_id' => 'Login',
			'pasword' => 'Password',
			'phone' => 'Phone',
			'email' => 'Email',
			'address' => 'Địa chỉ',
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
		$criteria->compare('login_id',$this->login_id,true);
		$criteria->compare('pasword',$this->pasword,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('address',$this->address,true);
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
	 * @return DeliveryStaff the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function beforeSave(){
        if($this->isNewRecord){
            $this->created = date("Y-m-d H:i:s");
        }
        $this->modified = date("Y-m-d H:i:s");

        return true;
    }
}
