<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) return;
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('student')->after('email'); // admin | teacher | student | parent
            $t->string('phone')->nullable()->after('role');
        });
    }
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'role')) return;
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'phone']));
    }
};
