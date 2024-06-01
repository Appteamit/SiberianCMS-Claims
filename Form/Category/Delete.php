<?php

/**
 * Class Claims_Form_Category_Delete
 */
class Claims_Form_Category_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/claims/application/delete-category"))
            ->setAttrib("id", "form-delete-claims-category")
            ->setConfirmText("You are about to remove this Category ! Are you sure ?");
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('claims_category')
            ->where('claims_category.id = :value')
        ;

        $category_id = $this->addSimpleHidden("id", p__('claims', 'Category'));
        $category_id->addValidator("Db_RecordExists", true, $select);
        $category_id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}