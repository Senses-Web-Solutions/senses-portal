<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponse;

use App\Models\Notification;

use App\Support\QueryBuilder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Notifications\ListNotificationRequest;
use App\Http\Requests\Notifications\ListUserNotificationRequest;
use App\Models\Transaction;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Notification
 *
 * APIs for managing notifications
 */
class NotificationController extends Controller
{

use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all notifications.
     * <aside><ul><li>list-invoice</li></ul></aside>
     */
    public function index(ListNotificationRequest $request)
    {
        return QueryBuilder::for(Notification::class)->list();
    }

    /**
     * userNotifications()
     *
     * Reads and returns a collection of all notifications for a user.
     * <aside><ul><li>list-notification</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     */
    public function userNotifications(ListUserNotificationRequest $request, $userID)
    {
        return QueryBuilder::for(Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID))->get();
    }

    /**
     * markAsRead()
     *
     * Marks a notification as read.
     * <aside><ul><li>update-notification</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam notification integer Notification ID. Example: 1
     */
    public function markAsRead(ListUserNotificationRequest $request, $userID, $notificationID)
    {
        Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID)->find($notificationID)->markAsRead();
        return $this->respond(['message' => 'Notification marked as read']);
    }

    /**
     * markAllTypeRead()
     *
     * Marks all notifications as read.
     * <aside><ul><li>update-notification</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam notification integer Notification ID. Example: 1
     */
    public function markAllTypeRead(ListUserNotificationRequest $request, $userID, $type)
    {
        $unreadNotifications = null;
        $unreadNotificationsQuery = Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID)->whereNull('read_at');

        if ($type == 'general') {
            $notificationTypes = ['App\Notifications\SlaOneHourRemaining', 'App\Notifications\ExportDownloadReady', 'App\\Notifications\\MessageReceived'];
            $unreadNotifications = $unreadNotificationsQuery->whereNotIn('type', $notificationTypes)->limit(25)->orderBy('created_at', 'asc')->get();
        }

        if ($type == 'slas') {
            $notificationType = 'App\Notifications\SlaOneHourRemaining';
            $unreadNotifications = $unreadNotificationsQuery->where('type', $notificationType)->limit(25)->orderBy('created_at', 'asc')->get();
        }

        if ($type == 'downloads') {
            $notificationType = 'App\Notifications\ExportDownloadReady';
            $unreadNotifications = $unreadNotificationsQuery->where('type', $notificationType)->limit(25)->orderBy('created_at', 'asc')->get();
        }

        if ($type == 'messages') {
            $notificationType = 'App\\Notifications\\MessageReceived';
            $unreadNotifications = $unreadNotificationsQuery->where('type', $notificationType)->limit(25)->orderBy('created_at', 'asc')->get();
        }

        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return $this->respond(['message' => 'All notifications marked as read']);
    }

    /**
     * markAllTypeRead()
     *
     * Marks all notifications as read.
     * <aside><ul><li>update-notification</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam notification integer Notification ID. Example: 1
     */
    public function markIDsAsRead(ListUserNotificationRequest $request, $userID)
    {
        $unreadNotifications = Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID)->whereNull('read_at')->whereIn('id', $request->ids)->get();

        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return $this->respond(['message' => 'All notifications marked as read']);
    }

    /**
     * markAllAsRead()
     *
     * Marks all notifications as read.
     * <aside><ul><li>update-notification</li></ul></aside>
     * @urlParam user integer User ID. Example: 1
     * @urlParam notification integer Notification ID. Example: 1
     */
    public function markAllAsRead(ListUserNotificationRequest $request, $userID)
    {
        $unreadNotifications = Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID)->whereNull('read_at')->limit(25)->orderBy('created_at', 'desc')->get();
        foreach ($unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return $this->respond(['message' => 'All notifications marked as read']);
    }

    /**
     * userNotificationCounts()
    */
    public function userNotificationCounts(ListUserNotificationRequest $request, $userID)
    {
        return [
            'notifications' => Notification::where('notifiable_type', 'user')->where('notifiable_id', $userID)->whereNull('read_at')->count(),
            'transactions' => Transaction::where('user_id', $userID)->whereNull('finished_at')->count()
        ];
    }
}
