<?php

namespace App\Console\Commands;

use App\Models\EvaluationResult;
use App\Models\Enrollment;
use Illuminate\Console\Command;

class UpdateCompletedEnrollments extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'enrollments:sync-completion';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sincroniza los enrollments con evaluaciones aprobadas, marcándolos como completados';

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de enrollments completados...');

        // Obtener todos los resultados de evaluaciones aprobadas
        $approvedResults = EvaluationResult::where('passed', true)
            ->with('evaluation:id,course_id', 'user:id')
            ->get();

        $updated = 0;

        foreach ($approvedResults as $result) {
            $enrollment = Enrollment::where('user_id', $result->user_id)
                ->where('course_id', $result->evaluation->course_id)
                ->first();

            if ($enrollment && $enrollment->status !== 'completado') {
                $enrollment->update([
                    'status' => 'completado',
                    'completed_at' => $result->attempted_at
                ]);
                $updated++;
            }
        }

        $this->info("✓ Se actualizaron {$updated} enrollments a estado 'completado'");
    }
}
