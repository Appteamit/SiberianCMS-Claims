<?php

/**
 * Class Claims_Form_Status_Delete
 */
class Claims_Form_Status_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/claims/application/delete-status"))
            ->setAttrib("id", "form-delete-claims-status")
            ->setConfirmText("You are about to remove this status ! Are you sure ?");
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('claims_status')
            ->where('claims_status.id = :value')
        ;

        $category_id = $this->addSimpleHidden("id", p__('claims', 'Status'));
        $category_id->addValidator("Db_RecordExists", true, $select);
        $category_id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}