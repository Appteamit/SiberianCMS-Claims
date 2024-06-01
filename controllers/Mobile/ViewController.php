<?php

use Siberian\Exception;
use Siberian\File;

/**
 * Class Claims_Mobile_ViewController
 */
class Claims_Mobile_ViewController extends Application_Controller_Mobile_Default
{
    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function findallAction()
    {
        try {
            $customerId = $this->_getCustomerId(true);
            $claims = (new Claims_Model_Business())
                ->getAllClaims($customerId);

            $claimsJson = [];
            foreach ($claims as $key => $value) {
                $value['currency_code'] = Core_Model_Language::getCurrencySymbol();
                $value['status_name'] = ucfirst($value['status_name']);
                $value['id'] = (integer)$value['id'];
                $value['start_date'] = !empty($value['start_date']) ? date("F j, Y", strtotime($value['start_date'])) : '';
                $value['end_date'] = !empty($value['end_date']) ? date("F j, Y", strtotime($value['end_date'])) : '';
                $value['title'] = ucfirst($value['title']);
                $claimsJson[] = $value;
            }

            $payload = [
                'success' => true,
                'page_title' => (string)$this->getCurrentOptionValue()->getTabbarName(),
                'claims' => $claimsJson
            ];
        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    public function claimSaveAction()
    {
        try {
            $request = $this->getRequest();
            $value_id = $request->getParam('value_id');
            $param = $request->getBodyParams();
            $customerId = $this->_getCustomerId(true);

            $start_date = "";
            if (!empty($param['start_date'])) {
                $startDate = new Zend_Date(strtotime($param['start_date']));
                $start_date = $startDate->toString('y-MM-dd');
            }

            $end_date = "";
            if (!empty($param['end_date'])) {
                $endDate = new Zend_Date(strtotime($param['end_date']));
                $end_date = $endDate->toString('y-MM-dd');
            }

            $getStatusId = (new Claims_Model_Status())
                ->find(['value_id' => $value_id, 'status_name' => 'Not Submitted']);

            $business = (new Claims_Model_Business())
                ->setValueId($value_id)
                ->setCustomerId($customerId)
                ->setTitle($param['title'])
                ->setLocation($param['location'])
                ->setStartDate($start_date)
                ->setEndDate($end_date)
                ->setAmount(0)
                ->setStatusId($getStatusId->getId())
                ->save();

            $payload = [
                'success' => true,
                'claim_id' => $business->getId()
            ];

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * @param $image
     * @return string
     * @throws Exception
     * @throws Zend_Exception
     */
    protected function _saveImageContent($image)
    {
        if (!preg_match("@^data:image/([^;]+);@", $image, $matches)) {
            throw new Exception($this->_("Unrecognized image format"));
        }
        $extension = $matches[1];
        $fileName = uniqid() . '.' . $extension;


        $relativePath = $this->getCurrentOptionValue()->getImagePathTo();

        $fullPath = Application_Model_Application::getBaseImagePath() . $relativePath;
        if (!is_dir($fullPath)) mkdir($fullPath, 0777, true);
        $filePath = $fullPath . '/' . $fileName;
        $contents = file_get_contents($image);
        if ($contents === FALSE) {
            throw new Exception($this->_("No uploaded image"));
        }
        $res = @File::putContents($filePath, $contents);
        if ($res === FALSE) throw new Exception('Unable to save image');
        $photoFile = $relativePath . '/' . $fileName;
        return $photoFile;
    }


    /**
     * Claim save
     *
     */
    public function claimDetailsByIdAction()
    {
        try {

            if ($value_id = $this->getRequest()->getParam('value_id')) {

                $claim_id = $this->getRequest()->getParam('claim_id');
                $claim_info = (new Claims_Model_Business())
                    ->find(['id' => $claim_id]);

                $claim_info_Json = [
                    'currency_code' => Core_Model_Language::getCurrencySymbol(),
                    'id' => (integer)$claim_info->getId(),
                    'title' => (string)ucfirst($claim_info->getTitle()),
                    'amount' => (string)$claim_info->getAmount(),
                    'paid_amount' => (string)$claim_info->getPaidAmount(),
                    'remaining_amount' => (string)$claim_info->getRemainingAmount(),
                    'status_id' => (integer)$claim_info->getStatusId(),
                    'start_date' => date("F j, Y", strtotime($claim_info->getStartDate())),
                    'end_date' => date("F j, Y", strtotime($claim_info->getEndDate())),
                ];

                $categories = (new Claims_Model_Category())->findAll(["value_id" => $value_id]);

                $categories_Json = [];
                foreach ($categories as $key => $value) {
                    $data = [];
                    $data['category_id'] = $value->getId();
                    $data['category_name'] = ucfirst($value->getCategoryName());
                    $categories_Json[] = $data;
                }

                $items = (new Claims_Model_Item())->itemById($claim_id);

                $itemsJson = [];
                foreach ($items as $key => $value) {
                    $value['currency_code'] = Core_Model_Language::getCurrencySymbol();
                    $value['id'] = (integer)$value['id'];
                    $value['expense_date'] = !empty($value['expense_date']) ? date("F j, Y", strtotime($value['expense_date'])) : '';
                    $value['receipts'] = (integer)$value['receipts'];
                    $itemsJson[] = $value;
                }

                $getStatusId = (new Claims_Model_Status())
                    ->find(['value_id' => $value_id, 'status_name' => 'Not Submitted']);
                $allow_edit = 0;
                if ($claim_info->getStatusId() == $getStatusId->getId()) {
                    $allow_edit = 1;
                }

                $payload = [
                    'success' => true,
                    'claim_info' => $claim_info_Json,
                    'categories' => $categories_Json,
                    'items' => $itemsJson,
                    'allow_edit' => $allow_edit
                ];
            }


        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * Claim find by id
     *
     */
    public function findClaimByIdAction()
    {
        try {
            if ($value_id = $this->getRequest()->getParam('value_id')) {
                $claim_id = $this->getRequest()->getParam('claim_id');
                $claimsInfo = (new Claims_Model_Business())
                    ->find($claim_id)->toArray();

                $payload = [
                    'success' => true,
                    'claim_info' => $claimsInfo
                ];
            }


        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    public function saveClaimItemAction()
    {

        try {

            if ($data = $this->getRequest()->getBodyParams()) {
                $claim_info = (new Claims_Model_Business())
                    ->find(['id' => $data['item']['claim_id']]);
                if (!$claim_info->getId()) {
                    throw new Exception(p__('claims', 'Claim is not available!'));
                }

                $itemModel = (new Claims_Model_Item())
                    ->setClaimId($data['item']['claim_id'])
                    ->setCategoryId($data['item']['category_id'])
                    ->setAmount($data['item']['amount'])
                    ->setExpenseDate(!empty($data['item']['expense_date']) ? $data['item']['expense_date'] : '')
                    ->setRemark(!empty($data['item']['remark']) ? $data['item']['remark'] : '')
                    ->save();

                $newAmount = $claim_info->getAmount() + $data['item']['amount'];

                $claim_info->setAmount($newAmount);
                $claim_info->setRemainingAmount($newAmount);
                $claim_info->save();

                // for upload Receipts image
                if (!empty($data["item"]["images"])) {
                    foreach ($data["item"]["images"] as $key => $value) {
                        $imageURL = $this->_saveImageContent($value);
                        $receipts = (new Claims_Model_Receipts())
                            ->setClaimId($data['item']['claim_id'])
                            ->setClaimItemId($itemModel->getId())
                            ->setImage($imageURL)
                            ->save();
                    }
                }


                $items = (new Claims_Model_Item())->itemById($data['item']['claim_id']);

                $itemsJson = [];
                foreach ($items as $key => $value) {
                    $value['currency_code'] = Core_Model_Language::getCurrencySymbol();
                    $value['id'] = (integer)$value['id'];
                    $value['expense_date'] = !empty($value['expense_date']) ? date("F j, Y", strtotime($value['expense_date'])) : '';
                    $value['receipts'] = (integer)$value['receipts'];
                    $itemsJson[] = $value;
                }


                $payload = [
                    'success' => true,
                    'items' => $itemsJson
                ];
            }


        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    public function submitClaimAction()
    {

        try {

            if ($value_id = $this->getRequest()->getParam('value_id')) {

                $claim_id = $this->getRequest()->getParam('claim_id');
                $claim_info = (new Claims_Model_Business())
                    ->find(['id' => $claim_id]);
                $getStatusId = (new Claims_Model_Status())
                    ->find(['value_id' => $value_id, 'is_default' => 1]);
                $claim_info->setStatusId($getStatusId->getId());
                $claim_info->save();

                $payload = [
                    'success' => true
                ];
            }


        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    /*    private function _sendClaimAdmin($param){
            if(empty($param)){
                return false;
            }

            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $sender = $config->getOption('sendermail');
            $layout = $this->getLayout()->loadEmail('claims', 'claims_sent_admin');

            $layout->getPartial('content_email')
                ->setEmail($param['email'])
                ->setMessage($param['message'])
                ->setBaseUrl($this->getRequest()->getBaseUrl())
                ->setCustomer($param['firstname'] . ' ' . $param['lastname'])
                ->setApp($this->getApplication()->getName())->setIcon($this->getApplication()->getIcon());

            $content = $layout->render();
            $mail = new Siberian_Mail();
            $mail->_is_default_mailer = false;
            $mail->setBodyHtml($content);
            $mail->setFrom($param['email'], $param['firstname'].' '.$param['lastname']);
            $mail->_sender_name = $param['firstname'].' '.$param['lastname'].' via '.$this->getApplication()->getName();
            $mail->addTo($param['admin_email'], "");
            $mail->setSubject(p__('propertylisting', 'New Claim Received by customer %s!', $param['firstname'] . ' ' . $param['lastname']));

            $mail->send();

        }*/

    public function deleteItemAction()
    {

        try {

            if ($value_id = $this->getRequest()->getParam('value_id')) {
                $item_id = $this->getRequest()->getParam('item_id');
                $item = (new Claims_Model_Item())
                    ->find(['id' => $item_id]);

                $claim_info = (new Claims_Model_Business())
                    ->find(['id' => $item->getClaimId()]);

                $newAmount = $claim_info->getAmount() - $item->getAmount();

                $claim_info->setAmount($newAmount);
                $claim_info->setRemainingAmount($newAmount);
                $claim_info->save();

                $item->delete();


                $payload = [
                    'success' => true
                ];
            }


        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * @param bool $throw
     * @return mixed
     * @throws Exception
     * @throws Zend_Exception
     */
    private function _getCustomerId($throw = true)
    {
        $session = $this->getSession();
        $customerId = $session->getCustomerId();
        if ($throw && empty($customerId)) {
            throw new Exception(p__('claims', 'Customer login required!'));
        }
        return $customerId;
    }

}
