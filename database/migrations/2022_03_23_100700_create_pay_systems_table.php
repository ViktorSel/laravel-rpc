<?php

use App\Models\PaySystem;
use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_systems', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id');
            $table->foreign('shop_id')
                ->on('shops')
                ->references('id')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->enum('label',PaySystem::PAY_SYSTEMS)->default(PaySystem::PAY_SYSTEMS[0]);
            $table->json('config')->default(json_encode(PaySystem::CONFIG_SYSTEMS[PaySystem::PAY_SYSTEMS[0]]));
            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
            $table->unique(['shop_id','label','active']);
        });
        DB::statement('ALTER TABLE pay_systems ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_systems');
    }
};
