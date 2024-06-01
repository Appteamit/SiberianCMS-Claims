<?php

class Claims_Form_Status extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/claims/application/save-status"))
            ->setAttrib("id", "form-add-claims")
            ->addNav("nav-add-claims", "Submit");
        
        self::addClass("create", $this); 
        
        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleText('status_name', p__('claims', 'Status Name'))->setRequired(true);     
        $this->addSimpleCheckbox('is_default', p__('claims', 'Is Default'));
        
   
    }

     public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  

}