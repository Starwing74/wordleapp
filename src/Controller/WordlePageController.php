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

        [['Q',"0"],['W',"0"],['E',"0"],['R',"0"],['T',"0"],['Y',"0"],['U',"0"],['I',"0"],['O',"0"],['P',"0"]],
        [['A',"0"],['S',"0"],['D',"0"],['F',"0"],['G',"0"],['H',"0"],['J',"0"],['K',"0"],['L',"0"]],
        [['Z',"0"],['X',"0"],['C',"0"],['V',"0"],['B',"0"],['N',"0"],['M',"0"]]

    ];

    public $tab = array();
    public $tabCheck = array();

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/start/0/0", name="app_wordle_page")
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
        $session->set('keyboard',$this->keyboard);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $this->tab,
            'x2' => 0,
            'y2' => 0,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $this->keyboard,
            'controller_name' => 'WordlePageController',
            'data' => $data,
            'enter' => "no",
            'exist' => "yes"
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{buttonLettre}/{x}/{y}", name="keyboard_click")
     */
    public function keyboardButtons($nombreEssais ,$tailleMot ,$buttonLettre, $x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');
        $keyboard= $session->get('keyboard');
        $data = $session->get('wordToGuess');

        if($buttonLettre == "delete"){

            if($x != 0){
                $tab[$y][$x-1][0] = "_";
                $x--;
            }else{
                $x = 0;
            }

            $session->set('tableWordle',$tab);

            return $this->render('wordle_page/index.html.twig', [
                'tableau_wordle' => $tab,
                'lettre' => $buttonLettre,
                'x2' => $x,
                'y2' => $y,
                'nombreEssais' => $nombreEssais,
                'tailleMot' => $tailleMot,
                'keyboard' => $keyboard,
                'data' => $data,
                'controller_name' => 'WordlePageController',
                'enter' => "no",
                'exist' => "yes"
            ]);
        }

        if($x == $tailleMot){
            $tab[$y][$x-1][0] = strtolower($buttonLettre);

            $x = $tailleMot;

            $session->set('tableWordle',$tab);
        }else{
            $tab[$y][$x][0] = strtolower($buttonLettre);
            $session->set('tableWordle',$tab);

            if($x <= $tailleMot){
                $x++;
            }
        }

        if($x == $tailleMot){
            return $this->render('wordle_page/index.html.twig', [
                'tableau_wordle' => $tab,
                'lettre' => $buttonLettre,
                'x2' => $x,
                'y2' => $y,
                'nombreEssais' => $nombreEssais,
                'tailleMot' => $tailleMot,
                'keyboard' => $keyboard,
                'data' => $data,
                'controller_name' => 'WordlePageController',
                'enter' => "yes",
                'exist' => "yes"
            ]);
        }

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
            'lettre' => $buttonLettre,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
            'enter' => "no",
            'exist' => "yes"
        ]);
    }

    /**
     * @Route("/wordle/page/{nombreEssais}/{tailleMot}/{x}/{y}", name="enter_click")
     */
    public function EnterWord($nombreEssais ,$tailleMot ,$x, $y, \Symfony\Component\HttpFoundation\Request $request, CallApiService $callApiService): Response
    {

        $session = $request->getSession();
        $tab = $session->get('tableWordle');
        $keyboard= $session->get('keyboard');

        $session->set('tableWordle',$tab);
        $data = $session->get('wordToGuess');

        $z = 0;
        $mot = "";

        while($z <= $tailleMot-1){

            $mot .= $tab[$y][$z][0];

            $z++;
        }

        $result = $callApiService->checkWordExist($mot);

        if($result == false){

            return $this->render('wordle_page/index.html.twig', [
                'tableau_wordle' => $tab,
                'x2' => $x,
                'y2' => $y,
                'nombreEssais' => $nombreEssais,
                'tailleMot' => $tailleMot,
                'keyboard' => $keyboard,
                'data' => $data,
                'controller_name' => 'WordlePageController',
                'enter' => "no",
                'exist' => "no",
            ]);
        }

        $i = 0;

        while($i <= $tailleMot-1){
            if($data["word"][$i] != $mot[$i]){
                $tab[$y][$i][1] = "2";
            }
            $i++;
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
            $session->set('tableWordle',$tab);
            return $this->redirectToRoute('app_score_page', ['won' => "yes",]);
        }

        if(($x >= $tailleMot-1) && ($y >= $nombreEssais-1)){
            $session->set('tableWordle',$tab);
            if($score == $tailleMot){
                return $this->redirectToRoute('app_score_page', ['won' => "yes",]);
            }else{
                return $this->redirectToRoute('app_score_page', ['won' => "no"]);
            }
        }

        $i = 0;
        $z = 0;
        $m = 0;

        while($i <= $tailleMot-1){
            while($z <= count($keyboard) - 1){
                while($m <= count($keyboard[$z]) - 1) {
                    if($tab[$y][$i][1] == "-1") {
                        if($tab[$y][$i][0] == strtolower($keyboard[$z][$m][0])){
                            if($keyboard[$z][$m][1] == "1"){
                                $keyboard[$z][$m][1] = "1";
                            }else{
                                $keyboard[$z][$m][1] = "-1";
                            }
                        }
                    }
                    if($tab[$y][$i][1] == "1") {
                        if($tab[$y][$i][0] == strtolower($keyboard[$z][$m][0])){
                            $keyboard[$z][$m][1] = "1";
                        }
                    }
                    if($tab[$y][$i][1] == "2") {
                        if($tab[$y][$i][0] == strtolower($keyboard[$z][$m][0])){
                            $keyboard[$z][$m][1] = "2";
                        }
                    }
                    $m++;
                }
                $m = 0;
                $z++;
            }
            $z = 0;
            $i++;
        }

        $x = 0;
        $y++;
        $session->set('tableWordle',$tab);
        $session->set('keyboard',$keyboard);

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
            'x2' => $x,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
            'enter' => "no",
            'exist' => "yes"
        ]);
    }

    /**
     * @Route("/wordle/deleteline/{nombreEssais}/{tailleMot}/{x}/{y}", name="delete_all_click")
     */
    public function removeLineLetters($nombreEssais ,$tailleMot ,$x, $y, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');
        $keyboard= $session->get('keyboard');

        $i = 0;

        while($i <= $tailleMot-1){
            $tab[$y][$i][0] = "_";
            $tab[$y][$i][1] = "0";
            $i++;
        }

        $session->set('tableWordle',$tab);

        $data = $session->get('wordToGuess');

        return $this->render('wordle_page/index.html.twig', [
            'tableau_wordle' => $tab,
            'x2' => 0,
            'y2' => $y,
            'nombreEssais' => $nombreEssais,
            'tailleMot' => $tailleMot,
            'keyboard' => $keyboard,
            'data' => $data,
            'controller_name' => 'WordlePageController',
            'enter' => "no",
            'exist' => "yes"
        ]);
    }
}
