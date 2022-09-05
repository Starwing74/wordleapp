<?php

namespace App\Controller;

use App\Service\CallApiService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Output\OutputInterface;
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

        for($i = 0; $i < $nombreEssais; $i++){
            for($y = 0; $y < $tailleMot; $y++){
                $this->tab[$i][$y] = "_";
            }
        }

        $session = $request->getSession();

        $data = $session->get('wordToGuess');
        $session->set('tableWordle',$this->tab);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $this->tab,
            'x2' => 0,
            'y2' => 0,
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
        $session = $request->getSession();
        $tab = $session->get('tableWordle');

        $tab[$y][$x] = $buttonLettre;

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        $x++;

        if($x > $tailleMot){
            $x = 0;
            $y++;

            dd("let's verfie the word!");
        }

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
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
        $tab = $session->get('tableWordle');

        //dd($x."---". $y);

        $tab[$y][$x] = "_";
        $x--;

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
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
