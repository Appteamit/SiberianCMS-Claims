<?php

/**
 * Class claims_ApplicationController
 */
class Claims_ApplicationController extends Application_Controller_Default
{
    /**
     * Editor
     */
    public function editAction()
    {
        parent::editAction();
    }

    /**
     *Save Category
     */
    public function saveCategoryAction()
    {
        try {
            $values = $this->getRequest()->getPost();
            $form = new  Claims_Form_Category();

            if ($form->isValid($values)) {
                $attendance = (new Claims_Model_Category())
                    ->find($values['id'], "id");
                $attendance->addData($values);
                $attendance->save();

                $payload = ["success" => "1", "success_message" => p__("clails", "Saved successfully"), 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0,];

            } else {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors(), "errors" => $form->getTextErrors(true),];
            }

        } catch (\Exception $e) {
            $payload = ["error" => true, "message" => $e->getMessage(),];
        }

        $this->_sendJson($payload);
    }

    /**
     *Edit Category
     */
    public function loadcategoryformAction()
    {

        if ($id = $this->getRequest()->getParam("id")) {
            try {

                $model = new Claims_Model_Category();
                $model->find($id);
                if ($model->getId()) {
                    $data = $model->getData();
                    $form = new Claims_Form_Category();
                    $form->populate($data);
                    $form->setElementValueById('value_id', $this->getCurrentOptionValue()->getId());
                    $form->addNav("edit-nav-category", "Save", false);
                    $form->removeNav("nav-add-category");
                    $form->setElementValueById('id', $model->getId());

                    $payload = array(
                        "check" => 'ready for use',
                        "success" => true,
                        "form" => $form->render(),
                        "message" => p__('clails', "Success."),
                    );

                } else {
                    $payload = array(
                        "error" => true,
                        "message" => p__('clails', 'Category you are trying to edit does not exists.'),
                    );
                }

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($payload);
    }


    public function deleteCategoryAction()
    {
        try {

            $request = $this->getRequest();
            $id = $request->getParam("id", null);

            $category = (new Claims_Model_Category())
                ->find($id);
            if (!$category->getId()) {
                throw new \Siberian\Exception("#07888-01" . p__("claims", "We are unable to delete this category!"));
            }

            $category->delete();

            $payload = [
                'success' => true,
                'message' => p__("claims", "Deleted successfully"),
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     *Save Status
     */
    public function saveStatusAction()
    {
        try {
            $values = $this->getRequest()->getPost();
            $form = new  Claims_Form_Status();

            if ($form->isValid($values)) {
                if ($values['is_default'] == 1) {
                    $defaultCheck = (new Claims_Model_Status())
                        ->find(['is_default' => $values['is_default'], 'value_id' => $values['value_id']]);
                    if ($defaultCheck->getId()) {
                        $defaultCheck->setIsDefault(0);
                        $defaultCheck->save();
                    }
                }

                $status = (new Claims_Model_Status())
                    ->find($values['id'], "id");
                $status->addData($values);
                $status->save();

                $payload = ["success" => "1", "success_message" => p__("claims", "Saved successfully"), 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0,];

            } else {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors(), "errors" => $form->getTextErrors(true),];
            }

        } catch (\Exception $e) {
            $payload = ["error" => true, "message" => $e->getMessage(),];
        }

        $this->_sendJson($payload);
    }

    /**
     *Edit Status
     */
    public function loadstatusformAction()
    {

        if ($id = $this->getRequest()->getParam("id")) {
            try {

                $model = new Claims_Model_Status();
                $model->find($id);
                if ($model->getId()) {
                    $data = $model->getData();
                    $form = new Claims_Form_Status();
                    $form->populate($data);
                    $form->setElementValueById('value_id', $this->getCurrentOptionValue()->getId());
                    $form->addNav("nav-add-status", "Save", false);
                    $form->removeNav("nav-add-claims");
                    $form->setElementValueById('id', $model->getId());

                    $payload = array(
                        "check" => 'ready for use',
                        "success" => true,
                        "form" => $form->render(),
                        "message" => p__('clails', "Success."),
                    );

                } else {
                    $payload = array(
                        "error" => true,
                        "message" => p__('clails', 'Status you are trying to edit does not exists.'),
                    );
                }

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($payload);
    }


    public function deleteStatusAction()
    {
        try {

            $request = $this->getRequest();
            $id = $request->getParam("id", null);

            $status = (new Claims_Model_Status())
                ->find($id);
            if (!$status->getId()) {
                throw new \Siberian\Exception("#07888-01" . p__("claims", "We are unable to delete this status!"));
            }

            $status->delete();

            $payload = [
                'success' => true,
                'message' => p__("claims", "Deleted successfully"),
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     * list
     */
    public function claimsAction()
    {
        $this->loadPartials();
    }


    public function fetchClaimsAction()
    {
        try {
            $request = $this->getRequest();
            $limit = $request->getParam("perPage", 25);
            $offset = $request->getParam("offset", 0);
            $sorts = $request->getParam("sorts", []);
            $queries = $request->getParam("queries", []);

            $filter = null;
            $startdate = null;
            $enddate = null;

            if (array_key_exists("search", $queries)) {
                $filter = $queries["search"];
            }
            if (array_key_exists("from", $queries)) {
                $startdate = date('Y-m-d', strtotime($queries["from"]));
            }
            if (array_key_exists("to", $queries)) {
                $enddate = date('Y-m-d', strtotime($queries["to"]));
            }
            if (array_key_exists("status_id", $queries)) {
                $status_id = $queries["status_id"];
            }


            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "status_id" => $status_id
            ];

            $value_id = (new Claims_Model_Claims())->getCurrentValueId();

            $application = $this->getApplication();
            $customers = (new Claims_Model_Business())
                ->findAllForApp($value_id, $params);

            $countAll = (new Claims_Model_Business())->countAllForApp($value_id);
            $countFiltered = (new Claims_Model_Business())->countAllForApp($value_id, $params);

            $customersJson = [];
            foreach ($customers as $customer) {
                $data = $customer->getData();
                $data['name'] = $customer->getFirstname() . ' ' . $customer->getLastname();;
                $data["start_date"] = date("F j, Y", strtotime($data["start_date"]));
                $data["end_date"] = date("F j, Y", strtotime($data["end_date"]));
                $data["created_at"] = date("F j, Y", strtotime($data["created_at"]));
                $data['amount'] = Core_Model_Language::getCurrencySymbol() . $data["amount"];
                $customersJson[] = $data;
            }

            $payload = [
                "records" => $customersJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0]
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     *
     */
    public function exportCsvAction()
    {
        if ($this->getApplication()->getId()) {

            try {

                $request = $this->getRequest();
                $queries = $request->getParam("queries", []);

                $filter = $request->getParam("search", null);
                $startdate = $request->getParam("from", null);
                $enddate = $request->getParam("to", null);

                $params = [
                    "filter" => $filter,
                    "startdate" => !empty($startdate) ? date('Y-m-d', strtotime($startdate)) : null,
                    "enddate" => !empty($enddate) ? date('Y-m-d', strtotime($enddate)) : null,
                ];

                $value_id = (new Claims_Model_Claims())->getCurrentValueId();
                $customers = (new Claims_Model_Business())
                    ->findAllForApp($value_id, $params);


                $csv_string = "FirstName,LastName,E-Mail,Start Date,End Date,total,Start Address,End Address\n";
                foreach ($customers as $customer) {
                    $data = $customer->getData();
                    $startaddress = str_replace(',', ' ', $data['startaddress']);
                    $endaddress = str_replace(',', ' ', $data['endaddress']);

                    $enddate = $data["enddate"] != null ? date('Y-m-d', strtotime($data["enddate"])) : '';

                    $csv_string .= $customer->getFirstname() . "," . $customer->getLastname() . "," . $customer->getEmail() . "," . date('Y-m-d', strtotime($data["startdate"])) . "," . $enddate . "," . $data['totaltime'] . "," . $startaddress . "," . $endaddress . "\n";
                }

                $date = date("Y-m-d_H-i-s");
                $filename = "customers_tracking_report_" . $date . ".csv";
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                echo $csv_string;
                exit();

            } catch (Exception $e) {
                if (APPLICATION_ENV === "development") {
                    Zend_Debug::dump($e);
                }
                return false;
            }
        }
    }


    /**
     * View
     */
    public function viewAction()
    {

        if ($id = $this->getRequest()->getParam('id')) {
            $business = (new Claims_Model_Business())->find($id);
            if (!$business->getId()) {
                $this->getRequest()->addError(p__("claims", "This claim does not exist."));
            }
        }

        $value_id = (new Claims_Model_Claims())->getCurrentValueId();
        $customer = (new Customer_Model_Customer())->find($business->getCustomerId());
        $status = (new Claims_Model_Status())->findAll(['value_id' => $value_id]);
        $items = (new Claims_Model_Item())->itemById($id);

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setBusiness($business)->setCustomer($customer)->setStatus($status)->setItems($items);
    }


    public function statusChangeAction()
    {

        try {

            if ($status_id = $this->getRequest()->getParam('status_id')) {

                $claim_id = $this->getRequest()->getParam('id');
                $claim_info = (new Claims_Model_Business())
                    ->find(['id' => $claim_id]);
                $claim_info->setStatusId($status_id);
                $claim_info->save();

                # Push not mandatory
                if (Push_Model_Message::hasIndividualPush() && class_exists("Push_Model_Customer_Message") && $claim_info->getCustomerId() > 0) {

                    $message_push = new Push_Model_Message();
                    $message_push->setMessageType(Push_Model_Message::TYPE_PUSH);
                    $data_push = [
                        "title" => $this->getApplication()->getName(),
                        "text" => p__('claims', 'Your claim status has beed updated!'),
                        "send_at" => time(),
                        "action_value" => $claim_info->getValueId(),
                        "value_id" => $claim_info->getValueId(),
                        "type_id" => $message_push->getMessageType(),
                        "app_id" => $this->getApplication()->getId(),
                        "send_to_all" => 0,
                        "send_to_specific_customer" => 1,
                    ];
                    $message_push->setData($data_push)->save();
                    $customer_message = new Push_Model_Customer_Message();
                    $customer_message_data = [
                        "customer_id" => $claim_info->getCustomerId(),
                        "message_id" => $message_push->getId(),
                    ];
                    $customer_message->setData($customer_message_data);
                    $customer_message->save();

                }
                # End push

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


}