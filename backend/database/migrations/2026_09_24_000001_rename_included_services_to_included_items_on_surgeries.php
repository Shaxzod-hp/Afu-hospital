<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Controller va frontend `included_items` bilan ishlaydi, ustun esa
     * `included_services` deb nomlangan edi — ma'lumot saqlanmay qolardi.
     */
    public function up(): void
    {
        if (Schema::hasColumn('surgeries', 'included_items')) {
            return;
        }

        Schema::table('surgeries', function (Blueprint $table) {
            $table->json('included_items')->nullable()->after('price');
        });

        if (Schema::hasColumn('surgeries', 'included_services')) {
            DB::table('surgeries')->update(['included_items' => DB::raw('included_services')]);

            Schema::table('surgeries', function (Blueprint $table) {
                $table->dropColumn('included_services');
            });
        }
    }

    public function down(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            $table->json('included_services')->nullable()->after('price');
        });

        DB::table('surgeries')->update(['included_services' => DB::raw('included_items')]);

        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropColumn('included_items');
        });
    }
};
