<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('private_session_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete(); // ربط مع الحجز الأساسي
            $table->dateTime('start_time'); // بداية الجلسة
            $table->dateTime('end_time')->nullable(); // نهاية الجلسة (تكون فارغة لو لسه جاري)
            $table->integer('attendees_count')->default(1); // عدد الأفراد في هذه الفترة
            $table->decimal('total_amount', 10, 2)->default(0); // التكلفة المحسوبة لهذه الفترة فقط
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_session_time_slots');
    }
};