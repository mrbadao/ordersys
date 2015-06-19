<?php

/**
 * This is the model class for table "content_product".
 *
 * The followings are the available columns in table 'content_product':
 * @property integer $id
 * @property string $name
 * @property string $thumbnail
 * @property string $description
 * @property string $price
 * @property integer $category_id
 * @property integer $del_flg
 * @property string $created
 * @property string $modified
 */
class ContentProduct extends CActiveRecord
{
    public $frendlyUrl = '';
    public $saleoff_price = '';
    public $saleoff_percent = '';
    public $saleoff_id = null;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'content_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, category_id, thumbnail', 'required'),
			array('category_id, del_flg', 'numerical', 'integerOnly'=>true),
			array('name, thumbnail', 'length', 'max'=>128),
			array('price', 'length', 'max'=>50),
			array('description, del_flg, created, modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, thumbnail, description, price, category_id, del_flg, created, modified', 'safe', 'on'=>'search'),
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
			'name' => 'Tên sản phẩm',
			'thumbnail' => 'Thumbnail',
			'description' => 'Miêu tả',
			'price' => 'Giá sản phẩm',
			'category_id' => 'Category',
			'del_flg' => 'Del Flg',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('thumbnail',$this->thumbnail,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('del_flg',$this->del_flg);
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
	 * @return ContentProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterSave(){
        $date = date("Y-m-d H:m:i");
        $c = new CDbCriteria();
        $c->alias = 's';
        $c->join = 'JOIN content_saleoff cs ON s.saleoff_id = cs.id';
        $c->addCondition('s.product_id = '.$this->id, 'AND');
        $c->addCondition('cs.enddate >= "'.$date.'"', 'AND');
        $c->addCondition('cs.startdate <= "'.$date.'"', 'AND');
        $c->order='s.modified DESC';

        $SaleoffRelation = SaleoffRelation::model()->find($c);

        if($SaleoffRelation){
            $ContentSaleOff = ContentSaleoff::model()->findByPk($SaleoffRelation->saleoff_id);
            $this->saleoff_price = $ContentSaleOff ? $this->price - ($this->price/100 * $ContentSaleOff->percent) : '';
            $this->saleoff_percent = $ContentSaleOff ? $ContentSaleOff->percent : '';
            $this->saleoff_id = $ContentSaleOff ? $ContentSaleOff->id : null;
        }

	}

	public function afterFind(){
        $date = date("Y-m-d H:m:i");
        $this->description = Helpers::removeHtmlTag($this->description);

        $c = new CDbCriteria();
        $c->alias = 's';
        $c->join = 'JOIN content_saleoff cs ON s.saleoff_id = cs.id';
        $c->addCondition('s.product_id = '.$this->id, 'AND');
        $c->addCondition('cs.enddate >= "'.$date.'"', 'AND');
        $c->addCondition('cs.startdate <= "'.$date.'"', 'AND');
        $c->order='s.modified DESC';

        $SaleoffRelation = SaleoffRelation::model()->find($c);

        if($SaleoffRelation){
            $ContentSaleOff = ContentSaleoff::model()->findByPk($SaleoffRelation->saleoff_id);
            $this->saleoff_price = $ContentSaleOff ? $this->price - ($this->price/100 * $ContentSaleOff->percent) : '';
            $this->saleoff_percent = $ContentSaleOff ? $ContentSaleOff->percent : '';
            $this->saleoff_id = $ContentSaleOff ? $ContentSaleOff->id : null;
        }
        return true;
	}

    public function getAttributes($names = true){
        return array_merge(array('saleoff_price' => $this->saleoff_price, 'saleoff_percent' => $this->saleoff_percent), parent::getAttributes());
    }
}
