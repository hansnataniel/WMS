<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmingroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admingroups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->boolean('admingroup_c');
            $table->boolean('admingroup_r');
            $table->boolean('admingroup_u');
            $table->boolean('admingroup_d');

            $table->boolean('admin_c');
            $table->boolean('admin_r');
            $table->boolean('admin_u');
            $table->boolean('admin_d');

            $table->boolean('gudang_c');
            $table->boolean('gudang_r');
            $table->boolean('gudang_u');
            $table->boolean('gudang_d');

            $table->boolean('rak_c');
            $table->boolean('rak_r');
            $table->boolean('rak_u');
            $table->boolean('rak_d');

            $table->boolean('product_c');
            $table->boolean('product_r');
            $table->boolean('product_u');

            $table->boolean('kendaraan_c');
            $table->boolean('kendaraan_r');
            $table->boolean('kendaraan_u');
            $table->boolean('kendaraan_d');

            $table->boolean('supplier_c');
            $table->boolean('supplier_r');
            $table->boolean('supplier_u');
            $table->boolean('supplier_d');

            $table->boolean('po_c');
            $table->boolean('po_r');
            $table->boolean('po_u');
            $table->boolean('po_d');

            $table->boolean('ri_c');
            $table->boolean('ri_r');
            $table->boolean('ri_u');
            $table->boolean('ri_d');

            $table->boolean('hbt_r');

            $table->boolean('invoice_c');
            $table->boolean('invoice_r');
            $table->boolean('invoice_u');
            $table->boolean('invoice_d');

            $table->boolean('return_c');
            $table->boolean('return_r');
            $table->boolean('return_u');
            $table->boolean('return_d');

            $table->boolean('payment_c');
            $table->boolean('payment_r');
            $table->boolean('payment_u');
            $table->boolean('payment_d');



            $table->boolean('setting_u');

            $table->boolean('productphoto_c');
            $table->boolean('productphoto_r');
            $table->boolean('productphoto_d');

            $table->boolean('inventory_c');
            $table->boolean('inventory_r');

            $table->boolean('bank_c');
            $table->boolean('bank_r');
            $table->boolean('bank_u');

            $table->boolean('adjustment_c');
            $table->boolean('adjustment_r');
            $table->boolean('adjustment_u');
            $table->boolean('adjustment_d');

            $table->boolean('stockcard_r');

            $table->boolean('customer_c');
            $table->boolean('customer_r');
            $table->boolean('customer_u');
            $table->boolean('customer_d');

            $table->boolean('transaction_c');
            $table->boolean('transaction_r');
            $table->boolean('transaction_u');
            $table->boolean('transaction_d');

            $table->boolean('tpayment_c');
            $table->boolean('tpayment_r');
            $table->boolean('tpayment_u');
            $table->boolean('tpayment_d');

            $table->boolean('treturn_c');
            $table->boolean('treturn_r');
            $table->boolean('treturn_u');
            $table->boolean('treturn_d');
            
            $table->boolean('accountsrecievable_r');

            $table->boolean('account_c');
            $table->boolean('account_r');
            $table->boolean('account_u');
            $table->boolean('account_d');

            $table->boolean('accountdetail_c');
            $table->boolean('accountdetail_r');
            $table->boolean('accountdetail_u');
            $table->boolean('accountdetail_d');

            $table->boolean('income_r');

            $table->boolean('is_active');

            $table->integer('create_id');
            $table->integer('update_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admingroups');
    }
}
