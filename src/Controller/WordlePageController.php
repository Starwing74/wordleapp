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
    public $tabCheck = array();

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}", name="app_wordle_page")
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

        $tab[$y][$x][0] = strtolower($buttonLettre);

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        $x++;

        $tab2 = array();

        if($x > $tailleMot-1){
            $z = 0;
            $mot = "";
            while($z <= $tailleMot-1){

                $mot .= $tab[$y][$z][0];

                $z++;
            }

            $x = 0;
            $z = 0;

            while($x <= $tailleMot-1){
                while($z <= $tailleMot-1){
                    $tab2[$x][$z] = [$data["word"][$x],$mot[$z]];
                    if($data["word"][$x] == $mot[$x]){
                        $tab[$y][$x][1] = "1";
                        break;
                    }
                    if($data["word"][$x] == $mot[$z]){
                        $tab[$y][$z][1] = "-1";
                        break;
                    }
                    $z++;
                }
                $z = 0;
                $x++;
            }
            $x = 0;
            $y++;
            $session->set('tableWordle',$tab);
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

        $tab[$y][$x][0] = "_";
        $x--;

        if($x == 0) {
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
        ]);
    }
}
