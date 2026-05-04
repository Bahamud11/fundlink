<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationManager extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('livewire.notification-manager', [
            'notifications' => $notifications
        ])->layout('layouts.app');
    }
}
