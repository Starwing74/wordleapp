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
    ['Z','X','C','V','B','N','M']
    ];

    public $tab = array();
    public $tabCheck = array();

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/0/0", name="app_wordle_page")
     */
    public function index($nombreEssais, $tailleMot, \Symfony\Component\HttpFoundation\Request $request, CallApiService $callApiService): Response
    {

        for($i = 0; $i < $nombreEssais; $i++){
            for($y = 0; $y < $tailleMot; $y++){
                $this->tab[$i][$y] = ["_","0"];
            }
        }

        $session = $request->getSession();

        $data = $session->get('wordToGuess');
        $session->set('tableWordle',$this->tab);
        $session->set('tableCheck',$this->tabCheck);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $this->tab,
            'x2' => 0,
            'y2' => 0,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
            'data' => $data,
            'enter' => "no"
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{buttonLettre}/{x}/{y}", name="keyboard_click")
     */
    public function keyboardButtons($nombreEssais ,$tailleMot ,$buttonLettre, $x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');

        $tab[$y][$x][0] = strtolower($buttonLettre);

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        if(($x > $tailleMot-1) && ($y >= $nombreEssais-1)){
            return $this->redirectToRoute('app_score_page');
        }

        if($x == $tailleMot-1){
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
                'enter' => "yes"
            ]);
        }

        if($x != $tailleMot-1){
            $x++;
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
            'enter' => "no"
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{x}/{y}", name="enter_click")
     */
    public function EnterWord($nombreEssais ,$tailleMot ,$x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        $z = 0;
        $mot = "";

        while($z <= $tailleMot-1){

            $mot .= $tab[$y][$z][0];

            $z++;
        }

        $i = 0;
        $z = 0;

        while($i <= $tailleMot-1){
            while($z <= $tailleMot-1){
                if($data["word"][$i] == $mot[$z]){
                    $tab[$y][$z][1] = "-1";
                    break;
                }
                $z++;
            }
            $z = 0;
            $i++;
        }

        $i = 0;
        $score = 0;

        while($i <= $tailleMot-1){
            if($data["word"][$i] == $mot[$i]){
                $tab[$y][$i][1] = "1";
                $score += 1;
            }
            $i++;
        }

        if($score == $tailleMot){
            return $this->redirectToRoute('app_score_page');
        }

        $x = 0;
        $y++;
        $session->set('tableWordle',$tab);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
            'enter' => "no"
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{x}/{y}", name="delete_click")
     */
    public function removeLetter($nombreEssais ,$tailleMot ,$x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');

        if($x != 0){
            $tab[$y][$x-1][0] = "_";
            $x--;
        }else{
            $x = 0;
        }

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
            'enter' => "no"
        ]);
    }
}
