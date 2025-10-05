<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\WarehouseNotification;
use Illuminate\Support\Facades\Notification;

class WarehouseNotifications
{
    /**
     * Send a low stock alert notification
     */
    public static function lowStock(string $productName, int $currentStock, int $threshold): void
    {
        $notification = [
            'type' => 'warning',
            'icon' => 'exclamation-triangle',
            'title' => 'Low Stock Alert',
            'message' => "Product '{$productName}' is running low on stock ({$currentStock} units remaining, threshold: {$threshold})",
            'link' => route('warehouse.in-stock'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a damaged material notification
     */
    public static function damagedMaterial(string $materialName, int $quantity, string $reason): void
    {
        $notification = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'title' => 'Material Damage Reported',
            'message' => "{$quantity} units of '{$materialName}' reported as damaged. Reason: {$reason}",
            'link' => route('warehouse.damaged-materials'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a new feeding request notification
     */
    public static function newFeedingRequest(string $orderNumber, string $priority): void
    {
        $notification = [
            'type' => 'success',
            'icon' => 'utensils',
            'title' => 'New Feeding Request',
            'message' => "New {$priority} priority feeding request for order #{$orderNumber}",
            'link' => route('warehouse.feeding-requests'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send an exit permission request notification
     */
    public static function exitPermissionRequest(string $orderNumber): void
    {
        $notification = [
            'type' => 'primary',
            'icon' => 'door-open',
            'title' => 'Exit Permission Request',
            'message' => "New exit permission request for order #{$orderNumber}",
            'link' => route('warehouse.exit-permission'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a stock replenishment notification
     */
    public static function stockReplenished(string $productName, int $quantity): void
    {
        $notification = [
            'type' => 'info',
            'icon' => 'box',
            'title' => 'Stock Replenished',
            'message' => "Added {$quantity} units to '{$productName}' stock",
            'link' => route('warehouse.in-stock'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a return request notification
     */
    public static function returnRequest(string $orderNumber, string $reason): void
    {
        $notification = [
            'type' => 'warning',
            'icon' => 'undo',
            'title' => 'New Return Request',
            'message' => "Return request for order #{$orderNumber}. Reason: {$reason}",
            'link' => route('warehouse.returns-requests'),
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a batch operation completed notification
     */
    public static function batchOperationCompleted(string $operation, int $itemsProcessed): void
    {
        $notification = [
            'type' => 'success',
            'icon' => 'check-circle',
            'title' => 'Batch Operation Completed',
            'message' => "Successfully completed {$operation} on {$itemsProcessed} items",
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Send a system alert notification
     */
    public static function systemAlert(string $title, string $message, string $type = 'danger'): void
    {
        $notification = [
            'type' => $type,
            'icon' => 'exclamation-circle',
            'title' => $title,
            'message' => $message,
        ];

        static::notifyWarehouseStaff($notification);
    }

    /**
     * Notify all warehouse staff
     */
    protected static function notifyWarehouseStaff(array $notification): void
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'warehouse_staff')
                ->orWhere('name', 'warehouse_manager');
        })->get();

        Notification::send($users, new WarehouseNotification($notification));
    }
}
