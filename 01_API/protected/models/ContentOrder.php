<?php

/**
 * This is the model class for table "content_order".
 *
 * The followings are the available columns in table 'content_order':
 * @property integer $id
 * @property string $name
 * @property string $customer_name
 * @property string $customer_address
 * @property string $order_phone
 * @property string $email
 * @property string $coordinate_lat
 * @property string $coordinate_long
 * @property integer $status
 * @property integer $delivery_id
 * @property string $created
 * @property string $completed
 */
class ContentOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'content_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, customer_name, customer_address, order_phone, email, coordinate_lat, coordinate_long', 'required'),
			array('status, delivery_id', 'numerical', 'integerOnly'=>true),
			array('name, customer_address', 'length', 'max'=>128),
			array('customer_name, email', 'length', 'max'=>100),
			array('order_phone', 'length', 'max'=>14),
			array('coordinate_lat, coordinate_long', 'length', 'max'=>50),
			array('created, completed', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, customer_name, customer_address, order_phone, email, coordinate_lat, coordinate_long, status, delivery_id, created, completed', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'customer_name' => 'Customer Name',
			'customer_address' => 'Customer Address',
			'order_phone' => 'Order Phone',
			'email' => 'Email',
			'coordinate_lat' => 'Coordinate Lat',
			'coordinate_long' => 'Coordinate Long',
			'status' => 'Status',
			'delivery_id' => 'Delivery',
			'created' => 'Created',
			'completed' => 'Completed',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('customer_name',$this->customer_name,true);
		$criteria->compare('customer_address',$this->customer_address,true);
		$criteria->compare('order_phone',$this->order_phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('coordinate_lat',$this->coordinate_lat,true);
		$criteria->compare('coordinate_long',$this->coordinate_long,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('delivery_id',$this->delivery_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('completed',$this->completed,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ContentOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
