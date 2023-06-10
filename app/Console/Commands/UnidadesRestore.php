<?php

namespace App\Console\Commands;

use App\Unidad;
use Illuminate\Console\Command;

class UnidadesRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unidades:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaura las unidades eliminadas lógicamente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Unidad $unidad)
    {
        $desactivados = Unidad::onlyTrashed()
            //->where('deleted_at', '>=', now()->addDays($unidad->desactivado)->toDateTimeString())
            ->whereDate('desactivado', now()->toDateString())
            ->get();

        foreach($desactivados as $restaurar)
        {
            $restaurar->restore();
        }

        echo "Hecho\n";
    }
}
