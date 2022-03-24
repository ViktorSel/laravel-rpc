<?php

use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('shops', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->boolean('active')->default(true);
                $table->string('label');
                $table->string('url');
                $table->string('success_url')->nullable();
                $table->string('fail_url')->nullable();
                $table->string('email');
                $table->decimal('inn',10,0,true);
                $table->enum('tax', Shop::TAX);
                $table->string('accounter');
                $table->decimal('accounter_inn',12,0,true);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                $table->softDeletes();

                $table->unique(['user_id','inn','active']);
            });
            DB::statement('ALTER TABLE shops ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
};
