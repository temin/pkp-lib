<?php

/**
 * @file classes/notification/managerDelegate/EditorAssignmentNotificationManager.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EditorAssignmentNotificationManager
 * @ingroup managerDelegate
 *
 * @brief Editor assignment notification types manager delegate.
 */

namespace PKP\notification\managerDelegate;

use APP\notification\Notification;
use PKP\db\DAORegistry;
use PKP\notification\NotificationManagerDelegate;

use PKP\notification\PKPNotification;

class EditorAssignmentNotificationManager extends NotificationManagerDelegate
{
    /**
     * @copydoc PKPNotificationOperationManager::getNotificationMessage($notification)
     */
    public function getNotificationMessage($request, $notification)
    {
        switch ($notification->getType()) {
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_SUBMISSION:
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_INTERNAL_REVIEW:
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_EXTERNAL_REVIEW:
                return __('notification.type.editorAssignment');
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_EDITING:
                return __('notification.type.editorAssignmentEditing');
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_PRODUCTION:
                return __('notification.type.editorAssignmentProduction');
        }
    }

    /**
     * @copydoc PKPNotificationOperationManager::getStyleClass()
     */
    public function getStyleClass($notification)
    {
        return NOTIFICATION_STYLE_CLASS_WARNING;
    }

    /**
     * @copydoc PKPNotificationOperationManager::isVisibleToAllUsers()
     */
    public function isVisibleToAllUsers($notificationType, $assocType, $assocId)
    {
        return true;
    }

    /**
     * @copydoc NotificationManagerDelegate::updateNotification()
     *
     * If we have a stage without a manager role user, then
     * a notification must be inserted or maintained for the submission.
     * If a user with this role is assigned to the stage, the notification
     * should be deleted.
     * Every user that have access to the stage should see the notification.
     */
    public function updateNotification($request, $userIds, $assocType, $assocId)
    {
        $context = $request->getContext();
        $notificationType = $this->getNotificationType();
        $submissionId = $assocId;

        // Check for an existing NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_...
        $notificationDao = DAORegistry::getDAO('NotificationDAO'); /** @var NotificationDAO $notificationDao */
        $notificationFactory = $notificationDao->getByAssoc(
            ASSOC_TYPE_SUBMISSION,
            $submissionId,
            null,
            $notificationType,
            $context->getId()
        );

        // Check for editor stage assignment.
        $stageAssignmentDao = DAORegistry::getDAO('StageAssignmentDAO'); /** @var StageAssignmentDAO $stageAssignmentDao */
        $editorAssigned = $stageAssignmentDao->editorAssignedToStage($submissionId, $this->_getStageIdByNotificationType());

        // Decide if we have to create or delete a notification.
        $notification = $notificationFactory->next();
        if ($editorAssigned && $notification) {
            // Delete the notification.
            $notificationDao->deleteObject($notification);
        } elseif (!$editorAssigned && !$notification) {
            // Create a notification.
            $this->createNotification(
                $request,
                null,
                $notificationType,
                $context->getId(),
                ASSOC_TYPE_SUBMISSION,
                $submissionId,
                Notification::NOTIFICATION_LEVEL_TASK
            );
        }
    }


    //
    // Helper methods.
    //
    /**
     * Return the correct stage id based on the notification type.
     *
     * @return int
     */
    public function _getStageIdByNotificationType()
    {
        switch ($this->getNotificationType()) {
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_SUBMISSION:
                return WORKFLOW_STAGE_ID_SUBMISSION;
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_INTERNAL_REVIEW:
                return WORKFLOW_STAGE_ID_INTERNAL_REVIEW;
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_EXTERNAL_REVIEW:
                return WORKFLOW_STAGE_ID_EXTERNAL_REVIEW;
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_EDITING:
                return WORKFLOW_STAGE_ID_EDITING;
            case PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_PRODUCTION:
                return WORKFLOW_STAGE_ID_PRODUCTION;
            default:
                return null;
        }
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\PKP\notification\managerDelegate\EditorAssignmentNotificationManager', '\EditorAssignmentNotificationManager');
}
