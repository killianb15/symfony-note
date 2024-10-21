<?php

// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Action sur le fichier json le read et l'afficher après
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/data/Note.json';
        $jsonData = file_get_contents($jsonFile);
        $tableData = json_decode($jsonData, true);
        return $this->render('home/index.html.twig', [
            'tableData' => $tableData
        ]);
    }

    #[Route('/add-note', name: 'add_note', methods: ['POST'])]
    public function addNote(Request $request): Response
    {
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/data/Note.json';
        $sujet = $request->request->get('sujet');
        $note = $request->request->get('note');
        $matiere = $request->request->get('matiere');
        $professeur = $request->request->get('professeur');

        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        $data[] = [
            'sujet' => $sujet,
            'note' => (int) $note,
            'matière' => $matiere,
            'professeur' => $professeur
        ];
        # JSON_PRETTY_PRINT rendre le json plus lisible 
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

        return $this->json([
            'sujet' => $sujet,
            'note' => $note,
            'matière' => $matiere,
            'professeur' => $professeur
        ]);
    }
    #[Route('/delete-note', name: 'delete_note', methods: ['POST'])]
    public function deleteNote(Request $request): Response
    {
        // Action sur le fichier json le read et l'afficher après
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/data/Note.json';
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        $requestData = json_decode($request->getContent(), true);

        $sujet = $requestData['sujet'];
        $note = $requestData['note'];
        $matiere = $requestData['matière'];
        $professeur = $requestData['professeur'];

        // mettre les valeur dans $data
        $data = array_filter($data, function($row) use ($sujet, $note, $matiere, $professeur) {
            return !($row['sujet'] === $sujet && $row['note'] == $note && $row['matière'] === $matiere && $row['professeur'] === $professeur);
        });
        $data = array_values($data);
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
        return $this->json(['status' => 'success']);
    }

}

