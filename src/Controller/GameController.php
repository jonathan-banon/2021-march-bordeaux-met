<?php

namespace App\Controller;

use App\Model\GameManager;
use App\Model\DepartmentManager;
use App\Model\ScoreManager;
use App\Service\ConnexionAPI;

class GameController extends AbstractController
{
    public function department()
    {
        if (!isset($_SESSION['pseudo'])) {
            header('Location: /');
        }

        $departmentManager = new DepartmentManager();
        $departments = $departmentManager->selectAll();
        return $this->twig->render('Game/department.html.twig', ['departments' => $departments]);
    }

    public function quizz($departmentId)
    {
        $connexionAPI = new ConnexionAPI();

        $pickedObject = $connexionAPI->showRandArtPiece(intval($departmentId));


        return $this->twig->render(
            'Game/quizz.html.twig',
            ['pickedObject' => $pickedObject, 'departmentId' => $departmentId]
        );
    }

    public function score($idSelected)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //TODO POST data to secure
            header('Location: /Game/score/' . $_POST['idDepartmentSelected']);
        }

        $departmentManager = new DepartmentManager();
        $departments = $departmentManager->selectAll();
        $scoreManager = new ScoreManager();
        $scores = $scoreManager->getScoresByDepartment($idSelected);
        return $this->twig->render('Game/score.html.twig', [
            'departments' => $departments,
            'idSelected' => $idSelected,
            'scores' => $scores
        ]);
    }

    public function solution()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $connexionApi = new ConnexionAPI();
            $objectData = $connexionApi->showObjectById(intval($_POST['objectId']));
            return $this->twig->render(
                'Game/solution.html.twig',
                ['answer' => $_POST['answer'],
                'objectData' => $objectData,
                'deptId' => $_POST['department'],
                    ]
            );
        }
        header('Location: /');
    }
}
