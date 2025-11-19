<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mostrar todas las notificaciones del usuario
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read

        $notifications = Auth::user()->notifications();

        if ($filter === 'unread') {
            $notifications = Auth::user()->unreadNotifications();
        } elseif ($filter === 'read') {
            $notifications = Auth::user()->readNotifications();
        }

        $notifications = $notifications->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notificación eliminada');
    }
}
