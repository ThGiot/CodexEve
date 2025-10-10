<?php
//require dirname(__DIR__, 3) . '/vendor/autoload.php';
namespace Grid;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

class WordDevisExporter
{
    public static function export(array $horaires, string $associationName, string $cheminFichier = 'devis.docx')
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Titre principal
        $section->addText(
            'Demande de DEVIS',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // Nom de l'association
        $section->addText(
            $associationName,
            ['italic' => true, 'size' => 14],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400]
        );

        // Regrouper les horaires par jour
        $groupedByDay = [];
        foreach ($horaires as $row) {
            $jour = $row['jour_semaine'];
            $groupedByDay[$jour][] = $row;
        }

        // Pour chaque jour, ajouter un tableau
        foreach ($groupedByDay as $jour => $entries) {
            $section->addText($jour, ['bold' => true, 'size' => 12], ['spaceAfter' => 200]);

            // Crée un tableau avec bordures simples
            $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);

            // En-têtes
            $table->addRow();
            $table->addCell(2000)->addText('Jour');
            $table->addCell(4000)->addText('Poste');
            $table->addCell(2000)->addText('Heure de début');
            $table->addCell(2000)->addText('Heure de fin');

            foreach ($entries as $entry) {
                

                $table->addRow();
                $table->addCell(2000)->addText($entry['jour_semaine']);
                $table->addCell(4000)->addText($entry['poste_type'].' '.$entry['poste_nom']);
                $table->addCell(2000)->addText(substr($entry['heure_debut'], 0, -3));
                $table->addCell(2000)->addText(substr($entry['heure_fin'], 0, -3));
            }

            $section->addTextBreak(1);
        }

        // Sauvegarde du fichier
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($cheminFichier);

        return $cheminFichier;
    }
}

?>