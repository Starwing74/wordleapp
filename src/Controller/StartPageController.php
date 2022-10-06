<?php

namespace App\Controller;

use App\DTO\settingDTO;
use App\Form\settingFormType;
use App\Service\CallApiService;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartPageController extends AbstractController
{
    /**
     * @Route("/startWordle", name="app_start_page")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request, CallApiService $callApiService): Response
    {
        $dto = new settingDTO();
        $session = $request->getSession();

        $form = $this->createForm(
            settingFormType::class,
            $dto
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $motaTrouver = $callApiService->getWordFromWordle($dto->tailleMot);

            $session->set('wordToGuess', $motaTrouver);
            $session->set('nombreEssais', $motaTrouver);
            $session->set('tailleMot', $motaTrouver);

            return $this->redirectToRoute('app_wordle_page', array('nombreEssais' => $dto->nombreEssais, 'tailleMot' => $dto->tailleMot));
        }

        return $this->render('start_page/index.html.twig', [
            'controller_name' => 'StartPageController',
            'form' => $form->createView(),
        ]);
    }
}
