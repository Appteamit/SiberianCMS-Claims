<?php

class Claims_Form_Category extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/claims/application/save-category"))
            ->setAttrib("id", "form-add-claims");
        
        self::addClass("create", $this); 
        
        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleText('category_name', p__('claims', 'Category Name'))->setRequired(true);     
     
    }

     public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  

}