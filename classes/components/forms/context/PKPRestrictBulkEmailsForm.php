<?php
/**
 * @file classes/components/form/site/PKPRestrictBulkEmailsForm.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPRestrictBulkEmailsForm
 * @ingroup classes_controllers_form
 *
 * @brief A form for setting restrictions on the sending of bulk emails in a context.
 */

namespace PKP\components\forms\context;

use PKP\components\forms\FieldOptions;
use PKP\components\forms\FormComponent;
use Illuminate\Support\LazyCollection;

define('FORM_RESTRICT_BULK_EMAILS', 'restrictBulkEmails');

class PKPRestrictBulkEmailsForm extends FormComponent
{
    /** @copydoc FormComponent::$id */
    public $id = FORM_RESTRICT_BULK_EMAILS;

    /** @copydoc FormComponent::$method */
    public $method = 'PUT';

    /**
     * Constructor
     *
     * @param string $action URL to submit the form to
     */
    public function __construct($action, $context, LazyCollection $userGroups)
    {
        $this->action = $action;

        $userGroupOptions = [];
        foreach ($userGroups as $userGroup) {
            $userGroupOptions[] = [
                'value' => $userGroup->getId(),
                'label' => $userGroup->getLocalizedData('name'),
            ];
        }

        $request = \Application::get()->getRequest();
        $siteSettingsUrl = $request->getDispatcher()->url($request, \PKPApplication::ROUTE_PAGE, null, 'admin', 'settings', null, null, 'setup/bulkEmails');

        $this->addField(new FieldOptions('disableBulkEmailUserGroups', [
            'label' => __('admin.settings.disableBulkEmailRoles.label'),
            'description' => __('admin.settings.disableBulkEmailRoles.description', ['siteSettingsUrl' => $siteSettingsUrl]),
            'value' => (array) $context->getData('disableBulkEmailUserGroups'),
            'options' => $userGroupOptions,
        ]));
    }
}
