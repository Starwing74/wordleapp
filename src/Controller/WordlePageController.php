<?php

namespace App\Controller;

use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session;

class WordlePageController extends AbstractController
{
    public $keyboard = [
    ['Q','W','E','R','T','R','Y','U','I','U','I','O','P'],
    ['A','S','D','F','G','H','J','K','L'],
    ['ENTER','Z','X','C','V','B','N','M','DELETE']
    ];

    public $tab = array();

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}", name="app_wordle_page")
     */
    public function index($nombreEssais, $tailleMot, \Symfony\Component\HttpFoundation\Request $request, CallApiService $callApiService): Response
    {
        for($i = 1; $i <= $nombreEssais; $i++){
            for($y = 1; $y <= $tailleMot; $y++){
                $this->tab[$i][$y] = "_";
            }
        }

        $session = $request->getSession();

        $data = $session->get('wordToGuess');

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $this->tab,
            'x2' => 0,
            'y2' => 1,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
            'data' => $data
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{buttonLettre}/{x}/{y}", name="keyboard_click")
     */
    public function keyboardButtons($nombreEssais ,$tailleMot ,$buttonLettre, $x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $x++;

        if($x > $tailleMot){
            $x = 1;
            $y++;
        }

        $session = $request->getSession();

        $tableau_wordle = $session->get('tableau_wordle', []);

        $tableau_wordle[$y][$x] = $buttonLettre;

        $session->set('tableau_wordle',$tableau_wordle);
        $data = $session->get('wordToGuess');

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tableau_wordle,
            'lettre' => $buttonLettre,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{x}/{y}", name="delete_click")
     */
    public function removeLetter($nombreEssais ,$tailleMot ,$x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();

        $tableau_wordle = $session->get('tableau_wordle', []);

        $tableau_wordle[$y][$x] = "_";

        $x--;

        $session->set('tableau_wordle',$tableau_wordle);
        $data = $session->get('wordToGuess');

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tableau_wordle,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
        ]);
    }
}
