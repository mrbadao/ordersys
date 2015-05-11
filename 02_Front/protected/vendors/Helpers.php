<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helper
 *
 * @author Administrator
 */
class Helpers {
    
    /**
     * 
     */
    public static function getRole(){
        $id = Yii::app()->user->id;
        if(!isset($id))
            Yii::app()->request->redirect('/admin/site/logout');
        $staff = User::model()->findByPk($id);
        if(!isset($staff))
            Yii::app()->request->redirect('/admin/site/logout');

        return $staff->is_super;
    }

    public static function checkAccessRule($options = null, $param = null){
        $errMes = 'Bạn không có quyền thực hiện hành động này.';
        $id = Yii::app()->user->id;
        if(!isset($id))
            Yii::app()->request->redirect('/admin/site/logout');

        $staff = User::model()->findByPk($id);

        if(!isset($staff))
            Yii::app()->request->redirect('/admin/site/logout');

        if(!in_array( $staff->is_super, $param, true)){
            throw new CHttpException(403, $errMes);
        }
    }

    public static function getFirstImg($contentHTML){
        $image = '';
        preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $contentHTML, $image);
        return ($image != null) ? $image[1] : substr(Yii::app()->request->getBaseUrl(true),0,-5).'upload/images/no_img.jpg';
    }

    public static function getNumChars($contentHTML, $num){
        $contentHTML = strip_tags($contentHTML);
        $worldList = array_slice(explode(' ', $contentHTML), 0, $num - 1);
        return (implode($worldList) != '') ? implode(' ',$worldList).' ...' : '';
    }

    public static function getTagDomain($str){
        $str = trim($str,'/');
        $str = trim($str);
        $str = strtolower($str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|� �|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|� �|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|� �|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|� �|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        $str = preg_replace('!\-+!', '-', $str);
        return $str;
    }

    public static function removeSQLInjectionChar($str){
        $str = trim($str,'/');
        $str = trim($str);
        return str_replace(array('&','<','>','\\','"',"'",'?','+',';'), '', $str);
    }

    public static function getTags($contentid, $relation){
        $c= new CDbCriteria();
        $c->alias ='t';
        $c->join = 'INNER JOIN tag_relation AS s ON s.tag_id = t.id';
        $c->addCondition('s.content_id = '.$contentid, 'AND');
        $c->addCondition('s.relation = "'.$relation.'"', 'AND');

        $ContentTags = ContentTag::model()->findAll($c);

        if($ContentTags){
            $tags = '';
            foreach($ContentTags as $tag){
                $tags .= $tag->name.'; ';
            }
            return $tags;
        }

        return null;
    }

    public static function deleteTags($contentid, $relation){
        $contentTags = TagRelation::model()->findAllByAttributes(array('content_id' => $contentid, 'relation' => $relation));
        foreach($contentTags as $rtag){
            $tag = ContentTag::model()->findByPk($rtag->tag_id);
            $tag->use_frequency -= 1;
            $tag->modified = date("Y-m-d H:m:i");
            $tag->save(false);
            $rtag->delete();
        }
    }

    public static  function saveTag($tagName, $contentid, $relation){
        if(!$tagName) return;

        $tagDomain = self::getTagDomain($tagName);

        $tag = ContentTag::model()->findByAttributes(array('abbr_cd' => $tagDomain));

        if($tag == null) $tag = new ContentTag();

        $tag->name = $tagName;
        $tag->abbr_cd = $tagDomain;

        if($tag->isNewRecord){
            $tag->created = date("Y-m-d H:m:i");
            $tag->use_frequency = 1;
        } else { $tag->use_frequency += 1; }

        $tag->modified = date("Y-m-d H:m:i");

        if($tag->validate()){
            $tag->save(false);
            $tagRelation = TagRelation::model()->findByAttributes(array('tag_id' =>$tag->id,
                                                                        'relation' => $relation,
                                                                        'content_id' => $contentid
                                                                 ));
            if($tagRelation == null){
                $tagRelation = new TagRelation();
                $tagRelation->tag_id = $tag->id;
                $tagRelation->relation = $relation;
                $tagRelation->content_id = $contentid;
                $tagRelation->created = date("Y-m-d H:m:i");
                $tagRelation->modified =date("Y-m-d H:m:i");
                $tagRelation->save(false);
            }
        }
    }
}

?>
