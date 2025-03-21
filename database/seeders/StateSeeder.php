<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('states')->insert([
            [
                'name' => 'Initiation du projet',
                'description' => 'Définir les objectifs et la portée du projet, réaliser une étude de faisabilité, identifier les parties prenantes et rédiger la charte du projet.',
            ],
            [
                'name' => 'Planification',
                'description' => 'Établir un plan détaillé (planning, jalons, livrables), définir les tâches, estimer les ressources, élaborer le budget et identifier les risques.',
            ],
            [
                'name' => 'Exécution',
                'description' => 'Mobiliser l’équipe, démarrer les travaux, assurer la communication et réaliser les tâches prévues pour produire les livrables.',
            ],
            [
                'name' => 'Suivi et Contrôle',
                'description' => 'Suivre l’avancement, contrôler la qualité, gérer les risques et appliquer les mesures correctives au besoin.',
            ],
            [
                'name' => 'Clôture du projet',
                'description' => 'Valider et livrer les livrables finaux, réaliser un bilan (leçons apprises) et clôturer administrativement et financièrement le projet.',
            ]
        ]);
    }
}
