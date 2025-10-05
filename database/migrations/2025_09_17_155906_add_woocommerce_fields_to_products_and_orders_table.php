<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWoocommerceFieldsToProductsAndOrdersTable extends Migration
{
    public function up()
    {
        // Products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'woocommerce_id')) {
                    $table->unsignedBigInteger('woocommerce_id')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('products', 'woocommerce_synced_at')) {
                    $table->timestamp('woocommerce_synced_at')->nullable()->after('woocommerce_id');
                }
            });
        }

        // Orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'woocommerce_id')) {
                    $table->unsignedBigInteger('woocommerce_id')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('orders', 'woocommerce_synced_at')) {
                    $table->timestamp('woocommerce_synced_at')->nullable()->after('woocommerce_id');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'woocommerce_synced_at')) {
                    $table->dropColumn('woocommerce_synced_at');
                }
                if (Schema::hasColumn('products', 'woocommerce_id')) {
                    $table->dropColumn('woocommerce_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'woocommerce_synced_at')) {
                    $table->dropColumn('woocommerce_synced_at');
                }
                if (Schema::hasColumn('orders', 'woocommerce_id')) {
                    $table->dropColumn('woocommerce_id');
                }
            });
        }
    }
}
