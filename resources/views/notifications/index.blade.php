@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Notificaciones</h1>
        <p class="mt-2 text-gray-600">Mantente al día con las actualizaciones de tus proyectos</p>
    </div>

    {{-- Tabs de filtrado --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               class="@if($filter === 'all') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Todas
                <span class="ml-2 @if($filter === 'all') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif py-0.5 px-2.5 rounded-full text-xs font-medium">
                    {{ auth()->user()->notifications()->count() }}
                </span>
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="@if($filter === 'unread') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                No leídas
                <span class="ml-2 @if($filter === 'unread') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif py-0.5 px-2.5 rounded-full text-xs font-medium">
                    {{ $unreadCount }}
                </span>
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read']) }}"
               class="@if($filter === 'read') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Leídas
            </a>
        </nav>
    </div>

    {{-- Acciones masivas --}}
    @if($unreadCount > 0)
    <div class="mb-4 flex justify-end">
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Marcar todas como leídas
            </button>
        </form>
    </div>
    @endif

    {{-- Lista de notificaciones --}}
    @if($notifications->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <li class="@if(!$notification->read_at) bg-blue-50 @endif hover:bg-gray-50 transition duration-150">
                        <a href="{{ $notification->data['url'] ?? '#' }}" class="block">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-start gap-4 flex-1">
                                        {{-- Icono --}}
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-{{ $notification->data['color'] ?? 'gray' }}-100 flex items-center justify-center">
                                                @php
                                                    $iconMap = [
                                                        'user-plus' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                                        'star' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                                                        'shield-check' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                                        'document-plus' => 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                                        'check-circle' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        'x-circle' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        'clipboard-check' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                                                        'exclamation-triangle' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                                        'exclamation-circle' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                    ];
                                                    $iconPath = $iconMap[$notification->data['icono'] ?? 'bell'] ?? 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
                                                @endphp
                                                <svg class="h-6 w-6 text-{{ $notification->data['color'] ?? 'gray' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                                                </svg>
                                            </div>
                                        </div>

                                        {{-- Contenido --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $notification->data['mensaje'] }}
                                            </p>
                                            @if(isset($notification->data['proyecto_nombre']))
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Proyecto: {{ $notification->data['proyecto_nombre'] }}
                                                </p>
                                            @endif
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        {{-- Indicador no leída --}}
                                        @if(!$notification->read_at)
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-blue-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Botón eliminar --}}
                                    <div class="ml-4 flex-shrink-0">
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" onclick="event.preventDefault(); if(confirm('¿Eliminar esta notificación?')) this.submit();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        {{-- Estado vacío --}}
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes notificaciones</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($filter === 'unread')
                    Todas tus notificaciones están leídas
                @elseif($filter === 'read')
                    No tienes notificaciones leídas aún
                @else
                    Cuando recibas notificaciones, aparecerán aquí
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
