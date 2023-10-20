<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use App\Traits\SensesNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExportDownloadReady extends Notification implements ShouldQueue
{
    use Queueable, SensesNotification;

    protected $file;
    protected $transaction;
    protected $title;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $file, ?Transaction $transaction = null, $title = null)
    {
        $this->file = $file;
        $this->transaction = $transaction;
        $this->title = $title;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**s
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $exportDescription = 'You can now download your export';
        if(isset($this->title)){
            $exportTitle = Str::title(str_replace('_', ' ', $this->title));
            $exportDescription = "You can now download your " . $exportTitle . " export";
        }
        return [
            'title' => 'Export Download Ready',
            'secondary_title' => $exportTitle . ' Table',
            'description' => $exportDescription,
            'url' => [
                'signed'=> true,
                'title' => 'Download',
                'type' => 'download',
                'href' => [
                    'route' => 'api.exports.download',
                    'params' => [
                        'export' => $this->file,
                    ],
                ]
            ]
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        $exportDescription = 'You can now download your export';
        if(isset($this->title)){
            $exportTitle = Str::title(str_replace('_', ' ', $this->title));
            $exportDescription = "You can now download your " . $exportTitle . " export";
        }
        return [
            'title' => 'Export Download Ready',
            'secondary_title' => $exportTitle . ' Table',
            'description' => $exportDescription,
            'url' => [
                'signed'=> true,
                'title' => 'Download',
                'type' => 'download',
                'href' => URL::temporarySignedRoute('api.exports.download', now()->addMinutes(30), [
                    'export' => $this->file,
                ])
            ]
        ];
    }
}
