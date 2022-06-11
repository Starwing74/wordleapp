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

    public $nombre_essais = 7;

    public $taille_mot = 7;

    public $tab = array();

    /**
     * @Route("/wordle/page", name="app_wordle_page")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request, CallApiService $callApiService): Response
    {
        for($i = 1; $i <= $this->nombre_essais; $i++){
            for($y = 1; $y <= $this->taille_mot; $y++){
                $this->tab[$i][$y] = "_";
            }
        }

        $session = $request->getSession();

        $session->set('tableau_wordle',$this->tab);

        $data = $callApiService->getWordFromWordle($this->taille_mot);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $this->tab,
            'x2' => 0,
            'y2' => 1,
            'nombreEssais' => $this->nombre_essais,
            'tailleMot' => $this->taille_mot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
            'data' => $data
        ]);
    }

    /**
     * @Route("/wordle/page/{buttonLettre}/{x}/{y}", name="keyboard_click")
     */
    public function keyboardButtons($buttonLettre, $x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $x++;

        if($x > $this->taille_mot){
            $x = 1;
            $y++;
        }

        $session = $request->getSession();

        $tableau_wordle = $session->get('tableau_wordle', []);

        $tableau_wordle[$y][$x] = $buttonLettre;

        $session->set('tableau_wordle',$tableau_wordle);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tableau_wordle,
            'lettre' => $buttonLettre,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $this->nombre_essais,
            'tailleMot' => $this->taille_mot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
        ]);
    }

    /**
     * @Route("/wordle/page/{x}/{y}", name="delete_click")
     */
    public function removeLetter($x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();

        $tableau_wordle = $session->get('tableau_wordle', []);

        $tableau_wordle[$y][$x] = "_";

        $x--;

        $session->set('tableau_wordle',$tableau_wordle);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tableau_wordle,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $this->nombre_essais,
            'tailleMot' => $this->taille_mot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
        ]);
    }
}
